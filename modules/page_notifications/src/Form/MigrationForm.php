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
class MigrationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_notifications_migration_form';
  }

  /**
   * The node storage.
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
      return self::fapiExamplePageTwo($form, $form_state);
    }

    $form_state->set('page_num', 1);
    $form['general_settings_header'] = [
      '#markup' => $this->t("<h2 id='page-notifications-config-page-header'>Step 1 - Node selection.</h2>
      <p>This migration allows to move subscribers from one node to another.</p>
      "),
      '#weight' => -1,
    ];
    $form['subscription_export'] = [
      '#type' => 'textfield',
      '#title' => $this->t('From Node'),
      '#description' => $this->t('Node title from where subscriptions needs to be moved'),
      '#default_value' => $form_state->getValue('subscription_export', ''),
      '#required' => TRUE,
      '#autocomplete_route_name' => 'page_notifications.autocomplete.subscriptions',
      '#maxlength' => 1024,
    ];
    $form['subscription_import'] = [
      '#type' => 'textfield',
      '#title' => $this->t('To Node'),
      '#description' => $this->t('Node title to where subscriptions needs to be moved'),
      '#default_value' => $form_state->getValue('subscription_import', ''),
      '#required' => TRUE,
      '#autocomplete_route_name' => 'page_notifications.autocomplete.subscriptions',
      '#maxlength' => 1024,
    ];
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['next'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Next'),
      '#submit' => ['::pageNotificationsSubscriptionsMigrationForm'],
      //'#validate' => ['::fapiExampleMultistepFormNextValidate'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $page_values = $form_state->get('page_values');
    $node_subscriptions_list = $form_state->getValue('page-notifications-list');
    $count = 0;
    foreach ($node_subscriptions_list as $key => $value) {
      if ($value['field_page_notify_node_id'] != 0) {
        $node = \Drupal\node\Entity\Node::load($value['field_page_notify_node_id']);
        $new_title = 'Subscription to - ' . $page_values['subscription_import'] . ' - ' . $node->field_page_notify_token->getString();
        $node->set('title', $new_title);
        $node->set('field_page_notify_node_id',  $page_values['subscription_import']);
        $node->save();
        $count++;
      }
    }
    $morethen = ($count > 2) ? '1 Subscription' : ' '.$count.' Subscriptions';
    $this->messenger()->addMessage($this->t('Updated total of: @count', ['@count' => $morethen]));
  }

  /**
   * Provides custom validation handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure of the form.a
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function fapiExampleMultistepFormNextValidate(array &$form, FormStateInterface $form_state) {
    /*if ($birth_year != '' && ($birth_year < 1900 || $birth_year > 2000)) {
      // Set an error for the form element with a key of "birth_year".
      $form_state->setErrorByName('birth_year', $this->t('Enter a year between 1900 and 2000.'));
    }*/
  }

  /**
   * Provides custom submission handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function pageNotificationsSubscriptionsMigrationForm(array &$form, FormStateInterface $form_state) {

    $subscription_export_id = EntityAutocomplete::extractEntityIdFromAutocompleteInput($form_state->getValue('subscription_export'));
    $subscription_import_id = EntityAutocomplete::extractEntityIdFromAutocompleteInput($form_state->getValue('subscription_import'));
    $form_state
      ->set('page_values', [
        'subscription_export' => $subscription_export_id,
        'subscription_import' => $subscription_import_id,
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
  public function fapiExamplePageTwo(array &$form, FormStateInterface $form_state) {
    $vals = $form_state->getStorage();
    $subscription_export_node = $vals['page_values']['subscription_export'];

    $nids = \Drupal::entityQuery('node')
      ->accessCheck(FALSE)
      ->condition('type', 'page_notify_subscriptions')
      ->condition('status', 1)
      ->condition('field_page_notify_node_id', $subscription_export_node , '=')
      ->sort('created', 'DESC')
      ->pager(10)
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    $nids = array_keys($nodes);
    $export_records_count = count($nids);
    if($export_records_count == 0){
      $form['general_settings_header'] = [
        '#markup' => $this->t("<h2 id='page-notifications-config-page-header'>No subscriptions found for this node.</h2>"),
        '#weight' => -1,
      ];
      $form['back'] = [
        '#type' => 'submit',
        '#value' => $this->t('Back'),
        '#submit' => ['::fapiExamplePageTwoBack'],
        '#limit_validation_errors' => [],
      ];
    } else {
      $morethen = ($export_records_count > 2) ? 'Subscription' : 'Subscriptions';
      $form['general_settings_header'] = [
        '#markup' => $this->t("<h2 id='page-notifications-config-page-header'>Step 2 - Review. Total of ".$export_records_count . " " . $morethen . " to transfer:</h2>"),
        '#weight' => -1,
      ];
      $form['pagenotifycheckall'] = array(
        '#type' => 'checkbox',
        '#title' => t('Select / Unselect all'),
        '#default_value' => 1,
        '#weight' => 0,
      );
      $form['page-notifications-list'] = array(
        '#type' => 'table',
        '#title' => 'List of Nodes',
        '#header' => ["Checkbox", "Title", "Email", "Title (Node ID)"],
    		'#multiple' => TRUE,
      );

      $i=0;
      foreach($nids as $nid) {
        $node = \Drupal\node\Entity\Node::load($nid);
        $contenttitle=$node->title->value;
        $receivername = $node->getOwner()->getDisplayName();
        $field_page_notify_email = $node->get('field_page_notify_email')->getValue();
        $field_page_notify_node_id = $node->get('field_page_notify_node_id')->getValue();
        $field_page_notify_token = $node->get('field_page_notify_token')->getValue();
        \Drupal::entityTypeManager()->getStorage('node')->resetCache(array($nid));

        $subscribed_node = \Drupal\node\Entity\Node::load($field_page_notify_node_id[0]['value']);
        $subscribed_node_url_str = $subscribed_node->toUrl()->toString();
        $subscribed_node_title = $subscribed_node->getTitle();
        $truncated_subscribed_node_title = (strlen($subscribed_node_title) > 20) ? substr($subscribed_node_title, 0, 20) . '...' : $subscribed_node_title;
        $subscribed_node_link = '<a href="'.$subscribed_node_url_str.'">'.$truncated_subscribed_node_title.'</a>';

        $form['page-notifications-list'][$i]['field_page_notify_node_id'] = array(
          '#type' => 'checkbox',
          '#return_value' => $nid,
          '#default_value' => 1,
          '#attributes' => array('checked' => 'checked')
          );
        $form['page-notifications-list'][$i]['Title'] = array(
          '#type' => 'label',
          '#title' => t($contenttitle),
        );
        $form['page-notifications-list'][$i]['Email'] = array(
          '#type' => 'label',
          '#title' => t($field_page_notify_email[0]['value']),
        );
        $form['page-notifications-list'][$i]['wiew_node'] = array(
          '#markup' => t($subscribed_node_link . ' (' . $field_page_notify_node_id[0]['value'] . ')'),
        );
        $i++;
      }
      $build['pager'] = array(
        '#markup' => 'pager',
      );

      $form['back'] = [
        '#type' => 'submit',
        '#value' => $this->t('Back'),
        '#submit' => ['::fapiExamplePageTwoBack'],
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
   * Provides custom submission handler for 'Back' button (page 2).
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function fapiExamplePageTwoBack(array &$form, FormStateInterface $form_state) {
    $form_state
      ->setValues($form_state->get('page_values'))
      ->set('page_num', 1)
      ->setRebuild(TRUE);
  }
}
