<?php

namespace Drupal\commerceformatage\Plugin\Field\FieldWidget;

use Drupal\address\Plugin\Field\FieldWidget\AddressDefaultWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'address_default' widget.
 *
 * @FieldWidget(
 *   id = "address_default_multicolonnes",
 *   label = @Translation("Address multicolonnes"),
 *   field_types = {
 *     "address"
 *   },
 * )
 */
class AddressMulticolonnesWidget extends AddressDefaultWidget {
  
  /**
   *
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $element['address']['#type'] = 'address_multicolonnes';
    return $element;
  }
  
}