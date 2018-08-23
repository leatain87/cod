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

jimport('joomla.application.component.controllerform');

/**
 * ReDJ Controller Page 404
 *
 * @package ReDJ
 *
 */
class ReDJControllerPage404 extends JControllerForm
{
  public function __construct($config = array())
  {
    parent::__construct($config);
    $this->view_list = 'pages404';
  }

  public function save($key = null, $urlVar = null)
  {
    // Check for request forgeries
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    $data = $this->input->post->get('jform', array(), 'array');
    // Trim blanks from string fields
    $data['title'] = trim($data['title']);
    $data['language'] = trim($data['language']);
    $data = $this->input->post->set('jform', $data);

    return parent::save($key, $urlVar);
  }

}