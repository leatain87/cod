<?php
/**
 * Imagerecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@imagerecycle.com *
 * @package Imagerecycle
 * @copyright Copyright (C) 2014 ImageRecycle (http://www.imagerecycle.com). All rights reserved.
 * @copyright Copyright (C) 2014 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerlegacy');

class imagerecycleControllerUpdate extends JControllerLegacy
{

    public function check()
    {
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/json');

        $cookie = JFactory::getApplication()->input->cookie->get('com_imagerecycle_noCheckUpdates', false);
        $hide = JComponentHelper::getParams('com_imagerecycle')->get('hideupdate', 0);

        if ($cookie !== false || $hide == 1) {
            $return = false;
        } else {

            $lastVersion = imagerecycleBase::getLastExtensionVersion();
            $currentVersion = imagerecycleBase::getExtensionVersion();

            if (version_compare($lastVersion, $currentVersion) == 1) {
                $return = $lastVersion;
            } else {
                $return = false;
            }

        }
        echo json_encode($return);
        JFactory::getApplication()->close();
    }

}
