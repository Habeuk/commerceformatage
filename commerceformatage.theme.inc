<?php

/**
 */
function template_preprocess_submit_commerce(&$vars) {
  // dump($vars);
}

function template_preprocess_commerceformatage_cart_bloc_count(&$vars) {
  // $vars += $vars['element'];
  $vars['content'] = $vars['element']['content'];
}