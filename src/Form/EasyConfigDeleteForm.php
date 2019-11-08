<?php

namespace Drupal\easy_config\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Builds the form to delete Easy config entities.
 */
class EasyConfigDeleteForm extends EntityConfirmFormBase {

  protected $easy_config_type;

  public function __construct() {
    $this->easy_config_type = \Drupal::routeMatch()->getParameter('easy_config_type');
  }

  public function setEntity($easy_config) {
//    echo $easy_config; die;
    $entity = $this->entityTypeManager->getStorage('easy_config')->load($this->easy_config_type . '.' . $easy_config);
    parent::setEntity($entity);
    return $this;
  }

  public function access(AccountInterface $account) {
    return AccessResult::allowed();


    // If the user has permission to view config, return TRUE.
    $route_match = \Drupal::service('current_route_match');
    $type = $route_match->getParameter('type');

    return AccessResult::allowedIf($account->hasPermission("view $type easy config"));
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %name?', ['%name' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.easy_config.collection', ['easy_config_type' => $this->easy_config_type]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->delete();

    $this->messenger()->addMessage(
      $this->t('content @type: deleted @label.', [
        '@type' => $this->entity->bundle(),
        '@label' => $this->entity->label(),
      ])
    );

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
