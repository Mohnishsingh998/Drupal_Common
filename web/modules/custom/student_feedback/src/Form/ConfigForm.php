<?php

namespace Drupal\student_feedback\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Making custom config form using the Config form base class.
 */
class ConfigForm extends ConfigFormBase {

  /**
   * Get the config setting from the student_feedback.settings.
   */
  protected function getEditableConfigNames() {
    return ['student_feedback.settings'];
  }

  /**
   * Reutrns the form id. uniqe for ther form .
   */
  public function getFormId() {
    return 'student_feedback_settings_form';
  }

  /**
   * Overiding the inbuilt buildForm method for custom use.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $feedbackConfig = $this->config('student_feedback.settings');

    $form['enable_feedback'] = [
      '#type' => 'checkbox',
      '#title' => 'Enable Feedback',
      '#default_value' => $feedbackConfig->get('enable_feedback'),
    ];

    $form['min_rating'] = [
      '#type' => 'number',
      '#title' => 'Minimum Rating',
      '#default_value' => $feedbackConfig->get('min_rating'),
    ];

    $form['admin_email'] = [
      '#type' => 'email',
      '#title' => 'Admin Email',
      '#default_value' => $feedbackConfig->get('admin_email'),
    ];

    // A method used in drupal to implement the config form.
    return parent::buildForm($form, $form_state);
  }

  /**
   * Again the inbuilt method to implement the subitFrom method for config form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $feedbackConfig = $this->config('student_feedback.settings');

    $feedbackConfig
      ->set('enable_feedback', $form_state->getValue('enable_feedback'))
      ->set('min_rating', $form_state->getValue('min_rating'))
      ->set('admin_email', $form_state->getValue('admin_email'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}