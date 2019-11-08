<?php

namespace Drupal\easy_config;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface EasyConfigInterface extends ConfigEntityInterface {

  /**
   * Gets the Easy Config type string.
   *
   * @return string
   *  Callback string.
   */
  public function getType();

  /**
   * Sets the config type of the easy config.
   *
   * @param $type
   * @return string
   *  Easy Config type.
   */
  public function setType($type);

  /**
   * Gets the Easy Config type string.
   *
   * @return string
   *  Callback string.
   */
  public function getConfigType();

  /**
   * Sets the config type of the easy config.
   *
   * @param $config_type
   * @return string
   *  Easy Config type.
   */
  public function setConfigType($config_type);

  /**
   * Gets the Easy Config value string.
   *
   * @return string
   *  Callback string.
   */
  public function getConfigValue();

  /**
   * Sets the config value of the easy config.
   *
   * @param $value
   * @return string
   *  Easy Config value.
   */
  public function setConfigValue($value);

  /**
   * Gets the Easy Config id string.
   *
   * @return string
   *  Callback string.
   */
  public function getConfigId();

  /**
   * Sets the id of the easy config.
   *
   * @param $id
   * @return string
   *  Easy Config id.
   */
  public function setConfigId($id);
}
