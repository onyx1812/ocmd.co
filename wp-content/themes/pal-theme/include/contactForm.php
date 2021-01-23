<?php

function contactForm(){
  $email = $_POST['email'];
  $name = $_POST['name'];
  $phone = $_POST['phone'];
  $message = $_POST['message'];
  $date = date("F j, Y, g:i a");

  $to = "support@ocmd.co";
  // $to = "onyx18121990@gmail.com";
  $subject = 'OCMD Support Request <<'.$email.'>> <<'.$date.'>>';
  $text .= 'email: '. $email . "\r\n";
  $text .= 'name: '. $name . "\r\n";
  $text .= 'phone: '. $phone . "\r\n";
  $text .= 'message: '. $message . "\r\n";


  $headers = 'From: OCMD LLC <support@ocmd.co>' . "\r\n" .
  'Reply-To: .'.$email. "\r\n" .
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
