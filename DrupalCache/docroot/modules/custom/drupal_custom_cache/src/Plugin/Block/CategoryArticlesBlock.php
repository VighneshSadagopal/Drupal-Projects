<?php

namespace Drupal\drupal_custom_cache\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\Entity\User;

/**
 * Provides a block that displays articles from the user's preferred category.
 *
 * @Block(
 *   id = "category_articles_block",
 *   admin_label = @Translation("User Favourite Category Articles")
 * )
 */
class CategoryArticlesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new block instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
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
  public function build() {
    $user = User::load($this->currentUser->id());
    
    if (!$user || !$user->hasField('field_favourite_category') || $user->get('field_favourite_category')->isEmpty()) {
      return ['#markup' => $this->t('No category selected.')];
    }

    $category_tid = $user->get('field_favourite_category')->target_id;

    // Fetch articles from the preferred category.
    $query = $this->entityTypeManager->getStorage('node')->getQuery()
      ->condition('type', 'article')
      ->condition('status', 1)
      ->condition('field_movie_category', $category_tid)
      ->accessCheck(TRUE)
      ->sort('created', 'DESC')
      ->range(0, 5);

    $nids = $query->execute();
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);

    $items = [];
    foreach ($nodes as $node) {
      $items[] = [
        '#markup' => $node->toLink()->toString(),
      ];
    }

    return [
      '#theme' => 'item_list',
      '#items' => $items,
      '#cache' => [
        'contexts' => ['user_favourite_category'],
      ],
    ];
  }
}
