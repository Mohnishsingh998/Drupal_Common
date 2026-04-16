<?php

namespace Drupal\student_feedback\Service;

use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 *
 */
class MailService {

  protected $mailManager;
  protected $config;
  protected $currentUser;

  public function __construct(
    MailManagerInterface $mail_manager,
    ConfigFactoryInterface $config_factory,
    AccountProxyInterface $current_user,
  ) {
    $this->mailManager = $mail_manager;
    $this->config = $config_factory->get('student_feedback.settings');
    $this->currentUser = $current_user;
  }

  /**
   *
   */
  public function sendFeedbackMail(array $data) {
    \Drupal::logger('student_feedback')->notice('Mail function triggered');

    $to = $this->config->get('admin_email');
    \Drupal::logger('student_feedback')->notice('Admin email: ' . $to);
    if (empty($to)) {
      return;
    }

    $params = [
      'name' => $data['name'],
      'email' => $data['email'],
      'message' => $data['message'],
      'rating' => $data['rating'],
    ];

    $result = $this->mailManager->mail(
      'student_feedback',
      'feedback_notification',
      $to,
      $this->currentUser->getPreferredLangcode(),
      $params
    );

    \Drupal::logger('student_feedback')->notice('Mail result: ' . json_encode($result));
    \Drupal::logger('student_feedback')->notice('MailService reached');
  }

}
