#!/usr/bin/env python3
import argparse
import os
import re
import sys


def find_repo_root(start_path):
    current = os.path.abspath(start_path)
    while True:
        if os.path.isdir(os.path.join(current, ".git")):
            return current
        parent = os.path.dirname(current)
        if parent == current:
            return os.path.abspath(start_path)
        current = parent


def find_diff_file(repo_root):
    candidates = [
        os.path.join(repo_root, "DIFF_ASIS_TOBE_TASKS.md"),
        os.path.join(repo_root, "api", "DIFF_ASIS_TOBE_TASKS.md"),
        os.path.join(repo_root, "docs", "DIFF_ASIS_TOBE_TASKS.md"),
        os.path.join(repo_root, "documentation", "DIFF_ASIS_TOBE_TASKS.md"),
        os.path.join(repo_root, "project", "DIFF_ASIS_TOBE_TASKS.md"),
    ]
    for path in candidates:
        if os.path.exists(path):
            return path
    for root, _, files in os.walk(repo_root):
        if "DIFF_ASIS_TOBE_TASKS.md" in files:
            return os.path.join(root, "DIFF_ASIS_TOBE_TASKS.md")
    return None


def parse_blocks(lines):
    headings = []
    heading_re = re.compile(r"^(#{2,3})\s+(.+)")
    for idx, line in enumerate(lines):
        match = heading_re.match(line)
        if match:
            level = len(match.group(1))
            headings.append((idx, level, match.group(2).strip()))

    blocks = []
    for i, (start, level, title) in enumerate(headings):
        end = len(lines)
        for j in range(i + 1, len(headings)):
            next_start, next_level, _ = headings[j]
            if next_level <= level:
                end = next_start
                break
        blocks.append(
            {
                "start": start,
                "end": end,
                "level": level,
                "title": title,
                "lines": lines[start:end],
            }
        )
    return blocks


def get_actions(block):
    actions = []
    has_checkboxes = any(re.search(r"- \[( |x|X)\]", line) for line in block["lines"])
    if has_checkboxes:
        for line in block["lines"]:
            for match in re.finditer(r"- \[( |x|X)\]\s*(.+?)(?=(?:\s+- \[(?: |x|X)\]|\s*$))", line):
                actions.append((match.group(2).strip(), match.group(1).lower() == "x"))
        return actions

    in_task_checklist = False
    for line in block["lines"]:
        if line.strip().lower().startswith("- task checklist"):
            in_task_checklist = True
            continue
        if in_task_checklist:
            if line.strip() == "":
                break
            if re.match(r"^#{2,3}\s+", line):
                break
            bullet = re.match(r"^\s*-\s+(.+)", line)
            if bullet:
                content = bullet.group(1).strip()
                checkbox = re.match(r"^\[( |x|X)\]\s*(.+)", content)
                if checkbox:
                    actions.append((checkbox.group(2).strip(), checkbox.group(1).lower() == "x"))
                else:
                    actions.append((content, None))
            else:
                break
    if actions:
        return actions

    for line in block["lines"]:
        if re.search(r"\b(TODO|TASK|AZIONE)\b", line, re.IGNORECASE):
            actions.append((line.strip(), None))
    return actions


def has_calcoli_routes(repo_root):
    path = os.path.join(repo_root, "api", "resource", "movarisch_db", "custom", "CalcoliCustom.php")
    if not os.path.exists(path):
        return False
    with open(path, "r", encoding="utf-8") as f:
        data = f.read()
    return "/calcoli/valutazioni" in data and "/calcoli/valutazioni/:id" in data


def has_calcoli_validation(repo_root):
    path = os.path.join(repo_root, "api", "resource", "movarisch_db", "custom", "CalcoliCustom.php")
    if not os.path.exists(path):
        return False
    with open(path, "r", encoding="utf-8") as f:
        data = f.read()
    return "validateCalcoloValutazioneRequest" in data and "respondApiError" in data


def has_calcoli_ownership(repo_root):
    path = os.path.join(repo_root, "api", "resource", "movarisch_db", "custom", "CalcoliCustom.php")
    if not os.path.exists(path):
        return False
    with open(path, "r", encoding="utf-8") as f:
        data = f.read()
    return "getValutazioneOwnerId" in data and "NOT_FOUND" in data


def has_calcoli_tests(repo_root):
    return os.path.exists(os.path.join(repo_root, "api", "scripts", "calcoli_smoke_tests.sh"))


def verify_action(repo_root, block_title, action_text):
    title = block_title.lower()
    action = action_text.lower()

    if title.startswith("calcoli"):
        if "validation" in action or "error mapping" in action:
            return has_calcoli_validation(repo_root), "validation helpers missing"
        if "ownership" in action or "rbac" in action or "repository" in action:
            return has_calcoli_ownership(repo_root), "ownership check missing"
        if "test" in action:
            return has_calcoli_tests(repo_root), "tests missing"

    return False, "unverified"


def is_block_completed(repo_root, block):
    actions = get_actions(block)
    if not actions:
        return False, [], []

    completed = []
    remaining = []
    for action_text, checkbox_done in actions:
        if checkbox_done is True:
            completed.append(action_text)
            continue
        if checkbox_done is False:
            remaining.append((action_text, "checkbox unchecked"))
            continue

        verified, reason = verify_action(repo_root, block["title"], action_text)
        if verified:
            completed.append(action_text)
        else:
            remaining.append((action_text, reason))
    return len(remaining) == 0, completed, remaining


def remove_blocks(lines, blocks_to_remove):
    remove_ranges = sorted((b["start"], b["end"]) for b in blocks_to_remove)
    output = []
    idx = 0
    for start, end in remove_ranges:
        if idx < start:
            output.extend(lines[idx:start])
        idx = end
        while idx < len(lines) and lines[idx].strip() == "":
            idx += 1
    output.extend(lines[idx:])
    return output


def main():
    parser = argparse.ArgumentParser(description="Update DIFF_ASIS_TOBE_TASKS.md based on completed paragraphs")
    parser.add_argument("--path", default=None, help="Path to DIFF_ASIS_TOBE_TASKS.md")
    parser.add_argument("--apply", action="store_true", help="Apply removals")
    parser.add_argument("--dry-run", action="store_true", help="Dry run (default)")
    args = parser.parse_args()

    script_dir = os.path.dirname(os.path.abspath(__file__))
    repo_root = find_repo_root(script_dir)
    diff_path = args.path or find_diff_file(repo_root)
    if diff_path is None:
        print("DIFF_ASIS_TOBE_TASKS.md not found in repo")
        return 1

    with open(diff_path, "r", encoding="utf-8") as f:
        lines = f.read().splitlines()
    print(f"READ FILE: {diff_path} (lines={len(lines)})")
    if len(lines) == 1:
        print("WARNING: file appears single-line; parsing may be degraded")

    blocks = parse_blocks(lines)
    eligible_blocks = []
    for block in blocks:
        if block["level"] == 2:
            if any(re.match(r"^###\s+", line) for line in block["lines"][1:]):
                continue
        if block["level"] in (2, 3):
            eligible_blocks.append(block)

    completed_blocks = []
    remaining_blocks = []
    for block in eligible_blocks:
        completed, done_actions, remaining_actions = is_block_completed(repo_root, block)
        if completed:
            completed_blocks.append((block, done_actions))
        else:
            remaining_blocks.append((block, remaining_actions))

    if completed_blocks:
        print("COMPLETED PARAGRAPHS:")
        for block, actions in completed_blocks:
            print(f"- {block['title']}")
        print("")
    else:
        print("COMPLETED PARAGRAPHS: None")
        print("")

    print("REMAINING PARAGRAPHS:")
    for block, remaining in remaining_blocks:
        if not remaining:
            print(f"- {block['title']}: no actions detected")
            continue
        reasons = "; ".join([f"{text} [{reason}]" for text, reason in remaining])
        print(f"- {block['title']}: {reasons}")
    print("")

    if not completed_blocks:
        next_action = None
        next_section = None
        for block, remaining in remaining_blocks:
            for item in remaining:
                if item and item[0]:
                    next_action = item[0]
                    next_section = block["title"]
                    break
            if next_action:
                break
        print("NO CHANGES")
        if next_action:
            print(f"NEXT ACTION: {next_action} (section: {next_section})")
        else:
            first_section = remaining_blocks[0][0]["title"] if remaining_blocks else "Unknown"
            print(f'NEXT ACTION: Add a "Task checklist" bullet list or checkboxes to the first remaining section (section: {first_section})')
        return 0

    if args.apply:
        updated_lines = remove_blocks(lines, [b for b, _ in completed_blocks])
        with open(diff_path, "w", encoding="utf-8") as f:
            f.write("\\n".join(updated_lines) + "\\n")
        print(f"UPDATED FILE: {diff_path}")
    else:
        print("DRY RUN: no changes applied")

    return 0


if __name__ == "__main__":
    sys.exit(main())
