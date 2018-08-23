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

// No direct access.
defined('_JEXEC') or die;
jimport('joomla.filesystem.file');

$tag = JFactory::getLanguage()->getTag();
if (JFile::exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'default_' . strtolower($tag) . '.php')) {
    echo $this->loadTemplate($tag);
} else {
    echo $this->loadTemplate('en-GB');
}
?>