<?php

namespace Drupal\easy_config\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\easy_config\EasyConfigTypeInterface;

/**
 * Defines the Easy config type entity.
 *
 * @ConfigEntityType(
 *   id = "easy_config_type",
 *   label = @Translation("Easy config type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\easy_config\Controller\EasyConfigTypeListBuilder",
 *     "form" = {
 *       "default" = "Drupal\easy_config\Form\EasyConfigTypeForm",
 *       "delete" = "Drupal\easy_config\Form\EasyConfigTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\easy_config\Routing\EasyConfigTypeEntityHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "type",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/easy-config/types/{easy_config_type}",
 *     "add-form" = "/admin/structure/easy-config/types/add",
 *     "edit-form" = "/admin/structure/easy-config/types/{easy_config_type}/edit",
 *     "delete-form" = "/admin/structure/easy-config/types/{easy_config_type}/delete",
 *     "collection" = "/admin/structure/easy-config/types"
 *   },
 *   config_export = {
 *     "label",
 *     "id",
 *     "description",
 *     "module",
 *   }
 * )
 */
class EasyConfigType extends ConfigEntityBase implements EasyConfigTypeInterface {

  /**
   * The Easy config type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Easy config type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Easy config type module.
   * @var string
   */
  protected $module;

  /**
   * The Easy config type description.
   * @var string
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->set('description', $description);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getModule() {
    return $this->module;
  }

  /**
   * {@inheritdoc}
   */
  public function setModule($module) {
    $this->set('module', $module);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    parent::calculateDependencies();
    $this->addDependency('module', $this->getModule());

    return $this->dependencies;
  }

}
