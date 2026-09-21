# FuuraikoWiki configuration

This repository is the rebuildable part of [FuuraikoWiki](https://wiki.flowing-wind.space): the Nord skin, NordNotes extension, modular site configuration, Apache examples, and a current-content snapshot of the small set of pages required by the site. It intentionally excludes MediaWiki core, the database, uploaded files, accounts, preferences, logs, revision history, and secrets.

The snapshot in `content/pages.json` contains the current Main Page, About page, policy pages, collection categories, article templates, sidebar, and required interface messages. Applying it creates ordinary edits on the destination wiki; it does not import old revision history.

## Repository layout

- `skins/Nord/` — FuuraikoWiki Nord skin.
- `extensions/NordNotes/` — site-specific dashboard, discovery, translation status, and New Article features.
- `config/` — settings loaded after MediaWiki's installer-generated `LocalSettings.php`.
- `content/pages.json` — current page content only.
- `ops/scripts/release-pages.php` — publishes the page snapshot as normal revisions.
- `ops/apache/` — local WSL and production Apache examples.
- `docs/` — reconstruction, database backup, and WSL handoff notes.

## Versions

- MediaWiki 1.46.0
- Nord 1.5.1
- NordNotes 0.3.2
- Translate and UniversalLanguageSelector revisions are pinned in `config/translation-versions.json`.

## Rebuild outline

1. Install the runtime packages and MediaWiki core described in `docs/REBUILD.md`.
2. Complete the MediaWiki installer and keep its generated `LocalSettings.php` private.
3. Link `skins/Nord` and `extensions/NordNotes` into the MediaWiki core tree.
4. Append the bootstrap line shown in `config/LocalSettings.append.php.example` to `LocalSettings.php`.
5. Install the pinned Translate and UniversalLanguageSelector revisions and Translate's Composer dependencies.
6. Run MediaWiki's database updater.
7. Publish `content/pages.json` with `ops/scripts/release-pages.php`.

Database and uploads must be backed up separately. See `docs/DATABASE-BACKUP.md`. Never commit `LocalSettings.php`, SQL dumps, uploaded files, certificates, or access credentials.

## License boundaries

Nord source is GPL-2.0-or-later. MediaWiki and third-party extensions retain their own licenses. Wiki page content is governed by the notices on the wiki, including CC BY-NC 4.0 where applicable. Site artwork and third-party assets retain their stated terms.

