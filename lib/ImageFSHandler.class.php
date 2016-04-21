<?php
	require_once(__DIR__ . "/config.class.php");
	
	/**
	 * This class is designed to handle image downloads from remote URLs, 
	 * and save them to a location on the disk by MD5 hash of the timestamp.
	 *
	 * It may also have methods to handle file uploads.
	 */
	class ImageFSHandler {
		public function __construct() {


		}

		/**
		 * @param  $url       The image URL to 
		 * @param  $media_dir Media directory to save images to
		 * @return true if successful
		 */
		public function downloadImage($url, $media_dir) {
			/*
			notes:
			we should make sure that the URL is in fact an image
			we should also consider filetypes
			 */
			$dest = md5(time());
			// we will use the first two characters in the hash as the directory
			$filename = substr($dest, 2);
			$dir = substr($dest, 0, 2);

			if (!is_dir($media_dir . '/' . $dir)) {
				mkdir($media_dir . '/' . $dir);
			}

			return copy($url, $media_dir . '/' . $dir . '/' . $filename);
		}
	}