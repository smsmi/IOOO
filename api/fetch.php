<?php
	// fetches more MEMOs under the given parameters.

	require_once("../lib/init.php");
	require_once("../lib/database.class.php");
	require_once("../lib/user.class.php");

	$db = Database::getInstance();


	// Get information about user's request
	$auth = isset($_SESSION['user']);
	$profile = $_POST['profile'];
	$searchQuery = isset($_POST['q']) ? $_POST['q'] : null;
	$offset = intval($_POST['offset']);

	//get profile ID from username
	$db->query("SELECT id FROM users WHERE username=?", array($profile));
	$r = $db->firstResult();
	$profileID = $r['id'];
	if ($db->count() == 0) {
		// No profile found for given username.
		die("No profile found.");
	}

	if (!empty($searchQuery)) {
		// load all posts with given tag of given profile
		$db->query("SELECT `id`, `imageurl` FROM `posts` WHERE user_id=? AND id IN (SELECT post_id FROM posts_tags WHERE user_id=? AND tag_id IN (SELECT id FROM tags WHERE user_id=? AND tagname=?)) ORDER BY date desc LIMIT 10 OFFSET ?", array($profileID, $profileID, $profileID, $searchQuery, $offset));
		$results = $db->result();
		$postsLoaded = $db->count();
		$postlist = array();
		foreach($results as $p) {
			array_push($postlist, "<div class='entry'><a href='index.php?profile=".$profile."&id=".$p['id']."'><img src='".$p['imageurl']."' /></a></div>");
		}

		// concatenate postlist and echo
		$response = array(
			'postlist' => implode('', $postlist),
			'offset' => $offset + $postsLoaded,
			'postsLoaded'=> $postsLoaded
		);
		// sizeof results should be number of posts loaded with response
		echo json_encode($response);
	} else {
		// load recent posts of given profile
		$db->query("SELECT id, imageurl FROM posts WHERE user_id=? ORDER BY `date` desc LIMIT 10 OFFSET ?", array($profileID, $offset));
		$results = $db->result();
		$postsLoaded = $db->count();
		$postlist = array();
		foreach($results as $p) {
			array_push($postlist, "<div class='entry'><a href='index.php?profile=".$profile."&id=".$p['id']."'><img src='".$p['imageurl']."' /></a></div>");
		}

		// concatenate postlist and echo
		$response = array(
			'postlist' => implode('', $postlist),
			'offset' => $offset + $postsLoaded,
			'postsLoaded'=> $postsLoaded
		);
		// sizeof results should be number of posts loaded with response
		echo json_encode($response);
	}