<?php

function contactForm(){
  $email = $_POST['email'];
  $name = $_POST['name'];
  $phone = $_POST['phone'];
  $message = $_POST['message'];

  // $to = "support@ocmd.co";
  $to = "onyx18121990@gmail.com";
  $subject = 'Submiting from contact form ocmd.co';
  $text .= 'email: '. $email;
  $text .= 'name: '. $name;
  $text .= 'phone: '. $phone;
  $text .= 'message: '. $message;


  $headers = 'From: Contact Form <webmaster@ocmd.co>' . "\r\n" .
  'Reply-To: $email' . "\r\n" .
  'X-Mailer: PHP/' . phpversion();

  $sending = wp_mail($to, $subject, $text, $headers);

  if($sending){
    echo 'sucess';
  } else {
    echo 'error';
  }

  wp_die();
}
add_action('wp_ajax_contactForm', 'contactForm');
add_action('wp_ajax_nopriv_contactForm', 'contactForm');
