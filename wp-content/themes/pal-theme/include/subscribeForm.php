<?php

function subscribeForm(){

	/*Send email to support*/
	$email = $_POST['email'];

	$to = "support@ocmd.co";
	// $to = "onyx18121990@gmail.com";
	$subject = 'Subscribing from subscribe form ocmd.co';
	$text = 'email: '. $email;

	$headers = 'From: Subscribe Form <admin@ocmd.co>' . "\r\n" .
	'Reply-To: '.$email. "\r\n" .
	'X-Mailer: PHP/' . phpversion();

	$sending = wp_mail($to, $subject, $text, $headers);

	if($sending){
		echo 'sucess';
	} else {
		echo 'error';
	}

	// /*Send email to sibscriber*/
	// $email = $_POST['email'];

	// $to = $email;
	// $subject = 'Subscribing from subscribe form ocmd.co';
	// $text = 'email: '. $email;

	// $headers = 'From: Subscribe Form <admin@ocmd.co>' . "\r\n" .
	// 'Reply-To: '.$email.'' . "\r\n" .
	// 'X-Mailer: PHP/' . phpversion();

	// $sending = wp_mail($to, $subject, $text, $headers);

	// if($sending){
	// 	echo 'sucess';
	// } else {
	// 	echo 'error';
	// }

	wp_die();
}
add_action('wp_ajax_subscribeForm', 'subscribeForm');
add_action('wp_ajax_nopriv_subscribeForm', 'subscribeForm');