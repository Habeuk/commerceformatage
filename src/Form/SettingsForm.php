<?php

namespace Drupal\commerceformatage\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Commerce Formatage settings.
 */
class SettingsForm extends ConfigFormBase {
  
  /**
   *
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerceformatage_settings';
  }
  
  /**
   *
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'commerceformatage.settings'
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $configs = $this->config('commerceformatage.settings')->getRawData();
    
    $form['commerce'] = [
      '#type' => 'details',
      '#title' => $this->t('Commerce button labels'),
      '#description' => $this->t('Configure the text displayed on Commerce action buttons.'),
      '#open' => true,
      '#tree' => true
    ];
    
    $form['commerce']['text_add_to_cart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Add to cart button label'),
      '#description' => $this->t('Text displayed on the "Add to cart" button on product pages.'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => $configs['commerce']['text_add_to_cart'] ?? 'Add to cart'
    ];
    
    $form['commerce']['checkout_button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Checkout button label'),
      '#description' => $this->t('Text displayed on the checkout button.'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => $configs['commerce']['checkout_button_text'] ?? 'Checkout'
    ];
    
    $form['commerce']['cart_button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('View cart button label'),
      '#description' => $this->t('Text displayed on the "View cart" button.'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => $configs['commerce']['cart_button_text'] ?? 'View cart'
    ];
    
    $form['commerce_style'] = [
      '#type' => 'details',
      '#title' => $this->t('Commerce CSS classes'),
      '#description' => $this->t('Define CSS classes applied to Commerce elements.'),
      '#open' => false,
      '#tree' => true
    ];
    
    $form['commerce_style']['css_add_to_cart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Add to cart button CSS classes'),
      '#description' => $this->t('CSS classes applied to the "Add to cart" button.'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => $configs['commerce_style']['css_add_to_cart'] ?? 'btn btn-primary'
    ];
    
    $form['commerce_style']['css_container'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Buttons container CSS classes'),
      '#description' => $this->t('CSS classes applied to the main buttons wrapper.'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => $configs['commerce_style']['css_container'] ?? 'row g-2 align-items-end'
    ];
    
    $form['commerce_style']['css_container_qty'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Quantity container CSS classes'),
      '#description' => $this->t('CSS classes applied to the quantity field container.'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => $configs['commerce_style']['css_container_qty'] ?? 'col-3'
    ];
    
    $form['commerce_style']['css_container_add_to_cart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Add to cart container CSS classes'),
      '#description' => $this->t('CSS classes applied to the add-to-cart button container.'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => $configs['commerce_style']['css_container_add_to_cart'] ?? 'col'
    ];
    
    $form['commerce_buy_now'] = [
      '#type' => 'details',
      '#title' => $this->t('Buy Now button'),
      '#description' => $this->t('Configure the direct purchase (Buy Now) button.'),
      '#open' => false,
      '#tree' => true
    ];
    
    $form['commerce_buy_now']['enable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Buy Now button'),
      '#description' => $this->t('Enable a direct purchase button that skips the cart.'),
      '#default_value' => $configs['commerce_buy_now']['enable'] ?? FALSE
    ];
    
    $form['commerce_buy_now']['text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Buy Now button label'),
      '#description' => $this->t('Text displayed on the Buy Now button.'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => $configs['commerce_buy_now']['text'] ?? 'Buy Now'
    ];
    
    $form['commerce_buy_now']['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Buy Now button CSS classes'),
      '#description' => $this->t('CSS classes applied to the Buy Now button.'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => $configs['commerce_buy_now']['class'] ?? 'btn btn-outline-secondary'
    ];
    
    return parent::buildForm($form, $form_state);
  }
  
  /**
   * * * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('commerceformatage.settings');
    $config->set('commerce', $form_state->getValue('commerce'));
    $config->set('commerce_style', $form_state->getValue('commerce_style'));
    $config->set('commerce_buy_now', $form_state->getValue('commerce_by_now'));
    $config->save();
    parent::submitForm($form, $form_state);
  }
}
