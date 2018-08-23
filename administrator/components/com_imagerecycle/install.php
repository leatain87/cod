<?php
/**
 * ImageRecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@imagerecycle.com *
 * @package ImageRecycle
 * @copyright Copyright (C) 2014 ImageRecycle (http://www.imagerecycle.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die;


class com_imagerecycleInstallerScript
{


    /**
     * method to install the component
     *
     * @return void
     */
    static function install($parent)
    {
        $lang = JFactory::getLanguage();
        $lang->load('com_imagerecycle', JPATH_ROOT . '/components/com_imagerecycle', null, true);
        $dbo = JFactory::getDbo();

        $queries = array(); //This array will contain all the queries which need to be excuted during install

        /* Completely arbritrary tables change to fit your needs */
        $queries[] = "CREATE TABLE IF NOT EXISTS `#__imagerecycle_files` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,				
					   `file` varchar(250) NOT NULL,
					   `md5` varchar(32) NOT NULL,
					   `extension` VARCHAR(5) NOT NULL,
					   `api_id` int(11) NOT NULL,
					   `size_before` int(11) NOT NULL,
					   `size_after` int(11) NOT NULL,	   
					   `date` datetime NOT NULL,
					   `expiration_date` datetime NOT NULL,
					   `status` varchar(32) NOT NULL,
					   PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8";

        $queries[] = "CREATE TABLE IF NOT EXISTS `#__imagerecycle_options` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,	
					   `option_name` varchar(50) NOT NULL,					  
					   `option_value` longtext NOT NULL,					
					   PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
        foreach ($queries as $query) {
            $dbo->setQuery($query);
            $dbo->query();
        }


    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent)
    {
        //Prevent the component uninstall without user confirmation
        JLoader::register('JControllerLegacy', JPATH_LIBRARIES . 'legacy/controller/legacy.php');
        $session = JFactory::getSession();
        if ($session->get('imagerecycleUninstall', false) == false) {
            //Show a message to the user, he needs to know that all content will be removed
            $session->set('imagerecycleUninstall', true);
            JFactory::getApplication()->enqueueMessage(JText::_('COM_IMAGERECYCLE_INSTALLER_UNINSTALL_DB'), 'warning');
            $controller = new JControllerLegacy();
            $controller->setRedirect('index.php?option=com_installer&view=manage&confirm=1');
            $controller->redirect();
        }

        $dbo = JFactory::getDbo();
        $queries = array();
        $queries[] = "DROP TABLE IF EXISTS `#__imagerecycle_files`";

        foreach ($queries as $query) {
            $dbo->setQuery($query);
            $dbo->query();
        }
    }

    /**
     * method to update the component
     * you can also run extra actions if needed
     * @return void
     */
    function update($parent)
    {
        // $parent is the class calling this method
        $dbo = JFactory::getDbo();
        $queries = array(); //This array will contain all the queries which need to be excuted during install
        if (version_compare($this->oldRelease, '2.0.0', 'lt')) {
            $queries[] = "CREATE TABLE IF NOT EXISTS `#__imagerecycle_options` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,	
					   `option_name` varchar(50) NOT NULL,					  
					   `option_value` longtext NOT NULL,					
					   PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
        }

        foreach ($queries as $query) {
            $dbo->setQuery($query);
            $dbo->query();
        }
    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        if ($type == 'uninstall') {
            //you can check here before uninstalling some things
        } elseif ($type == 'update') {
            //We don't want any update if the user tries to install an old version
            $this->oldRelease = $this->getVersion('com_imagerecycle');
            if (version_compare($this->oldRelease, $parent->get('manifest')->version, 'gt')) {
                Jerror::raiseWarning(null, 'You already have a newer version of ImageRecycle');
                jimport('joomla.application.component.controler');
                $controller = new JController();
                $controller->setRedirect('index.php?option=com_installer&view=install');
                $controller->redirect();
                return false;
            }
        } else {
            $this->release = $parent->get('manifest')->version;
            $jversion = new JVersion();
            // abort if the current Joomla release is older
            if (version_compare($jversion->getShortVersion(), '3.3.0', 'lt')) {
                Jerror::raiseWarning(null, 'Cannot install Imagerecycle component in a Joomla release prior to 3.3.0');
                $controller = new JController();
                $controller->setRedirect('index.php?option=com_installer&view=install');
                $controller->redirect();
                return false;
            }
        }
        $session = JFactory::getSession();
        $session->set('imagerecycleUninstall', false);
        return true;
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        if ($type == 'install') {
            //Once the component is installed we may want to do others things like adding categories for this component
            $dbo = JFactory::getDbo();
            $basePath = JPATH_ADMINISTRATOR . '/components/com_categories';
            require_once $basePath . '/models/category.php';
            $config = array('table_path' => $basePath . '/tables', 'name' => 'imagerecyclecats');
            $catmodel = new CategoriesModelCategory($config);
            $catData = array('id' => 0, 'parent_id' => 1, 'level' => 1, 'extension' => 'com_imagerecycle'
            , 'title' => JText::_('COM_IMAGERECYCLE_INSTALLER_NEW_CATEGORY'), 'alias' => 'new-category', 'published' => 1, 'language' => '*');
            $status = $catmodel->save($catData);

            $user = JFactory::getUser();

            if (!$status) {
                JError::raiseWarning(500, JText::_('Unable to create default content category!'));
            }
            $newcat = (int)$catmodel->getState('imagerecyclecats.id');

            $queries = array();

            foreach ($queries as $query) {
                $dbo->setQuery($query);
                $dbo->query();
            }
        }
        if ($type == 'install' || $type == 'update') {
            /*
     * Here we will install extra extensions like plugins, all this extensions are in the folder /admin/extensions/extname
     */
            $lang = JFactory::getLanguage();
            $lang->load('com_imagerecycle.sys', JPATH_ROOT . '/components/com_imagerecycle', null, true);

            $manifest = $parent->get('manifest');
            JLoader::register('ImagerecycleInstallerHelper', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/helpers/installer.php');
            //echo '<h2>'.JText::_('COM_IMAGERECYCLE_INSTALLER_TITLE').'</h2>';
            //echo JText::_('COM_IMAGERECYCLE_INSTALLER_MSG');
            ?>
            <div class="main-presentation"
                 style="margin: 0px auto; max-width: 1200px; background-color:#f0f1f4;font-family: helvetica,arial,sans-      serif;">
                <div class="main-textcontent"
                     style="margin: 0px auto; border-left: 0px dotted #d2d3d5; border-right: 0px dotted #d2d3d5; width:          840px; background-color:#fff;border-top: 5px solid #544766;"
                     cellspacing="0" cellpadding="0" align="center">

                    <a href="https://www.imagerecycle.com/" target="_blank"> <img
                            src="https://www.imagerecycle.com/images/Notification-mail/logo-image-recycle.png"
                            alt="logo image recycle" class="CToWUd"
                            style="display: block; outline: medium none; text-decoration: none; margin-left: auto; margin-right: auto; margin-top:15px;"
                            height="84" width="500"> </a>

                    <p style="background-color: #ffffff; color: #445566; font-family: helvetica,arial,sans-serif; font-size: 24px; line-height: 24px; padding-right: 10px; padding-left: 10px;"
                       align="center"><strong><?php echo JText::_('COM_IMAGERECYCLE_INSTALLER_WELCOME'); ?><br></strong>
                    </p>

                    <a style="width: 200px; height: 35px; background: #554766; font-size: 12px; line-height: 18px; text-align: center; margin-right:4px;color: #fff;font-size: 14px;text-decoration: none; text-transform: uppercase; padding: 8px 20px;font-weight:bold;"
                       href="index.php?option=com_imagerecycle"><?php echo JText::_('COM_IMAGERECYCLE_INSTALLER_GOTO'); ?></a>
                    <p><br/><br/></p>
                </div>
            </div>

            <?php
            $extensions = $manifest->extensions;

            foreach ($extensions->children() as $extension) {
                $folder = $extension->attributes()->folder;
                $enable = $extension->attributes()->enable;
                if (ImagerecycleInstallerHelper::install(JPATH_ADMINISTRATOR . '/components/com_imagerecycle/extensions/' . $folder, $enable)) {
                    //add module and asign to cpanel position
                    if ($folder == 'mod_imagerecycle_stats') {
                        $dbo = JFactory::getDbo();
                        $query = 'SELECT id FROM #__modules WHERE module=' . $dbo->quote($folder);
                        $dbo->setQuery($query);
                        $mid = $dbo->loadResult();
                        if (!$mid) {
                            $query = 'INSERT INTO #__modules (title,position,published,module,access, client_id) ' .
                                ' VALUES ("ImageRecycle Statistics","cpanel",1,' . $dbo->quote($folder) . ',1,1)';
                            $dbo->setQuery($query);
                            $dbo->query();

                            $query = 'SELECT id FROM #__modules WHERE module=' . $dbo->quote($folder);
                            $dbo->setQuery($query);
                            $mid = $dbo->loadResult();
                            $query = 'INSERT INTO #__modules_menu (`moduleid`,`menuid`) VALUES (' . $mid . ', 0 )';
                            $dbo->setQuery($query);
                            $dbo->query();

                        } else {
                            $query = 'UPDATE #__modules SET `position` = "cpanel" ,published=1 WHERE `id` = ' . $mid;
                            $dbo->setQuery($query);
                            $dbo->query();

                            $query = 'INSERT INTO #__modules_menu (`moduleid`,`menuid`) VALUES (' . $mid . ', 0 ) ON DUPLICATE KEY UPDATE `menuid` = 0';
                            $dbo->setQuery($query);
                            $dbo->query();
                        }
                    }

                } else {
                    echo '<img src="' . JURI::root() . '/components/com_imagerecycle/assets/images/exclamation.png" />' . $folder . ' : ' . JText::sprintf('COM_IMAGERECYCLE_INSTALLER_EXT_NOK', '') . '<br/>';
                }
            }

            //Set the default parameters
            if ($type == 'install') {
                $component = JComponentHelper::getComponent('com_imagerecycle');
                $data['params']['installed_time'] = time();

                $table = JTable::getInstance('extension');
                // Load the previous Data
                if (!$table->load($component->id)) {
                    return false;
                }
                // Bind the data.
                if (!$table->bind($data)) {
                    return false;
                }

                // Check the data.
                if (!$table->check()) {
                    return false;
                }

                // Store the data.
                if (!$table->store()) {
                    return false;
                }
            }


        }

        return true;
    }

    /**
     * Method to get the version of a component
     * @param string $option
     * @return null
     */
    private function getVersion($option)
    {
        $manifest = self::getManifest($option);
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
    private function getManifest($option)
    {
//                $component = JComponentHelper::getComponent($option);
        $dbo = JFactory::getDbo();
        $query = 'SELECT extension_id FROM #__extensions WHERE element=' . $dbo->quote($option) . ' AND type="component"';
        if (!$dbo->setQuery($query)) {
            return false;
        }
        if (!$dbo->query()) {
            return false;
        }
        $component = $dbo->loadResult();
        if (!$component) {
            return false;
        }
        $table = JTable::getInstance('extension');
        // Load the previous Data
        if (!$table->load($component, false)) {
            return false;
        }
        return json_decode($table->manifest_cache);
    }

}