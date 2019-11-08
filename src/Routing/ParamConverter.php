<?php

namespace Drupal\easy_config\Routing;

use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\Routing\Route;

/**
 * Class TokenConverter
 */
class ParamConverter implements ParamConverterInterface {

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    \Drupal::logger('my_module2')->debug($name);
    // "profiler" service isn't injected to prevent circular reference when
    // more than one language is active and "Account administration pages" is
    // enabled on admin/config/regional/language/detection. See #2710787 for
    // more information.
    /** @var \Drupal\webprofiler\Profiler\Profiler $profiler */

    $profiler = \Drupal::service('profiler');

    if (NULL === $profiler) {
      return NULL;
    }

    $profile = $profiler->loadProfile($value);

    if (NULL === $profile) {
      return NULL;
    }

    return $profile;
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
//    \Drupal::logger('my_module')->debug($name);
    if (!empty($definition['type']) && $definition['type'] === 'cc') {

      return TRUE;
    }
    return FALSE;
  }

}
