# Scripts

## update_diff_tasks.py

Aggiorna `DIFF_ASIS_TOBE_TASKS.md` rimuovendo i paragrafi completati al 100%.

Uso:
- Dry run (default): `python3 api/scripts/update_diff_tasks.py --dry-run`
- Apply: `python3 api/scripts/update_diff_tasks.py --apply`

Output:
- Elenca i paragrafi completati e quelli rimanenti con motivazione.
- In caso di nessuna rimozione, propone la NEXT ACTION.
