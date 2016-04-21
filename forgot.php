<?php
	require_once 'lib/Twig/Autoloader.php';
	Twig_Autoloader::register();

	require_once 'lib/init.php';
	require_once 'lib/database.class.php';
	require_once 'lib/user.class.php';

	// Configure Twig
	$loader = new Twig_Loader_Filesystem('templates/');
	$twig = new Twig_Environment($loader);

	$export = array(
		'response' => ''
	);


	if (isset($_POST['submit-forgot']))
	{
		// send email
		$success = User::sendPasswordResetMail($_POST['email']);

		if ($success) {
			$export['response'] = "Email sent";

			//header("Location: ".$GLOBALS['config']['domain'].$GLOBALS['config']['directory']."?profile=".$_SESSION['user']['username']);
			//die("Redirecting");
		} else {
			$export['response'] = "Error sending email...";
		}
	}

	echo $twig->render('forgot.html', $export);