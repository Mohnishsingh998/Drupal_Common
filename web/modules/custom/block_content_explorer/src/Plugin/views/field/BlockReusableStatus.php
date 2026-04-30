<?php

namespace Drupal\block_content_explorer\Plugin\views\field;

use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Renders the block reusable flag as a human-readable coloured badge.
 *
 * Displays "✅ Reusable" when the block is reusable, or
 * "🔒 Inline" when it is an inline/non-reusable block.
 */
#[ViewsField("block_reusable_status")]
class BlockReusableStatus extends FieldPluginBase {

  /**
   * {@inheritdoc}
   *
   * Calls parent so the reusable column is included in the SQL SELECT.
   */
  public function query(): void {
    parent::query();
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values): array {
    // The Views query aliases the column as {table}_{field}.
    $value = $values->block_content_field_data_reusable ?? NULL;

    if ($value) {
      return [
        '#markup' => '<span style="color:green;">✅ Reusable</span>',
      ];
    }

    return [
      '#markup' => '<span style="color:red;">🔒 Inline</span>',
    ];
  }

}
