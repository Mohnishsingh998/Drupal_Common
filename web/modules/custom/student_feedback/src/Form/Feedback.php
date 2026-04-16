<?php

namespace Drupal\student_feedback\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class Feedback extends FormBase {

  protected $manager;
  protected $id = NULL;

  public function __construct($manager) {
    $this->manager = $manager;
  }

  /**
   *
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('student_feedback.manager')
    );
  }

  /**
   *
   */
  public function getFormId() {
    return 'student_feedback_form';
  }

  /**
   *
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {

    if ($id) {
      $data = $this->manager->getFeedbackById($id);
      $this->id = $id;
    }

    $form['name'] = ['#type' => 'textfield', '#title' => 'Name', '#default_value' => $data->name ?? ''];
    $form['email'] = ['#type' => 'email', '#title' => 'Email', '#default_value' => $data->email ?? ''];
    $form['message'] = ['#type' => 'textarea', '#title' => 'Message', '#default_value' => $data->message ?? ''];
    $form['rating'] = ['#type' => 'number', '#title' => 'Rating', '#default_value' => $data->rating ?? 1];

    $form['submit'] = ['#type' => 'submit', '#value' => $id ? 'Update' : 'Submit'];

    return $form;
  }

  /**
   *
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $rating = $form_state->getValue('rating');

    if ($rating < 1 || $rating > 5) {
      $form_state->setErrorByName('rating', 'Rating must be between 1 and 5.');
    }
  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $data = [
      'name' => $form_state->getValue('name'),
      'email' => $form_state->getValue('email'),
      'message' => $form_state->getValue('message'),
      'rating' => $form_state->getValue('rating'),
    ];

    if ($this->id) {
      $this->manager->updateFeedback($this->id, $data);
      $this->messenger()->addMessage('Updated');
    }
    else {
      $this->manager->saveFeedback($data);
      $this->messenger()->addMessage('Saved');
    }
  }

}
