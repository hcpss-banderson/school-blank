<?php

namespace Drupal\kind_wildcat\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'kind_wildcat_rich_link_default' formatter.
 *
 * @FieldFormatter(
 *   id = "kind_wildcat_rich_link_default",
 *   label = @Translation("Default"),
 *   field_types = {"kind_wildcat_rich_link"}
 * )
 */
class RichLinkDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {

      if ($item->label) {
        $element[$delta]['label'] = [
          '#type' => 'item',
          '#title' => $this->t('Label'),
          '#markup' => $item->label,
        ];
      }

      if ($item->uri) {
        $element[$delta]['uri'] = [
          '#type' => 'item',
          '#title' => $this->t('URI'),
          'content' => [
            '#type' => 'link',
            '#title' => $item->uri,
            '#url' => Url::fromUri($item->uri),
          ],
        ];
      }

      if ($item->description) {
        $element[$delta]['description'] = [
          '#type' => 'item',
          '#title' => $this->t('Description'),
          '#markup' => $item->description,
        ];
      }

    }

    return $element;
  }

}
