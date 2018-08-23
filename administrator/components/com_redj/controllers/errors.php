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

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * ReDJ Controller Errors
 *
 * @package ReDJ
 *
 */
class ReDJControllerErrors extends JControllerAdmin
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        // Register Extra tasks
        $this->registerTask('purge', 'purge');
        $this->registerTask('clean', 'clean');
    }

    public function saveRedirectUrlAjax()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $id = $this->input->getInt('id');
        $redirect_url = $this->input->getString('redirectUrl');

        $model = $this->getModel('error');
        $return = $model->setRedirectUrl($id, $redirect_url);
        echo json_encode(array('result' => (int) $return));

        // Close the application
        JFactory::getApplication()->close();
    }

    public function purge($skip_redirected = false)
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('error');
        if(!$model->purge($skip_redirected)) {
          JError::raiseWarning(500, $model->getError());
        }

        $this->setRedirect( 'index.php?option=com_redj&view=errors' );
    }

    public function clean()
    {
      $this->purge(true);
    }

    public function resetstats()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('error');
        if(!$model->resetstats()) {
          $message = JText::sprintf('COM_REDJ_ERROR_RESETSTATS_FAILED', $model->getError());
          $this->setRedirect(JRoute::_('index.php?option=com_redj&view=errors', false), $message, 'error');
        } else {
          $this->setRedirect( 'index.php?option=com_redj&view=errors' );
        }
    }

    /**
     * Proxy for getModel
     */
    public function getModel($name = 'Error', $prefix = 'ReDJModel', $config = array('ignore_request' => true))
    {
      $model = parent::getModel($name, $prefix, $config);

      return $model;
    }

}
