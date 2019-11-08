<?php

namespace Drupal\easy_config\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Change path '/user/login' to '/login'.
//    if ($route = $collection->get('user.login')) {
////      $route->setPath('/login');
//    }
    // Always deny access to '/user/logout'.
    // Note that the second parameter of setRequirement() is a string.
    if ($route = $collection->get('entity.easy_config.edit_form')) {
//      $route->setRequirement('_access', 'FALSE');
    }
  }

}
