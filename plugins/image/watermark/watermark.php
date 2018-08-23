<?php
/**
 * @copyright	Copyright (C) 2011 Inspiration Web Design. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport('joomla.plugin.helper');
require_once(JPATH_SITE.'/components'.'/com_imgen/helpers/imgen.php');



class plgImageWatermark extends JPlugin
{

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	function plgImageWatermark( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	/**
	 * Image output method
	 *
	 * Method is called by the view
	 *
	 * @param 	object		The image object. A PHP image resource
	 * @param 	object		The image params
	 * @param 	context		The image context
	 */
	function onImageOutput( &$image, &$params, $context = '', $srcType )
	{
		//global $mainframe;
		
       if(JPluginHelper::isEnabled('image','watermark')==false) return;
		
		ini_set("allow_url_fopen","1");

		
	
		$pluginParams =& $this->params;
	
		
		if($srcType == 'png')
		{
			imagealphablending($image, false); imagesavealpha($image, true);
		}
		
		$watermark = $pluginParams->get('imageWatermark','media/watermark/copyright-sample.png');
		if(!empty($watermark))
	    {
     		$w = imgenHelper::getImagePath($watermark);
			if(isset($w->name)) //version 1.1.0+ of imgen
			{
				$watermark = $w->name;
			}
			else //earlier version
			{
				$watermark = $w;
			}
		}
		else
		{
		  return;	
		}
		
        $size = @getimagesize($watermark);	
		if(!$size)
		{
			return;
		}
		else
		{
		   list($markwidth, $markheight) = $size;
		}
		
		
		
		
		if(!$imageType = imgenHelper::getImageType($watermark))
		{
			  return;
		}
		
		$imgWidth = imagesx($image);
		$imgHeight = imagesy($image);
		
		$minSize = (int)$pluginParams->get('minImageSize',0);
		
		if($imgWidth < $minSize || $imgHeight < $minSize){ return; }
		
		$stretchWatermark = $pluginParams->get('watermarkStretch','no');
		if(($markwidth >= $imgWidth) || ($markheight >= $imgHeight))
		{
			$stretchWatermark = 'yes';
			
		}
		
		

		switch( $imageType )
		{
		  case 'jpg' : $iw = imagecreatefromjpeg($watermark); break;
		  case 'png' : $iw = imagecreatefrompng($watermark); 
                                                           break;
		  case 'gif' : $iw = imagecreatefromgif($watermark); break;
		}
		
		if($stretchWatermark == 'yes')
		{
			$imageWatermark = imagecreatetruecolor($imgWidth, $imgHeight);
			if($imageType == 'png')
			{
			   imagealphablending($imageWatermark, false); imagesavealpha($imageWatermark, true);
			   imagealphablending($iw, false); imagesavealpha($iw, true);
			}
			imagecopyresampled($imageWatermark, $iw, 0, 0, 0, 0, $imgWidth, $imgHeight, $markwidth, $markheight);
			$srcWidth = $imgWidth;
			$srcHeight = $imgHeight;
			$dstX = 0;
			$dstY = 0;
			$srcX = 0;
			$srcY = 0;
		    imagedestroy($iw);
			
		}
		else
		{
			//we are not resizing watermark, instead calculate co-ordinates
			$imageWatermark = $iw;
			if($imageType == 'png')
			{			
			   imagealphablending($imageWatermark, false); imagesavealpha($imageWatermark, true);
			}
			
			$srcWidth = $markwidth;
			$srcHeight = $markheight;
		    $srcX = 0;
		    $srcY = 0;
			
			$dstX = (int)$pluginParams->get('watermarkLeftPos',0);
			$dstY = (int)$pluginParams->get('watermarkTopPos',0);
			
			if($dstX < 0)
			{
			   $dstX = $imgWidth + $dstX - $srcWidth;	
			   if($dstX < 0)
			   {
				   $dstX = 0;
			   }
			   
			}
			if($dstX > $imgWidth - $srcWidth )
			{
				$dstX = max($imgWidth - $srcWidth,0);
			}
			
			if($dstY < 0)
			{
			   $dstY = $imgHeight + $dstY - $srcHeight;	
			   if($dstY < 0)
			   {
				   $dstY = 0;
			   }
			   
			}
			if($dstY > $imgHeight - $srcHeight )
			{
				$dstY = max($imgHeight - $srcHeight,0);
			}
			
		}
		
		
		//imagealphablending($imageWatermark, true); //?
		//imagealphablending($image, false); imagesavealpha($image, true);
		
		//imgenHelper::displayError('dstX '.$dstX . ' dstY '. $dstY . ' srcX ' . $srcX . ' srcY '. $srcY . ' srcWidth '. $srcWidth . ' srcHeight '. $srcHeight );
		
		
		$opacity = (int)$pluginParams->get('watermarkOpacity','10');
		if($imageType == 'png')
		{
		   imagelayereffect($image, IMG_EFFECT_ALPHABLEND);	
		   imagealphablending($imageWatermark, true); imagesavealpha($imageWatermark, false);
		   imagecopy($image, $imageWatermark, $dstX, $dstY, $srcX, $srcY, $srcWidth, $srcHeight);
		}
		else
		{			
		   imagecopymerge($image, $imageWatermark, $dstX, $dstY, $srcX, $srcY, $srcWidth, $srcHeight, $opacity);
		}
		
		imagedestroy($imageWatermark);  
	

	}


}
