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

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

class ReDJModelRedirects extends JModelList
{
  public function __construct($config = array())
  {
    if (empty($config['filter_fields'])) {
      $config['filter_fields'] = array(
        'id', 'a.id',
        'fromurl', 'a.fromurl',
        'tourl', 'a.tourl',
        'skip', 'a.skip',
        'skip_usergroups', 'a.skip_usergroups',
        'redirect', 'a.redirect',
        'case_sensitive', 'a.case_sensitive',
        'request_only', 'a.request_only',
        'decode_url', 'a.decode_url',
        'placeholders', 'a.placeholders',
        'comment', 'a.comment',
        'hits', 'a.hits',
        'last_visit', 'a.last_visit',
        'ordering', 'a.ordering',
        'published', 'a.published',
        'checked_out', 'a.checked_out',
        'checked_out_time', 'a.checked_out_time'
      );
    }

    parent::__construct($config);
  }

  /**
   * Method to auto-populate the model state
   *
   * Note. Calling getState in this method will result in recursion
   */
  protected function populateState($ordering = null, $direction = null)
  {
    $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
    // Convert search to lower case
    $search = JString::strtolower($search);
    $this->setState('filter.search', $search);

    $state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '');
    $this->setState('filter.state', $state);

    // Load the parameters.
    $params = JComponentHelper::getParams('com_redj');
    $this->setState('params', $params);

    // List state information.
    parent::populateState('a.ordering', 'asc');
  }

  /**
   * Method to build an SQL query to load the list data
   *
   * @return string An SQL query
   */
  protected function getListQuery()
  {
    // Create a new query object
    $db = $this->getDbo();
    $query = $db->getQuery(true);
    // Select required fields
    $query->select(
      $this->getState(
        'list.select',
        'a.id, a.fromurl, a.tourl, a.skip, a.skip_usergroups, a.redirect, a.case_sensitive, a.request_only, a.decode_url, a.placeholders, a.comment, a.hits, a.last_visit, a.ordering, a.published, a.checked_out, a.checked_out_time'
      )
    );

    // From the table
    $query->from('#__redj_redirects AS a');

    // Join over the users for the checked out user
    $query->select('uc.name AS editor');
    $query->join('LEFT', '#__users AS uc ON uc.id = a.checked_out');

    // Filter by state
    $state = $this->getState('filter.state');
    if (is_numeric($state)) {
      $query->where('(a.published = '.(int) $state.')');
    } else if ($state === '') {
      $query->where('(a.published IN (0, 1))'); // By default only published and unpublished
    }

    // Filter by search
    $search = $this->getState('filter.search');
    if (!empty($search)) {
      $query->where('(LOWER(a.fromurl) LIKE '.$db->quote('%'.$db->escape($search, true).'%').' OR LOWER(a.tourl) LIKE '.$db->quote('%'.$db->escape($search, true).'%').')');
    }

    // Add the list ordering clause
    $orderCol = $this->state->get('list.ordering', 'a.ordering');
    $orderDirn = $this->state->get('list.direction', 'asc');
    $query->order($db->escape($orderCol.' '.$orderDirn));

    return $query;
  }

}
