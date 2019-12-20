<?php

namespace Drupal\kind_wildcat\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Defines the 'kind_wildcat_rich_link' field widget.
 *
 * @FieldWidget(
 *   id = "kind_wildcat_rich_link",
 *   label = @Translation("Rich Link"),
 *   field_types = {"kind_wildcat_rich_link"},
 * )
 */
class RichLinkWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => isset($items[$delta]->label) ? $items[$delta]->label : NULL,
    ];

    $element['uri'] = [
      '#type' => 'url',
      '#title' => $this->t('URI'),
      '#default_value' => isset($items[$delta]->uri) ? $items[$delta]->uri : NULL,
    ];

    $element['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => isset($items[$delta]->description) ? $items[$delta]->description : NULL,
    ];

    $element['#theme_wrappers'] = ['container', 'form_element'];
    $element['#attributes']['class'][] = 'kind-wildcat-rich-link-elements';
    $element['#attached']['library'][] = 'kind_wildcat/kind_wildcat_rich_link';

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, FormStateInterface $form_state) {
    return isset($violation->arrayPropertyPath[0]) ? $element[$violation->arrayPropertyPath[0]] : $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $delta => $value) {
      if ($value['label'] === '') {
        $values[$delta]['label'] = NULL;
      }
      if ($value['uri'] === '') {
        $values[$delta]['uri'] = NULL;
      }
      if ($value['description'] === '') {
        $values[$delta]['description'] = NULL;
      }
    }
    return $values;
  }

}
