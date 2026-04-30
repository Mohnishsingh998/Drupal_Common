<?php

namespace Drupal\block_content_explorer\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\block_content\BlockContentInterface;

/**
 *
 */
class BlockContentAccessCheck implements AccessInterface {

  /**
   *
   */
  public function access(AccountInterface $account, ?BlockContentInterface $block_content = NULL): AccessResultInterface {
    \Drupal::logger('block_content_explorer')->notice('Access check fired. User: @user, has permission: @perm', [
      '@user' => $account->getAccountName(),
      '@perm' => $account->hasPermission('administer block content') ? 'YES' : 'NO',
    ]);

    if ($account->hasPermission('administer block content')) {
      return AccessResult::allowed()
        ->addCacheContexts(['user.permissions'])
        ->setCacheMaxAge(0);
    }
    return AccessResult::forbidden();
  }

}
