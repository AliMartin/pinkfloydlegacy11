<?php

namespace Drupal\legacy_media\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Renders historical string URL fields as safe external links.
 */
#[FieldFormatter(
  id: 'legacy_external_link',
  label: new TranslatableMarkup('Legacy external link'),
  field_types: ['string'],
)]
class ExternalLinkFormatter extends FormatterBase {

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $value = trim($item->value ?? '');
      // Only web URLs become links. Other values remain escaped plain text.
      if (filter_var($value, FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($value, PHP_URL_SCHEME) ?? ''), ['https', 'http'], TRUE)) {
        $elements[$delta] = [
          '#type' => 'link',
          '#title' => $items->getEntity()->label() ?: $value,
          '#url' => Url::fromUri($value),
          '#options' => ['attributes' => ['rel' => 'noopener noreferrer']],
        ];
      }
      else {
        $elements[$delta] = ['#plain_text' => $value];
      }
    }
    return $elements;
  }

}
