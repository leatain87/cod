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

jimport('joomla.application.component.helper');

class imagerecycleComponentHelper extends JComponentHelper
{
    /**
     * Method to set config parameters manually
     * @param array $datas
     * @return boolean
     */
    public static function setParams($datas)
    {
        $component = JComponentHelper::getComponent('com_imagerecycle');
        $table = JTable::getInstance('extension');
        // Load the previous Data
        if (!$table->load($component->id, false)) {
            return false;
        }
        $d = json_decode($table->params);
        foreach ($datas as $key => $data) {
            $d->$key = $data;
        }
        $table->params = json_encode($d);

        // Check the data.
        if (!$table->check()) {
            return false;
        }
        // Store the data.
        if (!$table->store()) {
            return false;
        }

        //Clean query cache
        self::cleanCache('_system', 0);
        self::cleanCache('_system', 1);

        unset(self::$components['com_imagerecycle']);
        return true;
    }

    static protected function cleanCache($group = null, $client_id = 0)
    {
        $conf = JFactory::getConfig();

        $options = array(
            'defaultgroup' => ($group) ? $group : JFactory::getApplication()->input->get('option'),
            'cachebase' => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'));

        $cache = JCache::getInstance('callback', $options);
        $cache->clean();
    }

    /**
     * Method to get the version of a component
     * @param string $option
     * @return null
     */
    static public function getVersion()
    {
        $manifest = self::getManifest();
        if (property_exists($manifest, 'version')) {
            return $manifest->version;
        }
        return null;
    }

    /**
     * Method to get an object containing the manifest values
     * @param string $option
     * @return object
     */
    static public function getManifest()
    {
        $component = self::getComponent('com_imagerecycle');
        $table = JTable::getInstance('extension');
        // Load the previous Data
        if (!$table->load($component->id, false)) {
            return false;
        }
        return json_decode($table->manifest_cache);
    }
}
