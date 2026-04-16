<?php

namespace Drupal\student_feedback\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * The class responsibe for managment of the form.
 */
class Manage {

  protected $config;
  protected $mailService;

  public function __construct(ConfigFactoryInterface $config_factory, MailService $mail_service) {
    // Here it is giving the Objects that i can read later.
    $this->config = $config_factory->get('student_feedback.settings');
    $this->mailService = $mail_service;
  }

  /**
   * For saving the Feedback provided via tha form.
   */
  public function saveFeedback(array $data) {
    // Check if the form is enabled or not.
    // reading object named 'enable_feedback' to check if form is enabled or not.
    if (!$this->config->get('enable_feedback')) {
      throw new \Exception('Feedback disabled');
    }
    // To check whether the rating is greater then minimum rating and not greater the highest 5.
    if ($data['rating'] < $this->config->get('min_rating') || $data['rating'] < 1 || $data['rating'] > 5) {
      throw new \Exception('Rating should be between 1 and 5');
    }
    // Inserting data in database.
    \Drupal::database()->insert('student_feedback')
      ->fields([
        'name' => $data['name'],
        'email' => $data['email'],
        'message' => $data['message'],
        'rating' => $data['rating'],
        'created' => time(),
      ])
      ->execute();
    \Drupal::logger('student_feedback')->notice('Before mail call');
    $this->mailService->sendFeedbackMail($data);
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
  public function deleteFeedback($id) {
    \Drupal::database()->delete('student_feedback')
      ->condition('id', $id)
      ->execute();
  }

  /**
   * Allow editing of the feedback.
   */
  public function updateFeedback($id, array $data) {
    \Drupal::database()->update('student_feedback')
      ->fields($data)
      ->condition('id', $id)
      ->execute();
  }

  /**
   * For getting all feedback on the path:'/admin/feedback'
   */
  public function getFeedbackById($id) {
    return \Drupal::database()->select('student_feedback', 'f')
      ->fields('f')
      ->condition('id', $id)
      ->execute()
      ->fetchObject();
  }

}
