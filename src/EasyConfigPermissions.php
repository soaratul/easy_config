<?php

namespace Drupal\easy_config;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides dynamic permissions for easy config of different types.
 */
class EasyConfigPermissions {

  use StringTranslationTrait;

  /**
   * Returns an array of node type permissions.
   *
   * @return array
   *   The easy config type permissions.
   *   @see \Drupal\user\PermissionHandlerInterface::getPermissions()
   */
  public function getPermissions() {
    $perms = [];
    // Generate node permissions for all node types.
    foreach ($this->load() as $type) {
      $perms += $this->buildPermissions($type);
    }

    return $perms;
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    $entity_ids = $this->getEntityIds();
    $entity_type_manager = \Drupal::service('entity_type.manager');
    return $entity_type_manager->getStorage('easy_config_type')->loadMultiple($entity_ids);
  }

  /**
   * Loads entity IDs using a pager sorted by the entity id.
   *
   * @return array
   *   An array of entity IDs.
   */
  protected function getEntityIds() {
    $entity_type_manager = \Drupal::service('entity_type.manager');
    $query = $entity_type_manager
      ->getStorage('easy_config_type')
      ->getQuery()
      ->sort('id');

    return $query->execute();
  }

  /**
   * Returns a list of node permissions for a given node type.
   *
   * @param \Drupal\node\Entity\NodeType $type
   *   The node type.
   *
   * @return array
   *   An associative array of permission names and descriptions.
   */
  protected function buildPermissions($type) {
    $type_id = $type->id();
    $type_params = ['%type_name' => $type->label()];

    return [
      "access easy config $type_id overview" => [
        'title' => $this->t('%type_name: Access the Easy Config overview page', $type_params),
      ],
      "create $type_id easy config" => [
        'title' => $this->t('%type_name: Create new easy config', $type_params),
      ],
      "edit $type_id easy config" => [
        'title' => $this->t('%type_name: Edit easy config', $type_params),
      ],
      "delete $type_id easy config" => [
        'title' => $this->t('%type_name: Delete easy config ', $type_params),
      ],
    ];
  }

}
