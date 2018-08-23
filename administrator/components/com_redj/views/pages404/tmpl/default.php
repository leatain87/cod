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

// Add tooltip style
$document = JFactory::getDocument();
$document->addStyleDeclaration( '.tip-text {word-wrap: break-word !important; word-break: break-all !important;}' );
$document->addStyleDeclaration( '.jrules td {padding: 0 10px 2px 0 !important; border: none !important;}' );

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
  Joomla.orderTable = function() {
    table = document.getElementById("sortTable");
    direction = document.getElementById("directionTable");
    order = table.options[table.selectedIndex].value;
    if (order != '<?php echo $listOrder; ?>') {
      dirn = 'asc';
    } else {
      dirn = direction.options[direction.selectedIndex].value;
    }
    Joomla.tableOrdering(order, dirn, '');
  }
  Joomla.submitbutton = function(pressbutton) {
  var form = document.adminForm;
    if (pressbutton == 'pages404.resetstats') {
        if ( confirm("<?php echo JText::_('COM_REDJ_RESET_STATS_CONFIRM', false); ?>") ) {
            Joomla.submitform('pages404.resetstats');
        }
    } else {
        Joomla.submitform(pressbutton);
    }
  }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_redj&view=pages404'); ?>" method="post" name="adminForm" id="adminForm">
  <?php if (!empty( $this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
  <?php else : ?>
    <div id="j-main-container">
  <?php endif;?>
    <?php
    // Search tools bar
    echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
    <table class="table table-striped" id="page404List">
      <thead>
        <tr>
          <th width="5%" class="center hidden-phone">
            <?php echo JText::_('COM_REDJ_NUM'); ?>
          </th>
          <th width="5%" class="center hidden-phone">
            <?php echo JHtml::_('grid.checkall'); ?>
          </th>
          <th width="25%">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_PAGES404_TITLE', 'p.title', $listDirn, $listOrder); ?>
          </th>
          <th width="15%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_PAGES404_LANGUAGE', 'p.language', $listDirn, $listOrder); ?>
          </th>
          <th width="35%">
            <?php echo JText::_('COM_REDJ_HEADING_PAGES404_PAGE'); ?>
          </th>
          <th width="5%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_PAGES404_HITS', 'p.hits', $listDirn, $listOrder); ?>
          </th>
          <th width="5%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_PAGES404_LAST_VISIT', 'p.last_visit', $listDirn, $listOrder); ?>
          </th>
          <th width="5%" class="nowrap center hidden-phone">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_PAGES404_ID', 'p.id', $listDirn, $listOrder); ?>
          </th>
        </tr>
      </thead>
      <tbody>
      <?php
        if( count( $this->items ) > 0 ) {
          foreach ($this->items as $i => $item) :
            $canCreate  = $user->authorise('core.create',     'com_redj.page404');
            $canEdit    = $user->authorise('core.edit',       'com_redj.page404.'.$item->id);
            $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
            $canChange  = $user->authorise('core.edit.state', 'com_redj.page404.'.$item->id) && $canCheckin;
            $item_link = JRoute::_('index.php?option=com_redj&task=page404.edit&id='.(int)$item->id);
      ?>
        <tr class="row<?php echo $i % 2; ?>" >
          <td class="center hidden-phone">
            <?php echo $this->pagination->getRowOffset( $i ); ?>
          </td>
          <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
          </td>
          <td class="small">
            <span style="display:block; word-wrap:break-word; word-break: break-all;">
            <?php if ($item->checked_out) : ?>
              <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'pages404.', $canCheckin); ?>
            <?php endif; ?>
            <?php
              $max_chars = 100;
              $item_title = ReDJHelper::trimText($item->title, $max_chars);
              if ($canEdit) : ?>
                <a href="<?php echo $item_link; ?>" title="<?php echo JText::_('COM_REDJ_EDIT_ITEM'); ?>"><?php echo $this->escape($item_title); ?></a>
              <?php else : ?>
                <span title="<?php echo JText::sprintf('COM_REDJ_HEADING_PAGES404_TITLE', $this->escape($item_title)); ?>"><?php echo $this->escape($item_title); ?></span>
              <?php endif; ?>
            </span>
          </td>
          <td class="small">
            <span style="display:block; word-wrap:break-word; word-break: break-all;">
            <?php
              $max_chars = 100;
              $item_language = ReDJHelper::trimText($item->language, $max_chars);
              if ($canEdit) : ?>
                <a href="<?php echo $item_link; ?>" title="<?php echo JText::_('COM_REDJ_EDIT_ITEM'); ?>"><?php echo $this->escape($item_language); ?></a>
              <?php else : ?>
                <span title="<?php echo JText::sprintf('COM_REDJ_HEADING_PAGES404_LANGUAGE', $this->escape($item_language)); ?>"><?php echo $this->escape($item_language); ?></span>
              <?php endif; ?>
            </span>
          </td>
          <td class="small">
            <span style="display:block; word-wrap:break-word; word-break: break-all;">
            <?php
              $max_chars = 100;
              $item_page = ReDJHelper::trimText($item->page, $max_chars);
              if ($canEdit) : ?>
                <a href="<?php echo $item_link; ?>" title="<?php echo JText::_('COM_REDJ_EDIT_ITEM'); ?>"><?php echo $this->escape($item_page); ?></a>
              <?php else : ?>
                <span title="<?php echo JText::sprintf('COM_REDJ_HEADING_PAGES404_PAGE', $this->escape($item_page)); ?>"><?php echo $this->escape($item_page); ?></span>
              <?php endif; ?>
            </span>
          </td>
          <td class="center">
            <?php echo $item->hits; ?>
          </td>
          <td class="center">
            <?php echo $item->last_visit; ?>
          </td>
          <td class="center hidden-phone">
            <?php echo $item->id; ?>
          </td>
        </tr>
      <?php
          endforeach;
        } else {
      ?>
        <tr>
          <td colspan="8">
            <?php echo JText::_('COM_REDJ_LIST_NO_ITEMS'); ?>
          </td>
        </tr>
      <?php
        }
      ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="8">
            <?php echo $this->pagination->getListFooter(); ?>
            <p class="footer-tip">
              <?php if ($this->enabled) : ?>
                <span class="enabled"><?php echo JText::sprintf('COM_REDJ_PLUGIN_ENABLED', JText::_('COM_REDJ_PLG_SYSTEM_REDJ')); ?></span>
              <?php else : ?>
                <span class="disabled"><?php echo JText::sprintf('COM_REDJ_PLUGIN_DISABLED', JText::_('COM_REDJ_PLG_SYSTEM_REDJ')); ?></span>
              <?php endif; ?>
            </p>
          </td>
        </tr>
      </tfoot>
    </table>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>
