<?php
/*
 * Template Name: Contact
 */
get_header();
while ( have_posts() ) : the_post();



    // $email = 'max@purpleadlab.com';
    // $name = 'Max';
    // $message = 'Hello world';

    // $to = "onyx18121990@gmail.com";
    // $subject = "Your order has been sent";
    // $text =  $message;

    // $headers = 'From: Thezense team <webmaster@ocmd.co>' . "\r\n" .
    // 'Reply-To: webmaster@ocmd.co' . "\r\n" .
    // 'X-Mailer: PHP/' . phpversion();

    // $sending = mail($to, $subject, $text, $headers);

    // if($sending) echo "Email sent!";



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
    $mail->setLanguage('ru', 'vendor/phpmailer/phpmailer/language/'); // Перевод на русский язык

    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 1;                                 // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP

    $mail->SMTPAuth = true;                               // Enable SMTP authentication

    //$mail->SMTPSecure = 'ssl';                          // secure transfer enabled REQUIRED for Gmail
    //$mail->Port = 465;                                  // TCP port to connect to
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
    $mail->Username = 'onyx18121990@gmail.com';               // SMTP username
    $mail->Password = '9J8iak45@@';                         // SMTP password

    //Recipients
    $mail->setFrom('webmaster@ocmd.co', 'webmaster');
    $mail->addAddress('webmaster@ocmd.co');              // Name is optional

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Test mail to user';
    $mail->Body    = 'This is the very simple HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}



  ?>

<section class="page-contact">
  <div class="container">
    <h1 class="section-title" style="text-align: center;">Contact Us</h1>
    <h3>Your Satisfaction Is Our Priority!</h3>
    <p class="text-center">We’d love to hear from you and answer any questions you might have about our products or services. <br>Feel free to contact us anytime!</p>
    <p>&nbsp;</p>
    <p>
      Give Us a Call: <br>
      America: <a href="tel:8772310127">(877) 231-0127</a>
    </p>
    <h4>Or Drop a message:</h4>
    <form class="contact row" id="contact">
      <div class="field col-sm-6">
        <label for="name">Name</label>
        <input type="text" name="name" id="name">
      </div>
      <div class="field col-sm-6">
        <label for="email">Email*</label>
        <input type="email" name="email" id="email" required>
      </div>
      <div class="field col-12">
        <label for="phone">Phone</label>
        <input type="tel" name="phone" id="phone">
      </div>
      <div class="field col-12">
        <label for="message">Message</label>
        <textarea name="message" id="message"></textarea>
      </div>
      <div class="field col-12">
        <input type="submit" value="Send">
      </div>
    </form>
  </div>
</section>

<?php
endwhile;
get_footer(); ?>