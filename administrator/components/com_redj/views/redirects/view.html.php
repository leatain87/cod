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
 * HTML View class for the ReDJ Redirects component
 *
 * @package ReDJ
 *
 */
class ReDJViewRedirects extends JViewLegacy
{
  protected $enabled;
  protected $items;
  protected $pagination;
  protected $state;

  public function display($tpl = null)
  {
    if ($this->getLayout() !== 'modal')
    {
      ReDJHelper::addSubmenu('redirects');
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
      JToolBarHelper::custom('redirects.copy', 'copy.png', 'copy_f2.png', 'COM_REDJ_TOOLBAR_COPY');
      JToolBarHelper::addNew('redirect.add');
    }

    if ($canDo->get('core.edit')) {
      JToolBarHelper::editList('redirect.edit');
    }

    JToolBarHelper::divider();

    if ($canDo->get('core.edit.state')) {
      if ($this->state->get('filter.state') != 2){
        JToolBarHelper::publish('redirects.publish', 'JTOOLBAR_PUBLISH', true);
        JToolBarHelper::unpublish('redirects.unpublish', 'JTOOLBAR_UNPUBLISH', true);
      }

      JToolBarHelper::divider();

      if ($this->state->get('filter.state') != -1 ) {
        if ($this->state->get('filter.state') != 2) {
          JToolBarHelper::archiveList('redirects.archive');
        }
        else if ($this->state->get('filter.state') == 2) {
          JToolBarHelper::unarchiveList('redirects.publish');
        }
      }

      //JToolBarHelper::checkin('redirects.checkin');
      JToolBarHelper::custom('redirects.checkin', 'checkin', '', 'JTOOLBAR_CHECKIN', true);
    }

    if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
      JToolBarHelper::deleteList('', 'redirects.delete', 'JTOOLBAR_EMPTY_TRASH');
    } elseif ($canDo->get('core.edit.state')) {
      JToolBarHelper::trash('redirects.trash');
    }

    if ( $canDo->get('core.delete')) {
      JToolBarHelper::custom('redirects.resetstats', 'chart', '', 'COM_REDJ_TOOLBAR_RESET_STATS', false);
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
      'a.id' => JText::_('COM_REDJ_HEADING_REDIRECTS_ID'),
      'a.fromurl' => JText::_('COM_REDJ_HEADING_REDIRECTS_FROMURL'),
      'a.tourl' => JText::_('COM_REDJ_HEADING_REDIRECTS_TOURL'),
      'a.skip' => JText::_('COM_REDJ_HEADING_REDIRECTS_SKIP'),
      'a.redirect' => JText::_('COM_REDJ_HEADING_REDIRECTS_REDIRECT'),
      'a.case_sensitive' => JText::_('COM_REDJ_HEADING_REDIRECTS_CASE_SENSITIVE'),
      'a.request_only' => JText::_('COM_REDJ_HEADING_REDIRECTS_REQUEST_ONLY'),
      'a.decode_url' => JText::_('COM_REDJ_HEADING_REDIRECTS_DECODE_URL'),
      'a.comment' => JText::_('COM_REDJ_HEADING_REDIRECTS_COMMENT'),
      'a.hits' => JText::_('COM_REDJ_HEADING_REDIRECTS_HITS'),
      'a.last_visit' => JText::_('COM_REDJ_HEADING_REDIRECTS_LAST_VISIT'),
      'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
      'a.published' => JText::_('COM_REDJ_HEADING_REDIRECTS_PUBLISHED')
    );
  }

}
