<?php

namespace Drupal\student_feedback\Service;

/**
 *
 */
class MailBuilder {

  /**
   *
   */
  public function build(array &$message, array $params) {

    $message['subject'] = 'New Feedback Received';

    $message['body'][] = 'Name: ' . $params['name'];
    $message['body'][] = 'Email: ' . $params['email'];
    $message['body'][] = 'Message: ' . $params['message'];
    $message['body'][] = 'Rating: ' . $params['rating'];
  }

}
