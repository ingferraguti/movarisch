# OpenAPI Audit Report

- Timestamp: 2026-01-16 14:27:47 UTC
- Repo root: /home/matteo/movarisch
- OpenAPI file: /home/matteo/movarisch/api/openapi.yaml
- Commit: b60bd7a273ca31691477ff87ce4fd2a0fdd71c78

## A) IMPLEMENTED_NOT_DOCUMENTED
- None

## B) DOCUMENTED_NOT_IMPLEMENTED
- None

## C) METHOD_MISMATCH
- None

## D) CONTRACT_MISMATCH
- None

## Route Inventory (code)
- GET /Users (no-auth, no-body) [api/security/ManageUser.php]
- POST /Users (no-auth, body) [api/security/ManageUser.php]
- DELETE /Users/{id} (no-auth, no-body) [api/security/ManageUser.php]
- GET /Users/{id} (no-auth, no-body) [api/security/ManageUser.php]
- POST /Users/{id} (no-auth, body) [api/security/ManageUser.php]
- GET /admin/users (auth, no-body) [api/security/ManageUser.php]
- POST /admin/users (auth, body) [api/security/ManageUser.php]
- DELETE /admin/users/{id} (auth, no-body) [api/security/ManageUser.php]
- GET /admin/users/{id} (auth, no-body) [api/security/ManageUser.php]
- PATCH /admin/users/{id} (auth, body) [api/security/ManageUser.php]
- GET /agenti-chimici (auth, no-body) [api/resource/movarisch_db/custom/AgentiChimiciCustom.php]
- POST /agenti-chimici (auth, body) [api/resource/movarisch_db/custom/AgentiChimiciCustom.php]
- DELETE /agenti-chimici/{id} (auth, no-body) [api/resource/movarisch_db/custom/AgentiChimiciCustom.php]
- GET /agenti-chimici/{id} (auth, no-body) [api/resource/movarisch_db/custom/AgentiChimiciCustom.php]
- PATCH /agenti-chimici/{id} (auth, body) [api/resource/movarisch_db/custom/AgentiChimiciCustom.php]
- POST /auth/login (no-auth, body) [api/security/Login.php]
- GET /auth/me (auth, no-body) [api/security/Login.php]
- POST /calcoli/valutazioni (auth, body) [api/resource/movarisch_db/custom/CalcoliCustom.php]
- POST /calcoli/valutazioni/{id} (auth, body) [api/resource/movarisch_db/custom/CalcoliCustom.php]
- GET /frasih (no-auth, no-body) [api/resource/movarisch_db/FrasiH.php]
- POST /frasih (no-auth, body) [api/resource/movarisch_db/FrasiH.php]
- DELETE /frasih/{id} (no-auth, no-body) [api/resource/movarisch_db/FrasiH.php]
- GET /frasih/{id} (no-auth, no-body) [api/resource/movarisch_db/FrasiH.php]
- POST /frasih/{id} (no-auth, body) [api/resource/movarisch_db/FrasiH.php]
- POST /login (no-auth, body) [api/security/Login.php]
- GET /sostanza (no-auth, no-body) [api/resource/movarisch_db/Sostanza.php]
- POST /sostanza (no-auth, body) [api/resource/movarisch_db/Sostanza.php]
- GET /sostanza/findByFrasiH/{key} (no-auth, no-body) [api/resource/movarisch_db/Sostanza.php]
- GET /sostanza/findByUser/{key} (no-auth, no-body) [api/resource/movarisch_db/Sostanza.php]
- GET /sostanza/findByVLEP/{key} (no-auth, no-body) [api/resource/movarisch_db/Sostanza.php]
- DELETE /sostanza/{id} (no-auth, no-body) [api/resource/movarisch_db/Sostanza.php]
- GET /sostanza/{id} (no-auth, no-body) [api/resource/movarisch_db/Sostanza.php]
- POST /sostanza/{id} (no-auth, body) [api/resource/movarisch_db/Sostanza.php]
- GET /user (no-auth, no-body) [api/resource/movarisch_db/User.php]
- POST /user (no-auth, body) [api/resource/movarisch_db/User.php]
- DELETE /user/{id} (no-auth, no-body) [api/resource/movarisch_db/User.php]
- GET /user/{id} (no-auth, no-body) [api/resource/movarisch_db/User.php]
- POST /user/{id} (no-auth, body) [api/resource/movarisch_db/User.php]
- POST /user/{id}/changePassword (no-auth, no-body) [api/resource/movarisch_db/User.php]
- PATCH /users/me (auth, body) [api/security/ManageUser.php]
- POST /users/me/change-password (auth, body) [api/security/ManageUser.php]
- POST /verifyToken (no-auth, body) [api/security/Login.php]

## OpenAPI Inventory
- GET /Users (no-security)
- POST /Users (no-security)
- DELETE /Users/{id} (no-security)
- GET /Users/{id} (no-security)
- POST /Users/{id} (no-security)
- GET /admin/users (security)
- POST /admin/users (security)
- DELETE /admin/users/{id} (security)
- GET /admin/users/{id} (security)
- PATCH /admin/users/{id} (security)
- GET /agenti-chimici (security)
- POST /agenti-chimici (security)
- DELETE /agenti-chimici/{id} (security)
- GET /agenti-chimici/{id} (security)
- PATCH /agenti-chimici/{id} (security)
- POST /auth/login (no-security)
- GET /auth/me (security)
- POST /calcoli/valutazioni (security)
- POST /calcoli/valutazioni/{id} (security)
- GET /frasih (no-security)
- POST /frasih (no-security)
- DELETE /frasih/{id} (no-security)
- GET /frasih/{id} (no-security)
- POST /frasih/{id} (no-security)
- POST /login (no-security)
- GET /sostanza (no-security)
- POST /sostanza (no-security)
- GET /sostanza/findByFrasiH/{key} (no-security)
- GET /sostanza/findByUser/{key} (no-security)
- GET /sostanza/findByVLEP/{key} (no-security)
- DELETE /sostanza/{id} (no-security)
- GET /sostanza/{id} (no-security)
- POST /sostanza/{id} (no-security)
- GET /user (no-security)
- POST /user (no-security)
- DELETE /user/{id} (no-security)
- GET /user/{id} (no-security)
- POST /user/{id} (no-security)
- POST /user/{id}/changePassword (no-security)
- PATCH /users/me (security)
- POST /users/me/change-password (security)
- POST /verifyToken (no-security)
