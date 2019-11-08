<?php

namespace Drupal\easy_config\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Url;

/**
 * Provides a listing of easy config type entity entities.
 */
class EasyConfigTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new EntityListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, AccountInterface $acount) {
    $this->entityTypeId = $entity_type->id();
    $this->storage = $storage;
    $this->entityType = $entity_type;
    $this->currentUser = $acount;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type, $container->get('entity_type.manager')->getStorage($entity_type->id()), $container->get('current_user')
    );
  }

  /**
   * Loads entity IDs using a pager sorted by the entity id.
   *
   * @return array
   *   An array of entity IDs.
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery()
      ->sort($this->entityType->getKey('id'));

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }
    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Type');
    $header['id'] = $this->t('ID');
    $header['description'] = $this->t('Description');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    if ($this->currentUser->hasPermission('access easy config ' . $entity->id() . ' overview')) {
      $row['label'] = $entity->label();
      $row['id'] = $entity->id();
      $row['description'] = $entity->getDescription();
      return $row + parent::buildRow($entity);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    $operations['list_configs'] = [
      'title' => t('List configs'),
      'url' => Url::fromRoute('entity.easy_config.collection', ['easy_config_type' => $entity->id()]),
      'weight' => -1
    ];

    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();

    if ($this->currentUser->hasPermission('create easy config type')) {
      $build['table']['#empty'] = $this->t('No Easy config available. <a href=":link">Add Easy Config type</a>.', [':link' => Url::fromRoute('entity.easy_config_type.add_form')->toString()]);
    }
    else {
      $build['table']['#empty'] = $this->t('No Easy config available.');
    }

    return $build;
  }

}
