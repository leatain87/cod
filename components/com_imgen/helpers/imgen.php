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

// Component Helper

jimport('joomla.application.component.helper');
require_once(JPATH_SITE.'/components/com_imgen/classes/imgenimage.php');


class imgenHelper
{

    /*
      calculates resized image co-ordinates
	  @param string $img, the image source
	  @param int $defaultWidth default width for image
	  @param int $defaultHeight default height for image
	  returns object with properties for source and destination, x, y, width and height
    */
	public static function getCoords(&$img, $defaultWidth = 0, $defaultHeight = 0)
	{
        $size = @getimagesize($img);	
		
		if($size === false)
		{
		  return false;
		}
		
		$resizing = true;
		list($srcwidth, $srcheight) = $size;
		
		if(!$srcwidth || !$srcheight){return false;}
	    $jinput = JFactory::getApplication()->input;
	    $new_width = (int)$jinput->get('width',0,'int');	
        $new_height = (int)$jinput->get('height',0,'int');
		
        if($new_height + $new_width == 0)
		{
			
			
			if($defaultWidth + $defaultHeight == 0)
		    {
				//no resizing
				$new_height = $srcheight;
				$new_width = $srcwidth;
				$resizing = false;
			}
			else
			{
			   //use default values
			   $new_width = $defaultWidth;
			   $new_height = $defaultHeight;
				
			}
		}
		
		if($new_width == 0)
		{
			$new_width = $new_height * $srcwidth/$srcheight;
		}
		else if($new_height == 0)
		{
			$new_height = $new_width * $srcheight/$srcwidth;
		}
		
		$coords = new stdClass();
		$coords->toX = 0;
		$coords->toY = 0;
		$coords->toWidth = $new_width;
		$coords->toHeight = $new_height;
		
		if($resizing == false)
		{
		   $fromX = 0;
		   $fromY = 0;
		   $fromWidth = $srcwidth;
		   $fromHeight = $srcheight;
			
		}
		else
		{
			$widthRatio = $new_width/$srcwidth;
			$heightRatio = $new_height/$srcheight;
			
			if($widthRatio > $heightRatio)
			{
				$fromWidth = $srcwidth;
				$fromHeight = $new_height/ $new_width * $srcwidth;
				$fromX = 0;
				$fromY =  max((int)(($srcheight-$fromHeight)/2), 0);
			}
			else
			{
				$fromHeight = $srcheight;
				$fromWidth = $srcheight * $new_width/$new_height;
				$fromY = 0;
				$fromX = max((int)(($srcwidth - $fromWidth)/2), 0);
			}
		}
		
		$coords->fromX = $fromX;
		$coords->fromY = $fromY;
		$coords->fromWidth = $fromWidth;
		$coords->fromHeight = $fromHeight;
		
		return $coords;
		
		
		
	}
		
		
		
	
	public static function getFolderPath($f)
	{
		
		$folder = $f;
		$folder = str_replace('\\','/',$folder);
		//$folder = str_replace('/',DS,$folder);
		if(JString::strrpos($folder, '/') < (JString::strlen($folder) - 1))
		{
			$folder = $folder. '/';
			
		}
				
		
		return $folder;
	}
		
		
    public static function getImagePath($imgName = null, $df = null)
	{
		
		$defaultFolder = self::getParamValue('defaultFolder','images/stories/');
		
		$image = new ImgenImage();
		$image->ishttp = false;
	    $jinput = JFactory::getApplication()->input;		
		$image->user = (int) $jinput->get('userid', 0,'int');
		
		
		
		if(!empty($imgName))
		{
           $img = $imgName;		
		}
		else
		{
			$img = $jinput->get('img',null,'string');
			if(is_null($img)||empty($img))
			{
				$imgencoded = $jinput->get('imgencoded',null,'string');	
				if(isset($imgencoded) && ! empty($imgencoded))
				{
					$img = base64_decode($imgencoded);
				}
			}
			if(is_null($img))
			{
				//fire plugin event to handle other components
			   return false;	
			}
		}
		
		
		
  		
		
		if(isset($df))
		{
		   $defaultFolder = self::getFolderPath($df);	
		}
		else
		{
		     $defaultFolder = self::getFolderPath($defaultFolder);
		}
		
		
		if(preg_match('/^http(s)?:/i',$img))
		{
			   $liveSite 	= JURI::base();
			   $img = str_replace( $liveSite, '', $img );
	           if(preg_match('/^http(s)?:/i',$img))
			   {   
			   
    	           $img = str_replace(' ','%20',$img);	
			   
				   //external image			   
			       ini_set('allow_url_fopen',1);
				   $httpImages = self::getParamValue('httpImages','no');
				   if($httpImages == 'no')
				   {
					   self::displayError('external image not permitted');
					   return;	
				   }
				   else if(ini_get('allow_url_fopen') != '1')
				   {
					   self::displayError('your php configuration does not permit resizing external images. You must set allow_url_fopen="1"');
					   return;	
				   }
				   $img = str_replace('\\','/',$img);
				   $image->ishttp = true;
				   $image->name = $img;
				   
			       $uri = JURI::getInstance($img);
			       $path = $uri->getPath();
				   $image->folder = $path;
		           $image->base = basename($path);
				   
				   
				   
			   }
			   else
			   {
					$img = str_replace('\\','/',$img);
					//$img = str_replace('/',DS,$img);	
					
				    $image->name = $img;
				    $image->base = basename($img);
				    $image->folder = self::getFolderPath(dirname($img));
					
			   }
		}
		else if ($img == basename($img))
		{
			//using default images folder
			if((int) $image->user > 0 && file_exists($defaultFolder.(int) $image->user . '/'.$img))
			{
			   $image->name = $defaultFolder.(int) $image->user . '/'.$img;
			   $image->base = $img;
		       $image->folder = $defaultFolder . (int) $image->user . '/';
			   $image->cacheFolder .= (int)$image->user .'/';
				
			}
			else if(file_exists( $defaultFolder.$img))
			{
			  $image->name = $defaultFolder.$img;
			  $image->base = $img;
			  $image->folder = $defaultFolder;
			}
			else
			{
			  return false;	
			}
			
		}
		else
		{
			
			//image contains path info
	    	$img = str_replace('\\','/',$img);
		    //$img = str_replace('/',DS,$img);
			$dir = self::getFolderPath( dirname($img));
			$img = basename( $img );
		    $cacheCompletePath = self::getParamValue('cacheCompletePath','no');
			
			//is it in sub folder of default folder?
			if(file_exists($defaultFolder . $dir. $img))
			{
		    	$image->name = $defaultFolder . $dir . $img;
		        $image->base = $img;
		        $image->folder = $defaultFolder . $dir;
			}
			else if(file_exists($dir. $img))
			{
			
			    $image->name = $dir . $img;
		        $image->base = basename($img);
		        $image->folder = $dir;
			}
			else
			{
                   //sometimes sub-directory path can cause problems so strip this out
					 $rootPath = JURI::root(true);				   
					 $pattern = '#^'.$rootPath.'/?#';					 
					 $dir = preg_replace($pattern,'',$dir);
					  if(file_exists( $dir . $img ))
					  {
						$image->name = $dir . $img;
						$image->base = $img;
						$image->folder = $dir;
					  }
					  else
					  {
						return false;
						//something is wrong, can't find image
						  
					  }
				
			}
			
		    if($cacheCompletePath == 'yes')
		    {
			  $image->cacheFolder = str_replace($defaultFolder, '', $dir);
		    }
			
			
			
		}
		
		return $image;
		
	}
	
	public static function clean(&$image)
	{
		//clean cacheFolder path
		$filter	= JFilterInput::getInstance();
		$image->cacheFolder = $filter->clean($image->cacheFolder,'PATH');
		$image->base = $filter->clean($image->base,'PATH');
		
        return $image;		
		
	}
		
    public static function getImageType($img)
	{
		if ( preg_match('/\.jpe?g$/i', $img )) { 
		  $imageType = 'jpg';
		}
		else if ( preg_match('/\.png$/i', $img )) { 
		  $imageType = 'png';
		}
		else if ( preg_match('/\.gif$/i', $img )) { 
		  $imageType = 'gif';
		}
		else
		{
		  $imageType = false;	
		}
        return $imageType;		
	}

	public static function getParamValue($key,$default=null)
	{
        $app = JFactory::getApplication('site');
		$params =  $app->getParams('com_imgen');
		
		$returnVal = $params->get($key,$default);
			
   
		return $returnVal;
		
	}
	
	public static function displayError($msg)
	{
 		
		if(self::getParamValue('displayErrors','no') == 'yes')
		{
		  $doc = JFactory::getDocument();
		  $doc->setMimeEncoding('text/html');
   			
		  echo $msg;	
		}
		
	}
	
	public static function getQuality()
	{
	    $jinput = JFactory::getApplication()->input;		
		$quality=(int)$jinput->get('quality',imgenHelper::getParamValue('quality',100),'int');
		{
		   if($quality < 0){$quality = 0;}
		   else if($quality > 100){ $quality = 100; }
		}
		return $quality;
	}


}
?>