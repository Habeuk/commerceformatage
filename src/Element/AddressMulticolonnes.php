<?php

namespace Drupal\commerceformatage\Element;

use Drupal\address\Element\Address;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an address form element.
 *
 * Use #field_overrides to override the country-specific address format,
 * forcing specific properties to be hidden, optional, or required.
 *
 * Usage example:
 *
 * @code
 * $form['address'] = [
 *   '#type' => 'address',
 *   '#default_value' => [
 *     'given_name' => 'John',
 *     'family_name' => 'Smith',
 *     'organization' => 'Google Inc.',
 *     'address_line1' => '1098 Alta Ave',
 *     'postal_code' => '94043',
 *     'locality' => 'Mountain View',
 *     'administrative_area' => 'CA',
 *     'country_code' => 'US',
 *     'langcode' => 'en',
 *   ],
 *   '#field_overrides' => [
 *     AddressField::ORGANIZATION => FieldOverride::REQUIRED,
 *     AddressField::ADDRESS_LINE2 => FieldOverride::HIDDEN,
 *     AddressField::POSTAL_CODE => FieldOverride::OPTIONAL,
 *   ],
 *   '#available_countries' => ['DE', 'FR'],
 * ];
 * @endcode
 *
 * @FormElement("address_multicolonnes")
 */
class AddressMulticolonnes extends Address {
  
  /**
   * Retourne tous les elements sur deux colonnes.
   *
   * @param array $element
   * @param FormStateInterface $form_state
   * @param array $complete_form
   * @return array|number|string|string[][]
   */
  public static function processAddress(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $element = parent::processAddress($element, $form_state, $complete_form);
    /**
     * Au final modifier juste la structure à ce niveau est une mauvaise idée,
     * car cela va entrainner des changements dans le formatage des données.
     */
    // Ne fonctionne pas une foix qu'ajax c'est executé, mais peut s'aranger au
    // niveau de la fonction ajax.
    // if (!empty($element['country_code']) && !empty($element['locality'])) {
    // $element['country_code_locality'] = [
    // '#type' => 'container',
    // '#attributes' => [
    // 'class' => [
    // 'row'
    // ]
    // ],
    // "#weight" => -10
    // ];
    // $element['country_code']['#attributes']['class'][] = "col-md-6";
    // $element['locality']['#class_wrappers'] = "col-md-6";
    // $element['country_code_locality']['country_code'] =
    // $element['country_code'];
    // $element['country_code_locality']['locality'] = $element['locality'];
    // unset($element['country_code']);
    // unset($element['locality']);
    // }
    // if (!empty($element['given_name']) && !empty($element['family_name'])) {
    // $element['given_name__family_name'] = [
    // '#type' => 'container',
    // '#attributes' => [
    // 'class' => [
    // 'row'
    // ]
    // ],
    // "#weight" => -8
    // ];
    // $element['given_name']['#attributes']['class'][] = "col-md-6";
    // $element['locality']['#class_wrappers'] = "col-md-6";
    // $element['given_name__family_name']['given_name'] =
    // $element['given_name'];
    // $element['given_name__family_name']['family_name'] =
    // $element['family_name'];
    // unset($element['given_name']);
    // unset($element['family_name']);
    // }
    // Ne fonctionne pas car on perd l'attribut name.
    // if (!empty($element['country_code']) && !empty($element['locality'])) {
    // $element['country_code_locality'] = [
    // '#theme' => 'commerceformatage_address_2colonnes',
    // "#weight" => -10,
    // '#col_left' => $element['country_code'],
    // '#col_right' => $element['locality']
    // ];
    // unset($element['country_code']);
    // unset($element['locality']);
    // }
    // dump($element);
    return $element;
  }
  
}