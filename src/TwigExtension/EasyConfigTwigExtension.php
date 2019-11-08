<?php

namespace Drupal\easy_config\TwigExtension;

use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Class EasyConfigTwigExtension.
 */
class EasyConfigTwigExtension extends \Twig_Extension {

  /**
   * Drupal\Core\Config\ConfigManagerInterface definition.
   *
   * @var ConfigManagerInterface
   */
  protected $configManager;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Entity\EntityManagerInterface definition.
   *
   * @var EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new EasyConfigTwigExtension object.
   */
  public function __construct(RendererInterface $renderer, ConfigManagerInterface $config_manager, EntityTypeManagerInterface $entity_type_manager, EntityManagerInterface $entity_manager) {
    $this->configManager = $config_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('easy_config', [$this, 'easy_config']),
    ];
  }

  /**
   * Returns $_GET query parameter
   *
   * @param string $name
   *   name of the query parameter
   *
   * @return string
   *   value of the query parameter name
   */
  public function easy_config($name) {
    return \Drupal::service('easy_config.service')->getTwigConfig($name);
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'easy_config';
  }

}
