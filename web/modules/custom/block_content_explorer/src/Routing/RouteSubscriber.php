<?php

namespace Drupal\block_content_explorer\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 *
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   *
   */
  protected function alterRoutes(RouteCollection $collection) {
    $routes_to_fix = [
      'entity.block_content.canonical',
      'entity.block_content.edit_form',
      'entity.block_content.delete_form',
    ];

    foreach ($routes_to_fix as $route_name) {
      if ($route = $collection->get($route_name)) {
        // Nuke all existing access requirements.
        $route->setRequirements([
          '_custom_access' => '\Drupal\block_content_explorer\Access\BlockContentAccessCheck::access',
        ]);
      }
    }
  }

}
