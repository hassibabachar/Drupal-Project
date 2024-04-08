<?php

namespace Drupal\page_notifications\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\Element\EntityAutocomplete;

/**
 * Defines a route controller for watches autocomplete form elements.
 */
class SubscriptionsAutoCompleteController extends ControllerBase {

  /**
   * The node storage.
   *
   * @var \Drupal\node\NodeStorage
   */
  protected $nodeStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->nodeStroage = $entity_type_manager->getStorage('node');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Handler for autocomplete request.
   */
  public function handleAutocomplete(Request $request) {
    $results = [];
    $input = $request->query->get('q');

    if (!$input) {
      return new JsonResponse($results);
    }

    $input = Xss::filter($input);

    $query = $this->nodeStroage->getQuery()
      ->condition('title', $input, 'CONTAINS')
      ->groupBy('nid')
      ->sort('created', 'DESC')
      ->range(0, 10);

    $ids = $query->execute();
    $nodes = $ids ? $this->nodeStroage->loadMultiple($ids) : [];

    foreach ($nodes as $node) {
      switch ($node->isPublished()) {
        case TRUE:
          $availability = '✅';
          break;

        case FALSE:
        default:
          $availability = '🚫';
          break;
      }

      $label = [
        $node->getTitle(),
        '<small>(' . $node->id() . ')</small>',
        $availability,
      ];

      $results[] = [
        'value' => EntityAutocomplete::getEntityLabels([$node]),
        'label' => implode(' ', $label),
      ];
    }
    return new JsonResponse($results);
  }
}
