<?php

namespace Drupal\block_content_explorer;

use Drupal\block_content\BlockContentAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 *
 */
class BlockContentExplorerAccessHandler extends BlockContentAccessControlHandler {

  /**
   *
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if ($account->hasPermission('administer block content')) {
      return AccessResult::allowed()->addCacheableDependency($entity);
    }
    return parent::checkAccess($entity, $operation, $account);
  }

}
