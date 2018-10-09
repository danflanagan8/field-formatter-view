<?php

namespace Drupal\field_formatter_view\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\views\Views;

/**
 *
 * @FieldFormatter(
 *   id = "field_formatter_view",
 *   label = @Translation("View"),
 *   description = @Translation("Pass the entity ids to a View."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class FieldFormatterView extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'view' => '',
      'link' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $views = Views::getViewsAsOptions();
    $elements['view'] = [
      '#type' => 'select',
      '#options' => $views,
      '#title' => t('View'),
      '#default_value' => $this->getSetting('view'),
      '#required' => TRUE,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = t('View: ' . $this->getSetting('view'));

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $ids = [];

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      $ids[] = $entity->id();
    }
    $args = implode($ids, '+');

    $view_parts = explode(':', $this->getSetting('view'));
    $view_id = $view_parts[0];
    $display_id = $view_parts[1];
    $view = views_embed_view($view_id, $display_id, $args);

    return $view;
  }


}
