<?php

namespace Drupal\student_feedback\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class FeedbackForm extends FormBase {

  protected $feedbackManager;
  protected $feedbackId = NULL;

  public function __construct($feedbackManager) {
    $this->feedbackManager = $feedbackManager;
  }

  /**
   * similar like new Feedback().  -- > $container inject depencecy of the student_feedback.manager to manage such that we can use them here.
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
    $feedbackData = NULL;

    $form['#attributes']['class'][] = 'feedback-form';
    $form['#attached']['library'][] = 'student_feedback/styles';

    if ($id) {
      $feedbackData = $this->feedbackManager->getFeedbackById($id);
      $this->feedbackId = $id;
    }

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => 'Name',
      '#default_value' => $feedbackData->name ?? '',
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => 'Email',
      '#default_value' => $feedbackData->email ?? '',
      '#required' => TRUE,
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => 'Message',
      '#default_value' => $feedbackData->message ?? '',
      '#required' => TRUE,
    ];

    $form['rating'] = [
      '#type' => 'select',
      '#title' => 'Rating',
      '#options' => [1, 2, 3, 4, 5],
      '#default_value' => $feedbackData->rating ?? 1,
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $id ? 'Update' : 'Submit'
    ];

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

    if (!filter_var($form_state->getValue('email'), FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('email', 'Invalid email address');
    }
  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $feedbackData = [
      'name' => $form_state->getValue('name'),
      'email' => $form_state->getValue('email'),
      'message' => $form_state->getValue('message'),
      'rating' => $form_state->getValue('rating'),
    ];

    if ($this->feedbackId) {
      $this->feedbackManager->updateFeedback($this->feedbackId, $feedbackData);
      $this->messenger()->addMessage('Updated');
      $form_state->setRedirect('student_feedback.list');
    }
    else {
      $this->feedbackManager->saveFeedback($feedbackData);
      $this->messenger()->addMessage('Saved');
      $form_state->setRedirect('student_feedback.list');
    }
  }

}