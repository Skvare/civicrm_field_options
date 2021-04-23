<?php

namespace Drupal\civicrm_field_options\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'civicrm_field_option' field widget.
 *
 * @FieldWidget(
 *   id = "civicrm_field_option_widget",
 *   label = @Translation("CiviCRM Option widget"),
 *   field_types = {
 *     "civicrm_field_option",
 *   }
 * )
 */
class OptionWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $settings = $items->getFieldDefinition()->getSettings();
    $field_option_group = $settings['option_group_id'];
    $optionList = [];
    if (empty($field_option_group)) {
      return $element;
    }

    \Drupal::service('civicrm')->initialize();
    $options = civicrm_api('OptionValue', 'get', ['version' => 3, 'is_active' => 1, 'option_group_id' => $field_option_group, 'option.limit' => 1000]);
    foreach ($options['values'] as $option) {
      $optionList[$option['value']] = $option['label'];
    }
    $default = (isset($items[$delta]->value) && isset($optionList[$items[$delta]->value])) ? $items[$delta]->value : NULL;

    $element['value'] = $element + [
        '#type' => 'select',
        '#options' => $optionList,
        '#empty_value' => '',
        '#default_value' => $default,
      ];
    // Return element(s).
    return $element;
  }

}
