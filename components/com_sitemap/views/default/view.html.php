<?php

/**
* Qlue Sitemap
*
* @author Jon Boutell
* @package QMap
* @license GNU/GPL
* @version 1.0
*
* This component gathers information from various Joomla Components and 
* compiles them into a sitemap, supporting both an HTML view and an XML 
* format for search engines.
*
*/

defined('_JEXEC') or die('Restricted Access');

class SitemapViewDefault extends JViewLegacy
{

	protected $items = null;

	public function display($tpl = null) {
		$this->items = $this->get('Items');
		$this->sitemap = $this->get('Item');

		parent::display($tpl);
	}
}

?>