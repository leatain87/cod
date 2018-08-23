<?php
/**
 * @package      Canonical Links All in One
 * @copyright    Marko Dedovic / ManageCMS.com. All rights reserved.
 * @license      GNU GPLv2 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die ('Restricted access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

class CanonicallinksCom_easyblog extends PlgSystemCanonicallinks
{

    public $input;
    public $viewsForRedirection = array('entry');

    // override inherited constructor
    public function __construct()
    {
        $this->app = JFactory::getApplication();
        $this->input = JFactory::getApplication()->input;
    }

    public function setCanonical($parsed)
    {
        $isSupported = $this->isVersionSupported();
        if(!$isSupported) {
            return;
        }
        // sh404sef will already output correct paginated links, so no need for us to do anything.
        // plus, sh404sef will make them with /page-1, /page-2 etc, which we don't manage to do because they are routed before we can attach
        // limit and limitstart, and routed link afterwards doesn't look right. so, leave the ones from sh404sef, they are fine anyway
        // and if we try to set the canonical link on a paginated page, it will be with ?start=xx instead of /page-xx, so better do not output anything there
        if(!empty($parsed['limitstart']) && self::sh404sefEnabled()) {
            return;
        }
        $document = JFactory::getDocument();
        $header = $document->getHeadData();
        foreach($header['links'] as $key => $array) {
            if($array['relation'] == 'canonical') {
                // deliberately overwriting previous value
                $canonical = $key;
                unset($document->_links[$key]);
            }
        }
        if(!empty($canonical)) {
            $view = $this->input->get('view');
            $uri = clone JUri::getInstance();
            $domain = self::$pluginParams->get('domain');
            if(empty($domain)) {
                $domain = $uri->toString(array('scheme', 'host', 'port'));
            }
            else {
                $currentDomain = $uri->toString(array('scheme', 'host', 'port'));
                $canonical = str_replace($currentDomain, '', $canonical);
            }
            if(strpos($canonical, $domain) === false) {
                $canonical = rtrim($domain, '/') . $canonical;
            }
            $setPagination = $this->isPaginatedView($parsed);
            if($setPagination) {
                $limitstart = 0;
                // set view to pass for pagination
                if(!empty($parsed['limitstart'])) {
                    $limitstart = $parsed['limitstart'];
                }
                // see comment at the top regarding sh404sef
                if(!self::sh404sefEnabled()) {
                    $this->setPaginationLinks($limitstart, $canonical, $parsed);
                }
            }
            if(!empty($limitstart)) {
                if(strpos($canonical, '?') === false) {
                    $canonical .= '?start=' . $limitstart;
                }
                else {
                    $canonical .= '&start=' . $limitstart;
                }
            }
            // if we are not in one of the predefined views, never redirect
            if(in_array($view, $this->viewsForRedirection)) {
                $this->addCanonicalLink($canonical, 'canonical', true);
            }
            else {
                $this->addHeadLink($canonical, 'canonical');
            }
        }
    }

    public function isPaginatedView($parsed)
    {
        if(!empty($parsed['id']) && !empty($parsed['view']) && !empty($parsed['layout']) && $parsed['view'] == 'categories' && $parsed['layout'] == 'listings') {
            return true;
        }
        else if(!empty($parsed['view']) && $parsed['view'] == 'latest') {
            return true;
        }
        return false;
    }

    public function setPaginationLinks($limitstart, $link, $parsed)
    {
        if($parsed['view'] == 'categories' && $parsed['layout'] == 'listings') {
            $model = EB::model('Category');
            $total = $model->getTotalPostCount($parsed['id']);
            $limit = EB::pagination(0, 0, 0)->getLimit(EBLOG_PAGINATION_CATEGORIES);
        }
        else if($parsed['view'] == 'latest') {
            $limit = EB::call('Pagination', 'getLimit', array('listlength'));
            $total = $this->getLatestViewTotal();
        }
        $this->addPaginationLinks($total, $limitstart, $limit, $link, true);
    }

    public function getLatestViewTotal()
    {
        $menu = JFactory::getApplication()->getMenu();
        if(!empty($menu)) {
            $current = $menu->getActive();
            if(!empty($current->params)) {
                $params = $current->params;
                // Get a list of category inclusions
                $inclusion = EB::getCategoryInclusion($params->get('inclusion'));
                if($params->get('includesubcategories', 0) && !empty($inclusion)) {
                    $tmpInclusion = array();
                    foreach($inclusion as $includeCatId) {
                        // Retrieve nested categories
                        $category = new stdClass();
                        $category->id = $includeCatId;
                        $category->childs = null;
                        EB::buildNestedCategories($category->id, $category);
                        $linkage = '';
                        EB::accessNestedCategories($category, $linkage, '0', '', 'link', ', ');
                        $catIds = array();
                        $catIds[] = $category->id;
                        EB::accessNestedCategoriesId($category, $catIds);
                        $tmpInclusion = array_merge($tmpInclusion, $catIds);
                    }
                    $inclusion = $tmpInclusion;
                }
                $excludeIds = array();
                $model = EB::model('Blog');
                if(!$params->get('post_include_featured', true)) {
                    // Retrieve a list of featured blog posts on the site.
                    $featured = $model->getFeaturedBlog($inclusion);
                    foreach($featured as $item) {
                        $excludeIds[] = $item->id;
                    }
                }
                // Try to retrieve any categories to be excluded.
                $excludedCategories = EB::config()->get('layout_exclude_categories');
                $excludedCategories = (empty($excludedCategories)) ? '' : explode(',', $excludedCategories);
                $catAccess = array();

                $db = EB::db();
                $queryWhere = '';
                $queryOrder = '';
                $queryLimit = '';
                $queryExclude = '';

                $isJSInstalled = false; // need to check if the site installed jomsocial.
                $file = JPATH_ROOT . '/components/com_community/libraries/core.php';
                $exists = JFile::exists($file);
                if($exists) {
                    $isJSInstalled = true;
                }
                $isJSGrpPluginInstalled = JPluginHelper::isEnabled('system', 'groupeasyblog');
                $isEventPluginInstalled = JPluginHelper::isEnabled('system', 'eventeasyblog');
                $includeJSGrp = ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
                $includeJSEvent = ($isEventPluginInstalled && $isJSInstalled) ? true : false;

                // contribution type sql
                $contributor = EB::contributor();
                $contributeSQL = ' AND ( (a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';
                if(EB::config()->get('main_includeteamblogpost')) {
                    $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a');
                }
                if($includeJSEvent) {
                    $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT, 'a');
                }
                if($includeJSGrp) {
                    $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP, 'a');
                }
                // Only process the contribution sql for EasySocial if EasySocial really exists.
                if(EB::easysocial()->exists()) {
                    $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, 'a');
                    $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, 'a');
                }

                $contributeSQL .= ')';

                if(!empty($excludeIds)) {
                    $queryExclude .= ' AND a.`id` NOT IN (';
                    for($i = 0; $i < count($excludeIds); $i++) {
                        $queryExclude .= $db->quote($excludeIds[$i]);
                        if(next($excludeIds) !== false) {
                            $queryExclude .= ',';
                        }
                    }
                    $queryExclude .= ')';
                }

                if(!empty($excludedCategories)) {
                    $catAccess['exclude'] = $excludedCategories;
                }

                $queryInclude = '';
                // Respect inclusion categories
                if(!empty($inclusion)) {
                    $catAccess['include'] = $inclusion;
                }

                $queryWhere = ' WHERE a.' . $db->quoteName('published') . '=' . $db->quote(EASYBLOG_POST_PUBLISHED);
                $queryWhere .= ' AND a.' . $db->quoteName('state') . '=' . $db->quote(EASYBLOG_POST_NORMAL);
                $queryWhere .= ' AND a.`access` = ' . $db->quote(BLOG_PRIVACY_PUBLIC);
                $queryWhere .= ' AND a.`frontpage` = ' . $db->quote('1');

                $filterLanguage = JFactory::getApplication()->getLanguageFilter();
                if($filterLanguage) {
                    $queryWhere .= EBR::getLanguageQuery('AND', 'a.language');
                }

                // category access here
                $catLib = EB::category();
                $catAccessSQL = $catLib->genAccessSQL('a.`id`', $catAccess);
                $queryWhere .= ' AND (' . $catAccessSQL . ')';

                $query = 'SELECT COUNT(*)';
                $query .= ' FROM `#__easyblog_post` AS a';
                $query .= ' LEFT JOIN `#__easyblog_featured` AS f';
                $query .= ' 	ON a.`id` = f.`content_id` AND f.`type` = ' . $db->quote('post');

                $query .= $queryWhere;
                $query .= $contributeSQL;
                $query .= $queryExclude;
                $query .= $queryInclude;
                $query .= $queryOrder;
                $query .= $queryLimit;
                $db->setQuery($query);
                return $db->loadResult();
            }
        }
    }

    public function isVersionSupported()
    {
        $contents = file_get_contents(JPATH_ADMINISTRATOR . '/components/com_easyblog/easyblog.xml');
        if($contents) {
            $parser = simplexml_load_string($contents);
            if(!empty($parser->version)) {
                return version_compare((string)$parser->version, '5', 'ge');
            }
        }
        return false;
    }
}