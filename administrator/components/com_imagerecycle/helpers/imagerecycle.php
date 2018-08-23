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


defined('_JEXEC') or die;

/**
 * @package        Joomla.Administrator
 * @subpackage    com_messages
 * @since        1.6
 */
class ImagerecycleHelper
{

    /**
     * @var    JObject  A cache for the available actions.
     * @since  1.6
     */
    protected static $actions;
    private $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
    public $settings = null;

    /**
     * Configure the Linkbar.
     *
     * @param    string    The name of the active view.
     *
     * @return    void
     * @since    1.6
     */

    public static function addSubmenu($vName)
    {
//		JSubMenuHelper::addEntry(
//			JText::_('COM_MESSAGES_ADD'),
//			'index.php?option=com_messages&view=message&layout=edit',
//			$vName == 'message'
//		);
//
//		JSubMenuHelper::addEntry(
//			JText::_('COM_MESSAGES_READ'),
//			'index.php?option=com_messages',
//			$vName == 'messages'
//		);
    }


    /**
     * Gets a list of the actions that can be performed.
     *
     * @return  JObject
     *
     * @since   1.6
     * @todo    Refactor to work with notes
     */
    public static function getActions()
    {
        if (empty(self::$actions)) {
            $user = JFactory::getUser();
            self::$actions = new JObject;

            $actions = JAccess::getActions('com_imagerecycle');

            foreach ($actions as $action) {
                self::$actions->set($action->name, $user->authorise($action->name, 'com_imagerecycle'));
            }
        }

        return self::$actions;
    }

    public function optimizeInQueue()
    {
        //Register  base class
        JLoader::register('irQueue', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/classes/irQueue.php');
        $irQueue = new irQueue();
        $count = $irQueue->count();

        $componentParams = JComponentHelper::getParams('com_imagerecycle');
        $compressionTypeDefault = $componentParams->get('compression_type', 'lossy');

        JLog::addLogger(
            array(
                // Sets file name
                'text_file' => 'com_imagerecycle.log.php',
                // Sets the format of each line
                'text_entry_format' => '{DATETIME} {MESSAGE}'
            ),
            // Sets all but DEBUG log level messages to be sent to the file
            JLog::ALL & ~JLog::DEBUG,
            // The log category which should be recorded in this file
            array('com_imagerecycle')
        );

        while (!$irQueue->isEmpty()) {
            $nextImg = $irQueue->dequeue();
            $irQueue->save();
            if ($nextImg) {
                $this->setOption('queue_process_lastRun', time());
                $this->write_log('start optimize file: ' . $nextImg);
                $file = JPATH_SITE . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $nextImg);
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $compressionType = $componentParams->get('compression_type_' . $ext, '');
                if (empty($compressionType)) {
                    $compressionType = $compressionTypeDefault;
                }
                $returned = $this->optimize($nextImg, $compressionType);
                $this->write_log($returned);
            }

            $irQueue->loadImages();
        }
        $this->setOption('queue_process_status', 0);

        //send email to admin
        $opt_start_optimzeAll = (int)$this->getOption('manual_start_optimzeAll', 0);
        $this->setOption('manual_start_optimzeAll', 0);

        $isOptimizeAll = ($opt_start_optimzeAll && ($count > 1)) ? true : false;
        $send_email = (int)$componentParams->get('send_email', 1);
        if ($isOptimizeAll && $send_email) {
            $lang = JFactory::getLanguage();
            $lang->load('com_imagerecycle', JPATH_ADMINISTRATOR . '/components/com_imagerecycle', null, true);
            $lang->load('com_imagerecycle.sys', JPATH_ADMINISTRATOR . '/components/com_imagerecycle', null, true);

            $subject = JText::_('COM_IMAGERECYCLE_OPTIMIZE_PROCESS_COMPLETE_SUBJECT');
            $message = JText::_('COM_IMAGERECYCLE_OPTIMIZE_PROCESS_COMPLETE_MSG');

            $config = JFactory::getConfig();
            $data = array();
            $data['fromname'] = $config->get('fromname');
            $data['mailfrom'] = $config->get('mailfrom');
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            // Get all admin users
            $query->clear()
                ->select($db->quoteName(array('name', 'email', 'sendEmail', 'id')))
                ->from($db->quoteName('#__users'))
                ->where($db->quoteName('sendEmail') . ' = ' . 1)
                ->where($db->quoteName('block') . ' = ' . 0);

            $db->setQuery($query);
            try {
                $rows = $db->loadObjectList();
                // Send mail to all users with creating permissions and receiving system emails
                foreach ($rows as $row) {
                    $user = JFactory::getUser($row->id);
                    if ($user->authorise('core.admin')) {

                        $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $subject, $message, true);
                        if ($return !== true) {
                            $this->write_log('mail can not sent ' . $row->email);
                        } else {
                            $this->write_log('mail sent to ' . $row->email);
                        }
                    }
                }
            } catch (Exception $e) {
                $this->write_log($e->getMessage());
                return false;
            }
        }

    }

    public function getOption($key, $default = "")
    {
        $db = JFactory::getDbo();
        $db->setQuery("SELECT option_value FROM #__imagerecycle_options WHERE option_name =" . $db->quote($key) . " LIMIT 1");
        $result = $db->loadResult();
        if ($result === null) { //NULL: not exist $key option in database
            $db->setQuery("INSERT INTO #__imagerecycle_options SET option_value =" . $db->quote($default) . ", option_name = " . $db->quote($key));
            $db->query();
            $data = $default;
        } else {
            $data = $result;
        }

        return $data;
    }

    public function setOption($key, $val)
    {
        $db = JFactory::getDbo();
        $db->setQuery("SELECT COUNT(option_value) FROM #__imagerecycle_options WHERE option_name =" . $db->quote($key) . " LIMIT 1");
        $result = $db->loadResult();
        if (!empty($result)) {
            $db->setQuery("UPDATE #__imagerecycle_options SET option_value =" . $db->quote($val) . " WHERE option_name =" . $db->quote($key));
        } else { //not exist ir_queue option in database
            $db->setQuery("INSERT INTO #__imagerecycle_options SET option_value =" . $db->quote($val) . ", option_name = " . $db->quote($key));
        }

        return $db->query();
    }

    public function write_log($log)
    {
        if (is_array($log) || is_object($log)) {
            error_log(print_r($log, true));
            JLog::add(print_r($log, true), JLog::INFO, 'com_imagerecycle');
        } else {
            error_log($log);
            JLog::add($log, JLog::INFO, 'com_imagerecycle');
        }

    }

    public function optimize($image, $compressionType)
    {

        $response = new stdClass();
        $response->status = false;
        $response->errCode = 0;
        $response->msg = Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_NOT_OPTIMIZED');
        $file = JPATH_SITE . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $image);
        if (!file_exists($file)) {
            $response->msg = Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_FILE_NOT_FOUND');
            return $response;
        }

        if(!is_writable($file)) {
            $response->msg = Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_FILE_NOT_WRITABLE');
            return $response;
        }
        
        $this->settings = JComponentHelper::getParams('com_imagerecycle');
        for ($i = 0; $i < count($this->allowed_ext); $i++) {
            $compression_type = $this->settings->get('compression_type_' . $this->allowed_ext[$i], '');
            if ($compression_type == "none") {
                unset($this->allowed_ext[$i]);
            }
        }
        $this->allowed_ext = array_values($this->allowed_ext);
        if (!in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $this->allowed_ext)) {
            $response->msg = Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_TYPE_NOT_ALLOWED');
            return $response;
        }

        $fparams = array("compression_type" => $compressionType);
        $resize_image = $this->settings->get('resize_image', 0);
        $resize_auto = $this->settings->get('resize_auto', 0);
        if ($resize_auto && $resize_image) {   //Only apply on new images
            $installed_time = (int)$this->settings->get('installed_time', '');
            if (empty($installed_time)) {
                $compHelper = new imagerecycleComponentHelper();
                $installed_time = time();
                $compHelper->setParams(array('installed_time' => $installed_time));
            }

            $size = @getimagesize($file);
            $fileCreated = filectime($file);
            if ($size && ($size[0] > $resize_image) && ($fileCreated > $installed_time)) {
                $fparams['resize'] = array("width" => $resize_image);
            }
        }

        if (!$this->settings->get('api_key') || !$this->settings->get('api_secret')) {
            $response->errCode = 403; //api key is not correct
            $response->msg = Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_CONFIG_NOT_SET');
            return $response;
        }

        include_once(JPATH_ADMINISTRATOR . '/components/com_imagerecycle/classes/ioa.class.php');
        $ioa = new ioaphp($this->settings->get('api_key'), $this->settings->get('api_secret'));
        // var_dump($fparams)   ;
        $return = $ioa->uploadFile($file, $fparams);
        if ($return === false || $return === null || is_string($return)) {
            $response->msg = $ioa->getLastError();
            $response->errCode = $ioa->getLastErrCode();
            return $response;
        }
        $md5 = md5_file($file);
        clearstatcache();
        $sizebefore = filesize($file);

        $optimizedFileContent = $this->getContent($return->optimized_url);
        if ($optimizedFileContent === false) {
            $response->msg = Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_OPTIMIZED_NOT_FOUND');
            return $response;
        }

        if (file_put_contents($file, $optimizedFileContent) === false) {
            $response->msg = Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_OPTIMIZED_DOWNLOAD_FAIL');
            return $response;
        }
        clearstatcache();
        $size_after = filesize($file);
        $image = str_replace('\\', '/', $image);
        if (strpos($image, '/') === 0) $image = substr($image, 1);
        $db = JFactory::getDbo();
        $db->setQuery("Select id From #__imagerecycle_files WHERE BINARY file=" . $db->quote($image));
        $id = $db->loadResult();
        if (!$id) {
            $db->setQuery("INSERT INTO #__imagerecycle_files (`file`,`md5`,`api_id`,`size_before`, `size_after`,`date`,`expiration_date`,`status`) VALUES ("
                . $db->quote($image) . "," . $db->quote($md5) . "," . $return->id . "," . (int)$sizebefore . "," . (int)$size_after . ", '" . date('Y-m-d H:i:s') . "'," . $db->quote($return->expiration_date) . ",'O')");
        } else {
            $db->setQuery("UPDATE #__imagerecycle_files SET status='O', `size_after` = " . (int)$size_after . ", `expiration_date`=" . $db->quote($return->expiration_date) . " WHERE `id` = " . $id);
        }
        $db->query();

        $response->status = true;

        $response->msg = sprintf(Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_OPTIMIZED_PERCENT'), round(($sizebefore - $size_after) / $sizebefore * 100, 2));
        $response->newSize = number_format($size_after / 1000, 2, '.', '');
        return $response;
    }

    public function revert($image)
    {

        $response = new stdClass();
        $response->status = false;
        $response->msg = Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_NOT_REVERTED');

        $db = JFactory::getDbo();
        $db->setQuery("SELECT api_id FROM #__imagerecycle_files  WHERE BINARY `file` = " . $db->quote($image));
        $api_id = $db->loadResult();

        if ($api_id) {

            $this->settings = JComponentHelper::getParams('com_imagerecycle');
            include_once(JPATH_COMPONENT_ADMINISTRATOR . '/classes/ioa.class.php');
            $ioa = new ioaphp($this->settings->get('api_key'), $this->settings->get('api_secret'));

            $return = $ioa->getImage($api_id);
            if (!isset($return->id)) {
                $response->msg = Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_API_ID_NOT_CORRECT');
                return $response;
            }

            $fileContent = $this->getContent($return->origin_url);
            if ($fileContent === false) {
                $response->msg = Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_FILE_NOT_FOUND');
                return $response;
            }

            $file = JPATH_SITE . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $image);
            if (file_put_contents($file, $fileContent) === false) {
                $response->msg = Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_CANT_WRITE_FILE');
                return $response;
            }
            clearstatcache();
            $size_after = filesize($file);

            $where = "api_id = " . $api_id;
            $db->setQuery("UPDATE #__imagerecycle_files SET status='R' WHERE " . $where);
            $result = $db->query();
            if ($result === false) {
                $response->msg = Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_CANT_DELETE_RECORD');
                return $response;
            }

            $response->newSize = number_format($size_after / 1000, 2, '.', '');//(int) $size_after;
            $response->status = true;
            $response->msg = Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_REVERTED');
        }
        return $response;
    }

    /**
     * Get content of specified resource via curl or file_get_content() function
     */
    protected function getContent($url)
    {
        if ($url == '') {
            return false;
        }

        //Replace all spaces in file name
        $url = str_replace(' ', '%20', $url);

        if (!function_exists('curl_version')) {
            if (!$content = @file_get_contents($url)) {
                return false;
            }
        } else {
            $options = array(
                CURLOPT_RETURNTRANSFER => true, // return content
                CURLOPT_FOLLOWLOCATION => true, // follow redirects
                CURLOPT_AUTOREFERER => true, // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 60, // timeout on connect
                CURLOPT_SSL_VERIFYPEER => false // Disabled SSL Cert checks
            );

            $ch = curl_init($url);
            curl_setopt_array($ch, $options);
            $content = curl_exec($ch);
            curl_close($ch);
        }

        return $content;
    }

}
