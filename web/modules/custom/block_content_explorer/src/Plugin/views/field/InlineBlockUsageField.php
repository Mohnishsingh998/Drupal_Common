<?php

namespace Drupal\block_content_explorer\Plugin\views\field;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\views\Attribute\ViewsField;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Shows where a block is used (inline + reusable).
 */
#[ViewsField("inline_block_usage_field")]
class InlineBlockUsageField extends FieldPluginBase {

  protected EntityTypeManagerInterface $entityTypeManager;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   *
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Format entity label consistently.
   */
  protected function formatEntityLabel(string $entity_type, $entity): array {
    $label = $entity->label();
    $id = $entity->id();

    $type = match ($entity_type) {
      'node' => 'Node',
      'entity_view_display' => 'Display',
      default => ucfirst(str_replace('_', ' ', $entity_type)),
    };

    // Special handling for entity_view_display.
    if ($entity_type === 'entity_view_display') {
      $parts = explode('.', $id);

      if (count($parts) === 3) {
        [, $bundle, $view_mode] = $parts;

        return [
          '#markup' => "Display: " . ucfirst(str_replace('_', ' ', $bundle)) . " ($view_mode)",
        ];
      }
    }

    if ($entity->hasLinkTemplate('canonical')) {
      $url = $entity->toUrl('canonical');
      $url->setOption('attributes', ['target' => '_blank']);

      return Link::fromTextAndUrl(
        "$type: $label (id: $id)",
        $url
      )->toRenderable();
    }

    return [
      '#markup' => "$type: $label (id: $id)",
    ];
  }

  /**
   *
   */
  public function render(ResultRow $values): array {
    $block = $values->_entity ?? NULL;

    if (!$block) {
      return ['#markup' => ''];
    }

    // ==========================================
    // INLINE BLOCKS (multi-usage)
    // ==========================================
    if (!$block->isReusable()) {

      $connection = \Drupal::database();

      $results = $connection->select('inline_block_usage', 'ibu')
        ->fields('ibu', ['layout_entity_type', 'layout_entity_id'])
        ->condition('block_content_id', $block->id())
        ->execute()
        ->fetchAll();

      if (empty($results)) {
        return [
          '#markup' => '<span style="color:orange;">⚠️ Orphaned</span>',
        ];
      }

      $items = [];

      foreach ($results as $record) {
        $entity_type = $record->layout_entity_type;
        $entity_id   = $record->layout_entity_id;

        try {
          $parent = $this->entityTypeManager
            ->getStorage($entity_type)
            ->load($entity_id);
        }
        catch (\Exception $e) {
          $parent = NULL;
        }

        if (!$parent) {
          continue;
        }

        $items[] = $this->formatEntityLabel($entity_type, $parent);
      }

      return [
        '#theme' => 'item_list',
        '#items' => $items,
      ];
    }

    // ==========================================
    // REUSABLE BLOCKS
    // ==========================================
    $uuid = $block->uuid();

    $node_storage = $this->entityTypeManager->getStorage('node');
    $nodes = $node_storage->loadMultiple();

    $used_in = [];

    foreach ($nodes as $node) {

      if (!$node->hasField('layout_builder__layout')) {
        continue;
      }

      $sections = $node->get('layout_builder__layout')->getValue();

      foreach ($sections as $section_data) {

        if (empty($section_data['section'])) {
          continue;
        }

        $section = $section_data['section'];

        foreach ($section->getComponents() as $component) {
          $config = $component->get('configuration');

          if (!empty($config['id']) && str_starts_with($config['id'], 'block_content:')) {
            $block_uuid = str_replace('block_content:', '', $config['id']);

            if ($block_uuid === $uuid) {
              $used_in[$node->id()] = $node;
            }
          }
        }
      }
    }

    if (empty($used_in)) {
      return [
        '#markup' => '<span style="color:gray;">Reusable (not placed)</span>',
      ];
    }

    $items = [];

    foreach ($used_in as $node) {
      $items[] = $this->formatEntityLabel('node', $node);
    }

    return [
      '#theme' => 'item_list',
      '#items' => $items,
    ];
  }

}
