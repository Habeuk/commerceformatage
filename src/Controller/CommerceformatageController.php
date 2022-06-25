<?php

namespace Drupal\commerceformatage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\commerceformatage\Services\CartsView;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Component\Serialization\Json;

/**
 * Returns responses for commerce formatage routes.
 */
class CommerceformatageController extends ControllerBase {
  
  /**
   *
   * @var CartsView
   */
  protected $CartsView;
  
  /**
   * Constructs a new CartBlock.
   *
   * @param array $configuration
   *        A configuration array containing information about the plugin
   *        instance.
   * @param string $plugin_id
   *        The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *        The plugin implementation definition.
   * @param \Drupal\commerce_cart\CartProviderInterface $cart_provider
   *        The cart provider.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *        The entity type manager.
   */
  public function __construct(CartsView $CartsView) {
    $this->CartsView = $CartsView;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // $instance = parent::create($container);
    return new static($container->get('commerceformatage.cartviews'));
  }
  
  /**
   * Route callback for hiding the Salutation block.
   * Only works for Ajax calls.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function refreshblock(Request $request) {
    if (!$request->isXmlHttpRequest()) {
      throw new NotFoundHttpException();
    }
    $response = new AjaxResponse();
    $build = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#attributes' => [
        'class' => [
          'p-4',
          'bg-primay'
        ],
        'id' => 'commerceformatage_cart_habeuk_view_id'
      ],
      '#value' => 'Mon nv panier : ' . rand(10, 999)
    ];
    $build = $this->CartsView->getCartRender();
    $command = new ReplaceCommand("#commerceformatage_cart_habeuk_view_id", $build);
    $response->addCommand($command);
    return $response;
  }
  
  /**
   *
   * @param Request $request
   * @param string $cart_id
   * @param string $item_id
   */
  public function removeProduct(Request $request, $cart_id, $item_id) {
    $ids = $this->CartsView->removeItemInCart($cart_id, $item_id);
    $configs = [
      'hello',
      $cart_id,
      $item_id,
      $ids
    ];
    return $this->reponse($configs);
  }
  
  /**
   *
   * @param array|string $configs
   * @param number $code
   * @param string $message
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  protected function reponse($configs, $code = null, $message = null) {
    if (!is_string($configs))
      $configs = Json::encode($configs);
    $reponse = new JsonResponse();
    if ($code)
      $reponse->setStatusCode($code, $message);
    $reponse->setContent($configs);
    return $reponse;
  }
  
}
