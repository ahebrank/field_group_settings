<?php

namespace Drupal\field_group_settings\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a settings render element for Field Groups.
 *
 * @RenderElement("field_group_settings")
 */
class Settings extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class();

    return [
      '#process' => [
        [$class, 'processGroup'],
      ],
      '#pre_render' => [
        [$class, 'preRenderGroup'],
      ],
      '#theme_wrappers' => ['field_group_settings'],
    ];
  }

}
