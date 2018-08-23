<?php
/**
 * @copyright	Copyright (C) 2011 Inspiration Web Design. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport('joomla.plugin.helper');
require_once(JPATH_SITE.'/components/com_imgen/helpers/imgen.php');



class plgImageImagerestrict extends JPlugin
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
	function plgImageImagerestrict( &$subject, $params )
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
	function onImagePrepare( &$image, &$params, $context = '' )
	{
		//global $mainframe;
		
       if(JPluginHelper::isEnabled('image','imagerestrict')==false) return;
		
		
		$pluginParams = $this->params;	
		
		$usergroups = (array)$pluginParams->get('usergroups', array());
		JArrayHelper::toInteger($usergroups);
		
		if(count($usergroups) == 0){return;}
		
		$user = JFactory::getUser();
		
		if(isset($user->groups) && count($user->groups) )
		{
			foreach($user->groups as $group)
			{
				if(in_array($group,$usergroups))
				{
					//user is permitted to view image so return
					return;
				}
			}
		}
		
		//user doesn't have permission to view image, so replace with restricted image
		
		if($pluginParams->get('showAlt','yes') == 'yes')
		{
		  $restrictedImage = $pluginParams->get('imageRestricted','media/imgen/restricted-image.png');
		  if(!empty($restrictedImage))
		  {
			  $image = imgenHelper::getImagePath($restrictedImage);
		  }
		  else
		  {
			$image = new ImgenImage();
			return;	
		  }
		}
		else
		{
            $image = new ImgenImage();
			return;	
			
		}
		
			
		
				  
	

	}


}
