<?php
/**
 * Dropfiles
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Dropfiles
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class ImagerecycleControllerConnector extends JControllerLegacy
{

    public function listdir()
    {
        $user = JFactory::getUser();
        if (!$user->authorise('core.admin')) {
            return json_encode(array());
        }
        $params = JComponentHelper::getParams('com_imagerecycle');
        $selected_folders = explode(',', $params->get('include_folders', 'images,media,templates'));
        // var_dump($excluded_folders);
        $path = JPATH_ROOT . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR;
        $dir = JFolder::makeSafe(JRequest::getString('dir'));

        $return = $dirs = array();

        if (file_exists($path . $dir)) {
            $files = scandir($path . $dir);

            natcasesort($files);
            if (count($files) > 2) { // The 2 counts for . and ..
                // All dirs
                $baseDir = ltrim(rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $dir), '/'), '/');
                if ($baseDir != '') $baseDir .= '/';
                foreach ($files as $file) {
                    if (file_exists($path . $dir . DIRECTORY_SEPARATOR . $file) && $file != '.' && $file != '..' && is_dir($path . $dir . DIRECTORY_SEPARATOR . $file)) {

                        if (in_array($baseDir . $file, $selected_folders)) {
                            $dirs[] = array('type' => 'dir', 'dir' => $dir, 'file' => $file, 'checked' => true);
                        } else {
                            $hasSubFolderSelected = false;
                            foreach ($selected_folders as $selected_folder) {
                                if (strpos($selected_folder, $baseDir . $file) === 0) {
                                    $hasSubFolderSelected = true;
                                }
                            }
                            if ($hasSubFolderSelected) {
                                $dirs[] = array('type' => 'dir', 'dir' => $dir, 'file' => $file, 'pchecked' => true);
                            } else {
                                $dirs[] = array('type' => 'dir', 'dir' => $dir, 'file' => $file);
                            }

                        }
                    }
                }
                $return = $dirs;
            }
        }
        echo json_encode($return);
        jexit();
    }

    public function setFolders()
    {
        $app = JFactory::getApplication();
        $folders = $app->input->getString('folders');
        $result = imagerecycleComponentHelper::setParams(array('include_folders' => $folders));
        echo json_encode($result);
        jexit();
    }
}