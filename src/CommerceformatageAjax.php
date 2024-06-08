<?php

namespace Drupal\commerceformatage;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\commerce_payment\Plugin\Commerce\CheckoutPane\PaymentInformation;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\RedirectCommand;

/**
 * Bug :
 *
 * @see https://www.drupal.org/project/commerce_shipping/issues/3226851
 * @author stephane
 *        
 */
class CommerceformatageAjax {
  /**
   *
   * @var \Drupal\commerce_order\Entity\Order
   */
  protected static $order;
  
  /**
   * Sauvegarde la methode de paiement
   *
   * @param array $element
   * @param FormStateInterface $form_state
   * @param array $form
   */
  static public function payment_method_callback_validate(array &$element, FormStateInterface $form_state, array $form) {
    if (!empty($form['payment_information']['#payment_options'])) {
      $payment_options = $form['payment_information']['#payment_options'];
      if (!empty($payment_options[$element['#value']])) {
        /**
         *
         * @var \Drupal\commerce_payment\PaymentOption $payment_option
         */
        $payment_option = $payment_options[$element['#value']];
        $order = self::getOrderFromFrom($form_state);
        
        // On recupere la methode selectionner.
        // Cela ne fonctionne pas, car il est modifié ailleurs.
        $order->set('payment_method', $payment_option->getId());
        // On Modifie directement le champs "payment_gateway".
        $order->set('payment_gateway', $payment_option->getPaymentGatewayId());
        $order->save();
        // \Stephane888\Debug\debugLog::kintDebugDrupal($payment_gateway,
        // 'payment_method_callback_validate__PaymentMethod', true);
      }
    }
  }
  
  /**
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @return \Drupal\Core\Ajax\AjaxResponse|mixed|NULL|array
   */
  static public function commerce_checkout_flow_callback(array $form, FormStateInterface $form_state) {
    $field = PaymentInformation::ajaxRefresh($form, $form_state);
    if (!empty($form['payment_information']['#id'])) {
      $response = new AjaxResponse();
      // Mise à jour des champs de paiements.
      $response->addCommand(new ReplaceCommand('#' . $form['payment_information']['#id'], $field));
      // Mise des champs de livraison ( Cette approche ne fonctionne pas ).
      if (!empty($form['shipping_information']['#id---'])) {
        $order = self::getOrderFromFrom($form_state);
        // On reconstruit le shipping
        /**
         *
         * @var \Drupal\commerce_shipping\Entity\Shipment|[] $shipments
         */
        $shipments = $order->get('shipments')->referencedEntities();
        // $db['shipping_information_old'] =
        // $form['shipping_information']['shipments'][0]['shipping_method'];
        // self::rebuildShippinMethod($form['shipping_information'], $shipments,
        // $form_state);
        // self::rebuildShippinMethod2($form['shipping_information'],
        // $shipments, $form_state);
        // $db['shipping_information_new'] =
        // $form['shipping_information']['shipments'][0]['shipping_method'];
        // \Stephane888\Debug\debugLog::$max_depth = 3;
        // \Stephane888\Debug\debugLog::kintDebugDrupal($form,
        // '_commerceformatage_commerce_checkout_flow_callback', true);
        $response->addCommand(new ReplaceCommand('#' . $form['shipping_information']['#id'], $form['shipping_information']));
      }
      // On recharge la page.
      // Cette approche ne fonctionne pas vraiment, mais le garde pour l'instant
      // en attendant.
      $response->addCommand(new RedirectCommand('<current>'));
      return $response;
    }
    else
      return $field;
  }
  
  static protected function rebuildShippinMethod2(&$pane_form, $shipments, FormStateInterface $form_state) {
    $shipment_storage = \Drupal::entityTypeManager()->getStorage('commerce_shipment');
    foreach ($shipments as $index => $shipment) {
      if ($shipment->isNew()) {
        continue;
      }
      // Reload the shipment in case it was updated e.g. the tax adjustments
      // were applied to the shipment .
      $pane_form['shipments'][$index]['#shipment'] = $shipment_storage->load($shipment->id());
    }
  }
  
  static protected function rebuildShippinMethod(&$pane_form, $shipments, FormStateInterface $form_state) {
    $single_shipment = count($shipments) === 1;
    foreach ($shipments as $index => $shipment) {
      /**
       *
       * @var \Drupal\commerce_shipping\Entity\ShipmentInterface $shipment
       */
      $pane_form['shipments'][$index] = [
        '#parents' => array_merge($pane_form['#parents'], [
          'shipments',
          $index
        ]),
        '#array_parents' => array_merge($pane_form['#parents'], [
          'shipments',
          $index
        ]),
        '#type' => $single_shipment ? 'container' : 'fieldset',
        '#title' => $shipment->getTitle()
      ];
      $form_display = EntityFormDisplay::collectRenderDisplay($shipment, 'checkout');
      $form_display->removeComponent('shipping_profile');
      $form_display->buildForm($shipment, $pane_form['shipments'][$index], $form_state);
      $pane_form['shipments'][$index]['#shipment'] = $shipment;
    }
  }
  
  /**
   *
   * @param FormStateInterface $form_state
   * @return \Drupal\commerce_order\Entity\Order
   */
  static public function getOrderFromFrom(FormStateInterface $form_state) {
    if (!self::$order) {
      /**
       *
       * @var \Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\MultistepDefault $getFormObject
       */
      $getFormObject = $form_state->getFormObject();
      /**
       *
       * @var \Drupal\commerce_order\Entity\Order $order
       */
      self::$order = $getFormObject->getOrder();
    }
    return self::$order;
  }
}