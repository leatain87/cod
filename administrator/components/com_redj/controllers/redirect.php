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
 * ReDJ Controller Redirect
 *
 * @package ReDJ
 *
 */
class ReDJControllerRedirect extends JControllerForm
{
  public function save($key = null, $urlVar = null)
  {
    // Check for request forgeries
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    $data = $this->input->post->get('jform', array(), 'array');
    // Trim blanks from string fields
    $data['fromurl'] = trim($data['fromurl']);
    $data['tourl'] = trim($data['tourl']);
    $data['skip'] = trim($data['skip']);
    // Fill the form data with checkbox values
    $data['case_sensitive'] = array_key_exists('case_sensitive', $data) ? 1 : 0;
    $data['request_only'] = array_key_exists('request_only', $data) ? 1 : 0;
    $data['decode_url'] = array_key_exists('decode_url', $data) ? 1 : 0;
    // Serialize multiple values fields
    $data['skip_usergroups'] = isset($data['skip_usergroups']) ? implode(',', $data['skip_usergroups']) : '';
    $data = $this->input->post->set('jform', $data);

    return parent::save($key, $urlVar);
  }

}