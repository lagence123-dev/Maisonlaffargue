# Datalog123 1.0.0 — LAGENCE123

PrestaShop 1.7.7+ / 8.x / 9.x (compatibilite annoncee, tests fonctionnels en boutique encore necessaires).

## Installation

1. Installer datalog123.zip depuis Modules > Gestionnaire de modules.
2. Activer les services Web dans Parametres avances > Service web.
3. Creer une cle Webservice avec seulement GET sur la ressource **datalog123**.
4. Depuis l'extranet (serveur, HTTPS), appeler `GET https://boutique.example/api/datalog123?output_format=JSON` avec Basic Auth (cle API comme identifiant, mot de passe vide).
5. Conserver la cle dans un coffre de secrets, limiter l'acces reseau si possible.

## Compatibilite JSON

Inchanges : `theme` (ses six champs), `_PS_VERSION_`, `PHP_VERSION`, `shopname`, `overrides`, `files_changed`, `list`, `trad`.
La branche `order=1` et la cle `stat` ont ete supprimees.
Les champs du tableau `list` sont limites aux metadonnees du module : les attributs PHP internes presents dans l'ancien cast `(array)$module` ne sont plus exposes pour des raisons de securite. Verifier les dependances de l'extranet a ces sous-champs.

Nouveaux : `email_config`, `modules_updates`, `modules_updates_status`, `files_deleted`, `files_deleted_status`, `files_changed_status`, `collected_at`.

## Limites importantes

- Le catalogue de mises a jour Addons n'est pas interroge en ligne : `modules_updates` ne contient que les indicateurs deja accessibles localement. Une liste vide ne signifie pas qu'aucune mise a jour n'existe.
- `files_changed` reprend le verificateur natif du coeur SI disponible et compatible ; sinon renvoie `[]`. Un tableau vide n'est pas une attestation d'integrite.
- `files_deleted` vaut toujours `[]` en v1 ; il faut un manifeste de reference signe et correspondant exactement a la version installee pour identifier les suppressions fiables.
- Ne pas exposer de mot de passe SMTP ou de jeton API.
- Certains formats et comportements de l'API Webservice peuvent differer suivant la version; tester les requetes en environnement de preproduction avant deployment.
