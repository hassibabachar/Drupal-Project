<?php

namespace Drupal\page_notifications\Routing;

use Symfony\Component\Routing\Route;

/**
 * Defines dynamic routes for our tab menu items.
 *
 * These routes support the links created in page_notifications.links.task.yml.
 *
 * @see page_notifications.links.task.yml
 * @see https://www.drupal.org/docs/8/api/routing-system/providing-dynamic-routes
 */
class PageNotificationsDynamicRoutes {

  /**
   * Returns an array of route objects.
   *
   * @return \Symfony\Component\Routing\Route[]
   *   An array of route objects.
   */
  public function routes() {
    $routes = [];

    $tabs = [
      'tabs' => 'General configuration',
      'tabs/second' => 'Migrate Subscribtions',
      'tabs/third' => 'Migrate Subscribtions Content Type',
      //'tabs/fourth' => 'Page Notifications - Node Subscribtions List',
      'tabs/default/second' => 'Messages configuration',
      //'tabs/default/third' => 'Third',
    ];

    foreach ($tabs as $path => $title) {
      $machine_name = 'page_notifications.' . str_replace('/', '_', $path);
      $routes[$machine_name] = new Route(

        '/admin/page-notifications/' . $path,
        [
          '_controller' => '\Drupal\page_notifications\Controller\PageNotificationsController::tabsPage',
          '_title' => $title,
          'path' => $path,
          'title' => $title,
        ],
        [
          '_access' => 'TRUE',
        ]
      );
    }

    return $routes;
  }

}
