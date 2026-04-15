<?php

namespace Drupal\custom_module\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 *
 */
class FormHook {

  /**
 *
 */
  #[Hook('form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state, $form_id) {

    if ($form_id === 'node_page_form') {
      $form['#validate'][] = [$this, 'validate'];
    }
  }

  /**
   *
   */
  public function validate(array &$form, FormStateInterface $form_state) {
    $title = $form_state->getValue('title')[0]['value'] ?? '';

    if (strlen($title) < 5) {
      $form_state->setErrorByName('title', 'Title must be at least 5 characters long. this call is from the hook number 1(custom_Module)');
    }
  }

}
