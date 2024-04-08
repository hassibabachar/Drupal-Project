<?php

namespace Drupal\page_notifications\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\examples\Utility\DescriptionTemplateTrait;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\page_notifications\Form\AccessVerificationStep;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\taxonomy\Entity\Term;

/**
 * Controller routines for page example routes.
 */
class SubscriberPage extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'subscriberpage';
  }

  public function subscriberpage($user_token = NULL) {
    if ($user_token && !is_null($user_token)) {
      $header = array(
        // We make it sortable by name.
        array('data' => $this->t('Watching pages list'), 'field' => 'title', 'sort' => 'asc'),
        array('data' => $this->t('')),
      );

      $query = \Drupal::entityQuery('node')
        ->accessCheck(FALSE)
        ->condition('type', 'page_notify_subscriptions')
        ->condition('field_page_notify_token_user_id', $user_token, '=')
        ->condition('status', 1)
        ->sort('created', 'DESC')
        ->pager(10);
      $records = $query->execute();

      // Populate the rows.
      $rows = array();
      foreach ($records as $record) {
        $subscriprion_node = \Drupal\node\Entity\Node::load($record);
        $record_field_page_notify_node_id = $subscriprion_node->get("field_page_notify_node_id")->getValue();
        $record_field_token_notify = $subscriprion_node->get("field_page_notify_token")->getValue();
        $record_field_token_notify_pieces = explode("-", $record_field_token_notify[0]['value']);

        if (count($record_field_token_notify_pieces) == 2) {
          $record_field_entity_type = $record_field_token_notify_pieces[1];
        } else {
          $record_field_entity_type = 'node';
        }

        if ($record_field_entity_type == 'node') {
          $node = \Drupal\node\Entity\Node::load($record_field_page_notify_node_id[0]['value']);
          $page_title = $node->getTitle();
          $subscribed_node_url = $node->toUrl()->setAbsolute()->toString();
        } elseif ($record_field_entity_type == 'term') {
          $node = Term::load($record_field_page_notify_node_id[0]['value']);
          $page_title = $node->getName();
          $subscribed_node_url = \Drupal\Core\Url::fromRoute('entity.taxonomy_term.canonical',['taxonomy_term' => $node->tid->value],['absolute' => TRUE])->toString();
        } else {
          $node = NULL;
        }

        $rows[] = array('data' => array(
          'title' => new FormattableMarkup('<a href="@page_url">@page_title</a>',
            [
              '@page_title' => $page_title,
              '@page_url' => $subscribed_node_url,
            ]),
          'cancel_one' => new FormattableMarkup('<a id="notify-cancel-@token" href="/nojs/cancel_subscription/@token" class="use-ajax btn btn-default notify-cancel-subscription">@name</a>',
              ['@name' => 'Stop Watching', '@token' => $record_field_token_notify[0]['value']]
            ),
        ));

        if ($rows) {
          $cancelall =  '<a id="notify-cancel-all" href="/nojs/cancel_all/' . $user_token .'" class="use-ajax btn btn-default notify-cancel-all-subscription">Unsubscribe from all</a>';
          $header = array(
            // We make it sortable by name.
            array('data' => $this->t('Page Name'), 'field' => 'title', 'sort' => 'asc'),
            array('data' => $this->t($cancelall)),
          );
        }
        else {
          $build = array();
        }
      }

      $page_name = '<h1>Manage Your Page Watching Subscriptions</h1>';
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

  public function cancel_subscription($token) {
      $response = new AjaxResponse();
      page_notifications_delete_record($token);
      $response->addCommand(new ReplaceCommand('#notify-cancel-' . $token, '<span class="notify-cancel-cancelled">Cancelled</span>'));
      return $response;
  }

  public function cancel_all($user_token) {
      $response = new AjaxResponse();
      page_notifications_delete_all_records($user_token);
      $response->addCommand(new ReplaceCommand('#notify-cancel-all', '<span class="notify-cancel-all-cancelled">All cancelled</span>'));
      $response->addCommand(new ReplaceCommand('#notify-cancel-', "Cancelled"));
      return $response;
  }
}



function checkForRecord($subscription_token_url, $email) {
  $record = current(\Drupal::entityTypeManager()->getStorage('node')
    ->loadByProperties([
      'field_page_notify_token' => $subscription_token_url,
      'field_page_notify_email' => $email
    ])
  );
  if ($record) {
    return $record;
  }
  else {
    return FALSE;
  }
}

function getAllRecords($email) {
  $query = \Drupal::entityQuery('node')
    ->accessCheck(FALSE)
    ->condition('status', 1)
    ->condition('field_page_notify_email', $email, '=');
  $records = $query->execute();
  foreach ($records as $key => $record) {
    $node = \Drupal\node\Entity\Node::load($record);
    $nodes[] = $node;
  }
  if ($nodes) {
    return $nodes;
  }
  else {
    return FALSE;
  }
}

function page_notifications_delete_record($token) {
  $num_deleted = \Drupal::entityQuery("node")
    ->accessCheck(FALSE)
    ->condition("type", "page_notify_subscriptions")
    ->condition("field_page_notify_token", $token)
    ->accessCheck(FALSE)
    ->execute();
  $storage_handler = \Drupal::entityTypeManager()->getStorage("node");
  $entities = $storage_handler->loadMultiple($num_deleted);
  $storage_handler->delete($entities);
}

function page_notifications_delete_all_records($user_token) {
  $num_deleted = \Drupal::entityQuery("node")
    ->accessCheck(FALSE)
    ->condition("type", "page_notify_subscriptions")
    ->condition("field_page_notify_token_user_id", $user_token)
    ->accessCheck(FALSE)
    ->execute();
  $storage_handler = \Drupal::entityTypeManager()->getStorage("node");
  $entities = $storage_handler->loadMultiple($num_deleted);
  $storage_handler->delete($entities);
}
