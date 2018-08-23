<?php
/*
 * @version 6.0.2
 * @package JotCache
 * @category Joomla 3.7
 * @copyright (C) 2010-2017 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
class MainViewRecache extends JViewLegacy {
protected $url = "http://kbase.jotcomponents.net/jotcache:help:direct60j3x:";
protected $filter = array();
protected $plugins = null;
function display($tpl = null) {
$input = JFactory::getApplication()->input;
$document = JFactory::getDocument();
$document->addScript('components/com_jotcache/assets/jotcache.js?ver=6.0.2');
$document->addStyleSheet('components/com_jotcache/assets/jotcache.css?ver=6.0.2');
$this->plugins = $this->get('Plugins');
$cid = $input->get('cid', null, 'array');
$this->filter['chck'] = (isset($cid)) ? true : false;
$this->filter['search'] = $input->getString('filter_search', '');
$this->filter['com'] = $input->getString('filter_com', '');
$this->filter['view'] = $input->getString('filter_view', '');
$this->filter['mark'] = ($input->getString('filter_mark', '')) ? 'Yes' : '';
$this->addToolbar();
parent::display($tpl);
}function stopRecache() {
$this->setLayout("stop");
parent::display();
}protected function addToolbar() {
JHTML::_('behavior.tooltip');
JHTML::_('behavior.keepalive');
JToolBarHelper::title(JText::_('JOTCACHE_RECACHE_TITLE'), 'jotcache-logo.gif');
    JToolBarHelper::custom('recache.start', 'start.png', 'start.png', JText::_('JOTCACHE_RECACHE_START'), false);
JToolBarHelper::spacer();
JToolBarHelper::custom('recache.stop', 'stop.png', 'stop.png', JText::_('JOTCACHE_RECACHE_STOP'), false);
JToolBarHelper::spacer();
JToolBarHelper::spacer();
JToolBarHelper::spacer();
JToolBarHelper::cancel('close', JText::_('JCANCEL'));
JToolBarHelper::spacer();
JToolbarHelper::help('Help', false, $this->url . 'recache_use');
}}