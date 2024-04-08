<?php

namespace Drupal\page_notifications\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\page_notifications\Form\PageNotificationsBlockForm;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\filter\Element\ProcessedText;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Render\Markup;

/**
 *
 * Provides a 'Page Notifications' block.
 *
 * @Block(
 *   id = "page_notifications",
 *   admin_label = @Translation("Page Notifications"),
 *   category = @Translation("Page Notifications")
 * )
 */

class PageNotificationsBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
   * {@inheritdoc}
   *
   * This method defines form elements for custom block configuration. Standard
   * block configuration fields are added by BlockBase::buildConfigurationForm()
   * (block title and title visibility) and BlockFormController::form() (block
   * visibility settings).
   *
   * @see \Drupal\block\BlockBase::buildConfigurationForm()
   * @see \Drupal\block\BlockFormController::form()
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $blockManager = \Drupal::service('plugin.manager.block');
    $contextRepository = \Drupal::service('context.repository');
    $definitions = $blockManager->getDefinitionsForContexts(
        $contextRepository->getAvailableContexts()
    );
    $buildInfo = $form_state->getBuildInfo();
    return $form;
  }


  /**
   * {@inheritdoc}
   *
   * This method processes the blockForm() form fields when the block
   * configuration form is submitted.
   *
   * The blockValidate() method can be used to validate the form submission.
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

  }


  /**
   * Form builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FormBuilderInterface $form_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    /*$config = $this->getConfiguration();
    $preload_template = \Drupal::service('load.databaseinnfo.service')->get_notify_email_template();
    $notify_settings = \Drupal::service('load.databaseinnfo.service')->get_notify_settings();
    $route_match = \Drupal::routeMatch();

    $output['body'] = [
      '#type' => 'processed_text',
      '#text' => $this->t($preload_template['subscription_not_available_web_page_message']),
      "#processed" => true,
      '#format' => $preload_template['subscription_not_available_web_page_message'],
    ];*/

    $output['form'] = $this->formBuilder->getForm(PageNotificationsBlockForm::class);
    return $output;
  }

  /**
     * {@inheritdoc}
     */
    public function getCacheMaxAge() {
        return 0;
    }

}
