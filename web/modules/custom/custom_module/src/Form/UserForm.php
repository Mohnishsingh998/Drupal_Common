<?php

namespace Drupal\custom_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
class UserForm extends FormBase {

  /**
   *
   */
  public function getFormId() {
    return 'details_form_user_form';
  }

  /**
   *
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#placeholder' => 'Enter your name',
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#placeholder' => 'Enter your email',
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Configuration'),
    ];

    return $form;
  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue('name');
    $email = $form_state->getValue('email');
    $age = $form_state->getValue('age');

    // Success message.
    \Drupal::messenger()->addMessage('Form submitted successfully!');

    // Log data to Drupal logs.
    \Drupal::logger('user_details_form')->notice(
    'User submitted: Name: @name, Email: @email, age: @age',
    [
      '@name' => $name,
      '@email' => $email,
      '@age' => $age,
    ]
    );
  }

}
