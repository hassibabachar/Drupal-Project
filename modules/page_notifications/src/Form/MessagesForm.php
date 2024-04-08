<?php

namespace Drupal\page_notifications\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @see \Drupal\Core\Form\FormBase
 * @see \Drupal\Core\Form\ConfigFormBase
 */
class MessagesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'markup' => [
        'format' => 'full_html',
        'value' => '',
      ],
    ] + parent::defaultConfiguration();
  }

  /**
   * Counter keeping track of the sequence of method invocation.
   *
   * @var int
   */
  protected static $sequenceCounter = 0;

  /**
   * {@inheritdoc}
   */
  public function __construct() {

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

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $template = \Drupal::service('load.databaseinnfo.service')->get_notify_email_template();
    $form['intro'] = [
      '#markup' => $this->t("<p>Here you can configure, customize your Email Confirmation message and others like status messages that are displayed on pages.</p>")
    ];
    $form['from_email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('The "from" email'),
      '#description' => $this->t('Leave empty to use site email.'),
      '#size' => 60,
      '#maxlength' => 128,
      '#default_value' => isset($template['from_email']) ? $template['from_email'] : '',
      ];
    $form['checkbox_field'] = [
      '#title'  => 'Checkbox field',
      '#description' => $this->t('Enter machine name only! Field that enables email notifications to be sent.'),
      '#type' => 'textfield',
      '#size' => 60,
      '#maxlength' => 128,
      '#default_value' => isset($template['checkbox_field']) ? $template['checkbox_field'] : '',
    ];
    $form['notes_field'] = [
      '#title'  => 'Notes field',
      '#description' => $this->t('Enter machine name only! Field that contains notes or message for subscribers.'),
      '#type' => 'textfield',
      '#size' => 60,
      '#maxlength' => 128,
      '#default_value' => isset($template['notes_field']) ? $template['notes_field'] : '',
    ];
    $form['node_timestamp'] = [
      '#title'  => 'Timestamp',
      '#description' => $this->t('Enter machine name only! Enter field name that will store timestamp for last sent email of the node.'),
      '#type' => 'textfield',
      '#size' => 60,
      '#maxlength' => 128,
      '#default_value' => isset($template['node_timestamp']) ? $template['node_timestamp'] : '',
    ];
    $form['body'] = [
      '#type' => 'text_format',
      '#title' => 'Page Notifications header text',
      '#format' => 'full_html',
      '#default_value' => isset($template['body']) ? $template['body'] : 'Subscribe to [notify_node_title]',
      '#description' => $this->t('Avaliable tokens: [notify_node_title]; [notify_node_url]'),
    ];
    $form['verification_email_subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject of Verification email'),
      '#size' => 60,
      '#maxlength' => 128,
      '#default_value' => isset($template['verification_email_subject']) ? $template['verification_email_subject'] : 'Subscription Confirmation – [notify_node_title]',
      '#description' => $this->t('Avaliable tokens: [notify_node_title]; [notify_user_email]'),
    ];
    $form['verification_email_text'] = [
      '#type' => 'text_format',
      '#title' => 'Body of verification email',
      '#format' => 'full_html',
      '#default_value' => isset($template['verification_email_text']) ? $template['verification_email_text'] : '
      <p>Hello [notify_user_email],</p>
      <p>Please confirm your subscription&nbsp;<a href="[notify_verify_url]">here</a>.</p>
      <p>Once complete, you will receive a "Now Subscribed" email notification.</p>
      <p>Thank you!</p>',
      '#description' => $this->t('Avaliable tokens: [notify_node_title]; [notify_node_url]; [notify_user_email]; [notify_verify_url]'),
    ];
    $form['confirmation_email_subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject of Confirmation email'),
      '#size' => 60,
      '#maxlength' => 128,
      '#default_value' => isset($template['confirmation_email_subject']) ? $template['confirmation_email_subject'] : 'You are now subscribed to - [notify_node_title]',
      '#description' => $this->t('Avaliable tokens: [notify_node_title]; [notify_user_email]'),
    ];
    $form['confirmation_email_text'] = [
      '#type' => 'text_format',
      '#title' => 'Confirmation email',
      '#format' => 'full_html',
      '#default_value' => isset($template['confirmation_email_text']) ? $template['confirmation_email_text'] : '
      <p>Hello [notify_user_email],</p>
      <p>You are now subscribed to <a href="[notify_node_url]">[notify_node_title]</a>.<br />
      <a href="[notify_unsubscribe_url]">Unsubscribe</a> or visit <a href="[notify_user_subscribtions]">Manage your subscriptions</a>.</p>
      <p>Thank you!</p>',
      '#description' => $this->t('Avaliable tokens: [notify_node_title]; [notify_node_url]; [notify_user_email]; [notify_unsubscribe_url]; [notify_user_subscribtions]'),
    ];
    $form['sent_verify_web_page_message'] = [
      '#type' => 'text_format',
      '#title' => 'Web message that verification email was sent.',
      '#format' => 'full_html',
      '#default_value' => isset($template['sent_verify_web_page_message']) ? $template['sent_verify_web_page_message'] : '
      <p>Hey [notify_user_email],</p>
      <p>Please check your email to finalize your subscription!</p>
      <p style="font-size:9px">*If you didn’t get an e-mail, please check the spam folder</p>',
      '#description' => $this->t('Avaliable tokens: [notify_node_title]; [notify_user_email];'),
    ];
    $form['record_exist_verify_web_page_message'] = [
      '#type' => 'text_format',
      '#title' => 'Web message that subscription exist for that email',
      '#format' => 'full_html',
      '#default_value' => isset($template['record_exist_verify_web_page_message']) ? $template['record_exist_verify_web_page_message'] : '
      <p>Hey [notify_user_email],</p>
      <p>You already subscribed to this page!</p>
      <p><a href="[notify_unsubscribe_url]">Unsubscribe from this page</a></p>',
      '#description' => $this->t('Avaliable tokens: [notify_node_title]; [notify_node_url]; [notify_user_email]; [notify_unsubscribe_url].'),
    ];
    $form['error_web_page_message'] = [
      '#type' => 'text_format',
      '#title' => 'Web message for error',
      '#format' => 'full_html',
      '#default_value' => isset($template['error_web_page_message']) ? $template['error_web_page_message'] : '
      <p>Hey [notify_user_email],</p>
      <p>There was an error on this page!</p>',
      '#description' => $this->t('This message will show when recaptcha validation is incorrect.'),
    ];
    $form['subscription_not_available_web_page_message'] = [
      '#type' => 'text_format',
      '#title' => '"Subscription is not available" web message',
      '#format' => 'full_html',
      '#default_value' => isset($template['subscription_not_available_web_page_message']) ? $template['subscription_not_available_web_page_message'] : '<p>Subscription is not available for this page.</p>',
      '#description' => $this->t('This message will be visible on pages that are not nodes and if block is not hidden.'),
    ];

    $form['confirmation_web_page_message'] = [
      '#type' => 'text_format',
      '#title' => 'Confirmation of subscription - web message',
      '#format' => 'full_html',
      '#default_value' => isset($template['confirmation_web_page_message']) ? $template['confirmation_web_page_message'] : '
      <p>Hey [notify_user_email],</p>
      <p>You are all set!</p>
      <p>Thank you for subscribing!</p>
      <p><a href="[notify_user_subscribtions]">Manage your subscriptions</a>.</p>',
      '#description' => $this->t('Avaliable tokens: [notify_node_title]; [notify_node_url]; [notify_user_email]; [notify_unsubscribe_url]; [notify_user_subscribtions]'),
    ];
    $form['general_email_template_subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject of E-mail'),
      '#size' => 60,
      '#maxlength' => 128,
      '#default_value' => isset($template['general_email_template_subject']) ? $template['general_email_template_subject'] : '[notify_node_title] – Notification of New Update',
      '#description' => $this->t('This is the Subject of Verification email that is sent to subscribers. Avaliable tokens: [notify_node_title]; [notify_user_email]'),
    ];
    $form['general_email_template'] = [
      '#type' => 'text_format',
      '#title' => 'Email body template for E-mail.',
      '#format' => 'full_html',
      '#default_value' => isset($template['general_email_template']) ? $template['general_email_template'] : '
      <p>Hello [notify_user_email],</p>
      <p>The "<a href="[notify_node_url]">[notify_node_title]</a>." has been updated.<br />
      If you would like to unsubscribe to this page please go <a href="[notify_user_email]">here</a> or visit <a href="[notify_user_subscribtions]">Manage your subscriptions</a>.</p>
      <p>[notify_notes]</p>
      <p>Thank you!</p>',
      '#description' => $this->t('This is the body of the Notify general email that is sent to subscribers. Avaliable tokens: [notify_node_title]; [notify_node_url]; [notify_user_email]; [notify_unsubscribe_url]; [notify_user_subscribtions]; [notify_notes]'),
    ];
    $form['#cache']['max-age'] = 0;
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
    ];
    $buildInfo = $form_state->getBuildInfo();
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    $this->displayMethodInvocation('getFormId');
    return 'form_api_example_build_form';
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $this->displayMethodInvocation('validateForm');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['body'] = $form_state->getValue('body');
    $this->configuration['verification_email_subject'] = $form_state->getValue('verification_email_subject');
    $this->configuration['verification_email_text'] = $form_state->getValue('verification_email_text');
    $this->configuration['confirmation_email_subject'] = $form_state->getValue('confirmation_email_subject');
    $this->configuration['confirmation_email_text'] = $form_state->getValue('confirmation_email_text');
    $this->configuration['sent_verify_web_page_message'] = $form_state->getValue('sent_verify_web_page_message');
    $this->configuration['record_exist_verify_web_page_message'] = $form_state->getValue('record_exist_verify_web_page_message');
    $this->configuration['error_web_page_message'] = $form_state->getValue('error_web_page_message');
    $this->configuration['subscription_not_available_web_page_message'] = $form_state->getValue('subscription_not_available_web_page_message');
    $this->configuration['confirmation_web_page_message'] = $form_state->getValue('confirmation_web_page_message');
    $this->configuration['general_email_template_subject'] = $form_state->getValue('general_email_template_subject');
    $this->configuration['general_email_template'] = $form_state->getValue('general_email_template');

    $notify_body_info = $form_state->getValue('body');
    $notify_verification_email_subject = $form_state->getValue('verification_email_subject');
    $notify_verification_email_text = $form_state->getValue('verification_email_text');
    $notify_confirmation_email_subject = $form_state->getValue('confirmation_email_subject');
    $notify_confirmation_email_text = $form_state->getValue('confirmation_email_text');
    $notify_sent_verify_web_page_message = $form_state->getValue('sent_verify_web_page_message');
    $notify_record_exist_verify_web_page_message = $form_state->getValue('record_exist_verify_web_page_message');
    $notify_error_web_page_message = $form_state->getValue('error_web_page_message');
    $notify_subscription_not_available_web_page_message = $form_state->getValue('subscription_not_available_web_page_message');
    $notify_confirmation_web_page_message = $form_state->getValue('confirmation_web_page_message');
    $notify_general_email_template_subject = $form_state->getValue('general_email_template_subject');
    $notify_general_email_template = $form_state->getValue('general_email_template');

    $template_exist = \Drupal::service('load.databaseinnfo.service')->get_notify_email_template();
    if ($template_exist) {
      $request_time = Drupal::time()->getRequestTime();
      $query = \Drupal::database()->delete('page_notify_email_template');
      $query->condition('template_id', $template_exist['template_id']);
      $query->execute();
      $query_insert = \Drupal::database()->insert('page_notify_email_template')
        ->fields([
          'from_email' => $form_state->getValue('from_email'),
          'checkbox_field' => $form_state->getValue('checkbox_field'),
          'notes_field' => $form_state->getValue('notes_field'),
          'node_timestamp' => $form_state->getValue('node_timestamp'),
          'created' => $request_time,
          'body' => $notify_body_info['value'],
          'verification_email_subject' => $form_state->getValue('verification_email_subject'),
          'verification_email_text' => $notify_verification_email_text['value'],
          'confirmation_email_subject' => $form_state->getValue('confirmation_email_subject'),
          'confirmation_email_text' => $notify_confirmation_email_text['value'],
          'sent_verify_web_page_message' => $notify_sent_verify_web_page_message['value'],
          'record_exist_verify_web_page_message' => $notify_record_exist_verify_web_page_message['value'],
          'error_web_page_message' => $notify_error_web_page_message['value'],
          'subscription_not_available_web_page_message' => $notify_subscription_not_available_web_page_message['value'],
          'confirmation_web_page_message' => $notify_confirmation_web_page_message['value'],
          'general_email_template_subject' => $form_state->getValue('general_email_template_subject'),
          'general_email_template' => $notify_general_email_template['value'],
        ]);
      $query_insert->execute();
      if($query_insert){
        \Drupal::messenger()->addStatus(t('The configuration options have been saved.'));
      }
    }
    else {
      $request_time = Drupal::time()->getRequestTime();
      $query = \Drupal::database()->insert('page_notify_email_template')
        ->fields([
          'from_email' => $form_state->getValue('from_email'),
          'checkbox_field' => $form_state->getValue('checkbox_field'),
          'notes_field' => $form_state->getValue('notes_field'),
          'node_timestamp' => $form_state->getValue('node_timestamp'),
          'created' => $request_time,
          'body' => $notify_body_info['value'],
          'verification_email_subject' => $form_state->getValue('verification_email_subject'),
          'verification_email_text' => $notify_verification_email_text['value'],
          'confirmation_email_subject' => $form_state->getValue('confirmation_email_subject'),
          'confirmation_email_text' => $notify_confirmation_email_text['value'],
          'sent_verify_web_page_message' => $notify_sent_verify_web_page_message['value'],
          'record_exist_verify_web_page_message' => $notify_record_exist_verify_web_page_message['value'],
          'error_web_page_message' => $notify_error_web_page_message['value'],
          'subscription_not_available_web_page_message' => $notify_subscription_not_available_web_page_message['value'],
          'confirmation_web_page_message' => $notify_confirmation_web_page_message['value'],
          'general_email_template_subject' => $form_state->getValue('general_email_template_subject'),
          'general_email_template' => $notify_general_email_template['value'],
        ]);
      $query->execute();
      if($query){
        \Drupal::messenger()->addStatus(t('The configuration options have been saved.'));
      }
    }
  }
}
