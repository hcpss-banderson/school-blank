<?php

namespace Drupal\kind_wildcat\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ContentChunkTypeForm.
 */
class ContentChunkTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $content_chunk_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $content_chunk_type->label(),
      '#description' => $this->t("Label for the Content chunk type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $content_chunk_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\kind_wildcat\Entity\ContentChunkType::load',
      ],
      '#disabled' => !$content_chunk_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $content_chunk_type = $this->entity;
    $status = $content_chunk_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Content chunk type.', [
          '%label' => $content_chunk_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Content chunk type.', [
          '%label' => $content_chunk_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($content_chunk_type->toUrl('collection'));
  }

}
