<?php
/**
 * @package         Cache Cleaner
 * @version         6.3.0
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * Module that cleans cache
 */

// return if Regular Labs Library plugin is not installed
jimport('joomla.filesystem.file');
if (
	! is_file(JPATH_PLUGINS . '/system/regularlabs/regularlabs.xml')
	|| ! is_file(JPATH_LIBRARIES . '/regularlabs/autoload.php')
)
{
	return;
}

// return if Regular Labs Library plugin is not enabled
if ( ! JPluginHelper::isEnabled('system', 'regularlabs'))
{
	return;
}

require_once JPATH_LIBRARIES . '/regularlabs/autoload.php';

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$helper = new ModCacheCleaner;
$helper->render();
