<?php
/**
 * @package      Canonical Links All in One
 * @copyright    Marko Dedovic / ManageCMS.com. All rights reserved.
 * @license      GNU GPLv2 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

class PlgSystemCanonicallinksInstallerScript
{

    function update($parent)
    {
        $this->install($parent);
    }

    function install($parent)
    {
        $db = JFactory::getDbo();
        $tableExtensions = $db->quoteName("#__extensions");
        $columnElement = $db->quoteName("element");
        $columnType = $db->quoteName("type");
        $columnEnabled = $db->quoteName("enabled");

        // Enable plugin
        $db->setQuery("UPDATE $tableExtensions SET $columnEnabled=1 WHERE $columnElement='canonicallinks' AND $columnType='plugin'");
        $db->execute();

        $this->activateUpdates();

        echo '<br /><span style="color: red; font-weight: bold;">Canonical links plugin has been activated!</span><br />';
    }

    public function activateUpdates()
    {
        $this->setUpdateSite();
        $downloadId = '71a1166bc6cdf4c25bd11024ac3883e3';
        $extensionId = $this->getExtensionId();
        if($extensionId) {
            // Load the update site record, if it exists
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('update_site_id')
                ->from($db->qn('#__update_sites_extensions'))
                ->where($db->qn('extension_id') . ' = ' . $db->q($extensionId));
            $db->setQuery($query);
            $updateSite = $db->loadResult();

            if($updateSite) {
                // Update the update site record
                $query = $db->getQuery(true)
                    ->update($db->qn('#__update_sites'))
                    ->set(array(
                        'extra_query = ' . $db->q('dlid=' . urlencode($downloadId)),
                        'enabled = ' . 1,
                        'last_check_timestamp = ' . 0,
                    ))
                    ->where($db->qn('update_site_id') . ' = ' . $db->q($updateSite));
                $db->setQuery($query);
                $db->execute();

                // Delete any existing updates (essentially flushes the updates cache for this update site)
                $query = $db->getQuery(true)
                    ->delete($db->qn('#__updates'))
                    ->where($db->qn('update_site_id') . ' = ' . $db->q($updateSite));
                $db->setQuery($query);
                $db->execute();
            }
        }
    }

    private function setUpdateSite()
    {
        $db = JFactory::getDbo();
        $location = 'https://www.managecms.com/canonical-links.xml';
        $name = 'Canonical Links All in One Updates';
        $type = 'extension';
        $enabled = '1';
        $extensionId = $this->getExtensionId();

        // Look if the location is used already; doesn't matter what type you can't have two types at the same address, doesn't make sense
        $query = $db->getQuery(true)
            ->select('update_site_id')
            ->from('#__update_sites')
            ->where('location = ' . $db->quote($location));
        $db->setQuery($query);
        $update_site_id = (int)$db->loadResult();

        // If it doesn't exist, add it!
        if(!$update_site_id) {
            $query->clear()
                ->insert('#__update_sites')
                ->columns(array($db->quoteName('name'), $db->quoteName('type'), $db->quoteName('location'), $db->quoteName('enabled')))
                ->values($db->quote($name) . ', ' . $db->quote($type) . ', ' . $db->quote($location) . ', ' . (int)$enabled);
            $db->setQuery($query);

            if($db->execute()) {
                // Link up this extension to the update site
                $update_site_id = $db->insertid();
            }
        }

        // Check if it has an update site id (creation might have faileD)
        if($update_site_id) {
            // Look for an update site entry that exists
            $query->clear()
                ->select('update_site_id')
                ->from('#__update_sites_extensions')
                ->where('update_site_id = ' . $update_site_id)
                ->where('extension_id = ' . $extensionId);
            $db->setQuery($query);
            $tmpid = (int)$db->loadResult();

            if(!$tmpid) {
                // Link this extension to the relevant update site
                $query->clear()
                    ->insert('#__update_sites_extensions')
                    ->columns(array($db->quoteName('update_site_id'), $db->quoteName('extension_id')))
                    ->values($update_site_id . ', ' . $extensionId);
                $db->setQuery($query);
                $db->execute();
            }
        }
    }

    public function getExtensionId()
    {
        $db = JFactory::getDbo();
        $tableExtensions = $db->quoteName("#__extensions");
        $columnElement = $db->quoteName("element");
        $columnType = $db->quoteName("type");
        $db->setQuery("SELECT extension_id FROM $tableExtensions WHERE $columnElement='canonicallinks' AND $columnType='plugin'");
        return $db->loadResult();
    }
}
