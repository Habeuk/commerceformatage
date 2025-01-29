<?php

namespace Drupal\commerceformatage\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\commerce_cart\CartProviderInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\commerce_cart\CartManager;
use Stephane888\Debug\Repositories\ConfigDrupal;

/**
 * Permet d'afficher et de gerer un panier.
 *
 * @author stephane
 *        
 */
class CartsView {
  /**
   * The cart provider.
   *
   * @var \Drupal\commerce_cart\CartProvider
   */
  protected $cartProvider;
  protected $CartManager;
  
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  
  function __construct(CartProviderInterface $cart_provider, EntityTypeManagerInterface $entity_type_manager, CartManager $CartManager) {
    $this->cartProvider = $cart_provider;
    $this->entityTypeManager = $entity_type_manager;
    $this->CartManager = $CartManager;
  }
  
  /**
   *
   * @param integer $commerce_order_id
   * @param integer $commerce_order_item_id
   * @return array
   */
  function removeItemInCart($commerce_order_id, $commerce_order_item_id) {
    $carts = $this->getCarts();
    $ids = [];
    foreach ($carts as $cart) {
      if ($cart->id() == $commerce_order_id) {
        $items = $cart->getItems();
        if ($items) {
          foreach ($items as $item) {
            /**
             *
             * @var $item \Drupal\commerce_order\Entity\OrderItem
             */
            if ($item->id() == $commerce_order_item_id) {
              $this->CartManager->removeOrderItem($cart, $item);
              $ids[] = [
                'card_id' => $commerce_order_id,
                'remove_order_item' => $commerce_order_item_id
              ];
            }
          }
        }
      }
    }
    return $ids;
  }
  
  /**
   *
   * @return \Drupal\commerce_order\Entity\OrderInterface[]
   */
  protected function getCarts() {
    /** @var \Drupal\commerce_order\Entity\OrderInterface[] $carts */
    $carts = $this->cartProvider->getCarts();
    //
    $carts = array_filter($carts,
      function ($cart) {
        /** @var \Drupal\commerce_order\Entity\OrderInterface $cart */
        // There is a chance the cart may have converted from a draft order, but
        // is still in session. Such as just completing check out. So we verify
        // that the cart is still a cart.
        return $cart->hasItems() && $cart->cart->value;
      });
    return $carts;
  }
  
  /**
   *
   * @return string[]
   *
   */
  function getCartRender() {
    /**
     * Il faudra un update afin de recuperer la configuration definie dans
     * wb_horizon_public pour commerceformatage et supprimer la verification du
     * module wb_horizon_public
     */
    if (\Drupal::moduleHandler()->moduleExists("wb_horizon_public")) {
      $configs = ConfigDrupal::config('wb_horizon_public.defaultconfigbydomain');
    }
    else {
      $configs = ConfigDrupal::config('commerceformatage.settings');
    }
    
    if (!empty($configs['commerce']['cart_button_text'])) {
      $cart_button_text = $configs['commerce']['cart_button_text'];
      $checkout_button_text = $configs['commerce']['checkout_button_text'];
    }
    else {
      $cart_button_text = 'See cart';
      $checkout_button_text = 'Checkout';
    }
    $cachable_metadata = new CacheableMetadata();
    $cachable_metadata->addCacheContexts([
      'user',
      'session'
    ]);
    
    $carts = $this->getCarts();
    
    $url = Url::fromRoute('commerce_checkout.checkout');
    $url->setOption('attributes', [
      'class' => 'btn btn-primary mr-4'
    ]);
    //
    $urlCart = Url::fromRoute('commerce_cart.page');
    $urlCart->setOption('attributes', [
      'class' => 'btn btn-link px-0'
    ]);
    
    if (!empty($carts)) {
      $build['cart'] = [
        '#type' => 'html_tag',
        '#tag' => 'section',
        '#attributes' => [
          'id' => 'commerceformatage_cart_habeuk_view_id'
        ],
        $this->getCartViews($carts),
        [
          '#type' => 'html_tag',
          '#tag' => 'section',
          '#attributes' => [
            'class' => [
              'd-flex',
              'my-5',
              'justify-content-between'
            ]
          ],
          [
            '#type' => 'link',
            '#url' => $urlCart,
            '#title' => Markup::create(
              '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="2rem" height="2rem" class="me-3" style="fill:currentColor;"> <path d="M0 32C0 14.3 14.3 0 32 0H48c44.2 0 80 35.8 80 80V368c0 8.8 7.2 16 16 16H608c17.7 0 32 14.3 32 32s-14.3 32-32 32H541.3c1.8 5 2.7 10.4 2.7 16c0 26.5-21.5 48-48 48s-48-21.5-48-48c0-5.6 1-11 2.7-16H253.3c1.8 5 2.7 10.4 2.7 16c0 26.5-21.5 48-48 48s-48-21.5-48-48c0-5.6 1-11 2.7-16H144c-44.2 0-80-35.8-80-80V80c0-8.8-7.2-16-16-16H32C14.3 64 0 49.7 0 32zM432 96V56c0-4.4-3.6-8-8-8H344c-4.4 0-8 3.6-8 8V96h96zM288 96V56c0-30.9 25.1-56 56-56h80c30.9 0 56 25.1 56 56V96 320H288V96zM512 320V96h16c26.5 0 48 21.5 48 48V272c0 26.5-21.5 48-48 48H512zM240 96h16V320H240c-26.5 0-48-21.5-48-48V144c0-26.5 21.5-48 48-48z"/></svg>' . t(
                $cart_button_text))
          ],
          [
            '#type' => 'link',
            '#url' => $url,
            '#title' => Markup::create(
              t($checkout_button_text) . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="2rem" height="2rem" class="ms-3" style="fill:currentColor;"><path d="M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l370.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z"/></svg>')
          ]
        ]
      ];
    }
    else
      $build['empty'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => t('Votre panier est vide.'),
        '#attributes' => [
          'id' => 'commerceformatage_cart_habeuk_view_id',
          'class' => [
            'hello',
            'px-4',
            'py-3'
          ]
        ]
      ];
    return $build;
  }
  
  /**
   * Gets the cart views for each cart.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface[] $carts
   *        The cart orders.
   *        
   * @return array An array of view ids keyed by cart order ID.
   */
  protected function getCartViews(array $carts) {
    $cart_views = [];
    //
    $order_type_ids = array_map(function ($cart) {
      return $cart->bundle();
    }, $carts);
    $order_type_storage = $this->entityTypeManager->getStorage('commerce_order_type');
    $order_types = $order_type_storage->loadMultiple(array_unique($order_type_ids));
    
    $available_views = [];
    foreach ($order_type_ids as $cart_id => $order_type_id) {
      /** @var \Drupal\commerce_order\Entity\OrderTypeInterface $order_type */
      $order_type = $order_types[$order_type_id];
      $available_views[$cart_id] = $order_type->getThirdPartySetting('commerce_cart', 'cart_block_view', 'commerce_cart_block');
    }
    
    foreach ($carts as $cart_id => $cart) {
      $cart_views[] = [
        '#prefix' => '<div class="cart cart-block" data_cart_id="' . $cart_id . '">',
        '#suffix' => '</div>',
        '#type' => 'view',
        '#name' => $available_views[$cart_id],
        '#arguments' => [
          $cart_id
        ],
        '#embed' => TRUE
      ];
    }
    return $cart_views;
  }
}