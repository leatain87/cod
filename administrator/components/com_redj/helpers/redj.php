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

/**
 * ReDJ Helper
 *
 * @package ReDJ
 *
 */
class ReDJHelper
{
  /**
   * Configure the Linkbar
   *
   * @param string The name of the active view
   *
   * @return void
   */
  public static function addSubmenu($vName)
  {
    $document = JFactory::getDocument();
    $document->addStyleDeclaration('.icon-48-redj {background-image: url(../administrator/components/com_redj/images/icon-48-redj.png);}');

    JHtmlSidebar::addEntry(
      JText::_('COM_REDJ_MENU_REDIRECTS'),
      'index.php?option=com_redj&view=redirects',
      $vName == 'redirects'
    );

    JHtmlSidebar::addEntry(
      JText::_('COM_REDJ_MENU_PAGES404'),
      'index.php?option=com_redj&view=pages404',
      $vName == 'pages404'
    );

    JHtmlSidebar::addEntry(
      JText::_('COM_REDJ_MENU_ERRORS'),
      'index.php?option=com_redj&view=errors',
      $vName == 'errors'
    );

    JHtmlSidebar::addEntry(
      JText::_('COM_REDJ_MENU_REFERERS'),
      'index.php?option=com_redj&view=referers',
      $vName == 'referers'
    );

    JHtmlSidebar::addEntry(
      JText::_('COM_REDJ_MENU_ABOUT'),
      'index.php?option=com_redj&view=about',
      $vName == 'about'
    );

  }

  /**
   * Gets a list of the actions that can be performed
   *
   * @return JObject
   */
  public static function getActions()
  {
    $user = JFactory::getUser();
    $result = new JObject;

    $assetName = 'com_redj';

    $actions = array(
      'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
    );

    foreach ($actions as $action) {
      $result->set($action,  $user->authorise($action, $assetName));
    }

    return $result;
  }

  /**
   * Gets a list of the error code
   *
   * @return array
   */
  public static function getErrorCodeOptions()
  {
    $options = array();

    /*$db = JFactory::getDbo();
    $query = $db->getQuery(true)
      ->select('DISTINCT error_code AS value, error_code AS text')
      ->from('#__redj_errors AS e')
      ->order('e.error_code');

    // Get the options.
    $db->setQuery($query);

    try
    {
      $options = $db->loadObjectList();
    }
    catch (RuntimeException $e)
    {
      JError::raiseWarning(500, $e->getMessage());
    }*/

    $codes = array("403", "404", "500");
    foreach ($codes as $code)
    {
      $code_obj = new stdClass;
      $code_obj->text = $code;
      $code_obj->value = $code;
      $options[] = $code_obj;
    }

    return $options;
  }

  /**
   * Gets a list of user group names from their id
   *
   * @param string Comma-separated list of groups is
   *
   * @return string Comma-separated list of groups name
   *
   */
  public static function getUserGroups($groups, $reload = false)
  {
    static $groupnames = array();

    if ( ($reload) || (empty($groupnames)) )
    {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true)
        ->select('id, title')
        ->from('#__usergroups')
        ->order('id');

      // Get the options.
      $db->setQuery($query);

      try
      {
        $groupnames = $db->loadAssocList('id');
      }
      catch (RuntimeException $e)
      {
        JError::raiseWarning(500, $e->getMessage());
      }
    }

    $names = '';
    $groups = array_filter(explode(',', $groups));
    foreach ($groups as $groupid) {
      $names .= isset($groupnames[$groupid]['title']) ? $groupnames[$groupid]['title'] . ',' : '';
    }
    $names= rtrim($names, ',');
    return $names;
  }

  /**
   * Return utf-8 substrings
   *
   * http://www.php.net/manual/en/function.substr.php#90148
   *
   */
  public static function substru($str, $from, $len)
  {
    return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $from .'}'.'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'. $len .'}).*#s','$1', $str);
  }

  public static function trimText($text_to_trim, $max_chars = '50')
  {
    $to_be_continued = '...';

    if ( (function_exists('mb_strlen')) && (function_exists('mb_substr')) )
    {
      // MultiByte version
      if( mb_strlen( $text_to_trim, 'UTF-8' ) > $max_chars ) {
        return mb_substr( $text_to_trim, 0, $max_chars, 'UTF-8' ) . $to_be_continued;
      } else {
        return $text_to_trim;
      }
    } else {
      // Safe version
      $text_trimmed = self::substru($text_to_trim, 0, $max_chars);
      if ( strlen($text_trimmed) < strlen ($text_to_trim) )
      {
        return $text_trimmed . $to_be_continued;
      } else {
        return $text_to_trim;
      }
    }
  }

  /**
   * Determines if the plugin for ReDJ to work is enabled
   *
   * @return boolean
   */
  public static function isEnabled()
  {
    $db = JFactory::getDbo();
    $db->setQuery(
      'SELECT enabled' .
      ' FROM #__extensions' .
      ' WHERE folder = '.$db->quote('system').
      '  AND element = '.$db->quote('redj')
    );
    $result = (boolean) $db->loadResult();
    if ($error = $db->getErrorMsg()) {
      JError::raiseWarning(500, $error);
    }
    return $result;
  }

}
