<?php
/**
 * @package      Canonical Links All in One
 * @copyright    Marko Dedovic / ManageCMS.com. All rights reserved.
 * @license      GNU GPLv2 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die ('Restricted access');

class CanonicallinksCom_virtuemart extends PlgSystemCanonicallinks
{

    public $input;
    public $viewsForRedirection = array('productdetails', 'category', 'virtuemart', 'manufacturer');

    // override inherited constructor
    public function __construct()
    {
        $this->app = JFactory::getApplication();
        $this->input = JFactory::getApplication()->input;
    }

    public function setCanonical($parsed)
    {
        $version = new JVersion();
        // in J3 both VM and Joomla output canonical link, which renders it obsolete, Google ignores when there are multiple ones
        // and in both J2.5 and J3 VM doesn't seem to add domain to canonical links
        $this->unsetDoubles();
    }

    public function unsetDoubles()
    {
        $document = JFactory::getDocument();
        $header = $document->getHeadData();
        foreach($header['links'] as $key => $array) {
            if($array['relation'] == 'canonical') {
                // deliberately overwriting previous value
                $canonical = $key;
                unset($document->_links[$key]);
            }
        }
        $view = $this->input->get('view');
        if(!empty($canonical)) {
            $uri = clone JUri::getInstance();
            $domain = rtrim(self::$pluginParams->get('domain'), '/');
            if(empty($domain)) {
                $domain = $uri->toString(array('scheme', 'host', 'port'));
            }
            else {
                $currentDomain = $uri->toString(array('scheme', 'host', 'port'));
                $canonical = str_replace($currentDomain, '', $canonical);
            }

            if(strpos($canonical, $domain) === false) {
                // sometimes link wasn't JRouted in VM
                if(strpos($canonical, 'index.php') === 0) {
                    jimport('joomla.methods');
                    $canonical = JRoute::_($canonical, false);
                }
                $canonical = rtrim($domain, '/') . $canonical;
            }
            if($view == 'virtuemart') {
                $canonical = str_replace(array('?productsublayout=0', '?productsublayout=products_horizon'), '', $canonical);
            }
            // if we are not in one of the predefined views, never redirect to prevent redirect loops
            if(in_array($view, $this->viewsForRedirection)) {
                $this->addCanonicalLink($canonical, 'canonical', true);
            }
            else {
                $this->addHeadLink($canonical, 'canonical');
            }
        }
        else {
            // if we are on the virtuemart homepage, they don't set canonical, so let's add it ourselves
            if($view == 'virtuemart') {
                // since we're adding whatever the current link is, check if html suffix should be added, if set in global
                $sefSuffix = JFactory::getConfig()->get('sef_suffix', 0);
                $currentLink = JUri::current();
                if($sefSuffix && stripos(strrev($currentLink), 'lmth.') !== 0) {
                    $currentLink .= '.html';
                }
                $this->addCanonicalLink(str_replace(array('?productsublayout=0', '?productsublayout=products_horizon'), '', $currentLink), 'canonical', true);
            }
        }
    }
}