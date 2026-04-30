<?php

/**
 * @file
 */

/**
 * Implements hook_views_data().
 */
function block_content_explorer_views_data() {
  $data = [];

  // Define the inline_block_usage table.
  $data['inline_block_usage']['table'] = [
    'group' => t('Inline Block Usage'),
    'provider' => 'block_content_explorer',
    'base' => [
      'field' => 'block_content_id',
      'title' => t('Inline Block Usage'),
      'help' => t('Tracks inline block usage.'),
    ],
  ];

  // Field: block_content_id.
  $data['inline_block_usage']['block_content_id'] = [
    'title' => t('Block Content ID'),
    'help' => t('Referenced block content ID'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  // Field: layout_entity_type.
  $data['inline_block_usage']['layout_entity_type'] = [
    'title' => t('Entity Type'),
    'help' => t('Parent entity type'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  // Field: layout_entity_id.
  $data['inline_block_usage']['layout_entity_id'] = [
    'title' => t('Entity ID'),
    'help' => t('Parent entity ID'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  return $data;
}

/**
 * Implements hook_views_data_alter().
 */
function block_content_explorer_views_data_alter(array &$data) {

  // Attach relationship to block_content table.
  $data['block_content_field_data']['inline_block_usage'] = [
    'title' => t('Inline Block Usage'),
    'help' => t('Relates block content to inline usage table.'),
    'relationship' => [
      'id' => 'standard',
      'base' => 'inline_block_usage',
      'base field' => 'block_content_id',
      'relationship field' => 'id',
      'label' => t('Inline Block Usage'),
    ],
  ];

  $data['block_content_field_data']['block_reusable_status'] = [
    'title' => t('Reusable Status'),
    'help' => t('Displays reusable vs inline badge.'),
    'field' => [
      'id' => 'block_reusable_status',
      'real field' => 'reusable',
      'table' => 'block_content_field_data',
    ],
  ];

  $data['inline_block_usage']['inline_block_usage_field'] = [
    'title' => t('Used By'),
    'help' => t('Shows where the block is used.'),
    'field' => [
      'id' => 'inline_block_usage_field',
      'real field' => 'layout_entity_id',
      'additional fields' => ['layout_entity_type'],
    ],
  ];
}
