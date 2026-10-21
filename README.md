# Pink Floyd Legacy

Drupal website using the repository root as its web root. All third party PHP
packages, contributed extensions and standalone frontend libraries are installed
by Composer; commit both Composer manifests and `composer.lock`.

## Local development

Requirements: Docker, DDEV, PHP 8.3 or later, and MariaDB 10.6 or later
(or MySQL 8.0 or later). This local project uses PHP 8.3 and MariaDB 11.8.

```sh
ddev start
ddev composer install
ddev drush status
```

For a new checkout, configure DDEV as `drupal11` with an empty docroot and PHP
8.3, import a database backup and public/private uploads, and create local
settings. The active site uses `legacy25`, based on the Composer-installed
Bootstrap 5 theme. Never run `site:install` against an existing site database.

- [Drupal 11 upgrade record and deployment/rollback instructions](docs/drupal-11-upgrade.md)
- Frontend dependencies: `composer.libraries.json`
- Exported configuration: `sync/`
- Custom theme: `themes/custom/legacy25/`

Do not commit databases, backups, generated dependencies, credentials or uploads.
Keep `sites/default/settings.local.php` local; configure the sync directory as
`DRUPAL_ROOT . '/sync'` on each environment.
