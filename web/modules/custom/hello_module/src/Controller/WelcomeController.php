<?php

namespace Drupal\hello_module\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for hello_module routes.
 */
class WelcomeController extends ControllerBase {

  public function welcome() {

    $body = "Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, 
when an unknown printer took a galley of type and scrambled it to make a type specimen book. 
It has survived not only five centuries, but also the leap into electronic typesetting,
remaining essentially unchanged. It was popularized in the 1960s with the release of Letraset
sheets containing Lorem Ipsum passages, and more recently with desktop publishing software
like Aldus PageMaker including versions of Lorem Ipsum.";

    // The key must be '#markup' and the value is your variable.
    return [
      '#theme' => 'welcome_body',
      '#body' => $body,
      '#attached' => [
        'library' => [
          'hello_module/custom',
        ],
      ]
    ];
  }
}