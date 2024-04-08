<?php

namespace Drupal\page_notifications\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Component\Utility\EmailValidator;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;


/**
 * @ingroup page_notifications
 */
class EmailUnsubscribePage extends FormBase {

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
    return 'page_notifications_unsubscribe';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $subscription_token = NULL) {
    if($subscription_token != strip_tags($subscription_token) || is_null($subscription_token)) {
      \Drupal::messenger()->addError(t('You link might be broken or incomplete. Please make sure you dont have extra space in your address link.'));
    } else {
      $host = \Drupal::request()->getSchemeAndHttpHost();
      if (is_string($subscription_token) && !is_null($subscription_token)) {
        $part_subscription_token = explode("-", $subscription_token);
        $inrecord_by_token = checkIfRecordExistUnsubscribebyTokens($subscription_token);

        if ($inrecord_by_token) {
          $field_page_notify_token_user_id = $inrecord_by_token->get('field_page_notify_token_user_id')->getValue();
          $subscriptions_url = $host . '/page-notifications/verify-list/' . $field_page_notify_token_user_id[0]['value'];
          $node_id = $inrecord_by_token->get('field_page_notify_node_id')->getValue();
          if (count($part_subscription_token) == 1 && strlen($part_subscription_token[0]) == 10) {
            $subscription_entity_type = 'node';
          } elseif (count($part_subscription_token) == 2) {
            $subscription_entity_type = $part_subscription_token[1];
          }

          if ($subscription_entity_type == 'node') {
            $node = \Drupal\node\Entity\Node::load($node_id[0]['value']);
            $page_title = $node->getTitle();
            $subscribed_node_url = $node->toUrl()->setAbsolute()->toString();
          } elseif ($subscription_entity_type == 'term') {
            $node = Term::load($node_id[0]['value']);
            $page_title = $node->getName();
            $subscribed_node_url = \Drupal\Core\Url::fromRoute('entity.taxonomy_term.canonical',['taxonomy_term' => $node->tid->value],['absolute' => TRUE])->toString();
          } else {
            $node = \Drupal\node\Entity\Node::load($node_id[0]['value']);
            $subscribed_node_url = $node->toUrl()->setAbsolute()->toString();
          }
        }

        if ($subscription_token) {
          $form['intro'] = [
            '#markup' => $this->t('<h1>Unsubscribe from "' . $page_title . '" page.</h1>'),
          ];
          $form['subscription_token'] = [
            '#type' => 'hidden',
            '#value' => $subscription_token,
          ];
          $form['email_unsubscribe'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Enter your E-mail Address:'),
            '#description' => $this->t('Please enter your email for confirmation.'),
            '#required' => TRUE,
          ];
          $form['unsubscribe_all'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Unsubscribe me from all my subscriptions'),
            '#required' => FALSE,
          ];
          $form['intro2'] = [
            '#markup' => new FormattableMarkup('<div>or visit <a target="_blank" href="@url">@name</a></div><br />',
            [
              '@name' => ' Manage Your Page Watching Subscriptions.',
              '@url' => $subscriptions_url
            ]),
          ];
          $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
          ];
          return $form;
        } else {
          $form['intro'] = [
            '#markup' => new FormattableMarkup('<div>The page you been subscribed is no longer available or moved to different location. <br />You can find out by going to: <a target="_blank" href="@url">@name</a></div><br />',
            [
              '@name' => ' Manage Your Page Watching Subscriptions.',
              '@url' => $subscriptions_url
            ]),
          ];
        }

      } else {
        $form['intro'] = [
          '#markup' => $this->t('<p>You link might be broken or incomplete.</p>'),
        ];
      }

      return $form;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!$this->emailValidator->isValid($form_state->getValue('email_unsubscribe')) || is_null($form_state->getValue('email_unsubscribe'))) {
      $form_state->setErrorByName('email_unsubscribe', $this->t('That e-mail address is not valid.'));
    } else {
      $email_unsubscribe = strip_tags($form_state->getValue('email_unsubscribe'));
      $subscription_token = strip_tags($form_state->getValue('subscription_token'));
      if(is_string($email_unsubscribe) && is_string($subscription_token)) {
        $record = \Drupal::service('load.databaseinnfo.service')->verifyByTokenAndEmail($form_state->getValue('email_unsubscribe'), $form_state->getValue('subscription_token'));
        if ($record == true) {
          $email_verify = $email_unsubscribe;
        } else {
          $form_state->setValue('email_unsubscribe', '');
          \Drupal::messenger()->addError(t("We don't have subscription for this email."));
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_values = $form_state->getValues();
    $unsubscribe_all = $form_state->getValue('unsubscribe_all');
    $email_unsubscribe = $form_state->getValue('email_unsubscribe');
    $subscription_token = $form_state->getValue('subscription_token');

    if ($subscription_token == strip_tags($subscription_token) && $email_unsubscribe == strip_tags($email_unsubscribe)) {
      $page_notify_user_token = \Drupal::service('load.databaseinnfo.service')->pageNotifyGetUserToken($email_unsubscribe);
      //$user_token =  $page_notify_user_token['field_page_notify_token_user_id'];

      if ($unsubscribe_all && !is_null($unsubscribe_all) && $page_notify_user_token != false) {
        $result = \Drupal::entityQuery("node")
          ->condition("type", "page_notify_subscriptions")
          ->condition("field_page_notify_email", $email_unsubscribe)
          ->accessCheck(FALSE)
          ->execute();
        $storage_handler = \Drupal::entityTypeManager()->getStorage("node");
        $entities = $storage_handler->loadMultiple($result);
        $storage_handler->delete($entities);
        \Drupal::messenger()->addStatus(t('You have successfully unsubscribed from all pages.'));
      } else {
        $inrecords = checkIfRecordExistUnsubscribe($email_unsubscribe, $subscription_token);

        if ($inrecords && !is_null($inrecords)) {
          $result = \Drupal::entityQuery("node")
            ->condition("type", "page_notify_subscriptions")
            ->condition("field_page_notify_token", $form_state->getValue('subscription_token'))
            ->accessCheck(FALSE)
            ->execute();
          $storage_handler = \Drupal::entityTypeManager()->getStorage("node");
          $entities = $storage_handler->loadMultiple($result);
          $storage_handler->delete($entities);
          if($result){
            \Drupal::messenger()->addStatus(t('You have successfully unsubscribed.'));
          }
        } else {
          \Drupal::messenger()->addError(t('This subscription no longer exist.'));
        }
      }
    } else {
      \Drupal::messenger()->addError(t('You link might be broken or incomplete.'));
    }

    $url = \Drupal\Core\Url::fromRoute('<front>')->toString();
    $response = new RedirectResponse($url);
    $response->send();
  }
}

function checkIfRecordExistUnsubscribebyTokens($subscription_token) {
  $record = current(\Drupal::entityTypeManager()->getStorage('node')
    ->loadByProperties([
      'field_page_notify_token' => $subscription_token
    ])
  );
  if ($record) {
    return $record;
  }
  else {
    return FALSE;
  }
}

function checkIfRecordExistUnsubscribe($email, $subscription_token) {
  $record = current(\Drupal::entityTypeManager()->getStorage('node')
    ->loadByProperties([
      'field_page_notify_email' => $email,
      'field_page_notify_token' => $subscription_token
    ])
  );
  if ($record) {
    return $record;
  }
  else {
    return FALSE;
  }
}
