<?php

namespace Drupal\easy_config\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\easy_config\EasyConfigInterface;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the Easy config entity.
 *
 * @ConfigEntityType(
 *   id = "easy_config",
 *   label = @Translation("Easy config"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\easy_config\Controller\EasyConfigListBuilder",
 *     "form" = {
 *       "delete" = "Drupal\easy_config\Form\EasyConfigDeleteForm",
 *       "add" = "Drupal\easy_config\Form\EasyConfigAddForm",
 *       "edit" = "Drupal\easy_config\Form\EasyConfigEditForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\easy_config\Routing\EasyConfigEntityHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "config_type" = "config_type",
 *     "type" = "type",
 *     "value" = "value",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/easy-config/types/{easy_config_type}/{easy_config}",
 *     "add-form" = "/admin/structure/easy-config/types/{easy_config_type}/add",
 *     "edit-form" = "/admin/structure/easy-config/types/{easy_config_type}/{easy_config}/edit",
 *     "delete-form" = "/admin/structure/easy-config/types/{easy_config_type}/{easy_config}/delete",
 *     "collection" = "/admin/structure/easy-config/types/{easy_config_type}"
 *   },
 *   config_export = {
 *     "label",
 *     "id",
 *     "config_type",
 *     "type",
 *     "value",
 *   }
 * )
 */
class EasyConfig extends ConfigEntityBase implements EasyConfigInterface {

  /**
   * The Easy config ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Easy config label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Easy config type id.
   *
   * @var string
   */
  protected $config_type;

  /**
   * The Easy config field type.
   *
   * @var string
   */
  protected $type;

  /**
   * The Easy config value.
   *
   * @var string
   */
  protected $value;

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  public function setType($type) {
    $this->type = $type;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigType() {
    return $this->config_type;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfigType($config_type) {
    $this->config_type = $config_type;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigValue() {
    return $this->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfigValue($value) {
    $type = $this->getType();
    kint($type);
    switch ($type) {
      case 'text':
        $value = $this->getValue();
      case 'long_text':
      case 'html':
      case 'json':
        kint($value);
        die;
        $value = $this->getValue();
        break;
      case 'file':
      case 'image':
        $value = $this->getValue();
        break;
    }
    $this->value = $value;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigId() {
    $dot_position = strrpos($this->getOriginalId(), '.');
    $altered_id = substr($this->getOriginalId(), ($dot_position + 1));
    $this->id = $altered_id;
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfigId($id) {
    $dot_position = strrpos($this->getOriginalId(), '.');
    $altered_id = substr($this->getOriginalId(), ($dot_position + 1));
    $this->id = $altered_id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
//    \Drupal::service('cache_tags.invalidator')->`invalidateTags($tags);
    $this->set('id', $this->getConfigType() . '.' . $this->get('id'));
    parent::preSave($storage);
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    parent::calculateDependencies();

    $this->addDependency('config', 'easy_config.type.app');

    return $this->dependencies;
  }

}
