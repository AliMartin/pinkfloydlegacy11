<?php

/**
 * Read-only post-upgrade checks. Run: ddev drush php:script scripts/verify-drupal11.php
 */

use Composer\Semver\Semver;

$failures = [];
$check = static function (bool $ok, string $message) use (&$failures): void {
  echo ($ok ? 'PASS' : 'FAIL') . ': ' . $message . PHP_EOL;
  if (!$ok) {
    $failures[] = $message;
  }
};

$check(version_compare(\Drupal::VERSION, '11.0', '>='), 'Drupal 11 or later: ' . \Drupal::VERSION);
$check(version_compare(PHP_VERSION, '8.3', '>='), 'PHP 8.3 or later: ' . PHP_VERSION);
foreach (['module', 'theme'] as $type) {
  $extensions = \Drupal::service('extension.list.' . $type)->getList();
  foreach (\Drupal::config('core.extension')->get($type) as $name => $weight) {
    $extension = $extensions[$name] ?? NULL;
    $constraint = $extension->info['core_version_requirement'] ?? '';
    $check($extension && $constraint && Semver::satisfies(\Drupal::VERSION, $constraint), "$type $name supports this core version");
  }
}

$libraries = json_decode(file_get_contents(DRUPAL_ROOT . '/composer.libraries.json'), TRUE, 512, JSON_THROW_ON_ERROR);
foreach ($libraries['repositories'] as $repository) {
  $package = $repository['package'];
  $directory = $package['extra']['installer-name'];
  $check(is_dir(DRUPAL_ROOT . '/libraries/' . $directory), "Composer library directory: $directory");
}
foreach ([
  'mmenu/dist/mmenu.js',
  'mmenu/dist/mmenu.css',
  'superfish/dist/js/superfish.min.js',
  'superfish/dist/js/hoverIntent.js',
  'fontawesome/css/all.min.css',
  'fontawesome/webfonts/fa-brands-400.woff2',
  'fontawesome/webfonts/fa-solid-900.woff2',
] as $file) {
  $check(is_file(DRUPAL_ROOT . '/libraries/' . $file), "Frontend asset: $file");
}

foreach (['node', 'media', 'user', 'background_image'] as $type) {
  $count = \Drupal::entityQuery($type)->accessCheck(FALSE)->count()->execute();
  echo "INFO: $type entity count = $count" . PHP_EOL;
}
$media_types = \Drupal::entityTypeManager()->getStorage('media_type')->loadMultiple();
foreach (\Drupal::entityTypeManager()->getStorage('media')->loadMultiple() as $media) {
  $check(isset($media_types[$media->bundle()]), 'Media ' . $media->id() . ' has an installed media type');
}
foreach (\Drupal::entityTypeManager()->getStorage('background_image')->loadMultiple() as $image) {
  $copy = unserialize(serialize($image));
  $check($copy->id() === $image->id() && (bool) $copy->getImageUrl(), 'Background ' . $image->id() . ' serializes and generates its image URL');
}
$storage = \Drupal::service('config.storage');
$sync = \Drupal::service('config.storage.sync');
$active_names = $storage->listAll();
$sync_names = $sync->listAll();
sort($active_names);
sort($sync_names);
$check($active_names === $sync_names, 'Active and exported configuration names match');
foreach ($active_names as $name) {
  if ($storage->read($name) !== $sync->read($name)) {
    $failures[] = "Configuration differs: $name";
    echo "FAIL: Configuration differs: $name" . PHP_EOL;
  }
}
$check(!$failures, 'All post-upgrade checks passed');
if ($failures) {
  throw new \RuntimeException(implode(PHP_EOL, $failures));
}
