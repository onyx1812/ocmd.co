<?php

function contactForm(){
	$email = $_POST['email'];
	$name = $_POST['name'];
	$phone = $_POST['phone'];
	$message = $_POST['message'];

	$to = "support@sara7skin.com";
	$subject = 'Submiting from contact form sara7skin.com';
	$text = 'email: $email; name: $name; phone: $phone; message: $message';

	$headers = 'From: Contact Form <admin@sara7skin.com>' . "\r\n" .
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
add_action('wp_ajax_contactForm', 'contactForm');
add_action('wp_ajax_nopriv_contactForm', 'contactForm');