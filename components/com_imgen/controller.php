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

jimport('joomla.application.component.controller');

class ImgenController extends JControllerLegacy
{
	/**
	 * Custom Constructor
	 */

	function __construct()
	{
		parent::__construct();
	}

	function display($cachable = false, $urlparams = Array())
	{
		$jinput = JFactory::getApplication()->input;
				
		if ( ! $jinput->get( 'view','imgen','cmd' ) ) {
			$jinput->set('view', 'imgen' );
		}

		if ( $jinput->get( 'format' )== 'html' ) {
         $view = $this->getView('imgen','html');
		}
		else
		{
          $view = $this->getView('imgen','image');
		  $jinput->set('tmpl','component');
		  $jinput->set('format','image');
		}
		
		
		if ($layout = $jinput->get( 'layout','','cmd') ) {
		    $view->setLayout($layout);
		}
		
	
		parent::display($cachable, $urlparams);
	}




}
?>