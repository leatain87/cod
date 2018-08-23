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

// Add pre style
$document = JFactory::getDocument();
$document->addStyleDeclaration( 'pre {font-size: 11px;}' );
?>
<script type="text/javascript">
  Joomla.submitbutton = function(task)
  {
    if (task == 'redirect.cancel' || document.formvalidator.isValid(document.id('redirect-form'))) {
      Joomla.submitform(task, document.getElementById('redirect-form'));
    }
  }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_redj&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="redirect-form" class="form-validate form-horizontal">
  <div class="row-fluid">
    <!-- Begin Content -->
    <div class="span10 form-horizontal">

      <ul class="nav nav-tabs">
        <li class="active"><a href="#redirect" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_REDJ_REDIRECT_NEW_REDIRECT') : JText::_('COM_REDJ_REDIRECT_EDIT_REDIRECT'); ?></a></li>
        <li><a href="#stats" data-toggle="tab"><?php echo JText::_('COM_REDJ_REDIRECT_STATS'); ?></a></li>
        <li><a href="#help" data-toggle="tab"><?php echo JText::_('COM_REDJ_REDIRECT_QUICK_HELP_LABEL'); ?></a></li>
      </ul>

      <fieldset>
      <div class="tab-content">

        <div class="tab-pane active" id="redirect">
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('fromurl'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('fromurl'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('tourl'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('tourl'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('skip'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('skip'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('skip_usergroups'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('skip_usergroups'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('redirect'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('redirect'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('case_sensitive'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('case_sensitive'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('request_only'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('request_only'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('decode_url'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('decode_url'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('placeholders'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('placeholders'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('comment'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('comment'); ?></div>
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
          <?php echo JHtml::_('sliders.panel',JText::_('COM_REDJ_REDIRECT_QUICK_HELP_LABEL'), 'quick-help'); ?>
          <fieldset class="panelform">
            <?php $errordocument = "<b>ErrorDocument 404 " . JUri::root(true) . "/</b>"; ?>
            <p><?php echo JText::sprintf('COM_REDJ_REDIRECT_QUICK_HELP_DESC', $errordocument); ?></p> 
          </fieldset>
          <?php echo JHtml::_('sliders.panel',JText::_('COM_REDJ_REDIRECT_PLACEHOLDERS_LABEL'), 'placeholders'); ?>
          <fieldset class="panelform">
            <p><?php echo JText::_('COM_REDJ_REDIRECT_PLACEHOLDERS_DESC'); ?></p>
          </fieldset>
          <?php echo JHtml::_('sliders.panel',JText::_('COM_REDJ_REDIRECT_SUPPORTED_MACROS_LABEL'), 'supported-macros'); ?>
          <fieldset class="panelform">
            <p><?php echo JHtml::_('redj.macros');?></p>
          </fieldset>
          <?php echo JHtml::_('sliders.end'); ?>
        </div>

      </div>
      </fieldset>

    </div>
    <!-- End Content -->

    <!-- Begin Sidebar -->
    <div class="span2">

      <h4><?php echo JText::_('COM_REDJ_REDIRECT_PUBLISHING_OPTIONS');?></h4>
      <hr />
      <fieldset class="form-vertical">
      <div class="control-group">
        <div class="control-group">
          <div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
          <div class="controls"><?php echo $this->form->getInput('published'); ?></div>
        </div>
      </div>
      </fieldset>

    </div>
    <!-- End Sidebar -->
  </div> <!-- row-fluid -->
  <input type="hidden" name="task" value="" />
  <?php echo JHtml::_('form.token'); ?>
</form>
 