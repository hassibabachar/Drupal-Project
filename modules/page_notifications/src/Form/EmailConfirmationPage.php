<?php

namespace Drupal\page_notifications\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Component\Utility\EmailValidator;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * @ingroup page_notifications
 */
class EmailConfirmationPage extends FormBase {

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
    return 'page_notifications_confirmation';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $email = NULL, $subscription_token = NULL) {
    if($subscription_token != strip_tags($subscription_token) && $email != strip_tags($email)) {
      \Drupal::messenger()->addError(t('Sorry, no data on this.'));
    } else {
      if($email && !is_null($email) && $subscription_token && !is_null($subscription_token)) {
        $subscription_token_pieces = explode("-", $subscription_token);
        $node_id = $subscription_token_pieces[0];
        $subscription_entity_type = $subscription_token_pieces[1];
        $subscription_token_raw = $subscription_token_pieces[2];
        $host = \Drupal::request()->getSchemeAndHttpHost();
        $inrecords = secondCheckIfRecordExistNode($email, $node_id, $subscription_entity_type, $subscription_token_raw);

        if ($inrecords && $inrecords->isPublished() == true) {
          $user_token_exist = \Drupal::service('load.databaseinnfo.service')->pageNotifyGetUserToken($email);
          if ($user_token_exist && $user_token_exist != false) {
            $user_token = $user_token_exist['field_page_notify_token_user_id'];
          }
          $unsubscribe_link = $host . "/page-notifications/verify-list/" . $user_token;
          $form['intro'] = [
            '#markup' => $this->t('<div clas="page-notifications-find-my-subscriptions"> Find your subscriptions <a href='. $unsubscribe_link .'>here</a>.</div>'),
          ];
          return $form;
        }
        else {
          $user_token_exist = \Drupal::service('load.databaseinnfo.service')->pageNotifyGetUserToken($email);
          if ($user_token_exist && $user_token_exist != false) {
            $user_token = $user_token_exist['field_page_notify_token_user_id'];
          } else {
            $user_token = \Drupal::service('load.databaseinnfo.service')->page_notifications_generateRandom_user_token();
          }

          if ($subscription_entity_type == 'node') {
            $node = \Drupal\node\Entity\Node::load($node_id);
            $page_title = $node->getTitle();
            $subscribed_node_url = $node->toUrl()->setAbsolute()->toString();
          } elseif ($subscription_entity_type == 'term') {
            $node = Term::load($node_id);
            $page_title = $node->getName();
            $subscribed_node_url = \Drupal\Core\Url::fromRoute('entity.taxonomy_term.canonical',['taxonomy_term' => $node->tid->value],['absolute' => TRUE])->toString();
          } else {
            $node = \Drupal\node\Entity\Node::load($node_id);
            $subscribed_node_url = $node->toUrl()->setAbsolute()->toString();
          }

          $new_submition = Node::create([
            'type' => 'page_notify_subscriptions',
            'title' => 'Subscription to - ' . $node_id . ' - ' . $subscription_entity_type . ' - ' . $subscription_token_raw,
            'field_page_notify_node_id' => $node_id,
            'field_page_notify_email' => $email,
            'field_page_notify_token' => $subscription_token_raw . '-' . $subscription_entity_type,
            'field_page_notify_token_user_id' => $user_token,
          ]);
          $new_submition->save();

          $all_subscriptions_url = $host . '/page-notifications/verify-list/' . $user_token;
          $unsubscribe_link = $host . "/page-notifications/unsubscribe/" . $subscription_token_raw . '-' . $subscription_entity_type;

          $template_info = \Drupal::service('load.databaseinnfo.service')->get_notify_email_template();
          if ($template_info['from_email']) {
            $from = $template_info['from_email'];
          }
          else {
            $from = \Drupal::config('system.site')->get('mail');
          }

          $subject_replacements = array(
            '[notify_user_name]' => '',
            '[notify_user_email]' => $email,
            '[notify_verify_url]' => '',
            '[notify_subscribe_url]' => '',
            '[notify_unsubscribe_url]' => '',
            '[notify_user_subscribtions]' => '',
            '[notify_node_title]' => $page_title,
            '[notify_node_url]' => '',
            '[notify_notes]' => '',
          );
          $body_replacements = array(
            '[notify_user_name]' => '',
            '[notify_user_email]' => $email,
            '[notify_verify_url]' => '',
            '[notify_subscribe_url]' => '',
            '[notify_unsubscribe_url]' => $unsubscribe_link,
            '[notify_user_subscribtions]' => $all_subscriptions_url,
            '[notify_node_title]' => $page_title,
            '[notify_node_url]' => $subscribed_node_url,
            '[notify_notes]' => '',
          );

          $tokanized_subject = \Drupal::service('load.databaseinnfo.service')->page_notifications_process_tokens($template_info['confirmation_email_subject'], $subject_replacements);
          $tokanized_body = \Drupal::service('load.databaseinnfo.service')->page_notifications_process_tokens($template_info['confirmation_email_text'], $body_replacements);
          strval($tokanized_subject);
          $message['to'] = $email;
          $message['subject'] = $tokanized_subject;
          $message['body'] = $tokanized_body;
          $result = \Drupal::service('plugin.manager.mail')->mail(
             'page_notifications',
             'configuration_email',
             $email,
             \Drupal::languageManager()->getDefaultLanguage()->getId(),
             $message
           );

          $tokanized_notify_confirmation_web_page_message = \Drupal::service('load.databaseinnfo.service')->page_notifications_process_tokens($template_info['confirmation_web_page_message'], $body_replacements);
          if ($template_info['confirmation_web_page_message']) {
            $form['intro'] = [
              '#type' => 'processed_text',
              '#text' => $this->t($tokanized_notify_confirmation_web_page_message),
              '#format' => 'full_html',
            ];
          }
          else {
            $form['intro'] = [
              '#markup' => new FormattableMarkup('<br /><p>You are now subscribed to</p> <h2><a href="@link">@title</a></h2><br />
                <p><a href="@all_subscriptions_url">Manage Your Page Watching Subscriptions</a>.</p>',
                ['@title' => $page_title, '@link' => $subscribed_node_url, '@all_subscriptions_url' => $all_subscriptions_url]
              ),
            ];
          }
          return $form;
        }
      }
      else {
        \Drupal::messenger()->addError(t('Sorry, no data on this.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $form_values = $form_state->getValues();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_values = $form_state->getValues();
  }
}

function secondCheckIfRecordExistNode($email, $node_id, $subscription_entity_type = NULL, $subscription_token_raw = NULL) {
  $record = current(\Drupal::entityTypeManager()->getStorage('node')
    ->loadByProperties([
      'field_page_notify_email' => $email,
      'field_page_notify_node_id' => $node_id,
      'field_page_notify_token' => $subscription_token_raw . '-' . $subscription_entity_type
    ])
  );
  if ($record) {
    return $record;
  }
  else {
    return FALSE;
  }
}
