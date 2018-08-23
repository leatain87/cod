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

jimport( 'joomla.application.component.view' );
require_once(JPATH_SITE.'/components/com_imgen/helpers/imgen.php');

class imgenViewImgen extends JViewLegacy
{
	function display($tpl = null){
		
		
        $app = JFactory::getApplication('site');
		$params =  $app->getParams('com_imgen');

		$dispatcher	= JDispatcher::getInstance();
		//$model = $this->getModel('imgen');
	    $jinput = JFactory::getApplication()->input;
	    $context = $jinput->get('context','','string');
		
		JPluginHelper::importPlugin('image');
		$img = new ImgenImage();
		$results = $dispatcher->trigger('onImageLoad', array (&$img, &$params, $context));  //trigger the on load image event  - can use this to integrate with components such as virtuemart, jomsocial
		
		
        if(empty($img->name))
		{		
		  //no plugin has returned result indicating success at detecting image path, so check default image handling
		  if(! $img = imgenHelper::getImagePath())
		  {
			 $this->_displayError('no image specified');
			 return;	
			  
		  }
    	}
		
		/*
		 * Process the prepare image plugins
		 */
		$results = $dispatcher->trigger('onImagePrepare', array (& $img, & $params, $context));  //trigger the on prepare image event  - can use this to check things like whether user is permitted to view image
		
		
		
		if (empty($img->base))
		{
			//there's a problem, output transparent 1 pixel png
            $image_p = imagecreatetruecolor(1, 1);	
			$black = imagecolorallocate($image_p, 0, 0, 0);

            // Make the background transparent
            imagecolortransparent($image_p, $black);
			
		
		    $doc = JFactory::getDocument();
		    $doc->setMimeEncoding('image/png');
			
		    imagepng($image_p);
			imagedestroy($image_p);
			return;

			
		}
		
		
		
		//hooray, we've got the image path, now determine the type

        if(!$imageType = imgenHelper::getImageType($img->name))
		{
		   $this->_displayError('not a valid  image type');
		   return;	
		}
		
		
		
        $img = imgenHelper::clean($img);		
		
		$cacheFolder = imgenHelper::getParamValue('cacheFolder','images/imgen/');
		$destFolder = imgenHelper::getFolderPath($cacheFolder).$img->cacheFolder;	
        if( ! is_dir($destFolder) && ! mkdir($destFolder, 0755))	
		{
			 $this->_displayError('caching folder could not be created ' . $destFolder);
			 return;	
			
		}
		
		
		
		
		$quality = imgenHelper::getQuality();
		
		
		
	    $fileout = $img->base;
		$fileout = preg_replace('/\.jpe?g$|\.png$|\.gif$/i','',$fileout);
		$fileout = preg_replace('/[^A-Za-z0-9\-\._% ]/','',$fileout);



		
		$layout = $this->getLayout();

        if($layout == 'default')
		{
			$layout = $imageType;
			
		}
		$this->setLayout($layout);
		
		switch ($layout)
		{
			case 'jpg': $mimeType = 'image/jpeg'; break;
			case 'png': $mimeType = 'image/png'; break;
			case 'gif': $mimeType = 'image/gif'; break;
			
		}
		
		
		
		$doc =  JFactory::getDocument();
		$doc->setMimeEncoding($mimeType);
		
		
		//now we can start resizing
		
		$defaultWidth = (int)imgenHelper::getParamValue('defaultWidth',0);
	    $defaultHeight = (int)imgenHelper::getParamValue('defaultHeight',0);
				
		
        $coords = imgenHelper::getCoords($img->name, $defaultWidth, $defaultHeight);
		if($coords === false)
		{		
		  $this->_displayError('Failed to detect image size: perhaps there was a mistake in the file name, please check:'. htmlspecialchars($img->name));
		}
		
		$displayErrors = imgenHelper::getParamValue('displayErrors', null);
		
		//check for caching
		$caching = false;
        $cachetime = (int)imgenHelper::getParamValue('cacheTime',60) * 60;
 		
        $fileout = $destFolder.$fileout.'_'.$coords->toWidth.'x'.$coords->toHeight.'q'.$quality.'.'.$layout; 
		
		if (imgenHelper::getParamValue('cacheImages','yes') == 'yes') {
			//thanks to David Hayes for this bit of code
			$app->allowCache(true);
			$app->modifiedDate = new JDate( file_exists($fileout) ? '@'.filemtime($fileout) : 'now');
			$app->setHeader('Cache-Control', 'public, max-age=14400', true);
			header_remove('Set-Cookie'); // neets removed for cloudflare to cache asset
		}		
		
		if((file_exists($fileout)) && (time() - filemtime($fileout) < $cachetime) && imgenHelper::getParamValue('cacheImages','yes') == 'yes')
        {
			$caching = true;
			$this->fileout  = $fileout;
			$this->caching = $caching;
	    	parent::display($tpl);
			return;
		}
		
		
		
		$mem_size = ini_get('memory_limit');
		if((int)$mem_size < 256)
		{
		   ini_set('memory_limit','256M');
		}

		switch( $imageType )
		{
		  case 'jpg' : $image = imagecreatefromjpeg($img->name); break;
		  case 'png' : $image = imagecreatefrompng($img->name); imagealphablending($image, false); imagesavealpha($image, true);
                                                           break;
		  case 'gif' : $image = imagecreatefromgif($img->name); break;
		}

        // Resample
    	$image_p = imagecreatetruecolor($coords->toWidth, $coords->toHeight);
		if($imageType == 'png')
		{
               imagealphablending($image_p, false); imagesavealpha($image_p, true);
		}
		imagecopyresampled($image_p, $image, $coords->toX, $coords->toY, $coords->fromX, $coords->fromY, $coords->toWidth, $coords->toHeight, $coords->fromWidth, $coords->fromHeight);
		
		
		$results = $dispatcher->trigger('onImageOutput', array (& $image_p, & $params, $context, $imageType));  //trigger the on output image event  - can use this to add watermark, copyright etc
		//now output result
		$this->quality = $quality;
		$this->image = $image_p;
		$this->params = $params;		
		$this->fileout = $fileout;
		$this->caching = $caching;
		
	    parent::display($tpl);
		
	    imagedestroy( $image );	
		ini_restore('memory_limit');

	}
	
	function _displayError($msg)
	{
 		
		
		imgenHelper::displayError($msg);
		
	}
	

}
?>