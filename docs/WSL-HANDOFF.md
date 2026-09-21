# WSL local environment handoff

The local project lives at `/home/fuuraiko/Projects/MediaWiki` inside the `Ubuntu-26.04` WSL 2 distribution. Work there directly; `/mnt/e/Projects/MediaWiki` is retired after migration.

## Layout

- `public/w/` — installed MediaWiki 1.46.0 runtime and private `LocalSettings.php` (ignored by Git).
- `skins/Nord/`, `extensions/NordNotes/`, `config/`, `content/`, `ops/`, `docs/` — rebuildable Git repository content.
- `backups/` — private database/file/migration archives, ignored by Git.
- `.codex/` — temporary agent work only; remove task residue when finished.

The Git remote is `https://github.com/flowing-wind/my-mediawiki.git`, branch `main`. Windows Git was used for the authenticated initial push. Linux Git can inspect and commit locally; use Windows Git through the WSL UNC path if its credential manager is needed for later pushes.

## Services

Apache binds only `127.0.0.1:8080` for MediaWiki. `http://localhost:8080/wiki/Main_Page` is the local entry point. MariaDB listens locally and retains the local wiki database.

Apache runs as `www-data`. That account is a member of the `fuuraiko` group so it can traverse `/home/fuuraiko` and read the project; preserve this membership if the account or service is rebuilt.

Another project has an Apache reverse proxy at `127.0.0.1:8090` forwarding to `127.0.0.1:3080`. It is a separate virtual host and does not conflict with MediaWiki. Its current logs still reference that project's own path and should not be changed as part of Wiki work.

Useful checks:

```bash
systemctl status apache2 mariadb
apache2ctl configtest
curl --noproxy '*' -I http://127.0.0.1:8080/wiki/Main_Page
git status --short --branch
```

The WSL shell currently defines an HTTP proxy on `127.0.0.1:7897`. Some curl versions do not interpret the `127.*` entry in `no_proxy` as expected and may return a proxy-generated 502 for the local wiki. `--noproxy '*'` verifies Apache directly; this does not indicate a Wiki or Apache failure.

## Change workflow

Edit tracked sources at the repository root. The core `skins/Nord` and `extensions/NordNotes` entries are symlinks back to those sources, so browser refreshes use the edits immediately. Put temporary data only in this project's `.codex/`. Do not commit the database, uploads, generated caches, official core, `LocalSettings.php`, SQL dumps, or credentials.

Before a schema/extension upgrade, snapshot the database and files using `docs/DATABASE-BACKUP.md`. Keep content edits in MediaWiki; export the chosen current pages back to `content/pages.json` when intentionally updating the rebuild snapshot.
