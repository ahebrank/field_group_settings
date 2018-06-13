<?php

namespace Drupal\field_group_settings\Plugin\field_group\FieldGroupFormatter;

use Drupal\Core\Render\Element;
use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'settings' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "settings",
 *   label = @Translation("Settings"),
 *   description = @Translation("Renders a field group as collapsible settings."),
 *   supported_contexts = {
 *     "form",
 *   }
 * )
 */
class Settings extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function preRender(&$element, $rendering_object) {

    parent::preRender($element, $rendering_object);

    $element += [
      '#type' => 'field_group_settings',
      '#options' => [
        'attributes' => [
          'class' => $this->getClasses(),
        ],
      ],
      '#access' => $this->isVisible(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm() {
    $form = parent::settingsForm();

    $roles = user_role_names(TRUE);
    // remove the admin -- that's always allowed
    unset($roles['administrator']);

    $form['visible_for_roles'] = [
      '#title' => $this->t('Roles that can view'),
      '#type' => 'checkboxes',
      '#options' => $roles,
      '#default_value' => $this->getSetting('visible_for_roles'),
      '#weight' => 2,
      '#description' => $this->t('Always visible for admin'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {

    // map allowed roles to their names
    $role_names = user_role_names(TRUE);
    $allowed_role_names = array_map(function($role_id) use ($role_names) {
      return $role_names[$role_id];
    }, array_filter($this->getSetting('visible_for_roles')));

    $summary = [];
    if ($allowed_role_names) {
      $summary[] = $this->t('Visible for: @roles',
        ['@roles' => implode(', ', $allowed_role_names)]
      );
    }

    return $summary;
  }

  protected function isVisible() {
    $user_roles = \Drupal::currentUser()->getRoles();
    if (in_array('administrator', $user_roles)) {
      return true;
    }
    $allowed = array_filter($this->getSetting('visible_for_roles'));
    $match = array_intersect($user_roles, $allowed);
    return (count($match) > 0);
  }

  /**
   * {@inheritdoc}
   */
  protected function getClasses() {
    $classes = ['field-group-settings'];
    $classes = array_merge($classes, parent::getClasses());
    return $classes;
  }
}
