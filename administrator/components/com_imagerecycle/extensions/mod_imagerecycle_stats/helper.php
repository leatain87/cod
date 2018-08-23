<?php
/**
 * ImageRecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@imagerecycle.com *
 * @package ImageRecycle
 * @copyright Copyright (C) 2014 ImageRecycle (http://www.imagerecycle.com). All rights reserved.
 * @copyright Copyright (C) 2014 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die;

/**
 * Helper class for admin stats module
 *
 * @since  3.0
 */
class ModImagerecycleStatsHelper
{
    /**
     * Load global file language
     */
    public static function loadLanguage()
    {
        $lang = JFactory::getLanguage();
        $lang->load('com_imagerecycle', JPATH_ADMINISTRATOR . '/components/com_imagerecycle', null, true);
        $lang->load('com_imagerecycle.sys', JPATH_ADMINISTRATOR . '/components/com_imagerecycle', null, true);
    }

    /**
     * Method to retrieve information about the site
     *
     * @param   JObject &$params Params object
     *
     * @return  array  Array containing site information
     *
     * @since   3.0
     */
    public static function getStats(&$params)
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $rows = array();


        $i = 0;
        $averageCompression = 0;
        $savedSpace = 0;
        $savedBandwidth = 0;
        $totalOptimizedFiles = 0;

        $q = "Select COUNT(id) as totalOptimizedFiles, SUM(`size_after`) as totalOptimized, SUM(`size_before`) as totalOriginal " .
            " From #__imagerecycle_files WHERE status!='R' ";
        $db->setQuery($q);
        $result = $db->loadObject();
        if ($result) {
            $averageCompression = $result->totalOptimized > 0 ? round((1 - ($result->totalOptimized / $result->totalOriginal)) * 100, 2) : 0;
            $savedSpace = $result->totalOriginal - $result->totalOptimized;
            $savedBandwidth = $savedSpace * 10000;
            $totalOptimizedFiles = $result->totalOptimizedFiles;
        }

        $rows[$i] = new stdClass;
        $rows[$i]->title = JText::_('MOD_IR_STATS_AVERAGE_COMPRESSION');
        $rows[$i]->data = $averageCompression . '%';
        $i++;

        $rows[$i] = new stdClass;
        $rows[$i]->title = JText::_('MOD_IR_STATS_SAVED_DISK');
        $rows[$i]->data = self::formatBytes($savedSpace, 2);
        $i++;

        $rows[$i] = new stdClass;
        $rows[$i]->title = JText::_('MOD_IR_STATS_BANDWIDTH_SAVED');
        $rows[$i]->data = self::formatBytes($savedBandwidth, 2);
        $i++;

        $rows[$i] = new stdClass;
        $rows[$i]->title = JText::_('MOD_IR_STATS_TOTAL_PROCESSED_FILES');
        $rows[$i]->data = $totalOptimizedFiles;
        $i++;

        return $rows;
    }

    public static function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        //$bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

}
