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
  
  function CreatePaymentMethode(\Drupal\commerce_stripe\Event\PaymentIntentCreateEvent $event) {
    $this->paymentMethod = $event->getPayment();
    // On desactive la reutilisation de la methode de paiement.
    if ($this->paymentMethod)
      $this->paymentMethod->setReusable(FALSE);
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