#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${BASE_URL:-http://localhost:8080}"
TOKEN="${TOKEN:-}"
VALUTAZIONE_ID="${VALUTAZIONE_ID:-1}"

echo "Base URL: ${BASE_URL}"
echo "Valutazione ID: ${VALUTAZIONE_ID}"

auth_header=()
if [[ -n "${TOKEN}" ]]; then
  auth_header=(-H "Authorization: Bearer ${TOKEN}")
fi

echo
echo "== 200 POST /calcoli/valutazioni =="
curl -i -sS "${auth_header[@]}" \
  -H "Content-Type: application/json" \
  -d '{
    "lavoratoreId": 1,
    "agenteChimicoId": 2,
    "metodoVersione": "1.0",
    "einal": 1.2,
    "tempoInalMin": 10
  }' \
  "${BASE_URL}/calcoli/valutazioni"

echo
echo "== 400 POST /calcoli/valutazioni (missing required) =="
curl -i -sS "${auth_header[@]}" \
  -H "Content-Type: application/json" \
  -d '{
    "lavoratoreId": 1
  }' \
  "${BASE_URL}/calcoli/valutazioni"

echo
echo "== 401 POST /calcoli/valutazioni (no token) =="
curl -i -sS \
  -H "Content-Type: application/json" \
  -d '{
    "lavoratoreId": 1,
    "agenteChimicoId": 2,
    "metodoVersione": "1.0"
  }' \
  "${BASE_URL}/calcoli/valutazioni"

echo
echo "== 200 POST /calcoli/valutazioni/{id} =="
curl -i -sS "${auth_header[@]}" \
  -H "Content-Type: application/json" \
  -d '{
    "metodoVersione": "1.0"
  }' \
  "${BASE_URL}/calcoli/valutazioni/${VALUTAZIONE_ID}"

echo
echo "== 404 POST /calcoli/valutazioni/{id} (not found or not owned) =="
curl -i -sS "${auth_header[@]}" \
  -H "Content-Type: application/json" \
  -d '{
    "metodoVersione": "1.0"
  }' \
  "${BASE_URL}/calcoli/valutazioni/999999"

