<?php

defined('_JEXEC') or die;
$controller	= JControllerLegacy::getInstance('Jotcache');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
