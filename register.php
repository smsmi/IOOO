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
		'responseR' => ''
	);


	if (isset($_POST['submit-register']))
	{
		// make sure required fields are submitted
		$required = array('email', 'password', 'retype', 'username');
		// Loop over field names, make sure each one exists and is not empty
		$error = false;
		foreach($required as $field) {
			if (empty($_POST[$field])) {
				$error = true;
			}
		}
		if ($error) {
			$export['responseR'] = "Missing required field.";
			die($twig->render('login.html', $export));
		}

		// ensure that passwords match
		if ($_POST['password'] !== $_POST['retype']) {
			$export['responseR'] = "Passwords do not match.";
			die($twig->render('login.html', $export));
		}

		// make sure that username or email do not already belong to a user
		if (User::userWithNameExists($_POST['username'])) {
			$export['responseR'] = "Someone already has that username...";
			die($twig->render('login.html', $export));
		}
		if (User::userWithEmailExists($_POST['email'])) {
			$export['responseR'] = "Someone has already signed up with that email...";
			die($twig->render('login.html', $export));
		}



		// Still here? Sign em up
		$user = new User();
		$success = $user->queueNewUser($_POST['email'], $_POST['username'], $_POST['password']);

		if ($success) {
			$export['responseR'] = "Verification email sent. Check your email to complete your registration.";
		} else {
			$export['responseR'] = "Error registering account.";
		}
	}

	echo $twig->render('login.html', $export);