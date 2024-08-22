<?php

namespace Drupal\commerceformatage\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure commerce formatage settings for this site.
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
    $configs = $this->config("commerceformatage.settings")->getRawData();
    $form['commerce'] = [
      '#type' => 'details',
      '#title' => 'Commerce configs',
      '#open' => true,
      '#tree' => true
    ];
    $form['commerce']['texte_add_to_cart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Texte &#039;add to cart&#039;'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => isset($configs['commerce']['texte_add_to_cart']) ? $configs['commerce']['texte_add_to_cart'] : 'Add to cart'
    ];
    $form['commerce']['checkout_button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Texte "Passer la commande"'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => isset($configs['commerce']['checkout_button_text']) ? $configs['commerce']['checkout_button_text'] : 'To order'
    ];
    $form['commerce']['cart_button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Texte "Voir le panier"'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => isset($configs['commerce']['cart_button_text']) ? $configs['commerce']['cart_button_text'] : 'See cart'
    ];
    
    $form['commerce_style'] = [
      '#type' => 'details',
      '#title' => 'Commerce CSS class',
      '#open' => false,
      '#tree' => true
    ];
    $form['commerce_style']['css_add_to_cart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Css button add to cart'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => isset($configs['commerce_style']['css_add_to_cart']) ? $configs['commerce_style']['css_add_to_cart'] : 'btn btn-primary'
    ];
    $form['commerce_style']['css_container'] = [
      '#type' => 'textfield',
      '#title' => $this->t('css_container'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => isset($configs['commerce_style']['css_container']) ? $configs['commerce_style']['css_container'] : 'row no-gutters g-2 d-flex align-items-end'
    ];
    $form['commerce_style']['css_container_qty'] = [
      '#type' => 'textfield',
      '#title' => $this->t('css_container_qty'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => isset($configs['commerce_style']['css_container_qty']) ? $configs['commerce_style']['css_container_qty'] : 'col-3'
    ];
    $form['commerce_style']['css_container_add_to_cart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('css_container_add_to_cart'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => isset($configs['commerce_style']['css_container_add_to_cart']) ? $configs['commerce_style']['css_container_add_to_cart'] : 'col'
    ];
    $form['commerce_by_now'] = [
      '#type' => 'details',
      '#title' => 'commerce_by_now',
      '#open' => false,
      '#tree' => true
    ];
    $form['commerce_by_now']['enable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('enable'),
      '#default_value' => isset($configs['commerce_by_now']['enable']) ? $configs['commerce_by_now']['enable'] : false
    ];
    $form['commerce_by_now']['text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('text'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => isset($configs['commerce_by_now']['text']) ? $configs['commerce_by_now']['text'] : 'Buy Now'
    ];
    $form['commerce_by_now']['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Class'),
      '#maxlength' => 250,
      '#size' => 64,
      '#default_value' => isset($configs['commerce_by_now']['class']) ? $configs['commerce_by_now']['class'] : 'btn btn-outline-secondary'
    ];
    return parent::buildForm($form, $form_state);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    //
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('commerceformatage.settings');
    $config->set('commerce', $form_state->getValue('commerce'));
    $config->set('commerce_style', $form_state->getValue('commerce_style'));
    $config->set('commerce_by_now', $form_state->getValue('commerce_by_now'));
    $config->save();
    parent::submitForm($form, $form_state);
  }
}
