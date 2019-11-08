<?php

namespace Drupal\easy_config;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface EasyConfigTypeInterface extends ConfigEntityInterface {

  /**
   * Gets the description callback string.
   *
   * @return string
   *  Callback string.
   */
  public function getDescription();

  /**
   * Sets the description of the easy config type.
   *
   * @param $description
   * @return string
   *  Easy config type description.
   */
  public function setDescription($description);

  /**
   * Gets the module callback string.
   *
   * @return string
   *  Callback string.
   */
  public function getModule();

  /**
   * Sets the module of the easy config type.
   *
   * @param $module
   * @return string
   *  Easy config type module.
   */
  public function setModule($module);
}
