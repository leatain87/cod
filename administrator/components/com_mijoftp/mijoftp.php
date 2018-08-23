<?php
/*
* @package		MijoFTP
* @copyright	2009-2012 Mijosoft LLC, www.mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION, '1.6.0', 'ge')) {
	if (!JFactory::getUser()->authorise('core.manage', 'com_mijoftp')) {
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}
	
	if (JFactory::getUser()->authorise('core.admin', 'com_mijoftp')) {
		JToolBarHelper::preferences('com_mijoftp', '550');
	}
}

require_once(JPATH_COMPONENT.'/mvc/model.php');
require_once(JPATH_COMPONENT.'/mvc/view.php');
require_once(JPATH_COMPONENT.'/mvc/controller.php');

$doc = JFactory::getDocument();
$doc->addStyleSheet('components/com_mijoftp/assets/css/mijoftp.css');
$doc->addScript('components/com_mijoftp/assets/js/iframeresizer.js');

JToolBarHelper::title(JText::_('MijoFTP'), 'mijoftp');

define('JPATH_MIJOFTP_QX', JPATH_ADMINISTRATOR.'/components/com_mijoftp/quixplorer');

ob_start();
require_once(JPATH_MIJOFTP_QX.'/index.php');
$output = ob_get_contents();
ob_end_clean();

$replace_output = array(
            'index.php?action=' => 'index.php?option=com_mijoftp&action=',
            'src="_img' => 'src="components/com_mijoftp/quixplorer/_img'
        );

foreach($replace_output as $key => $value){
    $output = str_replace($key, $value, $output);
}

echo $output;

echo '<div style="margin: 10px; text-align: center;"><a href="http://www.mijosoft.com/joomla-extensions/mijoftp-joomla-file-manager" target="_blank">MijoFTP | Copyright &copy; 2009-2012 Mijosoft LLC</a></div>';
