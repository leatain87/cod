<?php
/**
 * Imagerecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@imagerecycle.com *
 * @package Imagerecycle
 * @copyright Copyright (C) 2014 ImageRecycle (http://www.imagerecycle.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.framework');
JHtml::_('behavior.colorpicker');
JHtml::_('formbehavior.chosen', '.chzn-select');

jimport('joomla.html.html.bootstrap');
jimport('joomla.application.component.helper');

$app = JFactory::getApplication();
$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$filter_optimized = $this->escape($this->state->get('filter.optimized'));
$sortFields = array(
    'a.filename' => JText::_('COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_FILENAME'),
    'a.filesize' => JText::_('COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_SORTBY_SIZE'),
    'a.date' => JText::_('COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_DATE'),
    'a.status' => JText::_('JSTATUS')
);

$baseUrl = str_replace('administrator', '', JUri::root());
?>

<script type="text/javascript">
    Joomla.orderTable = function () {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        }
        else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }

    //override Joomla.submitform
    var doAjax = false;
    var origSubmitform = Joomla.submitform;
    Joomla.submitform = function (task, form, validate) {
        if (!doAjax) {
            origSubmitform();
            return;
        }

        if (!form) {
            form = document.getElementById('adminForm');
        }
        $ = jQuery;
        form.task.value = 'getList';
        $.ajax({
            url: 'index.php?option=com_imagerecycle&task=getList&format=raw',
            data: $(form).serialize(),
            type: 'post',
            success: function (response) {
                $("#imagerecycleList tbody").remove();
                $("#imagerecycleList tfoot").remove();
                $("#imagerecycleList thead").after(response);
                initButtons();
            }
        });

        //reset
        if (!task) {
            form.task.value = task;
        } else {
            form.task.value = "";
        }
        doAjax = false;
        return false;
    }
</script>
<?php if ($this->params->get('api_key') == "" || $this->params->get('api_secret') == "") {
    echo $this->loadTemplate('intro');
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_imagerecycle'); ?>" method="post" name="adminForm"
      id="adminForm">
    <?php if (!empty($this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
        <?php else : ?>
        <div id="j-main-container">
            <?php endif; ?>
            <div id="filter-bar" class="btn-toolbar">
                <div class="btn-group pull-left">
                    <label for="filter_optimized"
                           class="element-invisible"><?php echo JText::_('COM_IMAGERECYCLE_FILTER_OPTIMIZED_STATUS'); ?></label>
                    <select name="filter_optimized" id="filter_optimized" class="input-medium"
                            onchange="Joomla.submitform();">
                        <option value=""><?php echo JText::_('COM_IMAGERECYCLE_FILTER_OPTIMIZED_ALL'); ?></option>
                        <option
                            value="no" <?php if ($filter_optimized == 'no') echo 'selected="selected"'; ?>><?php echo JText::_('COM_IMAGERECYCLE_FILTER_OPTIMIZED_NO'); ?></option>
                        <option
                            value="yes" <?php if ($filter_optimized == 'yes') echo 'selected="selected"'; ?>><?php echo JText::_('COM_IMAGERECYCLE_FILTER_OPTIMIZED_YES'); ?></option>
                    </select>
                </div>
                <div class="filter-search btn-group pull-left">
                    <label for="filter_search"
                           class="element-invisible"><?php echo JText::_('COM_IMAGERECYCLE_FILTER_SEARCH_DESC'); ?></label>
                    <input type="text" name="filter_search" id="filter_search"
                           placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
                           value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip"
                           title="<?php echo JHtml::tooltipText('COM_IMAGERECYCLE_SEARCH_IN_NAME'); ?>"/>
                </div>
                <div class="btn-group pull-left">
                    <button type="submit" class="btn hasTooltip"
                            title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i
                            class="icon-search"></i></button>
                    <button type="button" class="btn hasTooltip"
                            title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>"
                            onclick="document.id('filter_search').value='';this.form.submit();"><i
                            class="icon-remove"></i></button>
                </div>

                <div class="btn-group pull-right hidden-phone">
                    <label for="limit"
                           class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                    <?php $limitbox = $this->pagination->getLimitBox();
                    echo str_replace('Joomla.submitform();', 'document.getElementById(\'limit\').value=this.value;Joomla.submitform();', $limitbox); ?>
                </div>
                <div class="btn-group pull-right hidden-phone">
                    <label for="directionTable"
                           class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
                    <select name="directionTable" id="directionTable" class="input-medium"
                            onchange="Joomla.orderTable()">
                        <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
                        <option
                            value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
                        <option
                            value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
                    </select>
                </div>
                <div class="btn-group pull-right">
                    <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
                    <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                        <option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
                        <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
                    </select>
                </div>

            </div>
            <div class="clearfix"></div>
            <div id="ir-loader">
                <img class="ir-loader"
                     src="<?php echo JUri::root(); ?>/components/com_imagerecycle/assets/images/ajax-loader-blue.gif"/>
            </div>

            <div id="action-bar" class="">
                <div class="pull-left">

                    <select class="ir-bulk-action" name="bulk_action">
                        <option
                            value="-1"><?php echo JText::_('COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_BULK_ACTION'); ?></option>
                        <option
                            value="optimize_selected"><?php echo JText::_('COM_IMAGERECYCLE_CTR_IMAGE_OPTIMIZE_SELETED'); ?></option>
                        <option
                            value="revert_selected"><?php echo JText::_('COM_IMAGERECYCLE_CTR_IMAGE_REVERT_SELECTED'); ?></option>
                    </select>
                    <button class="do-bulk-action btn flat-button"
                            style="margin-bottom: 9px;"><?php echo JText::_('COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_APPLY'); ?></button>

                </div>
                <?php
                $now = time();
                $helper = new ImagerecycleHelper();
                $ao_lastRun = (int)$helper->getOption('queue_process_lastRun', 0);
                $ao_status = (int)$helper->getOption('queue_process_status', 0);
                $manual_start = (int)$helper->getOption('manual_start_optimzeAll', 0);
                $ao_running = (($ao_status || $manual_start)) ? true : false;
                ?>
                <div class="pull-left">
                    <span
                        style="display: inline-block; margin:5px 18px 0 10px;float: left;"><?php echo JText::_('COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_OR'); ?></span>
                    <?php if (!$ao_running) { ?>
                        <input id="dooptimizeall" class="btn btn-success button-primary action"
                               style="float:left;margin-right:10px" type="button"
                               value="<?php echo JText::_('COM_IMAGERECYCLE_CTR_IMAGE_OPTIMIZE_ALL'); ?>">
                    <?php } else { ?>
                        <input id="stopoptimizeall" class="btn action" style="float:left;margin-right:10px"
                               type="button"
                               value="<?php echo JText::_('COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_STOP_OPTIMIZATION'); ?>">
                        <span class="spinner" id="wpio_processspinner"></span><span id="wpio_processstatus"></span>
                    <?php } ?>
                </div>

                <div class="pull-right">
                    <?php //echo $this->pagination->getListFooter(); ?>
                </div>
            </div>
            <table class="table table-striped image-recycle" id="imagerecycleList">
                <thead>
                <tr>
                    <th width="1%" class="hidden-phone">
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </th>
                    <th>
                        <?php echo JText::_('COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_IMAGE'); ?>
                    </th>
                    <th width="30%">
                        <?php echo JHtml::_('grid.sort', 'COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_FILENAME', 'a.filename', $listDirn, $listOrder); ?>
                    </th>

                    <th width="10%" style="min-width:55px" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_SIZE', 'a.filesize', $listDirn, $listOrder); ?>
                    </th>

                    <th width="10%" style="min-width:55px" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_DATE', 'a.date', $listDirn, $listOrder); ?>
                    </th>

                    <th width="20%" style="min-width:55px" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.status', $listDirn, $listOrder); ?>
                    </th>

                    <th width="12%" class="nowrap center hidden-phone">
                        <?php echo JText::_('COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_ACTION'); ?>
                    </th>
                </tr>
                </thead>

                <?php
                echo $this->loadTemplate('items');
                ?>

            </table>

            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
            <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
            <?php echo JHtml::_('form.token'); ?>
        </div>
</form>
<?php
$progressVal = 0;
$pressMsg = "";
?>
<div id="progress_init" style="display: none">
    <progress value="<?php echo $progressVal; ?>" max="100"></progress>
    <span><?php echo $pressMsg; ?></span>
    <p class="timeRemain"></p>
</div>
<script>
    var optimize_url = 'index.php?option=com_imagerecycle&task=image.optimize';
    var revert_url = 'index.php?option=com_imagerecycle&task=image.revert';
    var unqueue_url = 'index.php?option=com_imagerecycle&task=image.unqueue';
    var start_optimizeall_url = 'index.php?option=com_imagerecycle&task=image.startOptimizeAll';
    var queue_count_url = 'index.php?option=com_imagerecycle&task=image.countQueue';
    var stop_optimizeall_url = 'index.php?option=com_imagerecycle&task=image.stopOptimizeAll';
    var startTime = <?php echo $helper->getOption('process_startTime', 0); ?>;
    var totalImagesProcessing = <?php echo $helper->getOption('totalImagesProcessing', 0); ?>;

</script>    