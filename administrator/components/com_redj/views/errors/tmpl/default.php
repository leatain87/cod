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
    if (pressbutton == 'errors.purge') {
        if ( confirm("<?php echo JText::_('COM_REDJ_PURGE_CONFIRM', false); ?>") ) {
            Joomla.submitform('errors.purge');
        } else {
            return false;
        }
    }
    if (pressbutton == 'errors.clean') {
        if ( confirm("<?php echo JText::_('COM_REDJ_CLEAN_CONFIRM', false); ?>") ) {
            Joomla.submitform('errors.clean');
        } else {
            return false;
        }
    }
    if (pressbutton == 'errors.resetstats') {
        if ( confirm("<?php echo JText::_('COM_REDJ_RESET_STATS_CONFIRM', false); ?>") ) {
            Joomla.submitform('errors.resetstats');
        } else {
            return false;
        }
    }

    Joomla.submitform(pressbutton);
  }
  function saveRedirectUrlAjax(id)
  {
    var url = document.getElementById("redirectUrl_" + id).value;
    var token = document.getElementById("sessionToken").value;
    var params = {};
    params["id"] = id;
    params["redirectUrl"] = url;
    params[token] = 1;
    jQuery(document).ready(function ($){
      $.ajax({
              url: "index.php?option=com_redj&task=errors.saveRedirectUrlAjax",
              type: "POST",
              dataType: "json",
              data: params
             }).done(function(data) {
              if (data.result == 1) {
                $('#redirectCell_' + id).html(url);
              }
             });
    });
  }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_redj&view=errors'); ?>" method="post" name="adminForm" id="adminForm">
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
    <table class="table table-striped" id="errorList">
      <thead>
        <tr>
          <th width="4%" class="center hidden-phone">
            <?php echo JText::_('COM_REDJ_NUM'); ?>
          </th>
          <th width="4%" class="center hidden-phone">
            <?php echo JHtml::_('grid.checkall'); ?>
          </th>
          <th width="25%" style="min-width:55px" class="nowrap center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_ERRORS_VISITED_URL', 'e.visited_url', $listDirn, $listOrder); ?>
          </th>
          <th width="4%" class="center hidden-phone">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_ERRORS_ERROR_CODE', 'e.error_code', $listDirn, $listOrder); ?>
          </th>
          <th width="25%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_ERRORS_REDIRECT_URL', 'e.redirect_url', $listDirn, $listOrder); ?>
          </th>
          <th width="4%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_ERRORS_HITS', 'e.hits', $listDirn, $listOrder); ?>
          </th>
          <th width="5%" class="center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_ERRORS_LAST_VISIT', 'e.last_visit', $listDirn, $listOrder); ?>
          </th>
          <th width="25%" style="min-width:55px" class="nowrap center">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_ERRORS_LAST_REFERER', 'e.last_referer', $listDirn, $listOrder); ?>
          </th>
          <th width="4%" class="nowrap center hidden-phone">
            <?php echo JHtml::_('searchtools.sort', 'COM_REDJ_HEADING_ERRORS_ID', 'e.id', $listDirn, $listOrder); ?>
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
          <td class="center">
            <?php echo $item->error_code; ?>
          </td>
          <td class="center">
            <div id="redirectCell_<?php echo $item->id; ?>"><?php echo $item->redirect_url; ?></div>
            <a class="btn btn-micro pull-right" data-toggle="modal" data-target="#redirectModal_<?php echo $item->id; ?>"><i class="icon-edit"></i></a>
            <div class="modal fade" id="redirectModal_<?php echo $item->id; ?>" tabindex="-1" role="dialog" aria-labelledby="#redirectLabel_<?php echo $item->id; ?>" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="redirectLabel_<?php echo $item->id; ?>"><?php echo JText::_('COM_REDJ_HEADING_ERRORS_REDIRECT_URL'); ?></h4>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                      <input style="width: 90%;" type="text" class="form-control" id="redirectUrl_<?php echo $item->id; ?>" name="redirectUrl" value="<?php echo $item->redirect_url; ?>" placeholder="<?php echo JText::_('COM_REDJ_HEADING_ERRORS_REDIRECT_URL'); ?>">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="saveRedirectUrlAjax(<?php echo $item->id; ?>);"><?php echo JText::_('JSUBMIT'); ?></button>
                  </div>
                </div>
              </div>
            </div>
          </td>
          <td class="center">
            <?php echo $item->hits; ?>
          </td>
          <td class="center">
            <?php echo $item->last_visit; ?>
          </td>
          <td class="small">
            <?php
              if ($this->state->get('filter.decode')) {
                $content = urldecode($item->last_referer);
                $tip = JHtml::tooltipText('COM_REDJ_RAW', $item->last_referer);
              } else {
                $content = $item->last_referer;
                $tip = JHtml::tooltipText('COM_REDJ_DECODED', urldecode($item->last_referer));
              }
              if (strlen($item->last_referer) > 0)
              {
                $content .= '&nbsp;<a href="' . htmlspecialchars($item->last_referer) . '" target="_blank"><i class="icon-share"></i></a>';
              }
            ?>
            <span style="display:block; word-wrap:break-word; word-break: break-all;" class="hasTooltip" title="<?php echo $tip; ?>"><?php echo $content; ?></span>
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
          <td colspan="9">
            <?php echo JText::_('COM_REDJ_LIST_NO_ITEMS'); ?>
          </td>
        </tr>
      <?php
        }
      ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="9">
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
    <input type="hidden" id="sessionToken" value="<?php echo JSession::getFormToken(); ?>" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>
