<?php

function subscribeForm(){
	$email = $_POST['email'];

	$to = "support@sara7skin.com";
	$subject = 'Subscribing from sara7skin.com';
	$text = 'email: $email';

	$headers = 'From: Subscribe Form <admin@sara7skin.com>' . "\r\n" .
	'Reply-To: $email' . "\r\n" .
	'X-Mailer: PHP/' . phpversion();

	$sending = mail($to, $subject, $text, $headers);

	if($sending){
		echo 'sucess';
	} else {
		echo 'error';
	}

	wp_die();
}
add_action('wp_ajax_subscribeForm', 'subscribeForm');
add_action('wp_ajax_nopriv_subscribeForm', 'subscribeForm');