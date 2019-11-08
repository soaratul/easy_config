<?php

namespace Drupal\easy_config;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\easy_config\EasyConfigTrait;

/**
 * Class EasyConfigService.
 */
class EasyConfigService {

  use EasyConfigTrait;

  /**
   * Drupal\Core\Entity\EntityManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Config\ConfigManagerInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new EasyConfigService object.
   */
  public function __construct(EntityManagerInterface $entity_manager, EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory) {
    $this->entityManager = $entity_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory;
  }

  public function getConfig($type = '', $config = '') {
    $config_name = "easy_config.config.$type.$config";
    $entity = $this->configFactory->get($config_name);
    return $entity->get('value');
  }

  public function getMultipleConfigs($type = '', $configs = []) {
    $result = $config_names = [];

    foreach ($configs as $config) {
      $config_names[] = "easy_config.config.$type.$config";
    }

    $entities = $this->configFactory->loadMultiple($config_names);

    return $this->prepareResult($entities);
  }

  private function prepareResult($entities) {
    $result = [];

    foreach ($entities as $entity) {
      $id = $this->getConfigId($entity->get('id'));
      $result[$id] = $entity->get('value');
    }

    return $result;
  }

  public function getConfigsByType($type = '') {
    $config_names = $this->configFactory->listAll("easy_config.config.$type");
    $entities = $this->configFactory->loadMultiple($config_names);
    return $this->prepareResult($entities);
  }

  public function getTwigConfig($name = '') {
    $config_name = "easy_config.config.$name";
    $entity = $this->configFactory->get($config_name);
    return $entity->get('value');
  }

}
