
```
   _____ _          __  __      _     _           
  / ____| |        / _|/ _|    | |   | |          
 | (___ | | ____ _| |_| |_ ___ | | __| | ___ _ __ 
  \___ \| |/ / _` |  _|  _/ _ \| |/ _` |/ _ \ '__|
  ____) |   < (_| | | | || (_) | | (_| |  __/ |   
 |_____/|_|\_\__,_|_| |_| \___/|_|\__,_|\___|_|     

  _____  _    _ _____  
 |  __ \| |  | |  __ \ 
 | |__) | |__| | |__) |
 |  ___/|  __  |  ___/ 
 | |    | |  | | |     
 |_|    |_|  |_|_|     

```

--------------
## DEPLOY DELLE API (ISTRUZIONI DETTAGLIATE)
--------------
### Requisiti
* PHP 7.4+ con estensioni `mysqli` e `json`.
* MySQL 5.7+ oppure MariaDB equivalente.
* Un web server (Apache/Nginx) oppure il server built‑in di PHP per ambienti di test.

### 1) Configurazione del database
1. Crea un database vuoto (es. `mova_api`).
2. Importa lo schema:
   * Da terminale:
     ```bash
     mysql -u <utente> -p <nome_db> < /workspace/movarisch/api/db/NAME_DB_db_schema.sql
     ```
   * Oppure tramite client grafico (MySQL Workbench, phpMyAdmin), caricando il file
     `api/db/NAME_DB_db_schema.sql`.

### 2) Configurazione dell’applicazione
1. Apri il file `api/properties.php`.
2. Aggiorna le credenziali DB e le variabili di ambiente richieste:
   * host
   * nome database
   * utente
   * password
3. Salva il file.

### 3) Deploy su web server (consigliato per produzione)
#### Apache
1. Configura il VirtualHost con `DocumentRoot` puntato alla cartella del progetto.
2. Assicurati che PHP sia abilitato e che le estensioni richieste siano attive.
3. Riavvia Apache.

#### Nginx + PHP-FPM
1. Configura un server block con `root` sulla cartella del progetto.
2. Configura `fastcgi_pass` verso PHP‑FPM.
3. Riavvia Nginx e PHP‑FPM.

### 4) Avvio locale (sviluppo/test)
Se vuoi avviare rapidamente in locale:
```bash
cd /workspace/movarisch
php -S 0.0.0.0:8000 -t .
```
Poi apri `http://localhost:8000`.

### 5) Verifica
* Controlla i file `ENDPOINTS.md` o `openapi.yaml` per vedere gli endpoint disponibili.
* Se ottieni errori di connessione DB, ricontrolla `api/properties.php`.

--------------
## CONFIGURAZIONE
--------------
* Per configurare PHP e database modificare `api/properties.php`.
--------------
