<?php
/*
* @package		MijoFTP
* @copyright	2009-2012 Mijosoft LLC, www.mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('_JEXEC') or die ('Restricted access');

class com_MijoftpInstallerScript {
	
	public function postflight($type, $parent) {
?>
<img src="components/com_mijoftp/assets/images/logo.png" alt="Joomla File Manager" style="width:80px; height:80px; float: left; padding-right:15px;" />

<h2>MijoFTP Installation</h2>
<h2><a href="index.php?option=com_mijoftp">Go to MijoFTP</a></h2>
<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'MijoFTP '.JText::_('Component'); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
</table>
	<?php
    }
}