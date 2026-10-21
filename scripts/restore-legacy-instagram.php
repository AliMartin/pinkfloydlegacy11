<?php

/**
 * Restore missing Instagram bundle configuration, preserving existing records.
 * Run after enabling legacy_media, before the final config export.
 * Existing valid bundle configuration is left untouched.
 */

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\media\Entity\MediaType;

if (MediaType::load('instagram')) {
  echo "Instagram media type already exists; no changes made.\n";
  return;
}
$storage = FieldStorageConfig::loadByName('media', 'field_media_instagram');
if (!$storage || $storage->getType() !== 'string') {
  throw new RuntimeException('Expected existing Instagram string field storage.');
}
MediaType::create([
  'id' => 'instagram',
  'label' => 'Instagram (legacy link)',
  'description' => 'Historical Instagram media retained as external links.',
  'source' => 'legacy_external_link',
  'source_configuration' => ['source_field' => 'field_media_instagram'],
  'new_revision' => TRUE,
])->save();
FieldConfig::create([
  'field_storage' => $storage,
  'bundle' => 'instagram',
  'label' => 'Instagram URL',
  'required' => TRUE,
])->save();
EntityViewDisplay::create([
  'targetEntityType' => 'media',
  'bundle' => 'instagram',
  'mode' => 'default',
  'status' => TRUE,
])->setComponent('field_media_instagram', [
  'type' => 'legacy_external_link',
  'label' => 'hidden',
])->removeComponent('thumbnail')->save();
EntityFormDisplay::create([
  'targetEntityType' => 'media',
  'bundle' => 'instagram',
  'mode' => 'default',
  'status' => TRUE,
])->setComponent('name', ['type' => 'string_textfield'])
  ->setComponent('field_media_instagram', ['type' => 'string_textfield'])
  ->save();
echo "Restored Instagram configuration. Media records and URLs were not modified.\n";
