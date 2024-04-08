<?php

namespace Drupal\page_notifications\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @see \Drupal\Core\Form\FormBase
 * @see \Drupal\Core\Form\ConfigFormBase
 */
class GeneralSettingsForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    return [
      'markup' => [
        'format' => 'full_html',
        'value' => '',
      ],
    ] + parent::defaultConfiguration();
  }

  /**
   * @var int
   */
  protected static $sequenceCounter = 0;

  /**
   * {@inheritdoc}
   */

  /**
   * Update form processing information.
   *
   * Display the method being called and it's sequence in the form
   * processing.
   *
   * @param string $method_name
   *   The method being invoked.
   */
  private function displayMethodInvocation($method_name)
  {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $notify_settings = \Drupal::service('load.databaseinnfo.service')->get_notify_settings();

    $moduleHandler = \Drupal::service('module_handler');
    if ($moduleHandler->moduleExists('recaptcha')) {
      $form['page_notify_recaptcha'] = [
        '#type' => 'checkbox',
        '#title' => t('Enable/Disable reCaptcha'),
        '#description' => $this->t('Strongly recommened to use reCaptcha or Captcha for this module.'),
        '#default_value' => $notify_settings['page_notify_recaptcha'] ? $notify_settings['page_notify_recaptcha'] : 0,
        '#weight' => 0,
      ];
    }
    if ($moduleHandler->moduleExists('captcha')) {
      $form['page_notify_captcha'] = [
        '#type' => 'checkbox',
        '#title' => t('Enable/Disable Captcha'),
        '#description' => $this->t('Strongly recommened to use reCaptcha or Captcha for this module.'),
        '#default_value' => $notify_settings['page_notify_captcha'] ? $notify_settings['page_notify_captcha'] : 0,
        '#weight' => 0,
      ];
    }
    $form['page_notify_subscribers_count'] = [
      '#type' => 'checkbox',
      '#title' => t('Show number of subscribers on node edit.'),
      '#description' => $this->t('It will output number of subscribers on the node when editing that node.'),
      '#default_value' => $notify_settings['page_notify_subscribers_count'] ? $notify_settings['page_notify_subscribers_count'] : 0,
      '#weight' => 1,
    ];
    $form['enable_message_subscription_not_available'] = [
      '#type' => 'checkbox',
      '#title' => t('Display message when functionality is not available.'),
      '#description' => $this->t('The message displayed in "Subscription is not available web message" field in Messages configuration settings.'),
      '#default_value' => $notify_settings['enable_message_subscription_not_available'] ? $notify_settings['enable_message_subscription_not_available'] : 0,
      '#weight' => 1,
    ];
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Save Confirmation',
    ];
    $buildInfo = $form_state->getBuildInfo();
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    $this->displayMethodInvocation('getFormId');
    return 'notify_general_congig_form';
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $this->displayMethodInvocation('validateForm');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $notify_settings = \Drupal::service('load.databaseinnfo.service')->get_notify_settings();

    if ($notify_settings) {
      $query = \Drupal::database()->update('page_notify_settings')
        ->fields([
          'page_notify_settings_group_name' => 'page_notify_general_settings',
          'page_notify_recaptcha' => $form_state->getValue('page_notify_recaptcha'),
          'page_notify_captcha' => $form_state->getValue('page_notify_captcha'),
          'page_notify_subscribers_count' => $form_state->getValue('page_notify_subscribers_count'),
          'enable_message_subscription_not_available' => $form_state->getValue('enable_message_subscription_not_available'),
        ])
        ->condition('page_notify_settings_group_name', 'page_notify_general_settings')
        ->execute();
      \Drupal::messenger()->addStatus(t('The configuration options have been saved.'));
    } else {
      $query = \Drupal::database()->insert('page_notify_settings')
        ->fields([
          'page_notify_settings_group_name' => 'page_notify_general_settings',
          'page_notify_recaptcha' => $form_state->getValue('page_notify_recaptcha'),
          'page_notify_captcha' => $form_state->getValue('page_notify_captcha'),
          'page_notify_subscribers_count' => $form_state->getValue('page_notify_subscribers_count'),
          'enable_message_subscription_not_available' => $form_state->getValue('enable_message_subscription_not_available'),
        ]);
      $query->execute();
    }
    \Drupal::messenger()->addStatus(t('The configuration options have been saved.'));
  }
}
