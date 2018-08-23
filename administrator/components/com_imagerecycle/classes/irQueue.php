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

class irQueue
{
    private static $images;

    // Upon creation, load up our images from the file
    public function __construct($reset = true)
    {
        if ($reset) {
            self::$images = array();
        }
        $this->loadImages();
    }

    public function loadImages()
    {

        $db = JFactory::getDbo();
        $db->setQuery("SELECT option_value FROM #__imagerecycle_options WHERE option_name = 'ir_queue' LIMIT 1");
        $result = $db->loadResult();
        if (!empty($result)) {
            $data = $result;
        } else if ($result === '') { //exist but empty
            $data = '';
        } else { //NULL: not exist ir_queue option in database
            $db->setQuery("INSERT INTO #__imagerecycle_options SET option_value ='', option_name = 'ir_queue'");
            $db->query();

            $data = 0;
        }

        if (empty($data)) {
            self::$images = array();
        } else {
            self::$images = json_decode($data, true);
            if (!is_array(self::$images)) {
                self::$images = array();
            }
        }
    }

    // Add a file to the queue and if we are at our limit, drop one off the end.
    public function enqueue($strFile)
    {
        if (!empty($strFile) && !$this->isFilePresent($strFile)) {
            array_unshift(self::$images, $strFile);

        }
    }

    // Remove a file item from the end of our list
    public function dequeue()
    {
        if (count(self::$images) > 0) {
            return trim(array_pop(self::$images));
        }
        return "";
    }

    // Remove a file item from the end of our list
    public function unqueue($file)
    {
        if ($this->isFilePresent($file)) {
            $to_remove = array($file);
            self::$images = array_diff(self::$images, $to_remove);
            $this->save();
        }
        return true;
    }

    // Save the contents of our array back to the file.
    public function save()
    {
        //var_dump(  self::$images );
        $data = json_encode(self::$images); //var_dump($data); die();
        $db = JFactory::getDbo();
        $db->setQuery("UPDATE #__imagerecycle_options SET option_value =" . $db->quote($data) . "  WHERE option_name = 'ir_queue'");
        $db->query();
    }

    // check if queue is empty or not
    public function isEmpty()
    {
        if (count(self::$images) > 0) {
            return false;
        }
        return true;
    }

    public function count()
    {
        return count(self::$images);
    }

    public function clear()
    {
        if (count(self::$images) > 0) {
            self::$images = array();
            $this->save();
        }
    }
    // Check if an item is already in our list. 
    // Note: We could have also used in_array here instead of a loop.
    public function isFilePresent($strFile = "")
    {
        if (!empty($strFile) && !empty(self::$images)) {
            foreach (self::$images as $file) {
                $trimmedFile = trim($file);
                $strFile = trim($strFile);

                if (strtolower($strFile) == strtolower($trimmedFile)) {
                    return true;
                }
            }

            return false;
        }
        return false;
    }

    // Mainly a debug function to print our values to screen.
    public function printValues()
    {
        foreach (self::$images as $value) {
            echo "$value<br/>";
        }
    }
}
