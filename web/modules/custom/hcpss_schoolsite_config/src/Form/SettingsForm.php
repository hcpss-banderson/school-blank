<?php

namespace Drupal\hcpss_schoolsite_config\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {
  
  /**
   * {@inheritDoc}
   * @see \Drupal\Core\Form\FormInterface::getFormId()
   */
  public function getFormId() {
    return 'hcpss_schoolsite_config_settings_form';
  }
  
  /**
   * @return array
   */
  protected function getEditableConfigNames() {
    return [
      'hcpss_schoolsite_config.settings'
    ];
  }
  
  /**
   * {@inheritDoc}
   * @see \Drupal\Core\Form\ConfigFormBase::submitForm()
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::configFactory()->getEditable('hcpss_schoolsite_config.settings')
      ->set('acronym', $form_state->getValue('hcpss_schoolsite_config_acronym'))
      ->set('facebook', $form_state->getValue('hcpss_schoolsite_config_facebook'))
      ->set('blog', $form_state->getValue('hcpss_schoolsite_config_blog'))
      ->set('google_analytics_code', $form_state->getValue('hcpss_schoolsite_config_google_analytics_code'))
      ->save();
  }
  
  /**
   * {@inheritDoc}
   * @see \Drupal\Core\Form\ConfigFormBase::buildForm()
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('hcpss_schoolsite_config.settings');
    
    $form['hcpss_schoolsite_config_acronym'] = [
        '#type' => 'textfield',
        '#title' => 'School code',
        '#default_value' => $config->get('acronym'),
    ];
    
    $form['hcpss_schoolsite_config_facebook'] = [
        '#type' => 'textfield',
        '#title' => 'Facebook URL',
        '#default_value' => $config->get('facebook'),
    ];
    
    $form['hcpss_schoolsite_config_blog'] = [
        '#type' => 'textfield',
        '#title' => 'Blog URL',
        '#default_value' => $config->get('blog'),
    ];
    
    $form['hcpss_schoolsite_config_google_analytics_code'] = [
        '#type' => 'textfield',
        '#title' => 'Google Analytics Code',
        '#default_value' => $config->get('google_analytics_code'),
    ];
    
    return parent::buildForm($form, $form_state);
  }
}
