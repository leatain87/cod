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

// no direct access
defined('_JEXEC') or die;

class imagerecycleBase
{

    /**
     *
     */
    public static function initComponent()
    {
        //Load language from non default position
        self::loadLanguage();

        // Register helper class
        JLoader::register('ImagerecycleHelper', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/helpers/imagerecycle.php');
        // Register helper class
        JLoader::register('ImagerecycleComponentHelper', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/helpers/component.php');

        //Load scripts and stylesheets
        $document = JFactory::getDocument();
        JHtml::_('jquery.framework');
        $document->addStyleSheet(JURI::root() . 'components/com_imagerecycle/assets/css/style.css');
        $document->addScript(JURI::root() . 'components/com_imagerecycle/assets/js/script.js');
    }

    public static function initFrontComponent()
    {
        //Load language from non default position
        self::loadLanguage();

        // Register helper class
        JLoader::register('ImagerecycleHelper', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/helpers/imagerecycle.php');
        // Register helper class
        JLoader::register('ImagerecycleComponentHelper', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/helpers/component.php');
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root() . 'components/com_imagerecycle/assets/css/front.css');
    }

    /**
     * Search a param into the component config
     * @param string $path
     * @param type $default
     * @return param
     */
    public static function getParam($path, $default = null)
    {
        $params = JComponentHelper::getParams('com_imagerecycle');
        return $params->get($path, $default);
    }

    /**
     * Method to return the current joomla version
     * @param string $format
     * @return string version
     */
    public static function getJoomlaVersion($format = 'short')
    {
        $method = 'get' . ucfirst($format) . "Version";

        // Get the joomla version
        $instance = new JVersion();
        $version = call_user_func(array($instance, $method));

        return $version;
    }

    /**
     * Method to check if current joomla version is 3.X
     * @return boolean
     */
    public static function isJoomla30()
    {
        if (version_compare(self::getJoomlaVersion(), '3.0') >= 0) {
            return true;
        }
        return false;
    }


    /**
     * Check if a component is installed and activated
     * @param string $extension
     * @param string $type
     * @return boolean
     */
    public static function isExtensionActivated($extension, $type = '')
    {
        $db = JFactory::getDbo();
        $query = 'SELECT extension_id FROM #__extensions WHERE element=' . $db->quote($extension);

        if ($type != '') {
            $query .= ' AND type=' . $db->quote($type);
        }
        $query .= ' AND enabled=1';
        $db->setQuery($query);
        if ($db->query()) {
            if ($db->getNumRows() > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Method to set config parameters
     * @param array $datas
     * @return boolean
     */
    public static function setParams($datas)
    {
        return imagerecycleComponentHelper::setParams($datas);
    }


    /**
     * Load global file language
     */
    public static function loadLanguage()
    {
        $lang = JFactory::getLanguage();
        $lang->load('com_imagerecycle', JPATH_ROOT . '/components/com_imagerecycle', null, true);
        $lang->load('com_imagerecycle.sys', JPATH_ROOT . '/components/com_imagerecycle', null, true);
    }

    /**
     * Method to load a value whether it is an array or an object
     * @param array or object $var
     * @param string $value
     * @param mixte $default
     * @return the variable content or the default value
     */
    public static function loadValue($var, $value, $default = '')
    {
        if (is_object($var) && isset($var->$value)) {
            return $var->$value;
        } elseif (is_array($var) && isset($var[$value])) {
            return $var[$value];
        }
        return $default;
    }

    /**
     * Check on Imagerecycle website the latest version number of the component
     * @param string $extension
     * @return false or version number (string)
     */
    public static function getLastExtensionVersion($extension = null)
    {
        if ($extension === null) {
            $extension = JFactory::getApplication()->input->getString('option', '');
        }
        if (ini_get("allow_url_fopen") == 1) {
            $content = file_get_contents('http://www.imagerecycle.com/UPDATE-INFO/updates.json');
        } else {
            return false;
        }
        $json = json_decode($content);
        return $json->extensions->$extension->version;
    }

    /**
     * Get an extension version number from joomla manifest cache
     * @param type $extension
     * @param type $type
     * @return boolean
     */
    public static function getExtensionVersion($extension = null, $type = '')
    {
        if ($extension === null) {
            $extension = JFactory::getApplication()->input->getString('option', '');
        }
        $db = JFactory::getDbo();
        $query = 'SELECT manifest_cache FROM #__extensions WHERE element=' . $db->quote($extension);

        if ($type != '') {
            $query .= ' AND type=' . $db->quote($type);
        }
        $db->setQuery($query);
        if ($db->query()) {
            $manifest = $db->loadResult();
            $json = json_decode($manifest);
            if (property_exists($json, 'version')) {
                return $json->version;
            }
        }
        return false;
    }
}

?>
