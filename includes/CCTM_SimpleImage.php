<?php
/**
 * This file was damn useful, so I adapted it from Mr. Jarvis.  Thanks!
 * I'm using it to compensate for WordPress' erratic image resizing API.
 * Sorry, WP, but your API sucks.
 *
 * File: CCTM_SimpleImage.php
 * Author: Simon Jarvis
 * Copyright: 2006 Simon Jarvis
 * Date: 08/11/06
 * http://www.white-hat-web-design.co.uk/blog/resizing-images-with-php/
 * Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details:
 * http://www.gnu.org/licenses/gpl.html
 *
 * @package
 */


class CCTM_SimpleImage {

	public $image;
	public $image_type;

	//------------------------------------------------------------------------------
	/**
	 * Full path to image, also takes a URL
	 *
	 * @param string  $filename
	 */
	function load($filename) {

		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];
		if ( $this->image_type == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($filename);
		} elseif ( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);
		} elseif ( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * Save the new image.
	 *
	 * @param string  $filename full path to file
	 * @param string  (optional) $image_type
	 * @param integer (optional) $compression
	 * @param string  (optional) $permissions passed to chmod, e.g. 775
	 * @return	boolean	TRUE on success or FALSE on failure.
	 */
	function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
		$success = '';
		if ( $image_type == IMAGETYPE_JPEG ) {
			$success = imagejpeg($this->image, $filename, $compression);
		}
		elseif ( $image_type == IMAGETYPE_GIF ) {
			$success = imagegif($this->image, $filename);
		}
		elseif ( $image_type == IMAGETYPE_PNG ) {
			$success = imagepng($this->image, $filename);
		}
		
		if ( $permissions != null) {
			chmod($filename, $permissions);
		}
		
		// Free memory
		// http://www.binarytides.com/blog/php-resize-large-images-with-imagemagick/
		imagedestroy($this->image);
		
		return $success;
	}


	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @param string  $image_type (optional)
	 */
	function output($image_type=IMAGETYPE_JPEG) {

		if ( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image);
		} 
		elseif ( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image);
		} 
		elseif ( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image);
		}
	}


	/**
	 * Get the image's width, in pixels
	 *
	 * @return integer
	 */
	function getWidth() {
		return imagesx($this->image);
	}


	/**
	 * Get the image's height, in pixels
	 *
	 * @return integer
	 */
	function getHeight() {
		return imagesy($this->image);
	}


	/**
	 * Alter the image height to the new $height.
	 *
	 * @param integer $height
	 */
	function resizeToHeight($height) {
		$height = (int) $height;
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width, $height);
	}


	/**
	 * Resize the image width to the new $width.
	 *
	 * @param integer $width
	 */
	function resizeToWidth($width) {
		$with = (int) $width;
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width, $height);
	}


	/**
	 * An integer 1 to 100.
	 *
	 * @param integer $scale
	 */
	function scale($scale) {
		$scale = (int) $scale;
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width, $height);
	}


	/**
	 * Adjust the image dimensions.
	 *
	 * @param integer $width
	 * @param integer $height
	 */
	function resize($width, $height) {
		$with = (int) $width;
		$height = (int) $height;
		$new_image = imagecreatetruecolor($width, $height);
		if (!imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight())) {
			die('Resampling failed for '. $new_image);
		}
		$this->image = $new_image;
	}


}


/*EOF*/