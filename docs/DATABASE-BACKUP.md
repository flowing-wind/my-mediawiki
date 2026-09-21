# Database and file backup

The database is the authoritative store for pages, revisions, accounts, preferences, logs, categories, Translate state, and most wiki metadata. The Git repository cannot replace it.

## Create a consistent backup

Use a client option file so the password is not exposed in shell history or the process list:

```ini
# ~/.config/fuuraikowiki/mariadb-backup.cnf
[client]
host=localhost
user=YOUR_DATABASE_USER
password=YOUR_DATABASE_PASSWORD
```

Protect it and dump the database:

```bash
chmod 600 ~/.config/fuuraikowiki/mariadb-backup.cnf
mariadb-dump \
  --defaults-extra-file="$HOME/.config/fuuraikowiki/mariadb-backup.cnf" \
  --single-transaction --quick --default-character-set=binary \
  --routines --triggers mediawiki | gzip -9 > mediawiki-$(date +%F-%H%M%S).sql.gz
```

Back up uploads and private configuration at the same time:

```bash
tar -czf mediawiki-files-$(date +%F-%H%M%S).tar.gz \
  public/w/images public/w/LocalSettings.php
```

Store both archives outside the web root and preferably on another machine or storage provider. A usable recovery point consists of the matching database dump, uploads, private `LocalSettings.php`, this Git revision, and the recorded MediaWiki/extension versions.

## Restore

Stop edits or put the wiki in read-only mode, create an empty database with the same character settings, and restore:

```bash
gzip -dc mediawiki-TIMESTAMP.sql.gz | \
  mariadb --defaults-extra-file="$HOME/.config/fuuraikowiki/mariadb-backup.cnf" mediawiki
tar -xzf mediawiki-files-TIMESTAMP.tar.gz
php public/w/maintenance/run.php update
```

Restore into a compatible MediaWiki version first, then upgrade in supported steps. Test the restored site before replacing production. Database dumps contain private account and technical data; never publish or commit them.

