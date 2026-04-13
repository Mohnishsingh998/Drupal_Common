<?php
namespace Drupal\user_details\Controller;

use Drupal\Core\Controller\ControllerBase;

class GreetController extends ControllerBase {

  public function greet($name) {
    return [
      '#markup' => $this->t('Hello @name! Welcome to Drupal.', [
        '@name' => $name,
      ]),
    ];
  }
}