<?php

namespace Drupal\commerceformatage\Plugin\Block;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\commerce_cart\Plugin\Block\CartBlock as commerceCartBlock;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Form\FormStateInterface;
use Drupal\layoutgenentitystyles\Services\LayoutgenentitystylesServices;
use Drupal\commerce_cart\CartProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\Core\Render\Markup;
use Drupal\commerceformatage\Services\CartsView;

/**
 * Provides a cart bloc complet block.
 *
 * @Block(
 *   id = "commerceformatage_cart_bloc_complet",
 *   admin_label = @Translation("cart bloc complet"),
 *   category = @Translation("commerceformatage")
 * )
 */
class CartBlocCompletBlock extends commerceCartBlock {
  
  /**
   *
   * @var LayoutgenentitystylesServices
   */
  protected $LayoutgenentitystylesServices;
  
  /**
   *
   * @var CartsView
   */
  protected $CartsView;
  
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CartProviderInterface $cart_provider, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $cart_provider, $entity_type_manager);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->LayoutgenentitystylesServices = $container->get('layoutgenentitystyles.add.style.theme');
    $instance->CartsView = $container->get('commerceformatage.cartviews');
    return $instance;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function build() {
    $build[] = [
      '#type' => 'html_tag',
      '#tag' => 'i',
      '#attributes' => [
        'class' => [
          'fa-times',
          'fas',
          'commerceformatage_cart_habeuk_close'
        ]
      ]
    ];
    
    $build[] = $this->CartsView->getCartRender();
    //
    $url = Url::fromRoute('commerceformatage.refreshblock');
    $url->setOption('attributes', [
      'class' => 'use-ajax commerceformatage_cart_habeuk_click'
    ]);
    $build[] = [
      '#type' => 'link',
      '#url' => $url,
      '#title' => $this->t('Reload bloc with ajax')
    ];
    return [
      '#theme' => 'container',
      "#children" => $build,
      '#attributes' => [
        'class' => [
          'commerceformatage_cart_habeuk',
          'show0'
        ]
      ]
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'block_load_style_scss_js' => 'commerceformatage/cartfloat'
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $library = $this->configuration['block_load_style_scss_js'];
    $this->LayoutgenentitystylesServices->addStyleFromModule($library, 'commerceformatage_cart_bloc_complet', 'default');
  }
  
}
