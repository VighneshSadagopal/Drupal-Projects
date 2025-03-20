<?php

namespace Drupal\drupal_custom_cache\Cache\Context;

use Drupal\Core\Cache\Context\CacheContextInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\Entity\User;

/**
 * Defines a cache context for the user's preferred category.
 */
class CategoryContext implements CacheContextInterface
{

	/**
	 * The current user.
	 *
	 * @var \Drupal\Core\Session\AccountProxyInterface
	 */
	protected $currentUser;

	/**
	 * The entity type manager.
	 *
	 * @var \Drupal\Core\Entity\EntityTypeManagerInterface
	 */
	protected $entityTypeManager;

	/**
	 * Constructs the cache context.
	 */
	public function __construct(AccountProxyInterface $current_user, EntityTypeManagerInterface $entity_type_manager)
	{
		$this->currentUser = $current_user;
		$this->entityTypeManager = $entity_type_manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function create(ContainerInterface $container)
	{
		return new static(
			$container->get('current_user'),
			$container->get('entity_type.manager')
		);
	}

	
  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata() {
    return new \Drupal\Core\Cache\CacheableMetadata();
  }


	/**
	 * {@inheritdoc}
	 */
	public function getContext()
	{
		$user = User::load($this->currentUser->id());

			if ($user && $user->hasField('field_favourite_category') && !$user->get('field_favourite_category')->isEmpty()) {
				return 'user_favourite_category:' . $user->get('field_favourite_category')->target_id;
			}
		return 'user_favourite_category:none';
	}

	/**
	 * {@inheritdoc}
	 */
	public static function getLabel()
	{
		return t("User's Favourite category");
	}
}
