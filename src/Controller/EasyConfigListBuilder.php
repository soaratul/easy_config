<?php

namespace Drupal\easy_config\Controller;

use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Routing\RedirectDestinationTrait;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\easy_config\EasyConfigTrait;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Defines a generic implementation to build a listing of entities.
 *
 * @ingroup entity_api
 */
class EasyConfigListBuilder extends ControllerBase {

  use EasyConfigTrait,
      MessengerTrait,
      RedirectDestinationTrait;

  /**
   * The entity storage class.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The entity type ID.
   *
   * @var string
   */
  protected $entityTypeId;

  /**
   * Information about the entity type.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $entityType;

  /**
   * The number of entities to list per page, or FALSE to list all entities.
   *
   * For example, set this to FALSE if the list uses client-side filters that
   * require all entities to be listed (like the views overview).
   *
   * @var int|false
   */
  protected $limit = 50;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;
  protected $type;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'), $container->get('current_user')
    );
  }

  /**
   * Constructs a new EntityListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current user.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, AccountInterface $acount) {
    $this->entityTypeManager = $entity_type_manager;
    $this->storage = $this->entityTypeManager->getStorage('easy_config');
    $this->currentUser = $acount;
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The currently logged in account.
   *
   * @return string
   *   A \Drupal\Core\Access\AccessInterface constant value.
   */
  public function access(AccountInterface $account) {
    return AccessResult::allowed();
    // If the user has permission to view config, return TRUE.
    $route_match = \Drupal::service('current_route_match');
    $type = $route_match->getParameter('type');

    return AccessResult::allowedIf($account->hasPermission("view $type easy config"));
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    $entity_ids = $this->getEntityIds();
    return $this->storage->loadMultiple($entity_ids);
  }

  /**
   * Loads entity IDs using a pager sorted by the entity id.
   *
   * @return array
   *   An array of entity IDs.
   */
  protected function getEntityIds() {
    $query = $this->storage
      ->getQuery()
      ->condition('id', $this->type . '.', 'STARTS_WITH')
      ->sort('id');

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }
    return $query->execute();
  }

  public function getTitle($easy_config_type) {
    $config_type = $this->entityTypeManager->getStorage('easy_config_type')->load($easy_config_type);

    $config_type_name = $config_type->label();
    $site_name = \Drupal::configFactory()->get('system.site')->get('name');
    return $config_type_name . $this->t(' conifg list | ') . $site_name;
  }

  /**
   * Ensures that a destination is present on the given URL.
   *
   * @param \Drupal\Core\Url $url
   *   The URL object to which the destination should be added.
   *
   * @return \Drupal\Core\Url
   *   The updated URL object.
   */
  protected function ensureDestination(Url $url) {
    return $url->mergeOptions(['query' => $this->getRedirectDestination()->getAsArray()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    $operations = $this->getDefaultOperations($entity);
    $operations += $this->moduleHandler()->invokeAll('entity_operation', [$entity]);
    $this->moduleHandler->alter('entity_operation', $operations, $entity);
    uasort($operations, '\Drupal\Component\Utility\SortArray::sortByWeightElement');

    return $operations;
  }

  /**
   * Gets this list's default operations.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity the operations are for.
   *
   * @return array
   *   The array structure is identical to the return value of
   *   self::getOperations().
   */
  protected function getDefaultOperations(EntityInterface $entity) {
    $operations = [];

    if ($entity->access('update') && $entity->hasLinkTemplate('edit-form')) {
      $operations['edit'] = [
        'title' => t('Edit'),
        'url' => $this->ensureDestination($entity->toUrl('edit-form'))->setRouteParameter('easy_config_type', $entity->getConfigType())->setRouteParameter('easy_config', $entity->getConfigId()),
      ];
    }

    if ($entity->access('delete') && $entity->hasLinkTemplate('delete-form')) {
      $operations['delete'] = [
        'title' => t('Delete'),
        'url' => $this->ensureDestination($entity->toUrl('delete-form'))->setRouteParameter('easy_config_type', $entity->getConfigType(), 'easy_config', $entity->getConfigId()),
      ];
    }

    return $operations;
  }

  /**
   * Builds the header row for the entity listing.
   *
   * @return array
   *   A render array structure of header strings.
   *
   * @see \Drupal\Core\Entity\EntityListBuilder::render()
   */
  public function buildHeader() {
    return [
      $this->t('Name'),
      $this->t('ID'),
      $this->t('confit type'),
      $this->t('field_type'),
      $this->t('Operations'),
    ];
  }

  /**
   * Builds a row for an entity in the entity listing.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity for this row of the list.
   *
   * @return array
   *   A render array structure of fields for this entity.
   *
   * @see \Drupal\Core\Entity\EntityListBuilder::render()
   */
  public function buildRow(EntityInterface $entity) {
    return [
      $entity->label(),
      $this->getConfigId($entity->id()),
      $entity->get('config_type'),
      $entity->get('type'),
      $this->buildOperations($entity),
    ];
  }

  /**
   * Builds a renderable list of operation links for the entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity on which the linked operations will be performed.
   *
   * @return array
   *   A renderable array of operation links.
   *
   * @see \Drupal\Core\Entity\EntityListBuilder::buildRow()
   */
  public function buildOperations(EntityInterface $entity) {
    $build['data'] = [
      '#type' => 'operations',
      '#links' => $this->getOperations($entity),
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Builds the entity listing as renderable array for table.html.twig.
   *
   * @todo Add a link to add a new item to the #empty text.
   */
  public function render($easy_config_type) {
    $this->type = $easy_config_type;

    $build['table'] = [
      '#type' => 'table',
      '#header' => $this->buildHeader(),
      '#rows' => [],
      '#empty' => $this->t('No Easy config available.'),
    ];

    if ($this->currentUser->hasPermission("create $this->type easy config")) {
      $build['table']['#empty'] = $this->t('No Easy config available. <a href=":link">Add Easy config</a>.', [
        ':link' => Url::fromRoute('entity.easy_config.add_form', ['easy_config_type' => $this->type])->toString()
      ]);
    }
    else {
      $build['table']['#empty'] = t('No Easy config available.');
    }

    foreach ($this->load() as $entity) {
      if ($row = $this->buildRow($entity)) {
        $build['table']['#rows'][$entity->id()] = $row;
      }
    }

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $build['pager'] = [
        '#type' => 'pager',
      ];
    }
    return $build;
  }

}
