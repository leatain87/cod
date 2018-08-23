<?php
/*
 * @version 6.0.2
 * @package JotCachePlugins
 * @category Joomla 3.7
 * @copyright (C) 2010-2017 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
$app = JFactory::getApplication();
$depth = $app->getUserStateFromRequest('jotcache.crawlerext.depth', 'depth', $app->input->getInt('depth'), 'int');
$maxDepth = 5;
$lang = JFactory::getLanguage();
$lang->load('plg_jotcacheplugins_crawlerext', JPATH_ADMINISTRATOR, null, false, false);
$depthOptions = array();
for ($i = 1; $i < $maxDepth + 1; $i++) {
$depthOptions[$i] = $i;
}?>
<form action="<?php echo JRoute::_('index.php?option=com_jotcache'); ?>" method="post" name="adminForm_crawlerext" id="adminForm_Crawlerext">
  <h3><?php echo JText::_('PLG_JCPLUGINS_CRAWLEREXT_TITLE'); ?></h3>
  <table class="adminlist" style="width:400px;">
    <tr>
      <td style="padding-left: 0;" class="hasTip" title="<?php echo JText::_('PLG_JCPLUGINS_CRAWLEREXT_DEPTH_DESC'); ?>"><?php echo JText::_('PLG_JCPLUGINS_CRAWLEREXT_DEPTH'); ?> </td>
      <td style="padding-left: 0;"><?php echo JHTML::_('select.genericlist', $depthOptions, 'jcstates[depth]', 'style="width:100px;"', 'value', 'text', $depth); ?></td>
    </tr>
  </table>
  <input type="hidden" name="view" value="recache" />
  <input type="hidden" name="task" value="display" />
  <input type="hidden" name="scope" value="direct" />
  <input type="hidden" name="jotcacheplugin" value="crawlerext" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="hidemainmenu" value="0" />
  <?php echo JHtml::_('form.token'); ?>
</form>