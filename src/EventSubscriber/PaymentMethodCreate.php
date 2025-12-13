<?php

namespace Drupal\commerceformatage\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\commerce_stripe\Event\StripeEvents;

class PaymentMethodCreate implements EventSubscriberInterface {
  /**
   * The payment method.
   *
   * @var \Drupal\commerce_payment\Entity\PaymentMethodInterface
   */
  protected $paymentMethod;

  function CreatePaymentMethode($event) {
    \Drupal::messenger()->addStatus(" commerceformatage::Run event subscriber ");
    \Stephane888\Debug\debugLog::kintDebugDrupal($event, 'CreatePaymentMethode', true);
    $this->paymentMethod = $event->getPaymentMethod();
    // On desactive la reutilisation de la methode de paiement.
    $this->paymentMethod->setReusable(FALSE);
    // dd($paymentMethod);
  }

  /**
   *
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      StripeEvents::PAYMENT_INTENT_CREATE => [
        'CreatePaymentMethode'
      ]
    ];
  }
}