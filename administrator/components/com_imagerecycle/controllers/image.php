<?php
/**
 * Imagerecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@imagerecycle.com *
 * @package Imagerecycle
 * @copyright Copyright (C) 2014 ImageRecycle (http://www.imagerecycle.com). All rights reserved.
 * @copyright Copyright (C) 2014 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerlegacy');
require_once JPATH_ADMINISTRATOR . '/components/com_imagerecycle/classes/irQueue.php';

//JLoader::register('irQueue', JPATH_ADMINISTRATOR.'/components/com_imagerecycle/classes/irQueue.php');
class imagerecycleControllerImage extends JControllerLegacy
{

    public function optimize()
    {
        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/json');
        $app = JFactory::getApplication();
        $image = $app->input->getString('image', '');
        $helper = new ImagerecycleHelper();
        $file = JPATH_SITE . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $image);
        ob_implicit_flush(true);
        ob_end_flush();
        if (file_exists($file)) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $componentParams = JComponentHelper::getParams('com_imagerecycle');
            $compressionType = $componentParams->get('compression_type_' . $ext, '');
            if (empty($compressionType)) {
                $compressionType = $componentParams->get('compression_type', 'lossy');
            }
            $return = $helper->optimize($image, $compressionType);
            $this->ajaxReponse($return->status, $return);

        } else {
            $this->ajaxReponse(false);
        }
    }

    public function revert()
    {

        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/json');
        $app = JFactory::getApplication();
        $image = $app->input->getString('image', '');

        $helper = new ImagerecycleHelper();
        $return = $helper->revert($image);
        ob_implicit_flush(true);
        ob_end_flush();
        $this->ajaxReponse($return->status, $return);

    }

    public function unqueue()
    {

        $document = JFactory::getDocument();
        $document->setMimeEncoding('application/json');
        $app = JFactory::getApplication();
        $image = $app->input->getString('image', '');

        $irQueue = new irQueue(false);
        $irQueue->unqueue($image);

        ob_implicit_flush(true);
        ob_end_flush();
        $this->ajaxReponse(true);

    }

    /**
     * Be careful of this action if site has a very big amount of images
     */
    public function optimizeAll()
    {

        JLoader::register('imagerecycleComponentHelper', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/helpers/component.php');
        $componentHelper = new imagerecycleComponentHelper();

        $componentParams = JComponentHelper::getParams('com_imagerecycle');
        $compressionTypeDefault = $componentParams->get('compression_type', 'lossy');
        $mode = JFactory::getApplication()->input->getString('mode');
        if ($mode == 'manual') {
            $steps = 1;
        } else if ($mode == 'auto') {
            if ($componentParams->get('auto_method', 'ajax') !== 'ajax' || $componentParams->get('run_periodicity', 5) == '0') {
                //Do nothing if we do not call the method expected
                jexit();
            }
            if (ini_get('safe_mode')) {  // safe mode is on
                $steps = 1;
            } else {
                $steps = 10;
            }
        } else { //cron tab
            if ($componentParams->get('auto_method', 'ajax') !== 'crontab' || $componentParams->get('run_periodicity', 5) == '0') {
                //Do nothing if we do not call the method expected
                jexit();
            }
            ignore_user_abort(true);
            $steps = 10000;
        }

        $model = $this->getModel('imagerecycle');
        $images = $model->_getLocalImages();
        $helper = new ImagerecycleHelper();

        if (!isset($_SESSION['jir_failFiles'])) {
            $_SESSION['jir_failFiles'] = array();
        }
        if (!isset($_SESSION['jir_processed'])) {
            $_SESSION['jir_processed'] = 0;
        }
        ob_implicit_flush(true);
        ob_end_flush();
        foreach ($images as $image) {
            if ($image['optimized'] == false && ($mode != 'auto' || $image['optimized_datas']['status'] != 'R') && !in_array($image['filename'], $_SESSION['jir_failFiles'])) {
                @set_time_limit(30);
                if ($steps === 0) {
                    $componentHelper->setParams(array('last_run' => date('Y-m-d H:i:s')));
                    $this->ajaxReponse(true, array('continue' => true, 'totalImages' => $model->getTotalImages(), 'totalOptimizedImages' => $model->getTotalOptimizedImages(), 'processedImages' => $_SESSION['jir_processed']));
                }
                $file = JPATH_SITE . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $image['filename']);
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $compressionType = $componentParams->get('compression_type_' . $ext, '');
                if (empty($compressionType)) {
                    $compressionType = $compressionTypeDefault;
                }
                $returned = $helper->optimize($image['filename'], $compressionType);
                if ($returned === false || $returned->status === false) {
                    if ($returned->errCode == '401' || $returned->errCode == '403') { // Forbidden or Unauthorized
                        $this->ajaxReponse(false, array('continue' => false, 'errMsg' => $returned->msg));
                    }
                    $failFiles = (array)$_SESSION['jir_failFiles'];
                    $failFiles[] = $image['filename'];
                    $_SESSION['jir_failFiles'] = $failFiles;
                }
                $processed = (int)$_SESSION['jir_processed'];
                $_SESSION['jir_processed'] = $processed + 1;
                $steps--;
            }
        }

        $this->ajaxReponse(true, array('continue' => false, 'totalImages' => $model->getTotalImages(), 'totalOptimizedImages' => $model->getTotalOptimizedImages()));

    }

    public function startOptimizeAll()
    {
        $filters = array('optimized' => 'no');
        $mode = JFactory::getApplication()->input->getString('mode');// auto or manual
        if ($mode == 'auto') {
            $componentParams = JComponentHelper::getParams('com_imagerecycle');
            if ($componentParams->get('auto_method', 'ajax') !== 'ajax' || $componentParams->get('run_periodicity', 5) == '0') {
                //Do nothing if we do not call the method expected
                jexit();
            }
        }

        $model = $this->getModel('imagerecycle');
        $images = $model->_getLocalImages($filters);
        if (count($images)) {

            //Register  base class
            $irQueue = new irQueue();
            foreach ($images as $image) {
                set_time_limit(30);
                $irQueue->enqueue($image['filename']);
            }
            $irQueue->save();
            $helper = new ImagerecycleHelper();
            if ($mode != 'auto') {
                $helper->setOption('manual_start_optimzeAll', 1);
            }
            $helper->setOption('queue_process_status', 1);
            $helper->setOption('totalImagesProcessing', count($images));
            $helper->setOption('process_startTime', time());

            $this->headRequest(JUri::root() . '/components/com_imagerecycle/queue_process.php');
        }

        $this->ajaxReponse(true, array('totalImagesProcessing' => count($images), 'startTime' => time()));
    }

    public function stopOptimizeAll()
    {

        $helper = new ImagerecycleHelper();
        $helper->setOption('ir_queue', "");
        $helper->setOption('queue_process_status', 0);
        $helper->setOption('totalImagesProcessing', 0);
        $helper->setOption('process_startTime', 0);
        $helper->setOption('manual_start_optimzeAll', 0);
        $this->ajaxReponse(true);
    }

    public function countQueue()
    {
        $irQueue = new irQueue();
        $count = $irQueue->count();
        if ($count > 0) {
            $this->ajaxReponse(true, array('remainFiles' => $count, 'continue' => true, 'curTime' => time()));
        } else {
            $this->ajaxReponse(true, array('remainFiles' => $count, 'continue' => false));
        }
    }

    public function saveConfig()
    {
        $app = JFactory::getApplication();
        $config = array();
        $config['api_key'] = $app->input->getString('api_key', '');
        $config['api_secret'] = $app->input->getString('api_secret', '');

        if (empty($config['api_key']) || empty($config['api_secret'])) {
            $this->ajaxReponse(false);
            return;
        }

        $helper = new imagerecycleComponentHelper();
        $result = $helper->setParams($config);
        if ($result) {
            $app->enqueueMessage(JText::_("COM_IMAGERECYCLE_CONFIG_SAVE_SUCCESS"));
        }
        $this->ajaxReponse($result);
    }

    public function headRequest($url)
    {
        if (function_exists('curl_version')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000);

            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

            // Only calling the head
            curl_setopt($ch, CURLOPT_HEADER, true); // header will be at output
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD'); // HTTP request is 'HEAD'

            $content = curl_exec($ch);
            curl_close($ch);
        } else {

            $ctx = stream_context_create(
                array('http' =>
                    array(
                        'timeout' => 1,  //1 Seconds
                    )
                )
            );

            echo file_get_contents($url, false, $ctx);
        }
    }

    protected function ajaxReponse($status, $datas = null)
    {
        $response = array('status' => $status, 'datas' => $datas);
        echo json_encode($response);
        die();
    }

}
