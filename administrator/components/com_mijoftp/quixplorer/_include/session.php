<?php
/*
* @package		MijoFTP
* @copyright	2009-2012 Mijosoft LLC, www.mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
	This function allows access to session variables
*/
function session_get ($name)
{
	$user = $GLOBALS['__SESSION']["s_user"];
	if (!isset($GLOBALS['__SESSION']))
		return;

	if (!isset($GLOBALS['__SESSION'][$name]))
		return;
	
	return $GLOBALS['__SESSION'][$name];
}

?>
