<?php
session_start();
require 'vendor/phpmailer/PHPMailerAutoload.php';

class phpMail{

	function send_mail($sub,$msg,$from,$from_email,$recipient, $recipient_email){
		$mail = new PHPMailer;
		// $mail->SMTPDebug = 3;                               // Enable verbose debug output

		$mail->isSMTP();                                      // Set mailer to use SMTP
		
		/*
		$mail->Host = 'ssl://smtp.gmail.com';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'mailer.managehiring@gmail.com';                 // SMTP username
		$mail->Password = 'ur$939!3';                           // SMTP password
		//$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                                    // TCP port to connect to
		*/
		
		
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Port = 465; 

		if($from_email == 'noreply@managehiring.com'){
			$mail->Host = 'ssl://smtp.gmail.com';  // Specify main and backup SMTP servers
			$mail->Username = 'mailer.managehiring@gmail.com';                 // SMTP username
			$mail->Password = 'ur$939!3';                           // SMTP password
			//$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
			
		}else{
			$mail->Host = 'ssl://smtp.bizmail.yahoo.com';  // Specify main and backup SMTP servers
			$mail->Username = $_SESSION['email_id'];                 // SMTP username
			$from_email = $_SESSION['email_id'];
			$mail->Password = $_SESSION['user_pass'];                           // SMTP password
			// $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		}
		
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		$mail->setFrom($from_email, $from);

		// $mail->setFrom($from_email, $from);

		$mail->addAddress($recipient_email, $recipient);     // Add a recipient
		// $mail->addAddress('ellen@example.com');               // Name is optional
		// $mail->addReplyTo('info@example.com', 'Information');
		// $mail->addCC('cc@example.com');
		// $mail->addBCC('bcc@example.com');

		// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		// $mail->addAttachment($file_path, $file_name);    // Optional name
		
		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $sub;
		$mail->Body    = $msg;
		// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		if(!$mail->send()){
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			 // echo 'Message has been sent';
		}
	} 
	
	function send_mail_to_client($sub,$message,$from,$from_email,$recipient, $recipient_email,$mail_cc,$resume_type,$file){
		$mail = new PHPMailer;	
		
		// cc mail 
				
		
		
		if($mail_cc[0] != '' && count($mail_cc) > 0){
			foreach($mail_cc as $cc_mail){ 
				$mail->AddCC($cc_mail, '');
			}
		}
		
		
		//$mail->AddCC($mail_cc[0], 'ravichandran3');
		//$mail->AddCC($mail_cc[1], 'ravichandran2');
		
		
		$mail->SMTPAuth = true;  // Enable SMTP authentication
		$mail->isSMTP();                                      // Set mailer to use SMTP
		
		
		// checking for local
		
				
		$mail->Host = 'tls://smtp.gmail.com';  // Specify main and backup SMTP servers		                              
		$mail->Username = 'testing@bigspire.com';                 // SMTP username
		$mail->Password = 'bigspire1230';                           // SMTP password
		$mail->Port = 587;   
		
		
		
		// checking in live
		
		/*
		$mail->Port = 465; 
		if($from_email == 'noreply@managehiring.com'){
			$mail->Host = 'ssl://smtp.gmail.com';  // Specify main and backup SMTP servers
			$mail->Username = 'mailer.managehiring@gmail.com';                 // SMTP username
			$mail->Password = 'ur$939!3';                           // SMTP password
			//$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
			
		}else{
			$mail->Host = 'ssl://smtp.bizmail.yahoo.com';  // Specify main and backup SMTP servers
			$mail->Username = $_SESSION['email_id'];                 // SMTP username
			$from_email = $_SESSION['email_id'];
			$mail->Password = $_SESSION['user_pass'];                           // SMTP password
			// $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		}
		*/
		
		
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		$mail->setFrom($from_email, $from);

		// $mail->setFrom($from_email, $from);

		$mail->addAddress($recipient_email, $recipient);     // Add a recipient
		
		if($file != ''){
			$mail->AddAttachment($resume_type, $file);
		}
	
		// Add a recipient
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = $sub;
		$mail->Body    = $message;
		if(!$mail->send()){
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			// 'Message has been sent';
		}
	} 
}
$mailer = new phpMail();