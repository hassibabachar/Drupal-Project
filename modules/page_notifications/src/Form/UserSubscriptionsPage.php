<?php

namespace Drupal\page_notifications\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Component\Utility\EmailValidator;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\Entity\Node;

/**
 * Provides a form with two steps.
 *
 * This example demonstrates a multistep form with text input elements. We
 * extend FormBase which is the simplest form base class used in Drupal.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class UserSubscriptionsPage extends FormBase {
  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The email validator.
   *
   * @var \Drupal\Component\Utility\EmailValidator
   */
  protected $emailValidator;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new EmailUnsubscribePage.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Component\Utility\EmailValidator $email_validator
   *   The email validator.
   */
  public function __construct(MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager, EmailValidator $email_validator) {
    $this->mailManager = $mail_manager;
    $this->languageManager = $language_manager;
    $this->emailValidator = $email_validator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $form = new static(
      $container->get('plugin.manager.mail'),
      $container->get('language_manager'),
      $container->get('email.validator')
    );
    $form->setMessenger($container->get('messenger'));
    $form->setStringTranslation($container->get('string_translation'));
    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page-notifications-user-subscriptions-list';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,  $subscription_token = NULL) {

    if($subscription_token != strip_tags($subscription_token) || is_null($subscription_token)) {
      \Drupal::messenger()->addError(t('You link might be broken or incomplete. Please make sure you dont have extra space in your address link.'));
    } else {
      $host = \Drupal::request()->getSchemeAndHttpHost();
      if (is_string($subscription_token) && !is_null($subscription_token)) {
        $part_subscription_token = explode("-", $subscription_token);
        if (strlen($part_subscription_token[1]) == 10) {
          if ($form_state->has('page_num') && $form_state->get('page_num') == 2) {
            return self::userSubscriptionsPageTwo($form, $form_state);
          }
          $form_state->set('page_num', 1);
          $form['subscription_token'] = [
            '#type' => 'hidden',
            '#value' => $subscription_token,
          ];
          $form['email_check'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Enter your E-mail Address:'),
            '#description' => $this->t('Please enter your email for varification.'),
            '#default_value' => $form_state->getValue('email_check', ''),
            '#required' => TRUE,
          ];
          $form['actions'] = [
            '#type' => 'actions',
          ];
          $form['actions']['next'] = [
            '#type' => 'submit',
            '#button_type' => 'primary',
            '#value' => $this->t('Next'),
            '#submit' => ['::userSubscriptionsPageNextSubmit'],
            '#validate' => ['::userSubscriptionsPageNextValidate'],
          ];
        }

      } else {
        $form['intro'] = [
          '#markup' => $this->t('<p>You link might be broken or incomplete.</p>'),
        ];
      }
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Provides custom validation handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function userSubscriptionsPageNextValidate(array &$form, FormStateInterface $form_state) {
    if (!$this->emailValidator->isValid($form_state->getValue('email_check')) || is_null($form_state->getValue('email_check'))) {
      $form_state->setErrorByName('email_check', $this->t('That e-mail address is not valid.'));
    } else {
      $email_check = strip_tags($form_state->getValue('email_check'));
      $subscription_token = strip_tags($form_state->getValue('subscription_token'));
      if(is_string($email_check) && is_string($subscription_token)) {
        $record = \Drupal::service('load.databaseinnfo.service')->verifyByNodeAndEmail($form_state->getValue('email_check'), $form_state->getValue('subscription_token'));
        if ($record == true) {
          $email_verify = $email_check;
        } else {
          $form_state->setErrorByName('email_check', $this->t('Incorect E-Mail.'));
          \Drupal::messenger()->addError(t("We don't have subscription for this email."));
        }
      }
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
  public function userSubscriptionsPageNextSubmit(array &$form, FormStateInterface $form_state) {
    $email_check = $form_state->getValue('email_check');
    $form_state
      ->set('page_values', [
        'email_check' => $form_state->getValue('email_check'),
        'subscription_token' => $form_state->getValue('subscription_token'),
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
  public function userSubscriptionsPageTwo(array &$form, FormStateInterface $form_state) {
    $part_subscription_token = explode("-", $form_state->getValue('subscription_token'));
    $node_id_unsubscribe = $part_subscription_token[0];
    $subscription_token = $part_subscription_token[1];
    $page_notify_user_token = \Drupal::service('load.databaseinnfo.service')->pageNotifyGetUserToken($form_state->getValue('email_check'));
    $user_token = $page_notify_user_token['field_page_notify_token_user_id'];

    if ($user_token && !is_null($user_token)) {
      $header = array(
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
      $rows = array();
      foreach ($records as $record) {
        $node_record = \Drupal\node\Entity\Node::load($record);
        $field_token_notify = $node_record->get("field_page_notify_token")->getValue();
        $field_node_id_notify = $node_record->get("field_page_notify_node_id")->getValue();

        $subscriptions_record = \Drupal\node\Entity\Node::load($field_node_id_notify[0]['value']);
          $rows[] = array('data' => array(
            'title' => new FormattableMarkup('<a href="@page_url">@page_title</a>',
              [
                '@page_title' => $subscriptions_record->getTitle(),
                '@page_url' => $subscriptions_record->toUrl()->toString(),
              ]),
            'cancel_one' => new FormattableMarkup('<a id="notify-cancel-@token" href="/nojs/cancel_subscription/@token" class="use-ajax btn btn-default notify-cancel-subscription">@name</a>',
                ['@name' => 'Stop Watching', '@token' => $field_token_notify[0]['value']]
              ),
          ));

        if ($rows) {
          $cancelall =  '<a id="notify-cancel-all" href="/nojs/cancel_all/' . $user_token .'" class="use-ajax btn btn-default notify-cancel-all-subscription">Unsubscribe from all</a>';
          $header = array(
            array('data' => $this->t('Page Name'), 'field' => 'title', 'sort' => 'asc'),
            array('data' => $this->t($cancelall)),
          );
        } else {
          $build = array();
        }
      }

      /*$page_name = '<h1>Manage Your Page Watching Subscriptions</h1>';
      $build['page_name'] = [
        '#markup' => $page_name,
        '#attributes' => [
          'class' => ['page-notifications-user-list-page-name'],
        ],
      ];*/
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

    } else {
      $url = \Drupal\Core\Url::fromRoute('<front>')->toString();
      $response = new RedirectResponse($url);
      $response->send();
      \Drupal::messenger()->addError(t('You link might be broken or incomplete.'));
    }
    //return $form;
  }

  public function rebuildFormSubmit(array &$form, FormStateInterface $form_state) {
    $this->displayMethodInvocation('rebuildFormSubmit');
    $form_state->setRebuild(TRUE);
  }

  public function cancel_subscription($token) {
      $response = new AjaxResponse();
      page_notifications_user_delete_record($token);
      $response->addCommand(new ReplaceCommand('#notify-cancel-' . $token, '<span class="notify-cancel-cancelled">Cancelled</span>'));
      return $response;
  }

  public function cancel_all($user_token) {
      $response = new AjaxResponse();
      page_notifications_user_delete_all_records($user_token);
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

function page_notifications_user_delete_record($token) {
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

function page_notifications_user_delete_all_records($user_token) {
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
