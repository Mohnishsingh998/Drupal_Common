<?php
 
 
namespace Drupal\user_details\Form;
 
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
 
class UserDetailsForm extends FormBase {
 
  public function getFormId() {
    return 'user_details_form';
  }
 
  public function buildForm(array $form, FormStateInterface $form_state) {
 
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => 'Name',
      '#required' => TRUE,
    ];
 
    $form['email'] = [
      '#type' => 'email',
      '#title' => 'Email',
      '#required' => TRUE,
    ];
 
    $form['age'] = [
      '#type' => 'textfield',
      '#title' => 'age',
      '#required' => TRUE,
    ];
 
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
    ];
 
    return $form;
  }
 
  public function submitForm(array &$form, FormStateInterface $form_state) {
 
    $name = $form_state->getValue('name');
    $email = $form_state->getValue('email');
    $age = $form_state->getValue('age');
 
    //success message
    \Drupal::messenger()->addMessage('Form submitted successfully!');
 
    // Log data to Drupal logs
    \Drupal::logger('user_details_form')->notice(
      'User submitted: Name: @name, Email: @email, age: @age',
      [
        '@name' => $name,
        '@email' => $email,
        '@age' => $age,
      ]
    );
// disable form submisssion on the ref=resh button
    $form_state->setRedirect('<current>');
  }
}