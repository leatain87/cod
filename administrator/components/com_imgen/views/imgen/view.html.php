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
jimport( 'joomla.application.component.helper' );


class imgenViewImgen extends JViewLegacy
{
	function display($tpl = null){
		// global $mainframe;
		JToolBarHelper::title(   JText::_( 'IMGEN - Image generator for Joomla' ), 'generic.png' );
        JToolBarHelper::preferences('com_imgen', 400, 570);

		parent::display($tpl);

	}

}
?>