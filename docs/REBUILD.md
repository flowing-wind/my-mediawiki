# Rebuild FuuraikoWiki

## What Git restores

Git restores the Nord presentation, NordNotes behavior, site configuration modules, Apache examples, and the selected pages in `content/pages.json`. It does not restore accounts, passwords, user preferences, edit history, logs, deleted revisions, uploaded files, or other database state.

## Runtime prerequisites

The validated local environment uses Ubuntu 26.04 under WSL 2, Apache 2.4, PHP 8.5, MariaDB 11.8, ImageMagick, and MediaWiki 1.46.0. Required PHP modules include APCu, cURL, DOM/XML, GD, intl, mbstring, mysqli, and XSL.

Create the project as `/home/fuuraiko/Projects/MediaWiki`. Put the official MediaWiki tree at `public/w`; do not commit it. Verify the official archive checksum before extraction.

Create project links from the core tree:

```bash
ln -s ../../../skins/Nord public/w/skins/Nord
ln -s ../../../extensions/NordNotes public/w/extensions/NordNotes
```

Install Translate and UniversalLanguageSelector under `public/w/extensions/` at the revisions recorded in `config/translation-versions.json`. Install Translate's Composer dependencies using the lock information retained in this repository. Bundled extensions are listed in the same version file.

Run the MediaWiki web installer or `maintenance/run.php install`, using a dedicated MariaDB database and account. Keep the resulting `public/w/LocalSettings.php` private. Append:

```php
require dirname( __DIR__, 2 ) . '/config/bootstrap.php';
```

Then update the schema and check configuration:

```bash
php public/w/maintenance/run.php update
php public/w/maintenance/run.php showSiteStats
apache2ctl configtest
```

Publish the selected current contents with an existing administrator name:

```bash
php ops/scripts/release-pages.php \
  --manifest "$PWD/content/pages.json" \
  --user YOUR_ADMIN_USERNAME
```

This creates normal revisions. It does not import local histories or preferences. Existing pages with the same titles receive a new revision, so back up the database first.

## Local Apache

Copy `ops/apache/wsl-local.conf` to `/etc/apache2/sites-available/mediawiki.conf`, enable it, and ensure Apache listens on `127.0.0.1:8080`. The separate local acceptance proxy on `127.0.0.1:8090` can coexist because it has its own address and port.

```bash
sudo a2enmod rewrite
sudo a2ensite mediawiki.conf
sudo apache2ctl configtest
sudo systemctl reload apache2
```

The local URL is `http://localhost:8080/wiki/Main_Page`.

## Items that remain private or manual

- Database name, user, and password.
- `$wgSecretKey`, `$wgUpgradeKey`, server URL, and mail settings.
- Administrator account and all user preferences.
- TLS certificates and production-only Apache details.
- Database backups and `public/w/images/` uploads.

After a complete restore, choose the Nord skin and desired interface language in account preferences if the account already has explicit overrides. Theme, text size, and collapsed sidebars are stored in each browser's local storage.

