<?php

namespace Drupal\commerceformatage\Plugin\Block;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\commerce_cart\Plugin\Block\CartBlock as commerceCartBlock;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Form\FormStateInterface;
use Drupal\layoutgenentitystyles\Services\LayoutgenentitystylesServices;
use Drupal\commerce_cart\CartProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use KrepyshSpec\World\Currency;
use Drupal\commerce_price\Calculator;

/**
 * Provides a cart bloc complet block.
 *
 * @Block(
 *   id = "commerceformatage_cart_bloc_count",
 *   admin_label = @Translation("cart bloc count"),
 *   category = @Translation("commerceformatage")
 * )
 */
class CartBlocCount extends commerceCartBlock {
  /**
   *
   * @var LayoutgenentitystylesServices
   */
  protected $LayoutgenentitystylesServices;
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->LayoutgenentitystylesServices = $container->get('layoutgenentitystyles.add.style.theme');
    return $instance;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'show_subtotal' => false,
      'block_load_style_scss_js' => 'commerceformatage/cartfloat',
      'class_content' => 'd-flex justify-content-end align-items-center right_menu'
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['show_subtotal'] = [
      '#type' => 'radios',
      '#title' => $this->t('Display sub-total'),
      '#default_value' => (int) $this->configuration['show_subtotal'],
      '#options' => [
        $this->t('No'),
        $this->t('Yes')
      ]
    ];
    $form['class_content'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Set class content'),
      '#default_value' => $this->configuration['class_content']
    ];
    return $form;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function build(): array {
    $cachable_metadata = new CacheableMetadata();
    $cachable_metadata->addCacheContexts([
      'user',
      'session'
    ]);
    
    /** @var \Drupal\commerce_order\Entity\OrderInterface[] $carts */
    $carts = $this->cartProvider->getCarts();
    $carts = array_filter($carts,
      function ($cart) {
        /** @var \Drupal\commerce_order\Entity\OrderInterface $cart */
        // There is a chance the cart may have converted from a draft order, but
        // is still in session. Such as just completing check out. So we verify
        // that the cart is still a cart.
        return $cart->hasItems() && $cart->cart->value;
      });
    
    $count = 0;
    $subTotals = 0;
    
    if (!empty($carts)) {
      foreach ($carts as $cart_id => $cart) {
        /**
         *
         * @var \Drupal\commerce_order\Entity\Order $cart
         */
        foreach ($cart->getItems() as $order_item) {
          $count += (int) $order_item->getQuantity();
        }
        if ($subTotals)
          $subTotals = $subTotals->add($cart->getSubtotalPrice());
        else
          $subTotals = $cart->getSubtotalPrice();
        $cachable_metadata->addCacheableDependency($cart);
      }
    }
    
    $build = [];
    $build['content']['#theme'] = 'commerceformatage_cart_bloc_count';
    $build['content']['#attributes'] = [
      'class' => [
        $this->configuration['class_content']
      ]
    ];
    $build['content'][] = [
      '#theme' => 'commerceformatage_cart_bloc_count_svgback',
      '#count' => '(' . $count . ')'
    ];
    if ($this->configuration['show_subtotal'] && $subTotals) {
      $build['content'][] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#attributes' => [
          'class' => [
            'commerceformatage_cart_habeuk_open',
            'mr-0',
            'ml-2',
            'price-value',
            'd-flex'
          ]
        ],
        '#value' => Calculator::trim($subTotals->getNumber()) . ' ' . $this->getNormalsymbol($subTotals->getCurrencyCode())
      ];
    }
    $build['#cache'] = [
      'contexts' => [
        'cart'
      ]
    ];
    return $build;
  }
  
  /**
   * --
   */
  protected function getNormalsymbol($currencyCode) {
    $currency = \Drupal\commerce_price\Entity\Currency::load($currencyCode);
    if ($currency) {
      return $currency->getSymbol();
    }
    else {
      $symboles = Currency::all();
      if (!empty($symboles[$currencyCode]['symbol'])) {
        return $symboles[$currencyCode]['symbol'];
      }
      else
        return $currencyCode;
    }
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    //
    $this->configuration['show_subtotal'] = $form_state->getValue('show_subtotal');
    $this->configuration['class_content'] = $form_state->getValue('class_content');
    $library = $this->configuration['block_load_style_scss_js'];
    $this->LayoutgenentitystylesServices->addStyleFromModule($library, 'commerceformatage_cart_bloc_complet', 'default');
  }
}
