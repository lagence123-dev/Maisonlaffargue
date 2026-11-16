# Données de support pour Prestashop

## Liste des fichiers possibles et leur contenu

Ces fichiers peuvent ne pas exister dans l'archive.

- `info.json` : contient diverses infos.
- `instructions.md` : ce fichier.
- `dev-{date}.log` et `prod-{date}.log` : les fichiers de log normalement à `var/logs`, qui ont été filtrés et n'incluent que les lignes contenant "monetico".
- `php_error.log` : le fichier de log mentionné par le paramètre ini `error_log`, filtrer pour n'inclure que les lignes contenant "Monetico".
- `phpinfo.html` : contient l'output classique de la fonction `phpinfo()`.
