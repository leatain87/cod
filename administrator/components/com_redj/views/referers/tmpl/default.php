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
    if (pressbutton == 'referers.purge') {
        if ( confirm("<?php echo JText::_('COM_REDJ_PURGE_CONFIRM', false); ?>") ) {
            Joomla.submitform('referers.purge');
        } else {
            return false;
        }
    }
    if (pressbutton == 'referers.resetstats') {
        if ( confirm("<?php echo JText::_('COM_REDJ_RESET_STATS_CONFIRM', false); ?>") ) {
            Joomla.submitform('referers.resetstats');
        } else {
            return false;
        }
    }

    Joomla.submitform(pressbutton);
  }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_redj&view=referers'); ?>" method="post" name="adminForm" id="adminForm">
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
    <table class="table table-striped" id="refererList">
      <thead>
        <tr>
          <th width="4%" class="center hidden-phone">
            <?php echo JText::_('COM_REDJ_NUM'); ?>
          </th>
          <th width="4%" class="center hidden-phone">
            <?php echo JHtml::_('grid.checkall'); ?>
          </th>
          <th width="35%" style="min-width:55px" class="nowrap center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REFERERS_VISITED_URL', 'rv.visited_url', $listDirn, $listOrder); ?>
          </th>
          <th width="35%" class="center hidden-phone">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REFERERS_REFERER_URL', 'rr.referer_url', $listDirn, $listOrder); ?>
          </th>
          <th width="6%" class="center hidden-phone">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REFERERS_DOMAIN', 'rr.domain', $listDirn, $listOrder); ?>
          </th>
          <th width="6%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REFERERS_HITS', 'r.hits', $listDirn, $listOrder); ?>
          </th>
          <th width="6%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REFERERS_LAST_VISIT', 'r.last_visit', $listDirn, $listOrder); ?>
          </th>
          <th width="4%" class="nowrap center hidden-phone">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_REFERERS_ID', 'r.id', $listDirn, $listOrder); ?>
          </th>
        </tr>
      </thead>
      <tbody>
      <?php
        if( count( $this->items ) > 0 ) {
          foreach ($this->items as $i => $item) :
      ?>
        <tr class="row<?php echo $i % 2; ?>" >
          <td class="center hidden-phone">
            <?php echo $this->pagination->getRowOffset( $i ); ?>
          </td>
          <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
          </td>
          <td class="small">
            <?php
              if ($this->state->get('filter.decode')) {
                $content = urldecode($item->visited_url);
                $tip = JHtml::tooltipText('COM_REDJ_RAW', $item->visited_url);
              } else {
                $content = $item->visited_url;
                $tip = JHtml::tooltipText('COM_REDJ_DECODED', urldecode($item->visited_url));
              }
              if (strlen($item->visited_url) > 0)
              {
                $content .= '&nbsp;<a href="' . htmlspecialchars($item->visited_url) . '" target="_blank"><i class="icon-share"></i></a>';
              }
            ?>
            <span style="display:block; word-wrap:break-word; word-break: break-all;" class="hasTooltip" title="<?php echo $tip; ?>"><?php echo $content; ?></span>
          </td>
          <td class="small">
            <?php
              if ($this->state->get('filter.decode')) {
                $content = urldecode($item->referer_url);
                $tip = JHtml::tooltipText('COM_REDJ_RAW', $item->referer_url);
              } else {
                $content = $item->referer_url;
                $tip = JHtml::tooltipText('COM_REDJ_DECODED', urldecode($item->referer_url));
              }
              if (strlen($item->referer_url) > 0)
              {
                $content .= '&nbsp;<a href="' . htmlspecialchars($item->referer_url) . '" target="_blank"><i class="icon-share"></i></a>';
              }
            ?>
            <span style="display:block; word-wrap:break-word; word-break: break-all;" class="hasTooltip" title="<?php echo $tip; ?>"><?php echo $content; ?></span>
          </td>
          <td class="small">
            <?php
            if ($this->state->get('filter.decode')) {
                $content = urldecode($item->domain);
                $tip = JHtml::tooltipText('COM_REDJ_RAW', $item->domain);
              } else {
                $content = $item->domain;
                $tip = JHtml::tooltipText('COM_REDJ_DECODED', urldecode($item->domain));
              }
            ?>
            <span style="display:block; word-wrap:break-word; word-break: break-all;" class="hasTooltip" title="<?php echo $tip; ?>"><?php echo $content; ?></span>
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
