<?php

namespace Drupal\easy_config\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds the form to delete Easy config type entities.
 */
class EasyConfigTypeDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %name?', ['%name' => $this->entity->label()]);
  }

  /**
   * Loads entity IDs using a pager sorted by the entity id.
   *
   * @return array
   *   An array of entity IDs.
   */
  protected function getEntityIds() {
    $query = $this->entityTypeManager
      ->getStorage('easy_config')
      ->getQuery()
      ->condition('id', $this->entity->id() . '.', 'STARTS_WITH')
      ->sort('id');

    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForms(array $form, FormStateInterface $form_state) {
    $items = [];
    foreach ($this->load() as $entity) {
      $items[] = $this->t($entity->label());
    }

    if (!empty($items)) {
      $caption = '<p>' . $this->formatPlural(count($items), 'Following config is used on your site.', 'Following configs is used on your site.') . '</p>';
      $form['alert'] = ['#markup' => $caption];

      $form['items'] = [
        '#theme' => 'item_list',
        '#items' => $items,
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    $entity_ids = $this->getEntityIds();
    return $this->entityTypeManager->getStorage('easy_config')->loadMultiple($entity_ids);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.easy_config_type.collection');
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

//    foreach ($this->load() as $entity) {
//      $entity->delete();
//    }

    $this->messenger()->addMessage(
      $this->t('content @type: deleted @label.', [
        '@type' => $this->entity->bundle(),
        '@label' => $this->entity->label(),
      ])
    );

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
