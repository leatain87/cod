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

defined('JPATH_BASE') or die;

/**
 * ReDJ HTML class
 *
 * @package ReDJ
 *
 */
abstract class JHtmlReDJ
{
  /**
   * Display supported macros in a table
   *
   * @return  string  The HTML table of supported macros
   */
  public static function macros()
  {
    $macros = file_get_contents(JPATH_COMPONENT.'/helpers/html/macros.txt');
    return $macros;
  }

}
