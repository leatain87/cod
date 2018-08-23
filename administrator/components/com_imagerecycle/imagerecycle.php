<?php
/**
 * ImageRecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@imagerecycle.com *
 * @package ImageRecycle
 * @copyright Copyright (C) 2014 ImageRecycle (http://www.imagerecycle.com). All rights reserved.
 * @copyright Copyright (C) 2014 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die;

//Register  base class
JLoader::register('ImagerecycleBase', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/classes/imagerecycleBase.php');

imagerecycleBase::initComponent();

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_imagerecycle')) {
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

// Execute the task.
$controller = JControllerLegacy::getInstance('Imagerecycle');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
