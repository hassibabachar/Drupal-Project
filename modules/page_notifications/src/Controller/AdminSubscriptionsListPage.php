<?php

namespace Drupal\page_notifications\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\page_notifications\Form\AccessVerificationStep;
use Drupal\taxonomy\Entity\Term;

class AdminSubscriptionsListPage extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'admin-node-subscribers-list';
  }

  public function getNodeSubscribersList($node_id = NULL) {
  $node_id_parts = explode("-", $node_id);
  if (count($node_id_parts) == 2) {
    $node_id_row = $node_id_parts[0];
    $node_id_entity_type = $node_id_parts[1];
  } else {
    $node_id_row = $node_id;
    $node_id_entity_type = 'node';
  }

  $header = array(
    array('data' => $this->t('Node Title'), 'field' => 'title', 'sort' => 'asc'),
    array('data' => $this->t('E-Mail'), 'field' => 'e-mail'),
    array('data' => $this->t('Operations')),
  );

  if ($node_id_entity_type == 'node') {
    $query = \Drupal::entityQuery('node')
      ->accessCheck(FALSE)
      ->condition('type', 'page_notify_subscriptions')
      ->condition('field_page_notify_node_id', $node_id_row, '=')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->pager(25);
    $records = $query->execute();
  } elseif ($node_id_entity_type == 'term') {
    $query = \Drupal::entityQuery('node')
      ->accessCheck(FALSE)
      ->condition('type', 'page_notify_subscriptions')
      ->condition('field_page_notify_node_id', $node_id_row, '=')
      ->condition('field_page_notify_token', 'term', 'CONTAINS')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->pager(25);
      $records = $query->execute();
  } else {
    $query = \Drupal::entityQuery('node')
      ->accessCheck(FALSE)
      ->condition('type', 'page_notify_subscriptions')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->pager(25);
    $records = $query->execute();
  }
      $rows = array();
      foreach ($records as $record) {
        if ($record) {
          $node = \Drupal\node\Entity\Node::load($record);
          $page_title = $node->getTitle();
          $subscribed_node_url = $node->toUrl()->setAbsolute()->toString();
          $field_page_notify_email = $node->get("field_page_notify_email")->getValue();
        }

          $rows[] = array('data' => array(
            'title' => new FormattableMarkup('<a href="@page_url">@page_title</a>',
              [
                '@page_title' => $page_title,
                '@page_url' => $subscribed_node_url,
              ]),
            'e-mail' => new FormattableMarkup('@user_email',
                [
                  '@user_email' => $field_page_notify_email[0]['value'],
                ]),
            'cancel_one' => new FormattableMarkup('<a href="@record_link">@name</a>',
                ['@name' => 'View', '@record_link' => $subscribed_node_url]
              ),
          ));
      }


      $page_name = '<h1>Subscriptions List</h1>';
      $build['page_name'] = [
        '#markup' => $page_name,
        '#attributes' => [
          'class' => ['page-notifications-user-list-page-name'],
        ],
      ];
      $build['config_table'] = array(
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => t('No records found'),
        '#attributes' => [
          'class' => ['page-notifications-block-subscriberpage'],
          'id' => 'page-notifications-block-subscriberpage',
          'no_striping' => TRUE,
        ],
      );
      $build['pager'] = array(
        '#type' => 'pager'
      );
      return $build;
  }
}
