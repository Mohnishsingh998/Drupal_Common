<?php

namespace Drupal\custom_module\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 *
 */
class GreetController extends ControllerBase {

  /**
   *
   */
  public function greet($name) {

    return [
      '#markup' => 'Hello ' . $name . '! Welcome to Drupal.',
    ];
  }

}
