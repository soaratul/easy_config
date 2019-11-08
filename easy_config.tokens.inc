<?php

/**
 * @file
 * Builds placeholder replacement tokens for node-related data.
 */
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\easy_config\EasyConfigTypeInterface;

/**
 * Implements hook_token_info().
 */
function easy_config_token_info() {
  $type = [
    'name' => t('Easy config'),
    'description' => t('Tokens related to easy config.'),
  ];

  // Core tokens for nodes.
  $easy_config['config'] = [
    'name' => t("Config"),
    'description' => t('The Easy config token e.g. type.config where type is config type and config is config.'),
    'dynamic' => TRUE,
  ];

  return [
    'types' => ['easy-config' => $type],
    'tokens' => ['easy-config' => $easy_config],
  ];
}

/**
 * Implements hook_tokens().
 */
function easy_config_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];

  if ($type == 'easy-config') {

    foreach ($tokens as $name => $original) {
      $config_array = explode(':', $name);
      $config_id = end($config_array);
      $entity_type_manager = \Drupal::service('entity_type.manager');

      $config = $entity_type_manager->getStorage('easy_config')->load($config_id);
      $value = \Drupal::token()->replace($config->getConfigValue());

      $replacements[$original] = \Drupal\Core\Render\Markup::create($value);
    }
  }

  return $replacements;
}
