<?php

namespace Drupal\student_feedback\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * The class responsibe for managment of the form.
 */
class FeedbackManager {

  protected $feedbackConfig;
  protected $mailService;

  public function __construct(ConfigFactoryInterface $configFactory, MailService $mailService) {
    // Here it is giving the Objects that i can read later.
    $this->feedbackConfig = $configFactory->get('student_feedback.settings');
    $this->mailService = $mailService;
  }

  /**
   * For saving the Feedback provided via tha form.
   */
  public function saveFeedback(array $feedbackData) {
    // Check if the form is enabled or not.
    // reading object named 'enable_feedback' to check if form is enabled or not.
    if (!$this->feedbackConfig->get('enable_feedback')) {
      throw new \Exception('Feedback disabled');
    }

    // To check whether the rating is greater then minimum rating and not greater the highest 5.
    if (
      $feedbackData['rating'] < $this->feedbackConfig->get('min_rating') ||
      $feedbackData['rating'] < 1 ||
      $feedbackData['rating'] > 5
    ) {
      throw new \Exception('Rating should be between 1 and 5');
    }

    // Inserting data in database.
    \Drupal::database()->insert('student_feedback')
      ->fields([
        'name' => $feedbackData['name'],
        'email' => $feedbackData['email'],
        'message' => $feedbackData['message'],
        'rating' => $feedbackData['rating'],
        'created' => time(),
      ])
      ->execute();

    \Drupal::logger('student_feedback')->notice('Before mail call');
    $this->mailService->sendFeedbackMail($feedbackData);
    \Drupal::logger('student_feedback')->notice('After mail call');
  }

  /**
   * Get query to return all the feedback.
   */
  public function getAllFeedback() {
    return \Drupal::database()->select('student_feedback', 'f')
      ->fields('f')
      ->orderBy('created', 'DESC')
      ->execute()
      ->fetchAll();
  }

  /**
   * For deleting the feedback.
   */
  public function deleteFeedback($feedbackId) {
    \Drupal::database()->delete('student_feedback')
      ->condition('id', $feedbackId)
      ->execute();
  }

  /**
   * Allow editing of the feedback.
   */
  public function updateFeedback($feedbackId, array $feedbackData) {
    \Drupal::database()->update('student_feedback')
      ->fields($feedbackData)
      ->condition('id', $feedbackId)
      ->execute();
  }

  /**
   * For getting all feedback on the path:'/admin/feedback'
   */
  public function getFeedbackById($feedbackId) {
    return \Drupal::database()->select('student_feedback', 'f')
      ->fields('f')
      ->condition('id', $feedbackId)
      ->execute()
      ->fetchObject();
  }

}