# Backend PHP API – funzioni, input e output

Questo documento descrive le funzioni (endpoint HTTP) del backend PHP presenti in `api/`.

## Regole generali

- **Base path**: gli endpoint sono registrati in `api/action.php`.
- **Formato input**: JSON nel body per le richieste `POST`.
- **Formato output**: JSON.
- **Note su output standard** (vedi `api/db/dbmovarisch_dbManager.php`):
  - `SELECT` senza `LIMIT 1` → lista di oggetti JSON.
  - `SELECT ... LIMIT 1` → oggetto JSON (oppure errore se non trovato).
  - `INSERT` → ritorna `{ "id": <lastInsertId> }` se l’endpoint lo stampa direttamente, ma **molti endpoint fanno `echo` del body ricevuto**, quindi l’`id` non è sempre nel risultato.
  - `UPDATE` → di default ritorna i parametri passati (ma molti endpoint stampano il body ricevuto).
  - `DELETE` → JSON booleano (`true`/`false`).

## Autenticazione e sicurezza

### `POST /login`
**Descrizione**: autentica un utente tramite `username` e `password` e restituisce un JWT.
- **Input (body)**:
  ```json
  {
    "username": "...",
    "password": "..."
  }
  ```
- **Output (200)**: oggetto utente con `roles` (array) e `token` JWT. La password viene impostata a `null`.
- **Output (401)**: `{ "message": "Not Authorized" }`.

### `POST /auth/login`
**Descrizione**: autentica un utente e restituisce il token nel formato TO-BE.
- **Input (body)**:
  ```json
  {
    "username": "...",
    "password": "..."
  }
  ```
- **Output (200)**: oggetto con `accessToken`, `tokenType`, `expiresIn`, `user`.
- **Output (401)**: `{ "code": "UNAUTHORIZED", "message": "Not Authorized" }`.

### `GET /auth/me`
**Descrizione**: ritorna l'utente autenticato.
- **Header**: `Authorization: Bearer <token>`.
- **Output (200)**: oggetto utente in formato TO-BE.
- **Output (401)**: `{ "code": "UNAUTHORIZED", "message": "Not Authorized" }`.

### `POST /verifyToken`
**Descrizione**: verifica un token JWT.
- **Input (body)**:
  ```json
  { "token": "..." }
  ```
- **Output (200)**: payload decodificato del token.
- **Output (403)**: `{ "success": false, "message": "No token provided" }`.
- **Output (401)**: `{ "success": false, "message": "Failed to authenticate token" }`.

### Gestione utenti (admin)
Le rotte di gestione utenti usano il path **`/Users`** (nota la maiuscola) e gestiscono anche la tabella `roles`.

#### `POST /Users/`
**Descrizione**: crea un utente e associa ruoli.
- **Input (body)**:
  ```json
  {
    "mail": "...",
    "name": "...",
    "password": "...",
    "surname": "...",
    "username": "...",
    "roles": ["ADMIN", "USER"]
  }
  ```
- **Output**: echo del body (JSON).

#### `DELETE /Users/:id`
**Descrizione**: elimina un utente e rimuove i ruoli associati.
- **Input (path)**: `:id` = ID utente.
- **Output**: JSON booleano.

#### `GET /Users/:id`
**Descrizione**: recupera un utente con i ruoli.
- **Input (path)**: `:id`.
- **Output**: oggetto utente con `roles` (array).

#### `GET /Users/`
**Descrizione**: lista utenti con ruoli.
- **Output**: lista di utenti, ciascuno con `roles` (array).

#### `POST /Users/:id`
**Descrizione**: aggiorna anagrafica utente e ruoli.
- **Input (path)**: `:id`.
- **Input (body)**:
  ```json
  {
    "mail": "...",
    "name": "...",
    "surname": "...",
    "roles": ["ADMIN", "USER"]
  }
  ```
- **Output**: echo del body (JSON).

### Gestione utenti (admin, TO-BE)
Le rotte TO-BE usano `/admin/users` e richiedono `Authorization: Bearer <token>` con ruolo `ADMIN`.

#### `GET /admin/users`
**Descrizione**: lista utenti (solo ADMIN).
- **Output**: lista di utenti in formato TO-BE.

#### `POST /admin/users`
**Descrizione**: crea un utente (solo ADMIN).
- **Input (body)**:
  ```json
  {
    "username": "...",
    "password": "...",
    "roles": ["ADMIN", "USER"],
    "mail": "...",
    "name": "...",
    "surname": "..."
  }
  ```
- **Output (201)**: utente creato in formato TO-BE.

#### `GET /admin/users/:id`
**Descrizione**: dettaglio utente (solo ADMIN).
- **Output**: utente in formato TO-BE.

#### `PATCH /admin/users/:id`
**Descrizione**: aggiorna utente (solo ADMIN).
- **Input (body)**: campi opzionali `mail`, `name`, `surname`, `roles`.
- **Output (200)**: utente aggiornato in formato TO-BE.

#### `DELETE /admin/users/:id`
**Descrizione**: elimina utente (solo ADMIN).
- **Output (204)**: nessun contenuto.

### Profilo utente (TO-BE)
#### `PATCH /users/me`
**Descrizione**: aggiorna solo il proprio profilo.
- **Header**: `Authorization: Bearer <token>`.
- **Input (body)**:
  ```json
  { "mail": "...", "name": "...", "surname": "..." }
  ```
- **Output (200)**: utente aggiornato in formato TO-BE.

#### `POST /users/me/change-password`
**Descrizione**: cambio password del proprio utente.
- **Header**: `Authorization: Bearer <token>`.
- **Input (body)**:
  ```json
  { "oldPassword": "...", "newPassword": "..." }
  ```
- **Output (204)**: password cambiata.

## Risorsa: FrasiH
File: `api/resource/movarisch_db/FrasiH.php`

### `POST /frasih`
**Descrizione**: crea una FrasiH.
- **Input**:
  ```json
  { "Codice": "...", "Descrizione": "...", "Punteggio": 1.23 }
  ```
- **Output**: echo del body (JSON).

### `DELETE /frasih/:id`
**Descrizione**: elimina una FrasiH.
- **Output**: JSON booleano.

### `GET /frasih/:id`
**Descrizione**: recupera una FrasiH per ID.
- **Output**: oggetto FrasiH.

### `GET /frasih`
**Descrizione**: lista tutte le FrasiH.
- **Output**: lista di FrasiH.

### `POST /frasih/:id`
**Descrizione**: aggiorna una FrasiH.
- **Input**:
  ```json
  { "Codice": "...", "Descrizione": "...", "Punteggio": 1.23 }
  ```
- **Output**: echo del body (JSON).

## Risorsa: Sostanza
File: `api/resource/movarisch_db/Sostanza.php`

### `POST /sostanza`
**Descrizione**: crea una Sostanza e gestisce la relazione con `FrasiH`.
- **Input**:
  ```json
  {
    "Identificativo": "...",
    "Nome": "...",
    "Score": 1.23,
    "VLEP": true,
    "User": 1,
    "FrasiH": [1, 2, 3]
  }
  ```
- **Output**: echo del body (JSON).

### `DELETE /sostanza/:id`
**Descrizione**: elimina una Sostanza.
- **Output**: JSON booleano.

### `GET /sostanza/findByFrasiH/:key`
**Descrizione**: filtra Sostanza per chiave `FrasiH`.
- **Output**: lista di Sostanza.

### `GET /sostanza/findByUser/:key`
**Descrizione**: filtra Sostanza per `User`.
- **Output**: lista di Sostanza.

### `GET /sostanza/findByVLEP/:key`
**Descrizione**: filtra Sostanza per flag `VLEP`.
- **Output**: lista di Sostanza.

### `GET /sostanza/:id`
**Descrizione**: recupera una Sostanza e aggiunge l’array `FrasiH` dai join.
- **Output**: oggetto Sostanza con `FrasiH: [id, ...]`.

### `GET /sostanza`
**Descrizione**: lista tutte le Sostanze.
- **Output**: lista di Sostanza.

### `POST /sostanza/:id`
**Descrizione**: aggiorna una Sostanza e riallinea la relazione con `FrasiH`.
- **Input**: come per la creazione.
- **Output**: echo del body (JSON).

## Risorsa: Miscelanonpericolosa
File: `api/resource/movarisch_db/Miscelanonpericolosa.php`

### `POST /miscelanonpericolosa`
**Descrizione**: crea una miscelanonpericolosa e gestisce la relazione con `Sostanza`.
- **Input**:
  ```json
  { "Nome": "...", "Score": 1.23, "Sostanza": [1, 2] }
  ```
- **Output**: echo del body (JSON).

### `DELETE /miscelanonpericolosa/:id`
**Descrizione**: elimina una miscelanonpericolosa.
- **Output**: JSON booleano.

### `GET /miscelanonpericolosa/findByNome/:key`
**Descrizione**: filtra per `Nome`.
- **Output**: lista di miscelanonpericolosa.

### `GET /miscelanonpericolosa/findBySostanza/:key`
**Descrizione**: filtra per `Sostanza`.
- **Output**: lista di miscelanonpericolosa.

### `GET /miscelanonpericolosa/:id`
**Descrizione**: recupera una miscelanonpericolosa e aggiunge `Sostanza` dai join.
- **Output**: oggetto con `Sostanza: [id, ...]`.

### `GET /miscelanonpericolosa`
**Descrizione**: lista tutte le miscelanonpericolosa.
- **Output**: lista.

### `POST /miscelanonpericolosa/:id`
**Descrizione**: aggiorna e riallinea la relazione con `Sostanza`.
- **Input**: come per la creazione.
- **Output**: echo del body (JSON).

## Risorsa: Processo
File: `api/resource/movarisch_db/Processo.php`

### `POST /processo`
**Descrizione**: crea un processo e gestisce la relazione con `Sostanza`.
- **Input**:
  ```json
  { "AltaEmissione": true, "Nome": "...", "Sostanza": [1, 2] }
  ```
- **Output**: echo del body (JSON).

### `DELETE /processo/:id`
**Descrizione**: elimina un processo.
- **Output**: JSON booleano.

### `GET /processo/findByNome/:key`
**Descrizione**: filtra per `Nome`.
- **Output**: lista di processi.

### `GET /processo/:id`
**Descrizione**: recupera un processo e aggiunge `Sostanza` dai join.
- **Output**: oggetto con `Sostanza: [id, ...]`.

### `GET /processo`
**Descrizione**: lista tutti i processi.
- **Output**: lista.

### `POST /processo/:id`
**Descrizione**: aggiorna e riallinea la relazione con `Sostanza`.
- **Input**: come per la creazione.
- **Output**: echo del body (JSON).

## Agenti chimici (TO-BE)
File: `api/resource/movarisch_db/custom/AgentiChimiciCustom.php`

### `GET /agenti-chimici`
**Descrizione**: lista agenti chimici (sostanze, miscele, processi).
- **Output**: lista di oggetti AgenteChimico (TO-BE).

### `POST /agenti-chimici`
**Descrizione**: crea un agente chimico (owned).
- **Input (body)**: in base al tipo (`sostanza`, `miscelaP`, `miscelaNP`, `processo`).
- **Output (201)**: agente chimico creato.

### `GET /agenti-chimici/:id`
**Descrizione**: dettaglio agente chimico.
- **Output (200)**: oggetto AgenteChimico.

### `PATCH /agenti-chimici/:id`
**Descrizione**: aggiorna agente chimico.
- **Input (body)**: campi opzionali in base al tipo.
- **Output (200)**: agente chimico aggiornato.

### `DELETE /agenti-chimici/:id`
**Descrizione**: elimina agente chimico.
- **Output (204)**: nessun contenuto.

## Risorsa: User
File: `api/resource/movarisch_db/User.php`

### `POST /user`
**Descrizione**: crea un utente base (senza ruoli).
- **Input**:
  ```json
  {
    "mail": "...",
    "name": "...",
    "password": "...",
    "roles": "...",
    "surname": "...",
    "username": "..."
  }
  ```
- **Output**: echo del body (JSON).

### `DELETE /user/:id`
**Descrizione**: elimina un utente base.
- **Output**: JSON booleano.

### `GET /user/:id`
**Descrizione**: recupera un utente base.
- **Output**: oggetto utente.

### `GET /user`
**Descrizione**: lista utenti base.
- **Output**: lista utenti.

### `POST /user/:id`
**Descrizione**: aggiorna un utente base.
- **Input**: come per la creazione.
- **Output**: echo del body (JSON).

### `POST /user/:id/changePassword`
**Descrizione**: stub di servizio custom; attualmente ritorna `ok`.
- **Output**: `"ok"`.
