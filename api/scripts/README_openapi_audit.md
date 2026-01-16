# OpenAPI Audit

Script per verificare la coerenza tra le route reali del backend (PHP) e `openapi.yaml`.

Uso:
- Solo report:
  - `python3 api/scripts/openapi_audit.py`
- Report + aggiornamento automatico (aggiunge solo route presenti nel codice ma non documentate):
  - `python3 api/scripts/openapi_audit.py --fix`

Output:
- Report: `reports/openapi_audit.md`

Exit code:
- `0` se non ci sono divergenze
- `1` se ci sono differenze tra codice e contratto
