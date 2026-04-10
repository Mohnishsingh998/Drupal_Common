<?php

declare(strict_types=1);

namespace Drupal\mohnish_01\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Mohnish 01 routes.
 */
final class Mohnish01Controller extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
