<?php

namespace Drupal\easy_config\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Class EasyConfigAddForm.
 */
class EasyConfigAddForm extends EntityForm {

  protected $easy_config_type;

  public function __construct() {
    $this->easy_config_type = \Drupal::routeMatch()->getParameter('easy_config_type');
  }

  /**
   * {@custom_access}
   */
  public function access(AccountInterface $account) {
    return AccessResult::allowedIf($account->hasPermission("create $this->easy_config_type easy config"));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entity = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity->label(),
      '#description' => $this->t("Label for the Easy config."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\easy_config\Entity\EasyConfig::load',
      ],
      '#disabled' => !$entity->isNew(),
    ];

    $form['config_type'] = [
      '#type' => 'hidden',
      '#title' => $this->t('Easy config type'),
      '#default_value' => ($entity->getConfigType()) ? $entity->getConfigType() : $this->easy_config_type,
      '#description' => $this->t("Easy config type from which it belongs to."),
      '#required' => TRUE,
    ];

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#options' => [
        'file' => $this->t('File'),
        'html' => $this->t('Html'),
        'image' => $this->t('Image'),
        'json' => $this->t('Json'),
        'long_text' => $this->t('Long text'),
        'text' => $this->t('Text'),
      ],
      '#empty_option' => $this->t('- Select type -'),
      '#default_value' => $entity->get('type'),
      '#description' => $this->t("Select configuration type."),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateValue',
        'wrapper' => 'value-wrapper',
      ]
    ];

    $form['value_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'value-wrapper'],
    ];

    if ($entity->get('type')) {
      $type = $entity->get('type');
    }
    else {
      $type = $form_state->getValue('type');
    }

    switch ($type) {
      case 'file':
        $form['value_wrapper']['value'] = [
          '#type' => 'managed_file',
          '#title' => $this->t('Value'),
          '#default_value' => [$entity->get('value')],
          '#description' => $this->t("Upload file, allowed extensions are pdf, .doc(x), xls(x), and csv."),
          '#required' => TRUE,
          '#upload_location' => 'public://easy-config/images/',
          '#upload_validators' => [
            'file_validate_extensions' => ['pdf doc docx xls xlsx csv'],
          ],
        ];
        break;
      case 'html':
        $form['value_wrapper']['value'] = [
          '#type' => 'text_format',
          '#title' => $this->t('Value'),
          '#format' => 'full_html',
          '#default_value' => $entity->get('value'),
          '#description' => $this->t("Enter config value, you can use existing configuration key as token like @some_config."),
          '#required' => TRUE,
        ];
        break;
      case 'image':
        $form['value_wrapper']['value'] = [
          '#type' => 'managed_file',
          '#title' => $this->t('Value'),
          '#default_value' => [$entity->get('value')],
          '#description' => $this->t("Upload image, allowed extensions are png, gif, jpg, and jpeg."),
          '#required' => TRUE,
          '#upload_location' => 'public://easy-config/images/',
          '#upload_validators' => [
            'file_validate_extensions' => ['png gif jpg jpeg'],
          ],
        ];
        break;
      case 'json':
        $form['value_wrapper']['value'] = [
          '#type' => 'textarea',
          '#title' => $this->t('Value'),
          '#rows' => 15,
          '#default_value' => $entity->get('value'),
          '#description' => $this->t("Enter config value."),
          '#required' => TRUE,
        ];
        break;
      case 'long_text':
        $form['value_wrapper']['value'] = [
          '#type' => 'textarea',
          '#title' => $this->t('Value'),
          '#rows' => 5,
          '#default_value' => $entity->get('value'),
          '#description' => $this->t("Enter config value, you can use existing configuration key as token like @some_config."),
          '#required' => TRUE,
        ];
        break;
      case 'text':
        $form['value_wrapper']['value'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Value'),
          '#maxlength' => 255,
          '#default_value' => $entity->get('value'),
          '#description' => $this->t("Enter config value, you can use existing configuration key as token like @some_config."),
          '#required' => TRUE,
        ];
        break;
      default :
        $form['value_wrapper']['value'] = [
          '#type' => 'item',
          '#title' => t('Value'),
          '#markup' => t('Please select type for corresponding value.'),
        ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if ($this->entity->isNew()) {
      $id = $form_state->getValue('id');
      $config_type = $form_state->getValue('config_type');
      $key = $config_type . '.' . $id;

      $config_exists = $this->entityTypeManager->getStorage('easy_config')->load($key);

      // Let the plugins validate their own config data.
      if ($config_exists) {
        $form_state->setErrorByName('id', "'easy_config' entity with ID '" . $id . "' already exists.");
      }
    }
  }

  /**
   * Ajax callback for the value.
   */
  public function updateValue(array $form, FormStateInterface $form_state) {
    return $form['value_wrapper'];
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $easy_config = $this->entity;

    // get field type
    $type = $easy_config->get('type');
    if (in_array($type, ['file', 'image'])) {
      $easy_config->set('value', $easy_config->get('value')[0]);
    }
    if (in_array($type, ['html'])) {
      $easy_config->set('value', $easy_config->get('value')['value']);
    }

    $status = $easy_config->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Easy config.', [
            '%label' => $easy_config->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Easy config.', [
            '%label' => $easy_config->label(),
        ]));
    }
    $form_state->setRedirect('entity.easy_config.collection', ['easy_config_type' => $form_state->getValue('config_type')]);
  }

}
