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

jimport('joomla.application.component.controllerlegacy');

class ImagerecycleController extends JControllerLegacy
{

    /**
     * default view for this controller
     */
    protected $default_view = 'imagerecycle';

    /**
     * Method to display the view.
     *
     * @access    public
     */
    function display($cachable = false, $urlparams = false)
    {
        // Load the submenu.
        ImagerecycleHelper::addSubmenu(JRequest::getCmd('view', $this->default_view));


        $vName = JRequest::getCmd('view', $this->default_view);
        $layout = JRequest::getCmd('layout', 'default');

        if ($vName == 'imagerecycle') {
            $view = $this->getView($vName, 'html');
            $modelDefault = $this->getModel('imagerecycle');
            $view->setModel($modelDefault, true);

        } elseif ($vName == 'category' && $layout == 'default') {
            $view = $this->getView($vName, 'raw');
            $model = $this->getModel('category');
            $view->setModel($model, false);
        }
//        }
        parent::display($cachable, $urlparams);
        return $this;
    }

    function getList()
    {
        $vName = 'imagerecycle';
        $view = $this->getView($vName, 'raw');
        $modelDefault = $this->getModel('imagerecycle');
        $view->setModel($modelDefault, true);
        parent::display();
    }
}
