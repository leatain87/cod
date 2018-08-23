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

use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\StringHelper as RL_String;

class ModCacheCleaner
{
	public function __construct()
	{
		// Load plugin parameters
		$this->params = RL_Parameters::getInstance()->getPluginParams('cachecleaner');
	}

	public function render()
	{
		if ( ! isset($this->params->display_link))
		{
			return;
		}

		// load the admin language file
		RL_Language::load('mod_cachecleaner');

		JHtml::_('jquery.framework');

		$script = "
			var cachecleaner_base = '" . JUri::base(true) . "';
			var cachecleaner_root = '" . JUri::root() . "';
			var cachecleaner_msg_clean = '" . addslashes(RL_String::html_entity_decoder(JText::_('CC_CLEANING_CACHE'))) . "';
			var cachecleaner_msg_inactive = '" . addslashes(RL_String::html_entity_decoder(JText::sprintf('CC_SYSTEM_PLUGIN_NOT_ENABLED', '<a href=&quot;index.php?option=com_plugins&filter_type=system&filter_folder=system&search=cache cleaner&filter_search=cache cleaner&quot;>', '</a>'))) . "';
			var cachecleaner_msg_failure = '" . addslashes(RL_String::html_entity_decoder(JText::_('CC_CACHE_COULD_NOT_BE_CLEANED'))) . "';";
		RL_Document::scriptDeclaration($script);

		RL_Document::script('regularlabs/script.min.js');
		RL_Document::script('cachecleaner/script.min.js', '6.3.0');
		RL_Document::stylesheet('regularlabs/style.min.css');
		RL_Document::stylesheet('cachecleaner/style.min.css', '6.3.0');

		$text_ini = strtoupper(str_replace(' ', '_', $this->params->icon_text));
		$text     = JText::_($text_ini);
		if ($text == $text_ini)
		{
			$text = JText::_($this->params->icon_text);
		}

		if ($this->params->display_toolbar_button)
		{
			// Generate html for toolbar button
			$html    = [];
			$html[]  = '<a href="javascript:;" onclick="return false;"  class="btn btn-small cachecleaner_link">';
			$html[]  = '<span class="icon-reglab icon-cachecleaner"></span> ';
			$html[]  = $text;
			$html[]  = '</a>';
			$toolbar = JToolBar::getInstance('toolbar');
			$toolbar->appendButton('Custom', implode('', $html));
		}

		// Generate html for status link
		$html   = [];
		$html[] = '<div class="btn-group cachecleaner">';
		$html[] = '<span class="btn-group separator"></span>';
		$html[] = '<a href="javascript:;" onclick="return false;" class="cachecleaner_link">';

		if ($this->params->display_link != 'text')
		{
			$html[] = '<span class="icon-reglab icon-cachecleaner"></span> ';
		}

		if ($this->params->display_link != 'icon')
		{
			$html[] = $text;
		}

		$html[] = '</a>';
		$html[] = '</div>';

		echo implode('', $html);
	}
}
