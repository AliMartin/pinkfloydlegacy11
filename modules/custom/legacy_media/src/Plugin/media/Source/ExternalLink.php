<?php

namespace Drupal\legacy_media\Plugin\media\Source;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\Attribute\MediaSource;
use Drupal\media\MediaInterface;
use Drupal\media\MediaSourceBase;

/**
 * Retains historical external media without contacting a retired embed API.
 */
#[MediaSource(
  id: 'legacy_external_link',
  label: new TranslatableMarkup('Legacy external link'),
  description: new TranslatableMarkup('A preserved external URL.'),
  allowed_field_types: ['string'],
  default_thumbnail_filename: 'no-thumbnail.png',
)]
class ExternalLink extends MediaSourceBase {

  public function getMetadataAttributes() {
    return [];
  }

  public function getMetadata(MediaInterface $media, $attribute_name) {
    if ($attribute_name === 'default_name') {
      return $media->label() ?: $this->getSourceFieldValue($media);
    }
    return parent::getMetadata($media, $attribute_name);
  }

}
