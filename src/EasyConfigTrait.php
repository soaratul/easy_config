<?php

namespace Drupal\easy_config;

/**
 * Easy config helper trait.
 *
 */
trait EasyConfigTrait {

  /**
   * returns the the config id.
   *
   * @id: string
   * 
   */
  public function getConfigId($id) {
    if (empty($id)) {
      return '';
    }
    $dot_position = strrpos($id, '.');
    return substr($id, ($dot_position + 1));
  }

}
