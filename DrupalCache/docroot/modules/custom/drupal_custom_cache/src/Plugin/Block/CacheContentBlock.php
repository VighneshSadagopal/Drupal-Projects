<?php

namespace Drupal\drupal_custom_cache\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 *
 * @Block(
 *   id = "cache_content_block",
 *   admin_label = @Translation("Cache Content Block")
 * )
 */
class CacheContentBlock extends BlockBase implements ContainerFactoryPluginInterface
{

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   *
   * @param array $configuration
   *   A configuration array containing plugin instance configuration.
   * @param string $plugin_id
   *   The plugin_id for the block.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *  The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager,AccountProxyInterface $current_user)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $node_storage = $this->entityTypeManager->getStorage('node');

    $request = $node_storage->getQuery()
      ->condition('type', 'article')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->accessCheck(TRUE)
      ->range(0, 3);

    $nids = $request->execute();
    $nodes = $node_storage->loadMultiple($nids);
    $cache_tags = [];
    $mail = $this->currentUser->getEmail() ?? 'Anonmoyous';


    // Add email of current user.
    $output = '<p>' . $mail . '</p>';

    $output .= '<ul>';


    foreach ($nodes as $node) {
      $title = $node->getTitle();
      $cache_tags[] = 'node:' . $node->id();

      $output .= '<li>' . $title . '</li>';
    }
    $output .= '</ul>';
    return [
      '#markup' => $output,
      '#cache' => [
        'tags' => $cache_tags,
        'contexts' => ['user'],
      ],
    ];
  }
}