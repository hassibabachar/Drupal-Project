<?php

namespace Drupal\page_notifications\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Component\Utility\EmailValidator;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use \Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use \Drupal\Component\Utility\UrlHelper;
use Drupal\filter\Element\ProcessedText;
use Drupal\Core\Render\BubbleableMetadata;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;

/**
 * Submit a form without a page reload.
 */
class PageNotificationsBlockForm extends FormBase
{

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
   * Constructs a new EmailExampleGetFormPage.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Component\Utility\EmailValidator $email_validator
   *   The email validator.
   */
  public function __construct(MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager, EmailValidator $email_validator)
  {
    $this->mailManager = $mail_manager;
    $this->languageManager = $language_manager;
    $this->emailValidator = $email_validator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
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
  public function getFormId()
  {
    return 'page_notifications_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $preload_template = \Drupal::service('load.databaseinnfo.service')->get_notify_email_template();
    $notify_settings = \Drupal::service('load.databaseinnfo.service')->get_notify_settings();
    $host = \Drupal::request()->getSchemeAndHttpHost();
    $current_uri_row = \Drupal::request()->getRequestUri();
    $current_uri_pieces = explode("?", $current_uri_row);
    $current_uri = $current_uri_pieces[0];
    $reload_page_link = $host . $current_uri;
    $page_url = '';
    $page_id = '';
    
    $form['#prefix'] = '<div id="page-notifications-block-container">';
    $form['#suffix'] = '</div>';
    $route_match = \Drupal::routeMatch();

    if ($route_match->getRouteName() == 'entity.node.canonical') {
      $node = \Drupal::routeMatch()->getParameter('node');
      if ($node instanceof \Drupal\node\NodeInterface) {
        $node = \Drupal\node\Entity\Node::load($node->id());
        $page_title = $node->getTitle();
        $page_id = $node->id() . '-' . 'node';
        $page_entity_type = 'node';
        $node_node_url = $node->toUrl()->setAbsolute()->toString();
      }
    } elseif ($route_match->getRouteName() == 'entity.taxonomy_term.canonical') {
      $term_id = $route_match->getRawParameter('taxonomy_term');
      $term = Term::load($term_id);
      $page_id = $term_id . '-' . 'term';
      $page_entity_type = 'term';
      $page_title = $term->getName();
      $page_url = $term->toUrl()->setAbsolute()->toString();
    } else {
      $page_title = '';
      $page_url = '';
      $page_id = '';
    }

    if ($page_url == '' && $page_id == '') {
      $form['container'] = [
        '#type' => 'container',
        '#attributes' => ['id' => 'page-notifications-block-container'],
      ];
      $form['block_header'] = [
        '#type' => 'processed_text',
        '#text' => $this->t($preload_template['subscription_not_available_web_page_message']),
        "#processed" => true,
        '#format' => 'full_html',
      ];
    } else {
      $block_header_replacements = [
        '[notify_user_name]' => '',
        '[notify_user_email]' => '',
        '[notify_verify_url]' => '',
        '[notify_subscribe_url]' => '',
        '[notify_unsubscribe_url]' => '',
        '[notify_user_subscribtions]' => '',
        '[notify_node_title]' => $page_title,
        '[notify_node_url]' => $page_url,
        '[notify_notes]' => '',
      ];
      $tokanized_notify_body = \Drupal::service('load.databaseinnfo.service')->page_notifications_process_tokens($preload_template['body'], $block_header_replacements);
      $form['container'] = [
        '#type' => 'container',
        '#attributes' => ['id' => 'page-notifications-block-container'],
      ];
      $form['container']['box'] = [
        '#type' => 'processed_text',
        '#text' => $this->t($tokanized_notify_body),
        "#processed" => true,
        '#format' => 'full_html',
      ];
      $form['current_node'] = [
        '#type' => 'hidden',
        '#value' => $page_id,
      ];
      $form['current_path'] = [
        '#type' => 'hidden',
        '#value' => $page_url,
      ];
      $form['email_notify'] = [
        '#type' => 'email',
        '#title' => $this->t('Enter your E-mail Address:'),
        '#required' => true,
      ];
      $moduleHandler = \Drupal::service('module_handler');
      if ($moduleHandler->moduleExists('recaptcha') && $notify_settings['page_notify_recaptcha'] == "1") {
        $form['#attached']['library'][] = 'page_notifications/recaptcha';
        $form['recaptcha'] = [
          '#markup' => pageNotificationsRecaptchaHtml(),
        ];
      }
      if ($moduleHandler->moduleExists('captcha') && $notify_settings['page_notify_captcha'] == "1") {
        $captcha_type = \Drupal::config('captcha.settings')->get('default_challenge');
        $form['my_captcha_element'] = [
          '#type' => 'captcha',
          '#captcha_type' => $captcha_type,
        ];
      }
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#ajax' => [
        'callback' => '::promptCallback',
        'wrapper' => 'page-notifications-block-container',
      ],
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    if (!$this->emailValidator->isValid($form_state->getValue('email_notify')) || $form_state->getValue('email_notify') != strip_tags($form_state->getValue('email_notify'))) {
      $form_state->setErrorByName('email', $this->t('That e-mail address is not valid.'));
    }

  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
  }

  /**
   * Callback for submit_driven example.
   *
   * Select the 'box' element, change the markup in it, and return it as a
   * renderable array.
   *
   * @return array
   *   Renderable array (the box element)
   */
  public function promptCallback(array &$form, FormStateInterface $form_state)
  {
    $response = new AjaxResponse();
    $element = $form['container'];
    $host = \Drupal::request()->getSchemeAndHttpHost();
    $template_info = \Drupal::service('load.databaseinnfo.service')->get_notify_email_template();
    $input = $form_state->getUserInput();
    if (!is_null($input['g-recaptcha-response'])) {
      if ($input['g-recaptcha-response'] == "") {
        $form_state->setErrorByName('g-recaptcha-response', $this->t('Please enter recaptcha.'));
      }
    }
    $errors = $form_state->getErrors();

    if (!$errors) {
      // if email and node not correct
      if ($form_state->getValue('email_notify') != strip_tags($form_state->getValue('email_notify')) || $form_state->getValue('current_node') != strip_tags($form_state->getValue('current_node'))) {
        $email_notify_strip_tags = strip_tags($form_state->getValue('email_notify'));
        $replacements = [
          '[notify_user_name]' => '',
          '[notify_user_email]' => $email_notify_strip_tags,
          '[notify_verify_url]' => '',
          '[notify_subscribe_url]' => '',
          '[notify_unsubscribe_url]' => '',
          '[notify_user_subscribtions]' => '',
          '[notify_node_title]' => '',
          '[notify_node_url]' => '',
          '[notify_notes]' => '',
        ];
        $tokanized_error_web_page_message = \Drupal::service('load.databaseinnfo.service')->page_notifications_process_tokens($template_info['error_web_page_message'], $replacements);
        $element['box'] = [
          '#type' => 'processed_text',
          '#text' => $this->t($tokanized_error_web_page_message),
          '#format' => 'full_html',
        ];
      } else { // if email and node correct
        if ($form_state->getValue('email_notify') && $form_state->getValue('current_node')) {
          $email_notify = $form_state->getValue('email_notify');
          $current_node = $form_state->getValue('current_node');
          $record_exist = \Drupal::service('load.databaseinnfo.service')->checkIfRecordExistNode($email_notify, $current_node);

          if ($record_exist) {
            $token_pieces = explode("-", $record_exist['field_page_notify_token']);
            if (count($token_pieces) == 2) {
              $record_exist_node_id = $record_exist['field_page_notify_node_id'];
              $record_exist_token = $token_pieces[0];
              $record_exist_entity_type = $token_pieces[1];
            } elseif (count($token_pieces) == 1) {
              $record_exist_node_id = $record_exist['field_page_notify_node_id'];
              $record_exist_token = $token_pieces[0];
              $record_exist_entity_type = 'node';
            } else {
              $record_exist_node_id = $record_exist['field_page_notify_node_id'];
              $record_exist_entity_type = 'node';
            }

            $unsubscribe_link = $host . "/page-notifications/unsubscribe/" . $record_exist['field_page_notify_token'];
            if ($record_exist_entity_type == 'node') {
              $node = \Drupal\node\Entity\Node::load($record_exist_node_id);
              $page_title = $node->getTitle();
              $subscribed_node_url = $node->toUrl()->setAbsolute()->toString();
            } elseif ($record_exist_entity_type == 'term') {
              $node = Term::load($record_exist_node_id);
              $page_title = $node->getName();
              $subscribed_node_url = \Drupal\Core\Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $node->tid->value], ['absolute' => TRUE])->toString();
            } else {
              $node = NULL;
            }

            $replacements = [
              '[notify_user_name]' => '',
              '[notify_user_email]' => $email_notify,
              '[notify_verify_url]' => '',
              '[notify_subscribe_url]' => '',
              '[notify_unsubscribe_url]' => $unsubscribe_link ? $unsubscribe_link : '',
              '[notify_user_subscribtions]' => '',
              '[notify_node_title]' => $page_title ? $page_title : '',
              '[notify_node_url]' => $subscribed_node_url ? $subscribed_node_url : '',
              '[notify_notes]' => '',
            ];
            $tokanized_web_message = \Drupal::service('load.databaseinnfo.service')->page_notifications_process_tokens($template_info['record_exist_verify_web_page_message'], $replacements);
            $element['box'] = [
              '#type' => 'processed_text',
              '#text' => $this->t($tokanized_web_message),
              '#format' => 'full_html',
            ];
          } else { // this is when record doesn't exist
            $current_page_node_pieces = explode("-", $current_node);
            if (count($current_page_node_pieces) == 2) {
              $current_node_id = $current_page_node_pieces[0];
              $current_entity_type = $current_page_node_pieces[1];
            } else {
              $current_node_id = $current_node;
              $current_entity_type = 'node';
            }

            if ($current_entity_type == 'node') {
              $node = \Drupal\node\Entity\Node::load($current_node_id);
              $current_page_title = $node->getTitle();
              $current_subscribed_node_url = $node->toUrl()->setAbsolute()->toString();
            } elseif ($current_entity_type == 'term') {
              $node = Term::load($current_node_id);
              $current_page_title = $node->getName();
              $current_subscribed_node_url = \Drupal\Core\Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $node->tid->value], ['absolute' => TRUE])->toString();
            } else {
              $node = NULL;
            }
            $unsubscribe_link = '';
            if ($template_info['from_email']) {
              $from = $template_info['from_email'];
            } else {
              $from = \Drupal::config('system.site')->get('mail');
            }
            $current_uri_row = \Drupal::request()->getRequestUri();
            $current_uri_pieces = explode("?", $current_uri_row);
            $current_uri = $current_uri_pieces[0];
            //$reload_page_link = $host . $current_uri;
            $subscription_token = page_notifications_generateRandomString();
            $confrm_url = $host . "/page-notifications/confirmation/" . $email_notify . "/" . $current_node . "-" . $subscription_token;

            $subject_replacements = [
              '[notify_user_name]' => '',
              '[notify_user_email]' => $email_notify,
              '[notify_verify_url]' => '',
              '[notify_subscribe_url]' => '',
              '[notify_unsubscribe_url]' => '',
              '[notify_user_subscribtions]' => '',
              '[notify_node_title]' => $current_page_title,
              '[notify_node_url]' => '',
              '[notify_notes]' => '',
            ];
            $body_replacements = [
              '[notify_user_name]' => '',
              '[notify_user_email]' => $email_notify,
              '[notify_verify_url]' => $confrm_url,
              '[notify_subscribe_url]' => '',
              '[notify_unsubscribe_url]' => '',
              '[notify_user_subscribtions]' => '',
              '[notify_node_title]' => $current_page_title,
              '[notify_node_url]' => $current_subscribed_node_url,
              '[notify_notes]' => '',
            ];
            $tokanized_subject = \Drupal::service('load.databaseinnfo.service')->page_notifications_process_tokens($template_info['verification_email_subject'], $subject_replacements);
            $tokanized_body = \Drupal::service('load.databaseinnfo.service')->page_notifications_process_tokens($template_info['verification_email_text'], $body_replacements);
            $tokanized_web_page_message = \Drupal::service('load.databaseinnfo.service')->page_notifications_process_tokens($template_info['sent_verify_web_page_message'], $body_replacements);
            // send an email
            $message['to'] = $email_notify;
            $message['subject'] = $tokanized_subject;
            $message['body'] = $tokanized_body;
            $result = \Drupal::service('plugin.manager.mail')->mail(
              'page_notifications',
              'verification_email',
              $email_notify,
              \Drupal::languageManager()->getDefaultLanguage()->getId(),
              $message
            );

            if ($result['result'] !== true) {
              $element['box'] = [
                '#type' => 'processed_text',
                '#text' => $this->t('There was an error sending you an email verification. Please contact ' . $from . ' for assistance.'),
                '#format' => 'full_html',
              ];
            } else {
              $element['box'] = [
                '#type' => 'processed_text',
                '#text' => $this->t($tokanized_web_page_message),
                '#format' => 'full_html',
              ];
            }
          } //end of when record doesn't exist and email needs to be sent
        }
      }
    } else {
      $replacements = [
        '[notify_user_name]' => '',
        '[notify_user_email]' => '',
        '[notify_verify_url]' => '',
        '[notify_subscribe_url]' => '',
        '[notify_unsubscribe_url]' => '',
        '[notify_user_subscribtions]' => '',
        '[notify_node_title]' => '',
        '[notify_node_url]' => '',
        '[notify_notes]' => '',
      ];
      $tokanized_error_web_page_message = \Drupal::service('load.databaseinnfo.service')->page_notifications_process_tokens($template_info['error_web_page_message'], $replacements);
      $element['box'] = [
        '#type' => 'processed_text',
        '#text' => $this->t($tokanized_error_web_page_message),
        '#format' => 'full_html',
      ];
    }
    return $element;
  } //promptCallback
} //class

/**
 * Summary of Drupal\page_notifications\Form\pageNotificationsRecaptchaHtml
 * @return string
 */
function pageNotificationsRecaptchaHtml()
{
  $site_key = \Drupal::config('recaptcha.settings')->get('site_key');
  if (!is_null($site_key)) {
    define('RECAPTCHA_SITEKEY', $site_key);
    return '<div class="g-recaptcha mb-3" data-sitekey="' . RECAPTCHA_SITEKEY . '"></div>';
  }
}

/**
 * Summary of Drupal\page_notifications\Form\pageNotificationsPost
 * @param string $url
 * @param array $postdata
 * @return bool|string
 */
function pageNotificationsPost(string $url, array $postdata)
{
  $content = http_build_query($postdata);
  $opts = [
    'http' =>
      [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => $content
      ]
  ];
  $context = stream_context_create($opts);
  $result = file_get_contents($url, false, $context);
  return $result;
}

function pageNotificationsRecaptchaVerify()
{
  if (!isset($_POST['g-recaptcha-response'])) {
    return false;
  }
  $response = filter_input(INPUT_POST, 'g-recaptcha-response', FILTER_SANITIZE_STRING);
  $secret_key = \Drupal::config('recaptcha.settings')->get('secret_key');
  define('RECAPTCHA_SECRETKEY', $secret_key);
  define('RECAPTCHA_URL', 'https://www.google.com/recaptcha/api/siteverify');
  $json = pageNotificationsPost(RECAPTCHA_URL, ['secret' => RECAPTCHA_SECRETKEY, 'response' => $response]);
  $data = json_decode($json, true);
  if ($data['success'] == 1) {
    return true;
  }
  return false;
}

function page_notifications_generateRandomString($length = 10)
{
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}
