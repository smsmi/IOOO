<?php
	/*
	 * submit.php
	 *
	 * This script submits a post
	 *
	*/
	require_once("../lib/init.php");
	require_once("../lib/database.class.php");

	// First, we want to make sure the user is authenticated
	$auth = isset($_SESSION['user']);

	$msg = "";

	$r = array(
		"response"=>"",
		"success"=>false,
		"error"=>0
	);

	if ($auth) {
		$db = Database::getInstance();

		if ($_POST['edit'] == true) {
			$required = array('imageurl');
			// Loop over field names, make sure each one exists and is not empty
			$error = false;
			foreach($required as $field) {
				if (empty($_POST[$field])) {
					$error = true;
				}
			}

			if (!$error) {
				$db->query("UPDATE posts SET imageurl=?, description=? WHERE id=?", array($_POST['imageurl'], $_POST['description'], $_POST['id']));
				if ($db->error()) {
					$r['response'] = "Error updating post.";
					$r['error'] = 12; // General database error (while editing).
					$r['success'] = false;
				} else {
					// actual post was saved. let's save those tags along with it.

					//$db->query("SELECT LAST_INSERT_ID()");
					//$postID = $db->firstResult();

					if (parseTags($_POST['id'], $_POST['tags'], true)) {
						$r['response'] = "Successfully updated post.";
						$r['success'] = true;
						//ob_start();
						//var_dump($postID);
						//$r['error'] = ob_get_clean();
					} else {
						$r['response'] = "Error updating post.";
						$r['error'] = 22; // Error submitting post tags - otherwise, the post still exists (while editing)
						$r['success'] = false;
					}

				}
				die(json_encode($r));
			} else {
				$r['response'] = "Missing required field.";
				$r['error'] = 32; // Missing required field (while editing)
				$r['success'] = false;
				die(json_encode($r));
			}
		} else {
			$required = array('imageurl');
			// Loop over field names, make sure each one exists and is not empty
			$error = false;
			foreach($required as $field) {
				if (empty($_POST[$field])) {
					$error = true;
				}
			}

			if (!$error) {
				$db->query("INSERT INTO posts(user_id, imageurl, description, date) VALUES (?, ?, ?, now())", array($_SESSION['user']['id'], $_POST['imageurl'], $_POST['description']));
				if ($db->error()) {
					$r['response'] = "Error creating post.";
					$r['error'] = 1; // General database error.
					$r['success'] = false;
				} else {
					// actual post was saved. let's save those tags along with it.

					$db->query("SELECT LAST_INSERT_ID()");
					$postID = $db->firstResult();

					// Query up to get the ID we just saved
					if (parseTags($postID['LAST_INSERT_ID()'], $_POST['tags'], false)) {
						$r['response'] = "Successfully created post.";
						ob_start();
						var_dump($postID);
						$r['error'] = ob_get_clean();
						$r['success'] = true;
					} else {
						$r['response'] = "Error creating post.";
						$r['error'] = 2; // Error submitting post tags - otherwise, the post still exists
						$r['success'] = false;
					}

				}
				die(json_encode($r));
			} else {
				$r['response'] = "Missing required field.";
				$r['error'] = 3; // Missing required field
				$r['success'] = false;
				die(json_encode($r));
			}
		}
	} else {
		$r['response'] = "Please sign in.";
		$r['error'] = 4; // Missing AUTH
		$r['success'] = false;
		die(json_encode($r));
	}

	function parseTags($pid, $str, $isEdit) {
		$str = preg_replace('/\s*,\s*/', ',', strtolower($str));
		$tags = explode(',', $str);

		$db = Database::getInstance();

		// We have an array of all the tags for this post.
		// Find newly used tags and put them in the database.
		// Load the IDs for the newly stored tags as well as any existing ones

		// If editing the post, be sure to delete all tags in case the user removed a tag for that post.
		if ($isEdit) {	
			$db->query("DELETE FROM posts_tags WHERE post_id=?", array($pid));
		}


		
		///array_walk($tags, create_function('&$str', '$str = \'("$str")\';'));
		$tagID = array();
		for ($i = 0; $i < sizeof($tags); $i++) {
			$db->query("INSERT IGNORE INTO tags(user_id, tagname) VALUES (?, ?)", array($_SESSION['user']['id'], $tags[$i]));

			// Get all the IDs on those tags we just put in
			$db->query("SELECT id FROM tags WHERE tagname=? AND user_id=?", array($tags[$i], $_SESSION['user']['id']));
			$result = $db->firstResult();
			array_push($tagID, $result['id']);
		}

		// Put the pair post-id and tag-id in the posts/tags table

		// How can i make sure these IDs match up?
		for ($j = 0; $j < sizeof($tagID); $j++) {
			$db->query("INSERT INTO posts_tags(user_id, post_id, tag_id) VALUES (?, ?, ?)", array($_SESSION['user']['id'], $pid, $tagID[$j]));
		}

		return true;
	}