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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
?>
<?php if (!empty( $this->sidebar)): ?>
  <div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
  </div>
  <div id="j-main-container" class="span10">
<?php else : ?>
  <div id="j-main-container">
<?php endif;?>
<?php
  echo "<div style=\"float: left; margin-right: 20px;\"><img src=\"".JUri::root()."administrator/components/com_redj/images/logo.png\" alt=\"ReDJ\"/></div>";
  echo "<div style=\"float: none;\">".JText::_('COM_REDJ_COPYRIGHT')."</div>";
  echo "<div>".JText::_('COM_REDJ_ABOUT_DESC')."<br /><br /></div>";
  echo "<div>".JText::_('COM_REDJ_DONATE')."<br /><br /></div>";
  echo "<div>".JText::_('COM_REDJ_DONATE_PAYPAL')."</div>";
?>
  </div>