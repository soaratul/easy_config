<?php

namespace Drupal\easy_config\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Class EasyConfigTypeForm.
 */
class EasyConfigTypeForm extends EntityForm {

  public function access(AccountInterface $account) {
    return AccessResult::allowedIf($account->hasPermission("create easy config type"));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    // Make sure the install API is available.
    include_once DRUPAL_ROOT . '/core/includes/install.inc';

    // Get a list of all available modules.
    $modules = system_rebuild_module_data();
    $uninstallable = array_filter($modules, function ($module) use ($modules) {
      return empty($modules[$module->getName()]->info['required']) && $module->status;
    });

    // Sort all modules by their name.
    uasort($uninstallable, 'system_sort_modules_by_info_name');

    $easy_config_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $easy_config_type->label(),
      '#description' => $this->t("Label for the Easy config type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $easy_config_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\easy_config\Entity\EasyConfigType::load',
      ],
      '#disabled' => !$easy_config_type->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#maxlength' => 255,
      '#default_value' => $easy_config_type->getDescription(),
      '#description' => $this->t("Description for the Easy config type."),
    ];

    $module_options = [];
    foreach ($uninstallable as $module_key => $module) {

      $name = $module->info['name'] ?: $module->getName();
      $module_options[$module->getName()] = $name;
    }

    $form['module'] = [
      '#type' => 'select',
      '#title' => $this->t('Module'),
      '#options' => $module_options,
      '#default_value' => $easy_config_type->getModule() ?: 'easy_config',
      '#description' => $this->t("Label for the Easy config type."),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $easy_config_type = $this->entity;
    $status = $easy_config_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Easy config type.', [
            '%label' => $easy_config_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Easy config type.', [
            '%label' => $easy_config_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($easy_config_type->toUrl('collection'));
  }

}
