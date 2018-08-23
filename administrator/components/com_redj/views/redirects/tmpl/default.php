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
$canOrder = $user->authorise('core.edit.state');
$saveOrder = $listOrder=='a.ordering';
if ($saveOrder)
{
  $saveOrderingUrl = 'index.php?option=com_redj&task=redirects.saveOrderAjax&tmpl=component';
  JHtml::_('sortablelist.sortable', 'redirectList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
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
    if (pressbutton == 'redirects.resetstats') {
        if ( confirm("<?php echo JText::_('COM_REDJ_RESET_STATS_CONFIRM', false); ?>") ) {
            Joomla.submitform('redirects.resetstats');
        }
    } else {
        Joomla.submitform(pressbutton);
    }
  }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_redj&view=redirects'); ?>" method="post" name="adminForm" id="adminForm">
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
    <table class="table table-striped" id="redirectList">
      <thead>
        <tr>
          <th width="5%" class="center hidden-phone">
            <?php echo JText::_('COM_REDJ_NUM'); ?>
          </th>
          <th width="5%" class="nowrap center hidden-phone">
            <?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
          </th>
          <th width="5%" class="center hidden-phone">
            <?php echo JHtml::_('grid.checkall'); ?>
          </th>
          <th width="5%" class="nowrap center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REDIRECTS_PUBLISHED', 'a.published', $listDirn, $listOrder); ?>
          </th>
          <th width="20%">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REDIRECTS_FROMURL', 'a.fromurl', $listDirn, $listOrder); ?>
            <br />
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REDIRECTS_SKIP', 'a.skip', $listDirn, $listOrder); ?>
          </th>
          <th width="20%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REDIRECTS_TOURL', 'a.tourl', $listDirn, $listOrder); ?>
            <br />
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REDIRECTS_COMMENT', 'a.comment', $listDirn, $listOrder); ?>
          </th>
          <th width="5%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REDIRECTS_REDIRECT', 'a.redirect', $listDirn, $listOrder); ?>
          </th>
          <th width="20%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REDIRECTS_CASE_SENSITIVE', 'a.case_sensitive', $listDirn, $listOrder); ?>
            <br />
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REDIRECTS_REQUEST_ONLY', 'a.request_only', $listDirn, $listOrder); ?>
            <br />
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REDIRECTS_DECODE_URL', 'a.decode_url', $listDirn, $listOrder); ?>
          </th>
          <th width="10%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REDIRECTS_HITS', 'a.hits', $listDirn, $listOrder); ?>
          </th>
          <th width="10%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REDIRECTS_LAST_VISIT', 'a.last_visit', $listDirn, $listOrder); ?>
          </th>
          <th width="5%" class="nowrap center hidden-phone">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REDIRECTS_ID', 'a.id', $listDirn, $listOrder); ?>
          </th>
        </tr>
      </thead>
      <tbody>
      <?php
        if( count( $this->items ) > 0 ) {
          foreach ($this->items as $i => $item) :
            $ordering   = ($listOrder == 'a.ordering');
            $canCreate  = $user->authorise('core.create',     'com_redj.redirect');
            $canEdit    = $user->authorise('core.edit',       'com_redj.redirect.'.$item->id);
            $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
            $canChange  = $user->authorise('core.edit.state', 'com_redj.redirect.'.$item->id) && $canCheckin;
            $item_link = JRoute::_('index.php?option=com_redj&task=redirect.edit&id='.(int)$item->id);
      ?>
        <tr class="row<?php echo $i % 2; ?>" sortable-group-id="redirects">
          <td class="center hidden-phone">
            <?php echo $this->pagination->getRowOffset( $i ); ?>
          </td>
          <td class="order nowrap center hidden-phone">
            <?php
            $iconClass = '';
            if (!$canChange)
            {
              $iconClass = ' inactive';
            }
            elseif (!$saveOrder)
            {
              $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
            }
            ?>
            <span class="sortable-handler <?php echo $iconClass ?>">
              <i class="icon-menu"></i>
            </span>
            <?php if ($canChange && $saveOrder) : ?>
              <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
            <?php endif; ?>
          </td>
          <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
          </td>
          <td class="center">
            <div class="btn-group">
              <?php echo JHtml::_('jgrid.published', $item->published, $i, 'redirects.', $canChange, 'cb'); ?>
            </div>
          </td>
          <td class="small">
            <span style="display:block; word-wrap:break-word; word-break: break-all;">
            <?php if ($item->checked_out) : ?>
              <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'redirects.', $canCheckin); ?>
            <?php endif; ?>
            <?php
              $max_chars = 100;
              $item_fromurl = ReDJHelper::trimText($item->fromurl, $max_chars);
              if ($canEdit) : ?>
                <a href="<?php echo $item_link; ?>" title="<?php echo JText::_('COM_REDJ_EDIT_ITEM'); ?>"><?php echo $this->escape($item_fromurl); ?></a>
              <?php else : ?>
                <span title="<?php echo JText::sprintf('COM_REDJ_HEADING_REDIRECTS_FROMURL', $this->escape($item_fromurl)); ?>"><?php echo $this->escape($item_fromurl); ?></span>
              <?php endif; ?>
            </span>
            <span style="display:block; word-wrap:break-word; word-break: break-all;">
            <?php
              $max_chars = 100;
              $item_skip = ReDJHelper::trimText($item->skip, $max_chars);
              if ($canEdit) : ?>
                <a href="<?php echo $item_link; ?>" title="<?php echo JText::_('COM_REDJ_EDIT_ITEM'); ?>"><?php echo $this->escape($item_skip); ?></a>
              <?php else : ?>
                <span title="<?php echo JText::sprintf('COM_REDJ_HEADING_REDIRECTS_SKIP', $this->escape($item_skip)); ?>"><?php echo $this->escape($item_skip); ?></span>
              <?php endif; ?>
            </span>
            <br />
            <?php $skip_usergroups = ReDJHelper::getUserGroups($item->skip_usergroups); ?>
            <div style="border: 1px dashed silver; padding: 1px; margin-bottom: 1px; word-wrap:break-word; word-break: break-all;" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_REDJ_HEADING_REDIRECTS_SKIP_USERGROUPS', $skip_usergroups); ?>">
              <?php echo '<strong>' . JText::_('COM_REDJ_HEADING_REDIRECTS_SKIP_USERGROUPS') . '</strong><br />'. $skip_usergroups; ?>
            </div>
          </td>
          <td class="small">
            <span style="display:block; word-wrap:break-word; word-break: break-all;">
            <?php
              $max_chars = 100;
              $item_tourl = ReDJHelper::trimText($item->tourl, $max_chars);
              if ($canEdit) : ?>
                <a href="<?php echo $item_link; ?>" title="<?php echo JText::_('COM_REDJ_EDIT_ITEM'); ?>"><?php echo $this->escape($item_tourl); ?></a>
              <?php else : ?>
                <span title="<?php echo JText::sprintf('COM_REDJ_HEADING_REDIRECTS_TOURL', $this->escape($item_tourl)); ?>"><?php echo $this->escape($item_tourl); ?></span>
              <?php endif; ?>
            </span>
            <br />
            <div style="border: 1px dashed silver; min-height: 30px; word-wrap: break-word; word-break: break-all;" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_REDJ_FIELD_REDIRECT_COMMENT_LABEL', $item->comment); ?>">
              <?php
                $max_chars = 100;
                echo ReDJHelper::trimText(htmlspecialchars($item->comment, ENT_QUOTES), $max_chars);
              ?>
            </div>
          </td>
          <td class="center">
            <?php echo $item->redirect; ?>
          </td>
          <td class="center">
            <table class="jrules">
            <?php echo '<tr><td>'.JText::_('COM_REDJ_FIELD_REDIRECT_CASE_SENSITIVE_LABEL').'</td>';
            if ($item->case_sensitive) {
              $jtask = 'redirects.case_off'; $jtext = JText::_( 'JYES' ); $jstate = 'publish';
            } else {
              $jtask = 'redirects.case_on'; $jtext = JText::_( 'JNO' ); $jstate = 'unpublish';
            } ?>
            <td><div class="btn-group"><a class="btn btn-micro active" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $jtask; ?>')" title="<?php echo $jtext; ?>"><i class="icon-<?php echo $jstate; ?>"></i></a></div></td></tr>
            <?php echo '<tr><td>'.JText::_('COM_REDJ_FIELD_REDIRECT_REQUEST_ONLY_LABEL').'</td>';
            if ($item->request_only) {
              $jtask = 'redirects.request_off'; $jtext = JText::_( 'JYES' ); $jstate = 'publish';
            } else {
              $jtask = 'redirects.request_on'; $jtext = JText::_( 'JNO' ); $jstate = 'unpublish';
            } ?>
            <td><div class="btn-group"><a class="btn btn-micro active" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $jtask; ?>')" title="<?php echo $jtext; ?>"><i class="icon-<?php echo $jstate; ?>"></i></a></div></td></tr>
            <?php echo '<tr><td>'.JText::_('COM_REDJ_FIELD_REDIRECT_DECODE_URL_LABEL').'</td>';
            if ($item->decode_url) {
              $jtask = 'redirects.decode_off'; $jtext = JText::_( 'JYES' ); $jstate = 'publish';
            } else {
              $jtask = 'redirects.decode_on'; $jtext = JText::_( 'JNO' ); $jstate = 'unpublish';
            } ?>
            <td><div class="btn-group"><a class="btn btn-micro active" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $jtask; ?>')" title="<?php echo $jtext; ?>"><i class="icon-<?php echo $jstate; ?>"></i></a></div></td></tr>
            <tr><td><?php echo JText::_('COM_REDJ_FIELD_REDIRECT_PLACEHOLDERS_LABEL') . '&nbsp;' . JHTML::tooltip(nl2br($item->placeholders), JText::_('COM_REDJ_FIELD_REDIRECT_PLACEHOLDERS_LABEL'), 'tooltip.png', '', ''); ?></td><td><?php echo count(array_filter(explode("\n", trim($item->placeholders)))); ?></td></tr>
            </table>
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
          <td colspan="11">
            <?php echo JText::_('COM_REDJ_LIST_NO_ITEMS'); ?>
          </td>
        </tr>
      <?php
        }
      ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="11">
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
