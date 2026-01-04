#!/usr/bin/env bash
set -euo pipefail

BASE_URL=${BASE_URL:-"http://localhost:8000"}

MAIL=${MAIL:-"azienda@example.com"}
NAME=${NAME:-"Azienda Demo"}
SURNAME=${SURNAME:-"Referente"}
USERNAME=${USERNAME:-"azienda_demo"}
PASSWORD=${PASSWORD:-"changeme"}
ROLES=${ROLES:-"azienda"}

payload=$(cat <<JSON
{
  "mail": "${MAIL}",
  "name": "${NAME}",
  "surname": "${SURNAME}",
  "username": "${USERNAME}",
  "password": "${PASSWORD}",
  "roles": "${ROLES}"
}
JSON
)

curl --fail --silent --show-error \
  -X POST "${BASE_URL}/user" \
  -H "Content-Type: application/json" \
  -d "${payload}"
