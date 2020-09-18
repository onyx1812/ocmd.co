<?php

function contactForm(){

	var_dump($_POST);

	wp_die();
}
add_action('wp_ajax_contactForm', 'contactForm');
add_action('wp_ajax_nopriv_contactForm', 'contactForm');