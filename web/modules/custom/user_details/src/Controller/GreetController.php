<?php
namespace Drupal\user_details\Controller;

use Drupal\Core\Controller\ControllerBase;

class GreetController extends ControllerBase {

  public function greet($name) {
    return [
  '#markup' => 'Hello ' . $name . '! Welcome to Drupal.',
    ];
  }
}