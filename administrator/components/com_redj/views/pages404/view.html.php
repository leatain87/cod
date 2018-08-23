<?php
/**
 * ReDJ Community component for Joomla
 *
 * @author selfget.com (info@selfget.com)
 * @package ReDJ
 * @copyright Copyright 2009 - 2016
 * @license GNU Public License
 * @link http://www.selfget.com
 * @version 1.7.10
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_COMPONENT.'/helpers/redj.php';

/**
 * HTML View class for the ReDJ Pages 404 component
 *
 * @package ReDJ
 *
 */
class ReDJViewPages404 extends JViewLegacy
{
  protected $enabled;
  protected $items;
  protected $pagination;
  protected $state;

  public function display($tpl = null)
  {
    if ($this->getLayout() !== 'modal')
    {
      ReDJHelper::addSubmenu('pages404');
    }

    // Initialise variables
    $this->enabled = ReDJHelper::isEnabled();
    $this->items = $this->get('Items');
    $this->pagination = $this->get('Pagination');
    $this->state = $this->get('State');
    $this->filterForm = $this->get('FilterForm');
    $this->activeFilters = $this->get('ActiveFilters');

    // Check for errors
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode("\n", $errors));
      return false;
    }

    // We don't need toolbar in the modal window
    if ($this->getLayout() !== 'modal') {
      $this->addToolbar();
      $this->sidebar = JHtmlSidebar::render();
    }

    parent::display($tpl);
  }

  /**
   * Add the page title and toolbar
   *
   */
  protected function addToolbar()
  {
    JToolBarHelper::title(JText::_('COM_REDJ_MANAGER'), 'redj.png');

    $canDo = ReDJHelper::getActions();

    if ($canDo->get('core.create')) {
      JToolBarHelper::custom('pages404.copy', 'copy.png', 'copy_f2.png', JText::_('COM_REDJ_TOOLBAR_COPY'));
      JToolBarHelper::addNew('page404.add');
    }

    if ($canDo->get('core.edit')) {
      JToolBarHelper::editList('page404.edit');
    }

    JToolBarHelper::divider();

    if ($canDo->get('core.edit.state')) {
      //JToolBarHelper::checkin('pages404.checkin');
      JToolBarHelper::custom('pages404.checkin', 'checkin', '', 'JTOOLBAR_CHECKIN', true);
    }
    if ( $canDo->get('core.delete')) {
      JToolBarHelper::deleteList('', 'pages404.delete');
      JToolBarHelper::divider();
      JToolBarHelper::custom('pages404.resetstats', 'chart', '', 'COM_REDJ_TOOLBAR_RESET_STATS', false);
    }

    JToolBarHelper::divider();

    if ($canDo->get('core.admin')) {
      JToolBarHelper::preferences('com_redj');
    }

  }

  /**
   * Returns an array of fields the table can be sorted by
   *
   * @return  array  Array containing the field name to sort by as the key and display text as value
   *
   */
  protected function getSortFields()
  {
    return array(
      'p.id' => JText::_('COM_REDJ_HEADING_PAGES404_ID'),
      'p.title' => JText::_('COM_REDJ_HEADING_PAGES404_TITLE'),
      'p.language' => JText::_('COM_REDJ_HEADING_PAGES404_LANGUAGE'),
      'p.hits' => JText::_('COM_REDJ_HEADING_PAGES404_HITS'),
      'p.last_visit' => JText::_('COM_REDJ_HEADING_PAGES404_LAST_VISIT')
    );
  }

}
