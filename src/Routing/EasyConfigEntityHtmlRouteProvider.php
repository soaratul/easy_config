<?php

namespace Drupal\easy_config\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for easy_config entity entities.
 *
 * @see Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class EasyConfigEntityHtmlRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);
    $entity_type_id = $entity_type->id();

    if ($collection_route = $this->getCollectionRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.collection", $collection_route);
    }
    if ($add_form_route = $this->getAddFormRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.add_form", $add_form_route);
    }
    if ($edit_form_route = $this->getEditFormRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.edit_form", $edit_form_route);
    }
    if ($delete_form_route = $this->getDeleteFormRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.delete_form", $delete_form_route);
    }
    // Provide your custom entity routes here.
    return $collection;
  }

  /**
   * Gets the collection route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getAddFormRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('add-form') && $entity_type->hasListBuilderClass()) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('add-form'));
      $route
        ->setDefaults([
          '_entity_form' => "{$entity_type_id}.add",
          '_title' => 'Add Easy Config'
        ])
        ->setRequirement('_custom_access', '\Drupal\easy_config\Form\EasyConfigAddForm::access')
        ->setOption('parameters', [
          'easy_config_type' => [
            'type' => 'config_entity:easy_config_type',
          ],
        ])
        ->setOption('_admin_route', TRUE);
      return $route;
    }
  }

  /**
   * Gets the collection route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getEditFormRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('edit-form') && $entity_type->hasListBuilderClass()) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('edit-form'));
      $route
        ->setDefaults([
          '_entity_form' => "{$entity_type_id}.edit",
          '_title' => 'Edit Easy Config'
        ])
        ->setRequirement('_custom_access', '\Drupal\easy_config\Form\EasyConfigEditForm::access')
        ->setOption('parameters', [
          'easy_config_type' => [
            'type' => 'config_entity:easy_config_type',
          ],
          'easy_config' => [
            'type' => 'config_entity:easy_config',
          ],
        ])
        ->setOption('_admin_route', TRUE);
      return $route;
    }
  }

  /**
   * Gets the collection route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getDeleteFormRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('delete-form') && $entity_type->hasListBuilderClass()) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('delete-form'));
      $route
        ->setDefaults([
          '_entity_form' => "{$entity_type_id}.delete",
          '_title' => 'Delete Easy Config'
        ])
        ->setRequirement('_custom_access', '\Drupal\easy_config\Form\EasyConfigDeleteForm::access')
        ->setOption('parameters', [
          'easy_config_type' => [
            'type' => 'config_entity:easy_config_type',
          ],
          'easy_config' => [
            'type' => 'config_entity:easy_config',
          ],
        ])
        ->setOption('_admin_route', TRUE);
      return $route;
    }
  }

  /**
   * Gets the collection route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getCollectionRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('collection') && $entity_type->hasListBuilderClass()) {
      $route = new Route($entity_type->getLinkTemplate('collection'));
      $route
        ->setDefaults([
          '_controller' => '\Drupal\easy_config\Controller\EasyConfigListBuilder::render',
          '_title_callback' => '\Drupal\easy_config\Controller\EasyConfigListBuilder::getTitle'
        ])
        ->setRequirement('_custom_access', '\Drupal\easy_config\Controller\EasyConfigListBuilder::access')
        ->setOption('parameters', [
          'easy_config_type' => [
            'type' => 'config_entity:easy_config_type',
          ],
        ])
        ->setOption('_admin_route', TRUE);

      return $route;
    }
  }

}
