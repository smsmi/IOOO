<?php
	// reset password
	require_once 'lib/Twig/Autoloader.php';
	Twig_Autoloader::register();

	require_once 'lib/init.php';
	require_once 'lib/database.class.php';
	require_once 'lib/user.class.php';

	// Configure Twig
	$loader = new Twig_Loader_Filesystem('templates/');
	$twig = new Twig_Environment($loader);

	$export = array(
		'response' => '',
		'success' => false
	);

	isset($_GET['key']) ? $export['key'] = $_GET['key'] : $export['key'] = $_POST['key'];
	if (isset($_POST['submit-reset'])) {
		// form submitted, check the key
		$db = Database::getInstance();
		$db->query("SELECT * FROM password_reset_requests WHERE reset_key = ?", array($_POST['key']));
		$result = $db->firstResult();

		if ($result != null) {
			if ($_POST['password'] === $_POST['retype']) {
				if (User::updatePassword($result['email'], $_POST['password'])) {
					$export['response'] = "Successfully changed password.";
					$exprot['success'] = true;

					// Remove the data from reset table
					$db->query("DELETE * FROM password_reset_requests WHERE reset_key = ?", array($_POST['key']));
				} else {
					$export['response'] = "Error....";
				}
			} else {
				$export['response'] = "Passwords do not match.";
			}
		} else {
			$export['response'] = "Error...";
		}
	}

	echo $twig->render('reset.html', $export);