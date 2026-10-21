# Locked dependency versions

Upgrade performed on 21 October 2026. `composer.lock` is authoritative.

## Drupal extensions and upgrade tooling

| Package | Before | After |
| --- | --- | --- |
| `composer/installers` | v1.12.0 | v2.3.0 |
| `cweagans/composer-patches` | 1.7.3 | 1.7.3 |
| `drupal/address` | 2.0.4 | 2.0.4 |
| `drupal/admin_toolbar` | 3.6.3 | 3.6.3 |
| `drupal/background_image` | 2.0.2 | 2.0.2 |
| `drupal/backup_migrate` | 5.1.5 | 5.1.5 |
| `drupal/block_class` | 3.0.0 | 3.1.0 |
| `drupal/bootstrap5` | 4.0.8 | 4.0.8 |
| `drupal/captcha` | 2.0.10 | 2.0.10 |
| `drupal/core` | 10.6.14 | 11.4.11 |
| `drupal/core-composer-scaffold` | 10.6.14 | 11.4.11 |
| `drupal/core-project-message` | 10.6.14 | 11.4.11 |
| `drupal/core-recommended` | 10.6.14 | 11.4.11 |
| `drupal/core-vendor-hardening` | 10.6.14 | 11.4.11 |
| `drupal/crop` | 2.6.0 | 2.6.0 |
| `drupal/ctools` | 4.1.1 | 4.1.1 |
| `drupal/entity_browser` | 2.15.0 | 2.17.0 |
| `drupal/focal_point` | 2.1.2 | 2.1.3 |
| `drupal/geocoder` | 4.34.0 | 4.38.0 |
| `drupal/geofield` | 1.67.0 | 1.67.0 |
| `drupal/gtranslate` | 3.0.5 | 3.0.6 |
| `drupal/honeypot` | 2.2.2 | 2.2.2 |
| `drupal/inline_entity_form` | 3.0.0 | 3.0.0 |
| `drupal/insert_view` | 2.1.0 | 3.0.0 |
| `drupal/leaflet` | 10.4.9 | 10.4.13 |
| `drupal/leaflet_more_maps` | 2.2.3 | 2.2.3 |
| `drupal/linkit` | 7.0.16 | 7.0.17 |
| `drupal/manage_display` | 3.0.1 | 3.0.2 |
| `drupal/metatag` | 2.2.0 | 2.2.0 |
| `drupal/pathauto` | 1.15.0 | 1.15.0 |
| `drupal/rat` | 1.0.1 | 1.0.1 |
| `drupal/recaptcha` | 3.4.0 | 3.5.0 |
| `drupal/responsive_favicons` | 2.0.2 | 4.0.0 |
| `drupal/responsive_menu` | 4.4.5 | 4.4.5 |
| `drupal/token` | 1.17.0 | 1.17.0 |
| `drupal/video` | 3.1.0 | 3.2.0 |
| `drupal/views_bootstrap` | 5.5.0 | 5.5.6 |
| `drupal/webform` | 6.3.0 | 6.3.1 |
| `drupal/xmlsitemap` | 2.0.0 | 2.0.0 |
| `drupal/youtube` | 2.0.0 | 3.0.0-beta1 |
| `drush/drush` | 12.5.3 | 13.8.0 |
| `mglaman/composer-drupal-lenient` | — | 2.0.0 |
| `wikimedia/composer-merge-plugin` | — | v2.1.0 |

## Standalone frontend assets

Each package below is installed by Composer into its explicit library directory.
Webform definitions were copied from its published `composer.libraries.json`;
the site manifest is self-contained for clean installs.

| Composer package | Version | Directory |
| --- | --- | --- |
| `choices/choices` | 11.0.2 | `libraries/choices/` |
| `codemirror/codemirror` | 5.65.12 | `libraries/codemirror/` |
| `jquery/chosen` | 1.8.7 | `libraries/jquery.chosen/` |
| `jquery/hotkeys` | 0.2.0 | `libraries/jquery.hotkeys/` |
| `jquery/image-picker` | 0.3.1 | `libraries/jquery.image-picker/` |
| `jquery/inputmask` | 5.0.9 | `libraries/jquery.inputmask/` |
| `jquery/intl-tel-input` | 17.0.19 | `libraries/jquery.intl-tel-input/` |
| `jquery/rateit` | 1.1.5 | `libraries/jquery.rateit/` |
| `jquery/select2` | 4.0.13 | `libraries/jquery.select2/` |
| `jquery/textcounter` | 0.9.1 | `libraries/jquery.textcounter/` |
| `jquery/timepicker` | 1.14.0 | `libraries/jquery.timepicker/` |
| `popperjs/popperjs` | 2.11.6 | `libraries/popperjs/` |
| `progress-tracker/progress-tracker` | 2.0.7 | `libraries/progress-tracker/` |
| `signature_pad/signature_pad` | 2.3.0 | `libraries/signature_pad/` |
| `svg-pan-zoom/svg-pan-zoom` | 3.6.1 | `libraries/svg-pan-zoom/` |
| `tabby/tabby` | 12.0.3 | `libraries/tabby/` |
| `tippyjs/tippyjs` | 6.3.7 | `libraries/tippyjs/` |
| `pinkfloydlegacy-assets/mmenu` | 8.5.24 | `libraries/mmenu/` |
| `pinkfloydlegacy-assets/superfish` | 1.7.10 | `libraries/superfish/` |
| `pinkfloydlegacy-assets/hammerjs` | 2.0.8 | `libraries/hammerjs/` |
| `pinkfloydlegacy-assets/slick` | 1.8.1 | `libraries/slick/` |
| `pinkfloydlegacy-assets/colorbox` | 1.6.4 | `libraries/colorbox/` |
| `pinkfloydlegacy-assets/fitvids` | 1.1.0 | `libraries/fitvids/` |
| `pinkfloydlegacy-assets/jquery` | 4.0.0 | `libraries/jquery/` |
| `pinkfloydlegacy-assets/fontawesome` | 6.7.2 | `libraries/fontawesome/` |

Leaflet and Bootstrap runtime assets are shipped inside their Composer-installed
Drupal packages. Drupal core supplies the jQuery instance used by the website;
the separately preserved `libraries/jquery` package is not attached by the custom
theme and does not replace Drupal core jQuery.

The mmenu package retains its existing CC-BY-NC-4.0 license; confirm the existing
site license covers its use before a commercial deployment. Other asset license
metadata is recorded in `composer.libraries.json` and upstream license files.
