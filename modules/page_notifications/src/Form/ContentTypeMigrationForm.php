<?php

namespace Drupal\page_notifications\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\node\Entity\Node;

/**
 * @see \Drupal\Core\Form\FormBase
 */
class ContentTypeMigrationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_notifications_content_type_migration_form';
  }

  /**
   *
   * @var \Drupal\node\NodeStorage
   */
  protected $nodeStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    if ($form_state->has('page_num') && $form_state->get('page_num') == 2) {
      return self::pageNotificationsPageTwo($form, $form_state);
    }

    $form_state->set('page_num', 1);
    $form['intro'] = [
      '#markup' => $this->t("<h2 id='page-notifications-config-page-header'>Step 1 - Content Type selection.</h2>
      <h3>Instructions:</h3>
      <ol>
       <li>Create new content type</li>
       <li>Add custom Text (plain) fields to new content type</li>
       <li>After that come back here and enter content type machine name in fields below</li>
      </ol>
      <p>You can migrate subscriptions from one content type to another but the module will work just with page_notify_subscriptions content type.</p>
      "),
    ];
    $form['content_type_export'] = [
      '#type' => 'textfield',
      '#title' => $this->t('From Content Type'),
      '#description' => $this->t('Content Type machine name from where subscriptions needs to be moved'),
      '#default_value' => $form_state->getValue('content_type_export', ''),
      '#required' => TRUE,
      '#maxlength' => 1024,
    ];
    $form['content_type_import'] = [
      '#type' => 'textfield',
      '#title' => $this->t('To Content Type'),
      '#description' => $this->t('Content Type machine name to where subscriptions needs to be moved'),
      '#default_value' => $form_state->getValue('content_type_import', ''),
      '#required' => TRUE,
      '#maxlength' => 1024,
    ];
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['next'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Next'),
      '#submit' => ['::pageContentSubscriptionsMigrationForm'],
      '#validate' => ['::pageNotificationsMultistepFormNextValidate'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $page_values = $form_state->get('page_values');
    $count = 0;
    $clean_values = $form_state->cleanValues()->getValues();
    $result = \Drupal::entityQuery("node")
      ->condition("type", $page_values["content_type_export"])
      ->accessCheck(FALSE)
      ->execute();
    $storage_handler = \Drupal::entityTypeManager()->getStorage("node");
    $entities = $storage_handler->loadMultiple($result);

    foreach ($entities as $entity_key => $entity) {
      $new_values = array();
      foreach ($clean_values as $key => $clean_value) {
        if ($clean_value !== 'none'){
          $new_field = array();
          $node_filed_value = $entity->get($clean_value)->getValue();
          if($node_filed_value[0]["value"] && $node_filed_value !== "" || $node_filed_value !== null || !is_null($node_filed_value)){
            $new_field[$key] = $node_filed_value[0]["value"];
          } else {
            $new_field[$key] = $node_filed_value;
          }
          $new_values[$key] = $new_field[$key];
        }
      }

      $new_node = Node::create(['type' => $page_values["content_type_import"]]);
      $new_node->set('title', $entity->getTitle());
      foreach ($new_values as $key => $new_value) {
        $new_node->set($key, $new_value);
      }
      $new_node->enforceIsNew();
      $new_node->save();
      $count++;
    }

    $this->messenger()->addMessage($this->t('Migrated total of: @count', ['@count' => $count]));
  }

  /**
   * Provides custom validation handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure of the form.a
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function pageNotificationsMultistepFormNextValidate(array &$form, FormStateInterface $form_state) {
    if (is_null($form_state->getValue('content_type_export'))) {
      $form_state->setErrorByName('content_type_export', $this->t('Please enter a valid value'));
    } elseif (is_null($form_state->getValue('content_type_import'))) {
      $form_state->setErrorByName('content_type_import', $this->t('Please enter a valid value'));
    }
  }

  /**
   * Provides custom submission handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function pageContentSubscriptionsMigrationForm(array &$form, FormStateInterface $form_state) {
    $form_values = $form_state->getValue(array());

    $form_state
      ->set('page_values', [
        'content_type_export' => $form_values['content_type_export'],
        'content_type_import' => $form_values['content_type_import'],
      ])
      ->set('page_num', 2)
      ->setRebuild(TRUE);
  }

  /**
   * Builds the second step form (page 2).
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function pageNotificationsPageTwo(array &$form, FormStateInterface $form_state) {
    $vals = $form_state->getStorage();
    $content_type_export = $vals['page_values']['content_type_export'];
    $content_type_import = $vals['page_values']['content_type_import'];

    $content_type_export_fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $content_type_export);
    $content_type_import_fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $content_type_import);

    $export_count = count($content_type_export_fields);
    $import_count = count($content_type_import_fields);

    $result = \Drupal::entityQuery("node")
      ->condition("type", $content_type_export)
      ->accessCheck(FALSE)
      ->execute();
    $storage_handler = \Drupal::entityTypeManager()->getStorage("node");
    $entities = $storage_handler->loadMultiple($result);
    $entities_count = count($entities);


    if($import_count == 0){
      $form['intro'] = [
        '#markup' => $this->t("<h2 id='page-notifications-config-page-header'>Step 2 - Review. No Content type fields found.</h2>"),
      ];
    } else {
      $form['intro'] = [
        '#markup' => $this->t("<h2 id='page-notifications-config-page-header'>Step 2 - Review. There are ".$entities_count." node(s) to transfer.</h2>
        <p>Below is the list of content type fileds. Please map each field to corresponding page notifications content type field:</p>
        "),
      ];

      $options = array('none' => 'None');
      foreach($content_type_export_fields as $content_type_export_field) {
        if (str_starts_with($content_type_export_field->getName(), 'field_')) {
          $option = $content_type_export_field->getLabel() . ' (' . $content_type_export_field->getName() . ')';
          $options[$content_type_export_field->getName()] = $content_type_export_field->getLabel();
        }
      }

      $i=0;
      foreach($content_type_import_fields as $content_type_import_field) {
        $field_name = $content_type_import_field->getName();
        $field_type = $content_type_import_field->getType();
        $field_label = $content_type_import_field->getLabel();
        if (str_starts_with($field_name, 'field_')) {
          $form['fields-list'][$i][$field_name]= [
            '#type' => 'select',
            '#title' => $this->t($field_label),
            '#description' => $this->t('Machine Name: ' . $field_name),
            '#options' => $options,
          ];
          $i++;
        }
      }

      $build['pager'] = array(
        '#markup' => 'pager',
      );
      $form['back'] = [
        '#type' => 'submit',
        '#value' => $this->t('Back'),
        '#submit' => ['::pageNotificationsPageTwoBack'],
        '#limit_validation_errors' => [],
      ];
      $form['submit'] = [
        '#type' => 'submit',
        '#button_type' => 'primary',
        '#value' => $this->t('Submit'),
      ];
    }
    return $form;
  }

  /**
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function pageNotificationsPageTwoBack(array &$form, FormStateInterface $form_state) {
    $form_state
      ->setValues($form_state->get('page_values'))
      ->set('page_num', 1)
      ->setRebuild(TRUE);
  }



}
