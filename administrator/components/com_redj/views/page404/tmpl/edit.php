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

// Include the component HTML helpers
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
  Joomla.submitbutton = function(task)
  {
    if (task == 'page404.cancel' || document.formvalidator.isValid(document.id('page404-form'))) {
      Joomla.submitform(task, document.getElementById('page404-form'));
    }
  }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_redj&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="page404-form" class="form-validate form-horizontal">
  <div class="row-fluid">
    <!-- Begin Content -->
    <div class="span12 form-horizontal">

      <ul class="nav nav-tabs">
        <li class="active"><a href="#page404" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_REDJ_PAGE404_NEW') : JText::_('COM_REDJ_PAGE404_EDIT'); ?></a></li>
        <li><a href="#stats" data-toggle="tab"><?php echo JText::_('COM_REDJ_PAGE404_STATS'); ?></a></li>
        <li><a href="#help" data-toggle="tab"><?php echo JText::_('COM_REDJ_PAGE404_QUICK_HELP_LABEL'); ?></a></li>
      </ul>

      <fieldset>
      <div class="tab-content">

        <div class="tab-pane active" id="page404">
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('language'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('language'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('page'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('page'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
          </div>
        </div>

        <div class="tab-pane" id="stats">
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('hits'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('hits'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('last_visit'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('last_visit'); ?></div>
          </div>
        </div>

        <div class="tab-pane" id="help">
          <?php echo JHtml::_('sliders.start','redj-rule-sliders-'.$this->item->id, array('useCookie'=>1, 'allowAllClose'=>1, 'startOffset'=>-1)); ?>
          <?php echo JHtml::_('sliders.panel',JText::_('COM_REDJ_PAGE404_QUICK_HELP_LABEL'), 'quick-help'); ?>
          <fieldset class="panelform">
            <p><?php echo JText::_('COM_REDJ_PAGE404_QUICK_HELP_DESC'); ?></p>
          </fieldset>
          <?php echo JHtml::_('sliders.end'); ?>
        </div>

      </div>
      </fieldset>

    </div>
    <!-- End Content -->

  </div> <!-- row-fluid -->
  <input type="hidden" name="task" value="" />
  <?php echo JHtml::_('form.token'); ?>
</form>
