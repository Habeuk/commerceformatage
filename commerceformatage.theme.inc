<?php

/**
 */
function template_preprocess_submit_commerce(&$vars) {
  $vars['attributes'] = $vars['element']['#attributes'];
  $vars['value'] = $vars['element']['#value'];
}

function template_preprocess_commerceformatage_cart_bloc_count(&$vars) {
  // $vars += $vars['element'];
  $vars['content'] = $vars['element']['content'];
}