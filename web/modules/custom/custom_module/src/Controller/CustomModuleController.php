<?php

namespace Drupal\custom_module\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for custom module routes.
 */
class CustomModuleController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
