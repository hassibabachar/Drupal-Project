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
use Drupal\node\Entity\Node;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Implements the build demo form controller.
 *
 * This example uses the Messenger service to demonstrate the order of
 * controller method invocations by the form api.
 *
 * @see \Drupal\Core\Form\FormBase
 * @see \Drupal\Core\Form\ConfigFormBase
 */
class AccessVerificationStep extends FormBase {

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
   * Counter keeping track of the sequence of method invocation.
   *
   * @var int
   */
  protected static $sequenceCounter = 0;

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
    $form->setStringTranslation($container->get('string_translation'));
    return $form;
  }

  /**
   * Update form processing information.
   *
   * Display the method being called and it's sequence in the form
   * processing.
   *
   * @param string $method_name
   *   The method being invoked.
   */
  private function displayMethodInvocation($method_name) {
    self::$sequenceCounter++;
    $this->messenger()->addMessage(self::$sequenceCounter . ". $method_name");
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $subscription_token = NULL) {

    $host = \Drupal::request()->getSchemeAndHttpHost();
    if ($subscription_token && !is_null($subscription_token)) {
      $form['#id'] = 'page-notifications-block-verify';
      $form['subscription_token'] = [
        '#type' => 'hidden',
        '#value' => $subscription_token,
      ];
      $form['email_verify'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Enter your E-mail Address:'),
        '#description' => $this->t('Please enter your email for verification.'),
        '#required' => TRUE,
      ];
      $form['actions'] = [
        '#type' => 'actions',
      ];
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => 'Find my subscribtions',
      ];
      return $form;
    }
    else {
      $form['intro'] = [
        '#markup' => $this->t('<p>You link might be broken or incomplete.</p>'),
      ];
      return $form;
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page-notifications-block-verify';
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $email = $form_state->getValue('email_verify');
    $user_token = $form_state->getValue('subscription_token');
    if (!$this->emailValidator->isValid($form_state->getValue('email_verify')) && !is_null($form_state->getValue('email_verify'))) {
      $form_state->setErrorByName('email', $this->t('That e-mail address is not valid.'));
    }
    else {
      $record = page_notifications_verify_if_record_exist($email, $user_token);
      //$record = \Drupal::service('load.databaseinnfo.service')->verifyByNodeAndEmail($email, $subscription_token);
      if ($record == true) {
        $email_verify = $form_state->getValue('email_verify');
      }
      else {
        $form_state->setErrorByName('email', $this->t('We don\'t have any subscription for this email.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */

  /**
   * Implements ajax submit callback.
   *
   * @param array $form
   *   Form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $page_notifications_user_token = \Drupal::service('load.databaseinnfo.service')->pageNotifyGetUserToken($form_state->getValue('email_verify'));
    $user_token =  $page_notifications_user_token['field_page_notify_token_user_id'];
    $response = $this->redirect(
        'page_notifications.subscriberpage',
        array('user_token' => $user_token),
    );
    $response->send();
  }

  /**
   * Implements submit callback for Rebuild button.
   *
   * @param array $form
   *   Form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Current state of the form.
   */
  public function rebuildFormSubmit(array &$form, FormStateInterface $form_state) {
    $this->displayMethodInvocation('rebuildFormSubmit');
    $form_state->setRebuild(TRUE);
  }
}

function page_notifications_verify_if_record_exist($email, $user_token) {
  $record = current(\Drupal::entityTypeManager()->getStorage('node')
    ->loadByProperties([
      'field_page_notify_token_user_id' => $user_token,
      'field_page_notify_email' => $email,
    ])
  );
  if ($record && !is_null($record)) {
    return TRUE;
  }
  else {
    return FALSE;
  }
}
