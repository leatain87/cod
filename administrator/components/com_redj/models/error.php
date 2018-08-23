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
jimport('joomla.application.component.modeladmin');

class ReDJModelError extends JModelAdmin
{
  /**
   * Returns a reference to the a Table object, always creating it
   *
   * @param  type  The table type to instantiate
   * @param  string  A prefix for the table class name. Optional
   * @param  array  Configuration array for model. Optional
   * @return  JTable  A database object
   */
  public function getTable($type = 'Error', $prefix = 'ReDJTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Method for getting the form from the model
   *
   * @param  array    $data      Data for the form
   * @param  boolean  $loadData  True if the form is to load its own data (default case), false if not
   *
   * @return  mixed  A JForm object on success, false on failure
   *
   */
  public function getForm($data = array(), $loadData = true)
  {
    return false;
  }

  /**
   * Method to update redirect URL
   *
   * @access  public
   * @return  boolean  True on success
   */
  public function setRedirectUrl($id, $redirect_url)
  {
    $db = $this->getDbo();
    $redirect_url = trim($redirect_url);
    $query = 'UPDATE #__redj_errors SET `redirect_url` = ' . $db->quote($redirect_url) . ' WHERE `id` = ' . $db->quote($id);
    $db->setQuery( $query );
    if(!$db->query()) {
      $this->setError($db->getErrorMsg());
      return false;
    }
    return true;
  }

  /**
   * Method to purge all items
   *
   * @access  public
   * @return  boolean  True on success
   */
  public function purge($skip_redirected = false)
  {
    if ($this->canDelete(null)) {
      // Create a new query object
      $db = $this->getDbo();
      if ($skip_redirected) {
        $query = 'DELETE FROM #__redj_errors WHERE redirect_url = \'\'';
      } else {
        $query = 'TRUNCATE TABLE #__redj_errors';
      }
      $db->setQuery( $query );
      if(!$db->query()) {
        $this->setError($db->getErrorMsg());
        return false;
      }

      return true;

    } else {
      JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
      return false;
    }
  }

  /**
   * Method to reset hits and last visit for all items
   *
   * @access  public
   * @return  boolean  True on success
   */
  public function resetstats()
  {
    $db = $this->getDbo();
    $query = 'UPDATE #__redj_errors'
        . ' SET hits = 0, last_visit = \'0000-00-00 00:00:00\''
    ;
    $db->setQuery( $query );
    if(!$db->query()) {
      $this->setError($db->getErrorMsg());
      return false;
    }

    return true;
  }

}
