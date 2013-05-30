<!DOCTYPE html>
<html>
<head>
<link href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" rel="stylesheet"></em>
<em><link href="http://twitter.github.com/bootstrap/assets/css/bootstrap-responsive.css" rel="stylesheet">
<title>Bulk Email Sender</title>
</head>
<body>
<?php
require_once '../swiftmailer/lib/swift_required.php';

// Create the Transport
$transport = Swift_SmtpTransport::newInstance("smtp.gmail.com",587,"tls");
$transport->setUsername("your_gmail_id");
$transport->setPassword("your_gmail_passwd");
$transport->setLocalDomain("[127.0.0.1]");//to remove "this stream does not support SSL/crypto" error 


$tolist=strtok($_POST["to"],",");
$count=0;
$users=array();
while($tolist != false){
	$name=substr($tolist,0,strpos($tolist,"<"));
	$emailid=substr($tolist,strpos($tolist,"<")+1,strpos($tolist,">")-strpos($tolist,"<")-1);
	$users[$count]["fullname"]=$name;
	$users[$count]["email"]=$emailid;
	$tolist=strtok(",");
	$count++;
	//echo "$name "."$emailid<br>";
}

// Create the replacements array
$replacements = array();
foreach ($users as $user) {
  $replacements[$user["email"]] = array ("{fullname}" => $user["fullname"]);
	//echo $user["email"]." ".$user["fullname"];
}


// Create an instance of the plugin and register it
$plugin = new Swift_Plugins_DecoratorPlugin($replacements);
$mailer = Swift_Mailer::newInstance($transport);
$mailer->registerPlugin($plugin);

strtok($_POST["from"],"<");
$fromlist=strtok(">");
//echo "$fromlist</br>"


// Create the message
$message = Swift_Message::newInstance();
$message->setSubject($_POST["sub"]);
$message->setBody("Hi {fullname},<br>".$_POST["msg"],"text/html");
$message->setFrom("$fromlist");


// Send the email
foreach($users as $user) {
  $message->setTo($user["email"], $user["fullname"]);
  $mailer->send($message);
}

?>
<div class="alert-message block-message warning">
  <a class="close" href="#">×</a>
  <p><strong>Mails sent successfully</p>
  <div class="alert-actions">
    <a class="btn small" href="smail.html">Send More</a>
  </div>
</div>
</body>
</html>