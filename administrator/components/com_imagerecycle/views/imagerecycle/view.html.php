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


defined('_JEXEC') or die;


class ImagerecycleViewImagerecycle extends JViewLegacy
{

    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->canDo = ImagerecycleHelper::getActions();
        $this->params = JComponentHelper::getParams('com_imagerecycle');

        $model = $this->getModel();
        $this->items = $model->getItems();
        $this->state = $this->get('State');
        $this->pagination = $this->get('Pagination');
        $this->setLayout(JRequest::getCmd('layout', 'default'));
        $this->totalImages = $model->getTotalImages();
        $this->totalOptimizedImages = $model->getTotalOptimizedImages();
        parent::display($tpl);

        //reset list fail files in session
        if (isset($_SESSION['jir_failFiles'])) {
            $_SESSION['jir_failFiles'] = array();
        }
        if (isset($_SESSION['jir_processed'])) {
            $_SESSION['jir_processed'] = 0;
        }

        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            $this->addToolbar();
        }
    }

    /**
     * Add the page title and toolbar.
     *
     * @since    1.6
     */
    protected function addToolbar()
    {

        $canDo = ImagerecycleHelper::getActions();

        JToolBarHelper::title(JText::_('COM_IMAGERECYCLE_MAIN_PAGE'), 'imagerecycle.png');

        $toolbar = JToolBar::getInstance();

        if ($canDo->get('core.admin')) {

            $toolbar->appendButton('popup', 'support', JText::_('COM_IMAGERECYCLE_VIEW_SUPPORT'), 'index.php?option=com_imagerecycle&view=support&tmpl=component', 700, 600, 0, 0);
            JToolBarHelper::preferences('com_imagerecycle');
        }

        JToolBarHelper::divider();
    }
}
