<?php
/**
 * ImageRecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@imagerecycle.com *
 * @package ImageRecycle
 * @copyright Copyright (C) 2014 ImageRecycle (http://www.imagerecycle.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die;

//Register  base class
JLoader::register('ImagerecycleBase', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/classes/imagerecycleBase.php');

$config = array();

$view = JFactory::getApplication()->input->get('view', null);
$task = JFactory::getApplication()->input->get('task', null);

if (preg_match('/^front.*/', $task) || ($task === null && preg_match('/^front.*/', $view))) {
    imagerecycleBase::initFrontComponent();
    require_once JPATH_COMPONENT . '/helpers/category.php';
    require_once JPATH_COMPONENT . '/helpers/query.php';
} else {
    imagerecycleBase::initComponent();
    if ($task != 'image.optimizeAll' && $task != 'image.startOptimizeAll' && !JFactory::getUser()->authorise('core.manage', 'com_imagerecycle')) {
        return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
    }

    // Execute the task.
    $config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
}
// Include dependancies
jimport('joomla.application.component.controller');


$controller = JControllerLegacy::getInstance('Imagerecycle', $config);
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();