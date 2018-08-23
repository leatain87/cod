<?php
/**
 * @package         CDN for Joomla!
 * @version         6.1.1
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

// Do not instantiate plugin on install pages
// to prevent installation/update breaking because of potential breaking changes
if (
	in_array(JFactory::getApplication()->input->get('option'), ['com_installer', 'com_regularlabsmanager'])
	&& JFactory::getApplication()->input->get('action') != ''
)
{
	return;
}

if ( ! is_file(__DIR__ . '/vendor/autoload.php'))
{
	return;
}

require_once __DIR__ . '/vendor/autoload.php';

use RegularLabs\Plugin\System\CDNforJoomla\Plugin;

/**
 * Plugin that replaces stuff
 */
class PlgSystemCDNforJoomla extends Plugin
{
	public $_alias       = 'cdnforjoomla';
	public $_title       = 'CDN_FOR_JOOMLA';
	public $_lang_prefix = 'CDN';

	public $_page_types = ['html', 'feed', 'ajax', 'json', 'raw'];

	/*
	 * Below are the events that this plugin uses
	 * All handling is passed along to the parent run method
	 */
	public function onAfterRender()
	{
		$this->run();
	}
}
