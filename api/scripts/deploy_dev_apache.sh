#!/usr/bin/env bash
#
# Script di deploy per ambiente di sviluppo (Ubuntu + Apache).
#
# NOTE:
# - Questo script usa placeholder: sostituisci i valori tra <> con quelli reali.
# - Commenti abbondanti per spiegare ogni passaggio.
# - Esegue copia da cartella di sviluppo a document root Apache su stessa macchina.
# - Reimposta permessi e riavvia Apache alla fine.

set -euo pipefail

# --- Configurazione (placeholder) ---
# Cartella del codice sorgente in sviluppo (sulla stessa macchina).
SRC_DIR="<PERCORSO_ASSOLUTO_CARTELLA_SVILUPPO>"

# Document root Apache per l'ambiente di sviluppo.
DEST_DIR="<PERCORSO_ASSOLUTO_DOCUMENT_ROOT_APACHE_DEV>"

# Nome del servizio Apache su Ubuntu.
# Tipico: "apache2" (verifica con: systemctl list-units --type=service | rg apache)
APACHE_SERVICE="<NOME_SERVIZIO_APACHE>"

# Utente e gruppo con cui Apache serve i file (di solito www-data).
WEB_USER="<UTENTE_WEB>"
WEB_GROUP="<GRUPPO_WEB>"

# Eventuali percorsi o pattern da escludere durante il deploy.
# Aggiungi righe "--exclude 'pattern'" secondo necessità.
RSYNC_EXCLUDES=(
  # "--exclude" "<PATTERN_DA_ESCLUDERE>"
)

# --- Controlli preliminari ---
# Verifica che le directory placeholder siano state sostituite.
if [[ "$SRC_DIR" == "<PERCORSO_ASSOLUTO_CARTELLA_SVILUPPO>" ]]; then
  echo "ERRORE: imposta SRC_DIR con il percorso reale della cartella di sviluppo." >&2
  exit 1
fi

if [[ "$DEST_DIR" == "<PERCORSO_ASSOLUTO_DOCUMENT_ROOT_APACHE_DEV>" ]]; then
  echo "ERRORE: imposta DEST_DIR con il document root reale di Apache (dev)." >&2
  exit 1
fi

if [[ "$APACHE_SERVICE" == "<NOME_SERVIZIO_APACHE>" ]]; then
  echo "ERRORE: imposta APACHE_SERVICE con il nome reale del servizio Apache." >&2
  exit 1
fi

if [[ "$WEB_USER" == "<UTENTE_WEB>" || "$WEB_GROUP" == "<GRUPPO_WEB>" ]]; then
  echo "ERRORE: imposta WEB_USER e WEB_GROUP con utente/gruppo reali." >&2
  exit 1
fi

# Controllo che le directory esistano.
if [[ ! -d "$SRC_DIR" ]]; then
  echo "ERRORE: la directory sorgente non esiste: $SRC_DIR" >&2
  exit 1
fi

if [[ ! -d "$DEST_DIR" ]]; then
  echo "ERRORE: la directory di destinazione non esiste: $DEST_DIR" >&2
  exit 1
fi

# --- Deploy dei file ---
# Usa rsync per copiare e sincronizzare il contenuto.
# -a: conserva permessi/ownership dove possibile
# -v: verbose
# --delete: rimuove i file in DEST che non esistono più in SRC (ATTENZIONE)
# Modifica le opzioni se il tuo caso d'uso è diverso.

echo "Inizio deploy: $SRC_DIR -> $DEST_DIR"

# NOTA: se non vuoi usare sudo, assicurati che l'utente corrente abbia i permessi.
# In alternativa, anteponi sudo a rsync/chown/chmod/systemctl.
rsync -av --delete "${RSYNC_EXCLUDES[@]}" "$SRC_DIR/" "$DEST_DIR/"

# --- Permessi ---
# Reimposta ownership (utente/gruppo) sull'intera document root.
# Questo può essere costoso su directory grandi; valuta alternative se necessario.
chown -R "$WEB_USER:$WEB_GROUP" "$DEST_DIR"

# Imposta permessi più stretti per file e directory.
# Esempio tipico: 755 per directory, 644 per file.
find "$DEST_DIR" -type d -exec chmod 755 {} \;
find "$DEST_DIR" -type f -exec chmod 644 {} \;

# --- Riavvio Apache ---
# Riavvia il servizio Apache per applicare eventuali cambiamenti.
# In alternativa a restart, puoi usare reload se sufficiente.

echo "Riavvio del servizio Apache: $APACHE_SERVICE"
systemctl restart "$APACHE_SERVICE"

echo "Deploy completato con successo."
