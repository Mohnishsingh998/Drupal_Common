<?php

namespace Drupal\weather_widget\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
class WeatherSettingsForm extends ConfigFormBase {

  /**
   *
   */
  protected function getEditableConfigNames() {
    return ['weather_widget.settings'];
  }

  /**
   *
   */
  public function getFormId() {
    return 'weather_widget_settings_form';
  }

  /**
   *
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('weather_widget.settings');

    $form['default_city'] = [
      '#type' => 'textfield',
      '#title' => 'Default City',
      '#default_value' => $config->get('default_city'),
      '#required' => TRUE,
    ];

    $form['default_unit'] = [
      '#type' => 'select',
      '#title' => 'Default Unit',
      '#options' => ['metric' => 'Celsius', 'imperial' => 'Fahrenheit'],
      '#default_value' => $config->get('default_unit'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('weather_widget.settings')
      ->set('default_city', $form_state->getValue('default_city'))
      ->set('default_unit', $form_state->getValue('default_unit'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
