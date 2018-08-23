<?php
/**
 * @version		1.1.0 imgen $
 * @package		imgen
 * @copyright	Copyright © 2011 - All rights reserved.
 * @license		GNU/GPL
 * @author		Fiona Coulter
 * @author mail	joomla - at - iswebdesign.co.uk
 * @website		http://www.spiralscripts.co.uk
 *
 *

 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class ImgenImage extends JObject
{
	/*
	 is the image accessed by http:
	*/
	var $ishttp =  false; 
	/*
	  should the image be displayed? Assumed to be true unless turned off
	*/
	var $display = true;
	/*
	  image belongs to user id
	*/
	var $user = 0;
	/*
	 full image name including path
	*/
	var $name = '';
	/*
	  image file name
	*/
	var $base = '';
	/* 
	 image folder
	 */
	var $folder = '';
	
	/*
	 caching subfolder
    */
	
	var $cacheFolder = '';
	

	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		
	}



}



?>