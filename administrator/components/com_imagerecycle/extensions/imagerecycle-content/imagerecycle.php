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


//-- No direct access
defined('_JEXEC') || die('=;)');


jimport('joomla.plugin.plugin');
jimport('joomla.application.categories');
jimport('joomla.filesystem.file');

/**
 * Content Plugin.
 *
 * @package    Imagerecycle
 * @subpackage Plugin
 */
class plgContentImagerecycle extends JPlugin
{
    public static $scriptIncluded = 0;

    /**
     * Example before display content method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param  string $context The context for the content passed to the plugin.
     * @param  object &$article The content object.  Note $article->text is also available
     * @param  object &$params The content params
     * @param  int $limitstart The 'page' number
     *
     * @return string
     */
    public function onContentPrepare($context, &$article, &$params, $limitstart)
    {
        // Check if this function is enabled.
        $componentParams = JComponentHelper::getParams('com_imagerecycle');
        $auto_method = $componentParams->get('auto_method', 'ajax');
        $optimizeInterval = (int)$componentParams->get('run_periodicity', 5);
        if ($auto_method != "ajax" || !$optimizeInterval) {
            return true;
        }

        if ($componentParams->get('api_key') == '' || $componentParams->get('api_secret') == '') return true;

        $now = (int)strtotime(date('Y-m-d H:i:s'));

        //Register  base class
        JLoader::register('ImagerecycleHelper', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/helpers/imagerecycle.php');
        $helper = new ImagerecycleHelper();
        $ao_lastRun = (int)$helper->getOption('queue_process_lastRun', 0);
        $ao_status = (int)$helper->getOption('queue_process_status', 0);
        $ao_running = (($ao_status || ($now - $ao_lastRun) < 60)) ? true : false;
        if ($ao_running) {
            return;
        }

        if (!empty($ao_lastRun)) {
            $curInterval = $now - (int)strtotime($ao_lastRun);
        } else {
            $curInterval = $now;
        }

        if ($curInterval / 60 > $optimizeInterval && self::$scriptIncluded == 0) {
            JHtml::_('jquery.framework');
            $script = "jQuery(document).ready(function($){
                     var optimizeall_auto = '" . trim(JUri::root(),'/') . "/index.php?option=com_imagerecycle&task=image.startOptimizeAll&mode=auto';
                     optimizeAll = function() {
                                $.ajax({
                                    url: optimizeall_auto,
                                    data: {},
                                    type: 'post',
                                    dataType: 'json',
                                    success: function(response) {
                                        //do nothing
                                    }
                                })
                       }                            
                       optimizeAll();
                        
                });";
            $doc = JFactory::getDocument();
            $doc->addScriptDeclaration($script);
            self::$scriptIncluded = 1;
        }
    }

    /**
     * Article is passed by reference, but after the save, so no changes will be saved.
     * Method is called right after the content is saved
     *
     * @param   string $context The context of the content passed to the plugin (added in 1.6)
     * @param   object $article A JTableContent object
     * @param   boolean $isNew If the content is just about to be created
     *
     * @return  boolean   true if function not enabled, is in front-end or is new. Else true or
     *                    false depending on success of save function.
     *
     * @since   1.6
     */
    public function onContentAfterSave($context, $object_file, $isNew)
    {
        // Check we are handling the frontend edit form.
        if ($context != 'com_media.file') {
            return true;
        }

        // Check this is a new file.
        if (!$isNew) {
            return true;
        }

        // Check if this function is enabled.
        $componentParams = JComponentHelper::getParams('com_imagerecycle');
        if (!$componentParams->get('optimize_newmedia_auto', 0)) {
            return true;
        }

        try {

            $sizebefore = filesize($object_file->filepath);
            $ext = strtolower(pathinfo($object_file->filepath, PATHINFO_EXTENSION));
            $compressionType = $componentParams->get('compression_type_' . $ext, '');
            if (empty($compressionType)) {
                $compressionType = $componentParams->get('compression_type', 'lossy');
            } elseif ($compressionType == 'none') {
                return true;
            }
            // Register helper class
            JLoader::register('ImagerecycleHelper', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/helpers/imagerecycle.php');
            $helper = new ImagerecycleHelper();
            $return = $helper->optimize(substr($object_file->filepath, strlen(JPATH_ROOT)), $compressionType);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            return false;
        }

        $app = JFactory::getApplication();
        $lang = JFactory::getLanguage();
        $lang->load('com_imagerecycle', JPATH_ADMINISTRATOR . '/components/com_imagerecycle', null, true);
        if ($return->status) {
            $msg = substr($object_file->filepath, strlen(JPATH_ROOT)) . " - " . sprintf(Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_OPTIMIZED_PERCENT'), round(($sizebefore - $return->newSize) / $sizebefore * 100, 2));
        } else {
            $msg = $return->msg;
        }
        $app->enqueueMessage($msg);

        return $return->status;
    }

}