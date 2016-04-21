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


	if (isset($_POST['submit-login']))
	{
		// Credentials passed to the server
		$user = new User();
		$success = $user->login($_POST['email'], $_POST['pass']);
		// $user->login handles the session, so i think we're done here

		if ($success) {
			header("Location: ".$GLOBALS['config']['domain'].$GLOBALS['config']['directory']."?profile=".$_SESSION['user']['username']);
			die("Redirecting");
		} else {
			$export['response'] = "Incorrect login.";
		}
	}

	echo $twig->render('login.html', $export);