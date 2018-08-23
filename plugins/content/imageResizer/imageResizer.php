<?php
/**
 * @copyright	Copyright (C) 2011 Inspiration Web Design. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
jimport('joomla.plugin.helper');
jimport('cms.version.version');


class plgContentImageResizer extends JPlugin
{


	 function onContentPrepare($context,&$row, &$contentParams, $page=0)
	 {
        if(JPluginHelper::isEnabled('content','imageResizer')==false) return;
		
		$pluginParams =& $this->params;
		
		if($pluginParams->get('showInCustom','no') == 'no' && $context == 'mod_custom.content')
		{
		   return;	
		}	
		
		$blankSrc = 'images/imgen/blank.gif';
		static $loadedScripts =  false;
		
	
	   if($pluginParams->get('useLazyLoad','no') == 'yes' && !$loadedScripts)	
	   {
		    $version = new JVersion();
			if($version->RELEASE == '2.5' && $pluginParams->get('loadJQuery','no') == 'yes')
			{
				JHtml::_('script','plugins/content/imageResizer/assets/jquery.min.js');
			}
			else if($pluginParams->get('loadJQuery','no') == 'yes')
			{
				JHtml::_('jquery.framework');
			}
			JHtml::_('script','plugins/content/imageResizer/assets/jquery-noconflict.js');
			JHtml::_('script','plugins/content/imageResizer/assets/jquery.lazy.min.js');
			JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict(); jQuery(document).ready(function() { jQuery("img.lazy").lazy(); });');
			$loadedScripts = true;
	   }
		
		$layout = JRequest::getVar('layout','');
		
		if($layout == 'blog')
		{
          if ($pluginParams->get('resizeBlog','yes') == 'no')
		  {
			 return; 
		  }
		  $width = (int)$pluginParams->get('thumbWidth','100');	
		  $height = (int)$pluginParams->get('thumbHeight','100');	
		  
		}
		else
		{
		  $view = JRequest::getVar('view','article');
		  if($view == 'frontpage'||$view == 'featured')
		  {
			 if ($pluginParams->get('resizeFeatured','yes') == 'no')
			 {
				 return; 
			 }
			  
		     $width = (int)$pluginParams->get('frontpageWidth','200');	
		     $height = (int)$pluginParams->get('frontpageHeight','200');	
		  }
		  else
		  {
			 if ($pluginParams->get('resizeArticle','yes') == 'no')
			 {
				 return; 
			 }
			  
		     $width = (int)$pluginParams->get('articleWidth','300');	
		     $height = (int)$pluginParams->get('articleHeight','300');	
		  }
			
		}
		
		if(isset($row->images))
		{
			$images  = json_decode($row->images);
			if(isset($images->image_fulltext) && !empty($images->image_fulltext))
			{
					 if($pluginParams->get('encodeName','yes') == 'yes')
					 {
						$replacementSrc = JRoute::_('index.php?option=com_imgen&imgencoded='.base64_encode($images->image_fulltext).'&format=image&width='.$width.'&height='.$height);
					 }
					 else
					 {
						$replacementSrc = JRoute::_('index.php?option=com_imgen&img='.rawurlencode($images->image_fulltext).'&format=image&width='.$width.'&height='.$height);
					 }
					 $images->image_fulltext = $replacementSrc;
			}
			if(isset($images->image_intro) && !empty($images->image_intro))
			{
					 if($pluginParams->get('encodeName','yes') == 'yes')
					 {
						$replacementSrc = JRoute::_('index.php?option=com_imgen&imgencoded='.base64_encode($images->image_intro).'&format=image&width='.$width.'&height='.$height);
					 }
					 else
					 {
						$replacementSrc = JRoute::_('index.php?option=com_imgen&img='.rawurlencode($images->image_intro).'&format=image&width='.$width.'&height='.$height);
					 }
					 $images->image_intro = $replacementSrc;
			}
			$row->images = json_encode($images);
		}
		
		
		
		$entry_text = &$row->text ;
		$matches = array( );
		$user_params = array();
		if( preg_match_all( "/<img (.*?)\/?>/i", $entry_text, $matches, PREG_SET_ORDER ) > 0 ) {
			foreach( $matches as $match)//loop through image tag occurances
			{	
			
			
			   $tagContents = $match[1];
			   
			   $replacementClass='';
               if($pluginParams->get('useLazyLoad','no') == 'no')	
			   {
				   if(preg_match('/class *= *["\'](.*?)["\']/i',$tagContents, $subClass))
				   {
					   $replacementClass=' class="'.$subClass[1].'" ';
				   }
			   }
			   else if(preg_match('/class *= *["\'](.*?)["\']/i',$tagContents, $subClass))
			   {
				   $replacementClass=' class="'.$subClass[1].' lazy" ';
			   }
			   else
			   {
				   $replacementClass=' class="lazy" ';
			   }

			   
			   $replacementTitle='';
			   if(preg_match('/title *= *["\'](.*?)["\']/i',$tagContents, $subTitle))
			   {
				   $replacementTitle=' title="'.$subTitle[1].'" ';
			   }
			   
			   $replacementAlt='';
			   if(preg_match('/alt *= *["\'](.*?)["\']/i',$tagContents, $subAlt))
			   {
				   $replacementAlt=' alt="'.$subAlt[1].'" ';
			   }
			   
			   
			   $replacementStyle='';
			   if(preg_match('/style *= *["\'](.*?)["\']/i',$tagContents, $subStyle))
			   {
				   $replacementStyle=' style="'.$subStyle[1].'" ';
			   }
			   
			   $replacementAlign='';
			   if(preg_match('/align *= *["\'](.*?)["\']/i',$tagContents, $subAlign))
			   {
				   $replacementAlign=' align="'.$subAlign[1].'" ';
			   }
			   
			   
			   
			   
			   if(preg_match('/src *= *["\'](.*?)["\']/i',$tagContents, $subPatterns))
			   {
				   if(!preg_match('/\?/',$subPatterns[1]))
				   {
					   
					// $pattern = '#^'.$rootPath.'/?#';					 
					// $subPatterns[1] = preg_replace($pattern,'',$subPatterns[1]);
					   
					 if($pluginParams->get('encodeName','yes') == 'yes')
					 {
						$replacementSrc = JRoute::_('index.php?option=com_imgen&imgencoded='.base64_encode($subPatterns[1]).'&format=image&width='.$width.'&height='.$height);
					 }
					 else
					 {
						$replacementSrc = JRoute::_('index.php?option=com_imgen&img='.rawurlencode($subPatterns[1]).'&format=image&width='.$width.'&height='.$height);
					 }
					 
					 if($pluginParams->get('useLazyLoad','no') == 'no')	
					 {
						   
						   if ($pluginParams->get('htmlWidthHeight','yes') == 'yes')
						   {					 
							   $replacementText = ' src="'.$replacementSrc.'" width="'.$width.'" height="'.$height.'"'.$replacementClass.$replacementTitle.$replacementAlt.$replacementStyle.$replacementAlign;
						   }
						   else
						   {
							   $replacementText = ' src="'.$replacementSrc.'" '.$replacementClass.$replacementTitle.$replacementAlt;						 
						   }
					 }
					 else
					 {
						   if ($pluginParams->get('htmlWidthHeight','yes') == 'yes')
						   {					 
							   $replacementText = ' src="'.$blankSrc.'" data-src="'.$replacementSrc.'" width="'.$width.'" height="'.$height.'"'.$replacementClass.$replacementTitle.$replacementAlt.$replacementStyle.$replacementAlign;
						   }
						   else
						   {
							   $replacementText = ' src="'.$blankSrc.'" data-src="'.$replacementSrc.'" '.$replacementClass.$replacementTitle.$replacementAlt;						 
						   }
						 
					 }
					 
					 $row->text = str_ireplace($tagContents, $replacementText, $row->text);
					 
				   }
			   }
			}
	
			
		}
		
	 }
	 
}
