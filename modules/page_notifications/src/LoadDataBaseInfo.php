<?php

namespace Drupal\page_notifications;

/**
 * Class LoadDataBaseInfo.
 *
 * @package Drupal\page_notifications\src
 */

class LoadDataBaseInfo
{

  public function get_notify_email_template()
  {
    $sql = "SELECT * FROM page_notify_email_template ORDER BY template_id DESC LIMIT 1";
    $result = \Drupal::database()->query($sql);
    $template = [];
    if ($result) {
      while ($row = $result->fetchAssoc()) {
        $template = [
          'template_id' => $row['template_id'],
          'body' => $row['body'],
          'from_email' => $row['from_email'],
          'checkbox_field' => $row['checkbox_field'],
          'notes_field' => $row['notes_field'],
          'node_timestamp' => $row['node_timestamp'],
          'verification_email_subject' => $row['verification_email_subject'],
          'verification_email_text' => $row['verification_email_text'],
          'confirmation_email_subject' => $row['confirmation_email_subject'],
          'confirmation_email_text' => $row['confirmation_email_text'],
          'sent_verify_web_page_message' => $row['sent_verify_web_page_message'],
          'record_exist_verify_web_page_message' => $row['record_exist_verify_web_page_message'],
          'error_web_page_message' => $row['error_web_page_message'],
          'subscription_not_available_web_page_message' => $row['subscription_not_available_web_page_message'],
          'confirmation_web_page_message' => $row['confirmation_web_page_message'],
          'general_email_template_subject' => $row['general_email_template_subject'],
          'general_email_template' => $row['general_email_template'],
        ];
      }
      return $template;
    } else {
      return FALSE;
    }
  }

  public function get_notify_settings()
  {
    $sql = "SELECT * FROM page_notify_settings ORDER BY page_notify_id DESC LIMIT 1";
    $result = \Drupal::database()->query($sql);
    $settings = [];
    if ($result) {
      while ($row = $result->fetchAssoc()) {
        $settings = [
          'page_notify_id' => $row['page_notify_id'],
          'page_notify_recaptcha' => $row['page_notify_recaptcha'],
          'page_notify_captcha' => $row['page_notify_captcha'],
          'page_notify_subscribers_count' => $row['page_notify_subscribers_count'],
          'enable_message_subscription_not_available' => $row['enable_message_subscription_not_available'],
          'page_notify_settings_enable_content_type' => $row['page_notify_settings_enable_content_type'],
          'page_notify_settings_enable_view' => $row['page_notify_settings_enable_view'],
        ];
      }
      return $settings;
    } else {
      return FALSE;
    }
  }

  public function page_notifications_process_tokens($text, $replacements = null)
  {
    $find = array(
      '[notify_user_name]',
      '[notify_user_email]',
      '[notify_verify_url]',
      '[notify_subscribe_url]',
      '[notify_unsubscribe_url]',
      '[notify_user_subscribtions]',
      '[notify_node_title]',
      '[notify_node_url]',
      '[notify_notes]',
    );
    if (!is_null($replacements)) {
      $replace = $replacements;
    } else {
      $replace = array(
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        ''
      );
    }

    $new_text = str_replace($find, $replace, $text);
    return $new_text;
  }

  public function pageNotifyGetUserToken($email_notify)
  {
    $record = current(
      \Drupal::entityTypeManager()->getStorage('node')
        ->loadByProperties([
          'field_page_notify_email' => $email_notify,
        ])
    );
    if ($record && !is_null($record)) {
      $field_token_notify_user_id = $record->get("field_page_notify_token_user_id")->getValue();
      $record = [
        'field_page_notify_token_user_id' => $field_token_notify_user_id[0]['value'],
      ];
      return $record;
    } else {
      return FALSE;
    }
  }

  public function checkIfUserHasSubscription($email_notify)
  {
    $record = current(
      \Drupal::entityTypeManager()->getStorage('node')
        ->loadByProperties([
          'field_page_notify_email' => $email_notify,
        ])
    );
    if ($record && !is_null($record)) {
      $field_token_notify_user_id = $record->get("field_page_notify_token_user_id")->getValue();
      $user_record = [
        'field_page_notify_token_user_id' => $field_token_notify_user_id[0]['value'],
      ];
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function verifyByTokenAndEmail($email, $subscription_token)
  {
    $record = current(
      \Drupal::entityTypeManager()->getStorage('node')
        ->loadByProperties([
          'field_page_notify_email' => $email,
          'field_page_notify_token' => $subscription_token,
        ])
    );
    if ($record && $record->isPublished() == true) {
      return true;
    } else {
      return FALSE;
    }
  }

  public function checkIfRecordExistNode($email, $node)
  {
    $node_pieces = explode("-", $node);
    $node_id = $node_pieces[0];
    $node_entity_type = $node_pieces[1];

    $query = current(
      \Drupal::entityTypeManager()->getStorage('node')
        ->loadByProperties([
          'field_page_notify_email' => $email,
          'field_page_notify_node_id' => $node_id
        ])
    );


    if ($query && $query->isPublished() == true) {
      $field_token_notify_user_id = $query->get("field_page_notify_token_user_id")->getValue();
      $field_email_notify = $query->get("field_page_notify_email")->getValue();
      $field_token_notify = $query->get("field_page_notify_token")->getValue();
      $field_node_id_notify = $query->get("field_page_notify_node_id")->getValue();
      $records = [
        'field_page_notify_token_user_id' => $field_token_notify_user_id[0]['value'],
        'field_page_notify_email' => $field_email_notify[0]['value'],
        'field_page_notify_token' => $field_token_notify[0]['value'],
        'field_page_notify_node_id' => $field_node_id_notify[0]['value'],
      ];

      $field_page_notify_token_pieces = explode("-", $records['field_page_notify_token']);
      $field_page_notify_node_id_pieces = explode("-", $records['field_page_notify_node_id']);

      if ($field_page_notify_token_pieces[1] && $field_page_notify_token_pieces[1] == $node_entity_type && $field_page_notify_node_id_pieces[0] == $node_id) {
        return $records;
      } elseif (!$field_page_notify_token_pieces[1] && $field_page_notify_node_id_pieces[0] == $node_id) {
        return $records;
      } else {
        return FALSE;
      }
    } else {
      return FALSE;
    }
  }
  public function checkIfRecordExistByToken($email, $token)
  {
    $record = current(
      \Drupal::entityTypeManager()->getStorage('node')
        ->loadByProperties([
          'field_page_notify_email' => $email,
          'field_page_notify_token' => $token
        ])
    );
    if ($record && $record->isPublished() == true) {
      $field_token_notify_user_id = $record->get("field_page_notify_token_user_id")->getValue();
      $field_email_notify = $record->get("field_page_notify_email")->getValue();
      $field_token_notify = $record->get("field_page_notify_token")->getValue();
      $field_node_id_notify = $record->get("field_page_notify_node_id")->getValue();
      $user_record = [
        'field_page_notify_token_user_id' => $field_token_notify_user_id[0]['value'],
        'field_page_notify_email' => $field_email_notify[0]['value'],
        'field_page_notify_token' => $field_token_notify[0]['value'],
        'field_page_notify_node_id' => $field_node_id_notify[0]['value'],
      ];
      return $user_record;
    } else {
      return FALSE;
    }
  }

  public function getAllUserRecords($user_token, $email)
  {
    $query = \Drupal::entityQuery('node')
      ->accessCheck(FALSE)
      ->condition('status', 1)
      ->condition('field_page_notify_email', $email, '=')
      ->condition('field_page_notify_token_user_id', $user_token, '=');
    $records = $query->execute();
    if ($records && !is_null($records)) {
      $user_records = [];
      foreach ($records as $record) {
        $node_record = \Drupal\node\Entity\Node::load($record);
        if ($node_record && $node_record->isPublished() == true) {
          $field_token_notify_user_id = $node_record->get("field_page_notify_token_user_id")->getValue();
          $field_email_notify = $node_record->get("field_page_notify_email")->getValue();
          $field_token_notify = $node_record->get("field_page_notify_token")->getValue();
          $field_node_id_notify = $node_record->get("field_page_notify_node_id")->getValue();
          $user_record = [
            'field_page_notify_token_user_id' => $field_token_notify_user_id[0]['value'],
            'field_page_notify_email' => $field_email_notify[0]['value'],
            'field_page_notify_tokeny' => $field_token_notify[0]['value'],
            'field_page_notify_node_id' => $field_node_id_notify[0]['value'],
          ];
          array_push($user_records, $user_record);
        }
      }
      return $user_records;
    } else {
      return FALSE;
    }
  }

  public function getAllNodeSubscription($node)
  {
    $query = \Drupal::entityQuery('node')
      ->accessCheck(FALSE)
      ->condition('type', 'page_notify_subscriptions')
      ->condition('status', 1)
      ->condition('field_page_notify_node_id', $node, '=');
    $records = $query->execute();

    if ($records && !is_null($records)) {
      $node_subscriptions = [];
      foreach ($records as $record) {
        $node_record = \Drupal\node\Entity\Node::load($record);

        if ($node_record) {
          $field_token_notify_user_id = $node_record->get("field_page_notify_token_user_id")->getValue();
          $field_email_notify = $node_record->get("field_page_notify_email")->getValue();
          $field_token_notify = $node_record->get("field_page_notify_token")->getValue();
          $field_node_id_notify = $node_record->get("field_page_notify_node_id")->getValue();
          $node_record = [
            'field_page_notify_token_user_id' => $field_token_notify_user_id[0]['value'],
            'field_page_notify_email' => $field_email_notify[0]['value'],
            'field_page_notify_token' => $field_token_notify[0]['value'],
            'field_page_notify_node_id' => $field_node_id_notify[0]['value'],
            'subscription_node_id' => $node_record->id(),
          ];
          array_push($node_subscriptions, $node_record);
        }
      }

      return $node_subscriptions;
    } else {
      return FALSE;
    }
  }

  public function getCurrentPageInfo()
  {
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface) {
      $nid = $node->id();
      $pageinfo = [
        'current_node' => $nid,
        'current_path' => '',
      ];
    } else {
      $curr_path = \Drupal::service('path.current')->getPath();
      $pageinfo = [
        'current_node' => '',
        'current_path' => $curr_path,
      ];
    }
    return $pageinfo;
  }

  public function page_notifications_generateRandom_user_token($length = 6)
  {
    $numbers = '0123456789';
    $numbersLength = strlen($numbers);
    $randomNumbers = '';
    for ($i = 0; $i < $length; $i++) {
      $randomNumbers .= $numbers[rand(0, $numbersLength - 1)];
    }
    return $randomNumbers;
  }
}
