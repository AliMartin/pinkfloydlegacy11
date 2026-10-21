# Drupal 11 upgrade record

Date: 21 October 2026. Scope: this local checkout and its DDEV database.
Production has not been deployed. The repository root is the Drupal web root.

## Result and platform

- Core: Drupal 10.6.14 → prepared on 10.6.20 → Drupal 11.4.11.
- Drush: 12.5.3 → 13.8.0; Composer Installers: 1.12 → 2.3.
- Local runtime: PHP 8.3.30, MariaDB 11.8.6, Composer 2, nginx via DDEV.
- Drupal 11 requires PHP 8.3+ and a supported database (MariaDB 10.6+,
  MySQL 8.0+, PostgreSQL 16+, or SQLite 3.45+). This site's database driver
  is MySQL/MariaDB. Verify requirements for the exact release before deployment.
- `.ddev/config.yaml` now declares `drupal11`. It is tracked; generated DDEV
  files, TLS keys and local settings remain ignored.
- Full versions and frontend asset paths: [dependency-versions.md](dependency-versions.md).

## Dependency and compatibility decisions

All contributed extensions, PHP dependencies and standalone frontend assets are
Composer managed. `composer.json`, `composer.libraries.json`, `composer.lock`,
the local patch, custom module, custom theme and `sync/` belong in version control.
Generated `core/`, `vendor/`, `modules/contrib/`, `themes/contrib/` and `libraries/`
do not. Existing uncommitted work was preserved; no Git commit or push was made.

`wikimedia/composer-merge-plugin` includes the root `composer.libraries.json`.
This contains Webform's 17 published asset package definitions and all eight
site asset packages. Explicit package repositories use pinned upstream archives,
with `drupal-library` types and installer names matching Drupal's expected paths.
They do not require npm, Bower, manual downloads or Asset Packagist at deploy time.
The unnecessary asset installer extender and Asset Packagist repository were removed.
When updating Webform, compare its published library manifest with the root copy
and update the root definitions and lock together. Do not edit contributed files.

The custom theme now attaches local Font Awesome Free 6.7.2 CSS through its Drupal
library definition. The hosted Font Awesome kit was removed. Existing `fab`,
`fas` and `fa-*` class markup is supported. The theme's compiled CSS and its
existing Google Fonts import remain; analytics, reCAPTCHA, GTranslate, maps and
embedded media still use their respective external services.

Insert View moved to 3.0.0; Responsive Favicons to 4.0.0. YouTube uses
`^3.0@beta`, currently 3.0.0-beta1, because its stable 2.0 release does not support
Drupal 11. This is a package-specific exception; global minimum stability remains
stable. Review and move to a stable compatible YouTube release when available.

### Background Image compatibility patch

The final dependency is the stable 2.0.2 release (`~2.0.2`) plus
`patches/background-image-drupal11.patch`. Its development branch includes a
Context/Media migration and new dependencies beyond Drupal 11 compatibility.
A trial of that branch failed during its update; the original database backup
was restored before continuing with 2.0.2. None of that migration remains in the
final database. All 25 original background image entities are retained.

The local patch backports the runtime fixes from upstream commit
`31217734bbd3e57f8fba4beca7398abfd397f4b6` (issue #3428206), plus fixes found when
running on the installed Drupal 11/Symfony 7 versions:

- Declare Drupal 11 support in the info file.
- Replace GD toolkit `getResource()` with `getImage()`.
- Replace removed `file_create_url()` calls with the file URL generator.
- Add the event subscriber's `array` return type.
- Remove obsolete Symfony `ContainerAwareTrait`/`ContainerAwareInterface` use.
  The entity obtains Drupal's container directly; the manager already injects
  its services and does not use the removed trait.
- Match the core entity `__sleep(): array` signature.

`mglaman/composer-drupal-lenient` permits the core constraint for **only**
`drupal/background_image`, allowing Composer to resolve 2.0.2 before the patch is
applied. It is not a general compatibility bypass. `cweagans/composer-patches`
applies the tracked patch and fails installation if it cannot apply. A clean
Composer install was verified to apply the patch. Revisit this patch and the
lenient exception together when upgrading Background Image. Never skip patch
failures or change only a module's compatibility declaration.

### Configuration and legacy media

The deprecated `webform_bootstrap` module required Bootstrap 3 despite this
site using the Bootstrap 5 theme. It was uninstalled while still on Drupal 10.
Webform, all its other enabled submodules, and the Bootstrap 5 theme remain.
Core database updates installed Search Node and Search Help as required by the
existing Search/Node/Help configuration; those changes are in the fresh export.

Two existing media records (IDs 2 and 19, bundle `instagram`) retained their URL
field storage and values but lacked their media type, field instance and display
configuration. On Drupal 11 this caused a page and cron search indexing to fail.
With the user's approval, `modules/custom/legacy_media` provides an external-link
media source and string URL formatter. The idempotent recovery script
`scripts/restore-legacy-instagram.php` recreated only missing bundle/field/display
configuration. Media IDs, UUIDs, names, thumbnails, revisions and stored URLs
were not rewritten or deleted. New configuration is included in `sync/`.
The formatter links only valid HTTP(S) URLs and escapes all other values.
It makes no Instagram API requests. Existing embeds now show their media titles
as links; reinstating rich Instagram embeds would be a separate feature.

The imported local database initially had maintenance mode enabled. It was
turned off locally for HTTP verification. Production maintenance mode should be
managed during deployment, not copied from this local database.

## Backups and recovery material

Ignored local rollback material is in `.upgrade-backups/`:

- `pre-drupal11/database.sql.gz`: original 10.6.14 database.
- `pre-drupal11/composer.json` and `composer.lock`: original dependency files.
- `pre-drupal11/sync.tar.gz`: original on-disk sync contents.
- `pre-drupal11/active-config/`: original database configuration export,
  which differed from the old on-disk sync directory.
- `pre-drupal11/libraries-original/` and `libraries.tar.gz`: original manual assets.
- `pre-drupal11/prepared-drupal10.sql.gz`: database after restoring the original,
  removing Webform Bootstrap 3, and completing Drupal 10 preparation.
- Composer, update, validation, cron and HTTP check logs.

Keep these backups outside any deployed web root. They can contain private
configuration and database content; do not commit, publish or upload them.

## Deploying to another environment

1. Rehearse using a fresh production database and uploads in staging. Check PHP,
   database and extension requirements. Back up the deployment's own database,
   code/lockfiles, settings, public uploads, and private uploads.
2. While production is still on Drupal 10, first bring it to a supported current
   Drupal 10 release and run `drush updatedb -y` and `drush cache:rebuild`.
   Uninstall `webform_bootstrap` before replacing core code. The locally rehearsed
   source was 10.6.14/10.6.20; older databases need their intervening updates.
3. Enable maintenance mode, then deploy this code, both Composer manifests,
   lockfile, patch, custom module/theme and exported configuration. Run the
   following in the project root using PHP 8.3+:

   ```sh
   composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
   vendor/bin/drush updatedb -y
   vendor/bin/drush config:import -y
   vendor/bin/drush cache:rebuild
   vendor/bin/drush updatedb:status
   vendor/bin/drush php:script scripts/verify-drupal11.php
   ```

   Importing the exported config enables `legacy_media` and restores the missing
   Instagram configuration. The recovery script is only needed to reproduce the
   local repair without importing `sync/`; do not rerun it as a routine deploy step.
4. Check the homepage, menu at desktop/mobile widths, shows, tour map, news,
   article pages, contact form, login, media library, editors and admin pages.
   Test outbound email and reCAPTCHA on staging with appropriate credentials;
   automated checks here did not submit forms or send email.
5. Disable maintenance mode, rebuild caches and repeat public smoke checks.
   Configure cron and monitor the logs. Do not deploy the local database over
   a live site with newer content.

The existing `.cpanel.yml` merely copies files and does **not** run Composer or
Drush, handle dotfiles, migrate the database, or implement rollback. A production
release process must perform the steps above. Production was not changed here.

For a new clone in DDEV, use the tracked config and `ddev start`, then
`ddev composer install`, import the database and uploads, and configure local
settings. Set `config_sync_directory` to the root `sync` directory. Never commit
real credentials or use `site:install` against an existing site's database.

### Web root protection

Because this layout has configuration and runtime directories in the web root,
nginx must explicitly deny direct HTTP access to `/sync/`, `/private/`,
`/private-files/` and `/tmp/`. The tracked `.ddev/nginx/private-paths.conf` does
this locally. Add equivalent production nginx rules, or honor Drupal's `.htaccess`
files on Apache. Prefer a private uploads directory outside the production web
root. The configured local private-files directory was missing; it was created
and Drupal generated its `.htaccess`. The existing `private/` directory was not
moved, merged or deleted.

### Rollback

Rollback needs matching code **and** database. Do not downgrade core code against
an upgraded database. For this local site, restore the original manifests from
`pre-drupal11/`, remove the new `composer.libraries.json` merge from the active
manifest by restoring the original `composer.json`, then run `ddev composer install`.
Restore the original manual `libraries/` directory if returning to the original
manifest; restore the old theme library/Twig changes as well, since that theme
used a hosted Font Awesome kit. Import `pre-drupal11/database.sql.gz`, restore the
old sync archive, set DDEV type back to `drupal10` and rebuild caches. The added
custom module may remain on disk disabled. Keep a full copy of this upgraded
checkout before rollback. For production, use the environment's own matched
predeployment code/database/uploads backup rather than these local backups.

## Validation and repeating checks

```sh
ddev composer validate --strict
ddev composer check-platform-reqs
ddev composer audit
ddev drush updatedb:status
ddev drush core:requirements
ddev drush php:script scripts/verify-drupal11.php
ddev exec php scripts/smoke-test.php
```

`verify-drupal11.php` checks core/PHP, compatibility declarations of every enabled
extension, all 25 Composer library directories, menu/icon assets, media bundle
integrity, serialization and URL generation for every background image, entity
counts, and exact active/exported config agreement. The HTTP script checks key
public routes and their local CSS/JS assets; an optional text file argument adds
other paths. The upgrade validation included every published node URL.

A separate empty directory `/tmp/pfl-drupal11-clean` inside DDEV was populated
with only the two manifests, lockfile and patch, then installed with
`composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction`.
This checks dependency/asset/patch reproducibility without relying on the old
manual library folders. No new site or database was installed there.

### Verified outcome

- `composer validate --strict` and platform requirements pass.
- Composer audit reports no advisories and no abandoned packages.
- Database updates report no pending updates; cron completes without errors.
- All 50 published node pages, homepage, login, shows, news and contact return 200.
- All 30 unique local CSS/JavaScript assets on those pages return 200.
- All 25 background images pass serialization and image URL checks.
- Entity counts are unchanged: 71 nodes, 41 media, 5 users, 25 background images.
- Fresh sync contains 455 configuration YAML files plus its access-protection
  `.htaccess`. No stale YAML or `.DS_Store` remains. Active/exported config matches.
- A dependency installation from an empty directory passes, including assets
  and the Background Image patch.
- nginx denies direct access to sync/private directories with HTTP 403.

No browser automation surface was available in this session, so visual desktop /
mobile inspection and interactive form submission remain deployment acceptance
checks. HTTP checks verify server rendering and asset availability, not browser
JavaScript execution, third party services or outbound email.

The final requirements report contains **no errors**. Remaining warnings are
writable local settings files, enabled HTML5 upload filename validation, and
core extensions deprecated for a future major release: Field Layout, History
and Stable 9. These existing extensions/configuration were retained. Production
settings files should have appropriate read-only permissions; plan replacements
for deprecated extensions before a future Drupal 12 upgrade.

## Refreshing exported configuration

Only after updates and compatibility repairs, the old sync directory was moved
into the ignored backup area, a completely empty `sync/` was created, and a fresh
`ddev drush config:export -y` was run. This removes stale YAML and operating-system
files. `drush config:status` and the verification script must then report no
active/exported differences. Do not import the stale pre-upgrade export.

For future refreshes, back up active config and the existing sync directory first,
confirm the configured path with `ddev drush status --field=config-sync`, empty
only that directory, run `ddev drush config:export -y`, and review the diff before
deployment. Keep the directory's access protection.

## References

- [Official Drupal 10 to 11 upgrade guide](https://www.drupal.org/docs/upgrading-drupal/upgrading-drupal/how-to-upgrade-from-drupal-10-to-drupal-11)
- [Drupal database requirements](https://www.drupal.org/docs/getting-started/system-requirements/database-server-requirements)
- [Background Image upstream compatibility commit](https://git.drupalcode.org/project/background_image/-/commit/31217734bbd3e57f8fba4beca7398abfd397f4b6)
- [YouTube 3.0.0-beta1 release](https://www.drupal.org/project/youtube/releases/3.0.0-beta1)

### Final status recheck

A later status recheck confirmed Drupal 11.4.11 bootstraps successfully, no
database updates remain, and active configuration matches the export. That
recheck also emitted a non-blocking PHP deprecation from the contributed Video
module: `VideoRemoteStreamWrapper::basePath()` implicitly nullable `$site_path`.
Review an upstream fix when updating Video; this did not prevent the checks.
