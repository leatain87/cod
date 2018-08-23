<?php
/*
* @package		MijoFTP
* @copyright	2009-2012 Mijosoft LLC, mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.controller');

if (!class_exists('MijosoftController')) {
    if (interface_exists('JController')) {
        abstract class MijosoftController extends JControllerLegacy {}
    }
    else {
        class MijosoftController extends JController {}
    }
}

class MijoftpController extends MijosoftController {

	public function __construct() {
		parent::__construct();
	}

    public function display($cachable = false, $urlparams = false) {
        parent::display($cachable, $urlparams);
	}
}
