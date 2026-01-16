#!/usr/bin/env python3
import argparse
import datetime
import os
import re
import subprocess
import sys
from collections import defaultdict

import yaml


METHODS = {"get", "post", "put", "patch", "delete", "options", "head"}


def find_repo_root(start_path):
    try:
        result = subprocess.run(
            ["git", "rev-parse", "--show-toplevel"],
            cwd=start_path,
            stdout=subprocess.PIPE,
            stderr=subprocess.DEVNULL,
            text=True,
            check=True,
        )
        return result.stdout.strip()
    except Exception:
        current = os.path.abspath(start_path)
        while True:
            if os.path.isdir(os.path.join(current, ".git")):
                return current
            parent = os.path.dirname(current)
            if parent == current:
                return os.path.abspath(start_path)
            current = parent


def find_openapi_path(repo_root):
    candidates = [
        os.path.join(repo_root, "api", "openapi.yaml"),
        os.path.join(repo_root, "openapi.yaml"),
    ]
    for c in candidates:
        if os.path.exists(c):
            return c
    for root, _, files in os.walk(repo_root):
        if "openapi.yaml" in files:
            return os.path.join(root, "openapi.yaml")
    return None


def normalize_path(path):
    if not path:
        return path
    path = path.strip()
    if len(path) > 1 and path.endswith("/"):
        path = path[:-1]
    path = re.sub(r"/+", "/", path)
    path = re.sub(r":([A-Za-z0-9_]+)", r"{\1}", path)
    return path


def collect_php_files(api_root):
    php_files = []
    for root, dirs, files in os.walk(api_root):
        if os.path.basename(root) == "lib":
            continue
        if "/lib/" in root.replace("\\", "/"):
            continue
        for file in files:
            if file.endswith(".php"):
                php_files.append(os.path.join(root, file))
    return php_files


def get_required_files_from_action(api_root):
    action_path = os.path.join(api_root, "action.php")
    if not os.path.exists(action_path):
        return None
    with open(action_path, "r", encoding="utf-8") as f:
        data = f.read()
    req_re = re.compile(r"(require|require_once|include|include_once)\s*(\(|\s)*['\"]([^'\"]+)['\"]")
    files = {action_path}
    for match in req_re.finditer(data):
        rel = match.group(3)
        rel = rel.lstrip("./")
        full = os.path.normpath(os.path.join(api_root, rel))
        if os.path.exists(full) and full.endswith(".php"):
            if "/lib/" in full.replace("\\", "/"):
                continue
            files.add(full)
    return sorted(files)


def extract_routes_from_file(path):
    with open(path, "r", encoding="utf-8") as f:
        data = f.read()

    route_re = re.compile(r"\$app->(get|post|put|patch|delete|options|head)\(\s*'([^']+)'", re.I)
    matches = list(route_re.finditer(data))
    routes = []
    for idx, match in enumerate(matches):
        method = match.group(1).upper()
        raw_path = match.group(2)
        if method == "OPTIONS":
            continue
        start = match.start()
        end = matches[idx + 1].start() if idx + 1 < len(matches) else len(data)
        block = data[start:end]
        has_auth_user = "ensureAuthenticatedUser" in block
        has_admin = "ensureAdminUser" in block
        auth_required = has_auth_user or has_admin
        has_body = "json_decode($app->request()->getBody())" in block

        status_codes = set()
        for m in re.finditer(r"respondApiError\(\s*\$app\s*,\s*(\d+)", block):
            status_codes.add(m.group(1))
        for m in re.finditer(r"response\(\)->status\((\d+)\)", block):
            status_codes.add(m.group(1))
        for m in re.finditer(r"setStatus\((\d+)\)", block):
            status_codes.add(m.group(1))
        if has_auth_user:
            status_codes.add("401")
        if has_admin:
            status_codes.update(["401", "403"])

        routes.append(
            {
                "method": method,
                "path": normalize_path(raw_path),
                "handler": os.path.relpath(path, os.getcwd()),
                "authRequired": auth_required,
                "hasBody": has_body,
                "statusCodes": sorted(status_codes),
                "note": "",
            }
        )
    return routes


def build_route_inventory(api_root):
    routes = []
    required_files = get_required_files_from_action(api_root)
    files = required_files if required_files is not None else collect_php_files(api_root)
    for path in files:
        routes.extend(extract_routes_from_file(path))
    return routes


def build_openapi_inventory(openapi_path):
    with open(openapi_path, "r", encoding="utf-8") as f:
        spec = yaml.safe_load(f)

    paths = spec.get("paths", {}) or {}
    inventory = []
    for raw_path, ops in paths.items():
        if not isinstance(ops, dict):
            continue
        for method, op in ops.items():
            if method.lower() not in METHODS:
                continue
            operation = op or {}
            security = operation.get("security")
            inventory.append(
                {
                    "method": method.upper(),
                    "path": normalize_path(raw_path),
                    "security": security,
                    "requestBody": operation.get("requestBody"),
                    "responses": list((operation.get("responses") or {}).keys()),
                }
            )
    return spec, inventory


def compare_inventories(code_routes, openapi_routes):
    code_set = {(r["path"], r["method"]) for r in code_routes}
    openapi_set = {(r["path"], r["method"]) for r in openapi_routes}

    code_by_path = defaultdict(set)
    for r in code_routes:
        code_by_path[r["path"]].add(r["method"])

    openapi_by_path = defaultdict(set)
    for r in openapi_routes:
        openapi_by_path[r["path"]].add(r["method"])

    implemented_not_documented = sorted(code_set - openapi_set)
    documented_not_implemented = sorted(openapi_set - code_set)

    method_mismatch = []
    for path in sorted(set(code_by_path.keys()) & set(openapi_by_path.keys())):
        if code_by_path[path] != openapi_by_path[path]:
            method_mismatch.append(
                {
                    "path": path,
                    "codeMethods": sorted(code_by_path[path]),
                    "openapiMethods": sorted(openapi_by_path[path]),
                }
            )

    contract_mismatch = []
    openapi_map = {(r["path"], r["method"]): r for r in openapi_routes}
    for route in code_routes:
        key = (route["path"], route["method"])
        if key not in openapi_map:
            continue
        op = openapi_map[key]
        mismatch = {}

        openapi_security = op.get("security")
        openapi_requires_auth = openapi_security is not None and len(openapi_security) > 0
        if route["authRequired"] != openapi_requires_auth:
            mismatch["security"] = {
                "code": route["authRequired"],
                "openapi": openapi_security,
            }

        openapi_has_body = op.get("requestBody") is not None
        if route["hasBody"] != openapi_has_body and route["method"] not in ("GET", "DELETE"):
            mismatch["requestBody"] = {
                "code": route["hasBody"],
                "openapi": openapi_has_body,
            }

        code_status = set(route["statusCodes"])
        openapi_status = set(op.get("responses") or [])
        if code_status - openapi_status:
            mismatch["responses"] = {
                "code": sorted(code_status),
                "openapi": sorted(openapi_status),
            }

        if mismatch:
            contract_mismatch.append(
                {
                    "path": route["path"],
                    "method": route["method"],
                    "diff": mismatch,
                }
            )

    return {
        "implemented_not_documented": implemented_not_documented,
        "documented_not_implemented": documented_not_implemented,
        "method_mismatch": method_mismatch,
        "contract_mismatch": contract_mismatch,
    }


def ensure_security_scheme(spec):
    components = spec.setdefault("components", {})
    schemes = components.setdefault("securitySchemes", {})
    if "bearerAuth" not in schemes:
        schemes["bearerAuth"] = {
            "type": "http",
            "scheme": "bearer",
            "bearerFormat": "JWT",
        }


def add_missing_routes(spec, code_routes, implemented_not_documented):
    if not implemented_not_documented:
        return
    paths = spec.setdefault("paths", {})
    ensure_security_scheme(spec)

    code_map = {(r["path"], r["method"]): r for r in code_routes}

    for path, method in implemented_not_documented:
        route = code_map[(path, method)]
        entry = paths.setdefault(path, {})
        op = {
            "summary": "Auto-generated from code inventory",
            "description": "Auto-generated from code inventory. TODO: refine request/response schemas.",
        }
        if route["authRequired"]:
            op["security"] = [{"bearerAuth": []}]

        if route["hasBody"]:
            op["requestBody"] = {
                "required": True,
                "content": {
                    "application/json": {
                        "schema": {"type": "object", "additionalProperties": True}
                    }
                },
            }

        responses = {}
        status_codes = route["statusCodes"]
        if status_codes:
            for code in status_codes:
                if code == "204":
                    responses[code] = {"description": "No Content"}
                else:
                    responses[code] = {
                        "description": "Response",
                        "content": {
                            "application/json": {
                                "schema": {"type": "object", "additionalProperties": True}
                            }
                        },
                    }
        else:
            responses["200"] = {
                "description": "Response",
                "content": {
                    "application/json": {
                        "schema": {"type": "object", "additionalProperties": True}
                    }
                },
            }

        if "responses" not in op:
            op["responses"] = responses

        entry[method.lower()] = op


def write_report(report_path, repo_root, openapi_path, diff, code_routes, openapi_routes):
    os.makedirs(os.path.dirname(report_path), exist_ok=True)
    now = datetime.datetime.now(datetime.timezone.utc).strftime("%Y-%m-%d %H:%M:%S UTC")
    try:
        git_hash = subprocess.run(
            ["git", "rev-parse", "HEAD"],
            cwd=repo_root,
            stdout=subprocess.PIPE,
            stderr=subprocess.DEVNULL,
            text=True,
            check=True,
        ).stdout.strip()
    except Exception:
        git_hash = "unknown"

    lines = []
    lines.append("# OpenAPI Audit Report")
    lines.append("")
    lines.append(f"- Timestamp: {now}")
    lines.append(f"- Repo root: {repo_root}")
    lines.append(f"- OpenAPI file: {openapi_path}")
    lines.append(f"- Commit: {git_hash}")
    lines.append("")

    def render_list(title, items, formatter=None):
        lines.append(f"## {title}")
        if not items:
            lines.append("- None")
            lines.append("")
            return
        for item in items:
            if formatter:
                lines.append(f"- {formatter(item)}")
            else:
                lines.append(f"- {item}")
        lines.append("")

    render_list(
        "A) IMPLEMENTED_NOT_DOCUMENTED",
        diff["implemented_not_documented"],
        lambda v: f"{v[1]} {v[0]}",
    )
    render_list(
        "B) DOCUMENTED_NOT_IMPLEMENTED",
        diff["documented_not_implemented"],
        lambda v: f"{v[1]} {v[0]}",
    )

    lines.append("## C) METHOD_MISMATCH")
    if not diff["method_mismatch"]:
        lines.append("- None")
    else:
        for item in diff["method_mismatch"]:
            lines.append(
                f"- {item['path']}: code={','.join(item['codeMethods'])} openapi={','.join(item['openapiMethods'])}"
            )
    lines.append("")

    lines.append("## D) CONTRACT_MISMATCH")
    if not diff["contract_mismatch"]:
        lines.append("- None")
    else:
        for item in diff["contract_mismatch"]:
            lines.append(f"- {item['method']} {item['path']}")
            for key, value in item["diff"].items():
                lines.append(f"  - {key}: {value}")
    lines.append("")

    lines.append("## Route Inventory (code)")
    for route in sorted(code_routes, key=lambda r: (r["path"], r["method"])):
        auth = "auth" if route["authRequired"] else "no-auth"
        body = "body" if route["hasBody"] else "no-body"
        lines.append(f"- {route['method']} {route['path']} ({auth}, {body}) [{route['handler']}]")
    lines.append("")

    lines.append("## OpenAPI Inventory")
    for route in sorted(openapi_routes, key=lambda r: (r["path"], r["method"])):
        sec = "security" if route["security"] else "no-security"
        lines.append(f"- {route['method']} {route['path']} ({sec})")
    lines.append("")

    with open(report_path, "w", encoding="utf-8") as f:
        f.write("\n".join(lines))


def main():
    parser = argparse.ArgumentParser(description="OpenAPI audit: code vs openapi.yaml")
    parser.add_argument("--fix", action="store_true", help="Update openapi.yaml with missing routes")
    parser.add_argument("--report", default=None, help="Override report path")
    args = parser.parse_args()

    script_dir = os.path.dirname(os.path.abspath(__file__))
    repo_root = find_repo_root(script_dir)
    openapi_path = find_openapi_path(repo_root)
    if openapi_path is None:
        print("openapi.yaml not found")
        return 1

    api_root = os.path.join(repo_root, "api")
    code_routes = build_route_inventory(api_root)
    spec, openapi_routes = build_openapi_inventory(openapi_path)

    diff = compare_inventories(code_routes, openapi_routes)

    if args.fix and diff["implemented_not_documented"]:
        add_missing_routes(spec, code_routes, diff["implemented_not_documented"])
        with open(openapi_path, "w", encoding="utf-8") as f:
            yaml.safe_dump(spec, f, sort_keys=False)

        spec, openapi_routes = build_openapi_inventory(openapi_path)
        diff = compare_inventories(code_routes, openapi_routes)

    report_path = args.report or os.path.join(repo_root, "reports", "openapi_audit.md")
    write_report(report_path, repo_root, openapi_path, diff, code_routes, openapi_routes)

    has_diff = any(
        [
            diff["implemented_not_documented"],
            diff["documented_not_implemented"],
            diff["method_mismatch"],
            diff["contract_mismatch"],
        ]
    )
    return 1 if has_diff else 0


if __name__ == "__main__":
    sys.exit(main())
