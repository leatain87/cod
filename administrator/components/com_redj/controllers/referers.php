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
 * ReDJ Controller Referers
 *
 * @package ReDJ
 *
 */
class ReDJControllerReferers extends JControllerAdmin
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        // Register Extra tasks
        $this->registerTask('purge', 'purge');
    }

    public function purge()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('referer');
        if(!$model->purge()) {
          JError::raiseWarning(500, $model->getError());
        }

        $this->setRedirect( 'index.php?option=com_redj&view=referers' );
    }

    public function resetstats()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('referer');
        if(!$model->resetstats()) {
          $message = JText::sprintf('COM_REDJ_ERROR_RESETSTATS_FAILED', $model->getError());
          $this->setRedirect(JRoute::_('index.php?option=com_redj&view=referers', false), $message, 'error');
        } else {
          $this->setRedirect( 'index.php?option=com_redj&view=referers' );
        }
    }

    /**
     * Proxy for getModel
     */
    public function getModel($name = 'Referer', $prefix = 'ReDJModel', $config = array('ignore_request' => true))
    {
      $model = parent::getModel($name, $prefix, $config);

      return $model;
    }

}
