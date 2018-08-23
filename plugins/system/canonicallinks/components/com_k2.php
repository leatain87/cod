<?php
/**
 * @package      Canonical Links All in One
 * @copyright    Marko Dedovic / ManageCMS.com. All rights reserved.
 * @license      GNU GPLv2 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die ('Restricted access');

JLoader::register('K2Plugin', JPATH_SITE . '/administrator/components/com_k2/lib/k2plugin.php');
JLoader::register('K2HelperUtilities', JPATH_SITE . '/components/helpers/utilities.php');
JLoader::register('K2ModelItemlist', JPATH_SITE . '/components/models/itemlist.php');
JTable::addIncludePath(JPATH_SITE . '/administrator/components/com_k2/tables');
require_once(JPATH_SITE . '/components/com_k2/helpers/route.php');

class CanonicallinksCom_k2 extends PlgSystemCanonicallinks
{

    //params needed for K2
    public $pluginName = 'k2canonical';
    public $pluginNameHumanReadable = 'K2canonical';
    public $db;
    public $input;

    // override inherited constructor
    public function __construct()
    {
        $this->app = JFactory::getApplication();
        $this->db = JFactory::getDbo();
        $this->input = JFactory::getApplication()->input;
    }

    public function setCanonical($parsed)
    {
        $limitstart = 0;
        if(!empty($parsed['limitstart'])) {
            $limitstart = $parsed['limitstart'];
        }
        if(empty($parsed['task'])) {
            $parsed['task'] = '';
        }

        if(!empty($parsed['task']) && !empty($parsed['tag']) && $parsed['task'] == 'tag' && empty($parsed['id'])) {
            $link = K2HelperRoute::getTagRoute($parsed['tag']);
            $this->itemId = $this->findItemid($parsed['task']);
            $this->setPaginationLinks($limitstart, $link, 'tag');
        }
        else if(!empty($parsed['task']) && $parsed['task'] == 'user' && !empty($parsed['id']) && !empty($parsed['view']) && $parsed['view'] == 'itemlist') {
            $link = K2HelperRoute::getUserRoute((int)$parsed['id']);
            if(strpos($link, 'Itemid') === false) {
                $this->itemId = $this->findItemid($parsed['task']);
            }
            $this->setPaginationLinks($limitstart, $link, 'user');
        }
        else if(!empty($parsed['task']) && $parsed['task'] == 'date' && !empty($parsed['Itemid']) && !empty($parsed['view']) && $parsed['view'] == 'itemlist') {
            // set current menu path as canonical link for a given date
            $day = !empty($parsed['day']) ? $parsed['day'] : 0;
            $month = !empty($parsed['month']) ? $parsed['month'] : 0;
            $year = !empty($parsed['year']) ? $parsed['year'] : 0;
            $catid = !empty($parsed['catid']) ? $parsed['catid'] : 0;
            $link = JRoute::_('index.php?Itemid=' . $parsed['Itemid']);
            $link .= '/itemlist/date';
            if(!empty($year)) {
                $link .= '/' . $year;
            }
            if(!empty($month)) {
                $link .= '/' . $month;
            }
            if(!empty($day)) {
                $link .= '/' . $day;
            }
            if(!empty($catid)) {
                if(strpos($link, '?') === false) {
                    $link .= '?catid=' . $catid;
                }
                else {
                    $link .= '&catid=' . $catid;
                }
            }
        }
        else if(!empty($parsed['id']) && !empty($parsed['view']) && ($parsed['view'] == 'item' || ($parsed['view'] == 'itemlist' && $parsed['task'] == 'category'))) {
            // don't refactor this double if, I cannot move findItemid down because pagination needs it
            if($parsed['view'] == 'item') {
                $item = JTable::getInstance('K2Item', 'Table');
                $item->load((int)$parsed['id']);
                if(!empty($item->catid)) {
                    $item->category = JTable::getInstance('K2Category', 'Table');
                    $item->category->load($item->catid);
                }
                else {
                    return;
                }
            }
            else {
                $item = JTable::getInstance('K2Category', 'Table');
                $item->load($parsed['id']);
            }
            $this->itemId = $this->findItemid($parsed['task'], $item, $parsed['view']);
            if($parsed['view'] == 'item') {
                $link = K2HelperRoute::getItemRoute($item->id . ':' . rawurlencode($item->alias), $item->catid . ':' . rawurlencode($item->category->alias));
                // enable ChronoForms support
                $cf = $this->input->get('cf_id');
                if(!empty($cf)) {
                    $link .= '&cf_id=' . $cf;
                }
            }
            else {
                $link = K2HelperRoute::getCategoryRoute($item->id . ':' . rawurlencode($item->alias));
                $this->setPaginationLinks($limitstart, $link, 'category', $item->id);
            }
        }
        // special case for links like http://localhost/joomla/en/k2-child-category-2/itemlist, where id is not passed but we can still guess the canonical link
        else if(empty($parsed['id']) && !empty($parsed['view']) && $parsed['view'] == 'itemlist' && !empty($parsed['Itemid'])) {
            $link = 'index.php?option=com_k2&Itemid=' . $parsed['Itemid'];
        }
        else if(!empty($parsed['view']) && ($parsed['view'] == 'latest')) {
            $this->addHeadLink($this->getCurrentLink());
        }
        if(!empty($link)) {
            if(!empty($limitstart)) {
                if(strpos($link, '?') === false) {
                    if(self::sh404sefEnabled()) {
                        $link .= '?limitstart=' . $limitstart . (isset($parsed['limit']) ? '&limit=' . $parsed['limit'] : '');
                    }
                    else {
                        $link .= '?start=' . $limitstart;
                    }
                }
                else {
                    if(self::sh404sefEnabled()) {
                        $link .= '&limitstart=' . $limitstart . (isset($parsed['limit']) ? '&limit=' . $parsed['limit'] : '');
                    }
                    else {
                        $link .= '&start=' . $limitstart;
                    }
                }
            }
            $this->addCanonicalLink($link);
        }
    }

    public function setPaginationLinks($limitstart, $link, $view, $catId = null)
    {
        $params = K2HelperUtilities::getParams('com_k2');
        if($view == 'category') {
            $category = JTable::getInstance('K2Category', 'Table');
            $category->load($catId);
            $cparams = new JRegistry($category->params);
            if($cparams->get('inheritFrom')) {
                $masterCategory = JTable::getInstance('K2Category', 'Table');
                $masterCategory->load($cparams->get('inheritFrom'));
                $cparams = new JRegistry($masterCategory->params);
            }
            $params->merge($cparams);
            $limit = $params->get('num_leading_items') + $params->get('num_primary_items') + $params->get('num_secondary_items') + $params->get('num_links');
        }
        else if($view == 'tag') {
            $limit = $params->get('tagItemCount');
        }
        else {
            $limit = $params->get('userItemCount');
        }
        $model = new K2ModelItemlist();
        $total = $model->getTotal();
        $this->addPaginationLinks($total, $limitstart, $limit, $link);
    }

    public function findItemid($task, $item = null, $view = null)
    {
        $currentLanguage = JFactory::getLanguage()->getTag();
        $menus = $this->getK2Menus();

        // special case for tag views
        if($task == 'tag') {
            // if tag was filtered by categories, output current Itemid, since each tag page will produce different articles
            $menu = JFactory::getApplication()->getMenu();
            if(!empty($menu)) {
                $current = $menu->getActive();
                $menuParams = $current->params;
                if(!empty($menuParams)) {
                    $categoriesFilter = $menuParams->get('categoriesFilter');
                    if(!empty($categoriesFilter)) {
                        return $current->id;
                    }
                }
            }
            // check if there is a direct menu link to the tag
            $tag = urlencode($this->input->get('tag', '', 'string'));
            foreach($menus as $menu) {
                if('index.php?option=com_k2&view=itemlist&layout=tag&tag=' . $tag . '&task=tag' === $menu->link) {
                    return $menu->id;
                }
                // apparently $tag can be decoded as well, e.g. with spaces in the link, so the about statement wouldn't match, but this could
                else if('index.php?option=com_k2&view=itemlist&layout=tag&tag=' . urldecode($tag) . '&task=tag' === $menu->link) {
                    return $menu->id;
                }
            }
            $defaultMenu = self::$pluginParams->get('k2tagmenu', 0);
            if(!empty($defaultMenu)) {
                return $defaultMenu;
            }
            // then return first menu id of a category which has articles tagged with that tag
            $categories = $this->getTagCategories($this->input->get('tag', '', 'string'));
            foreach($categories as $category) {
                foreach($menus as $menu) {
                    if('index.php?option=com_k2&view=itemlist&layout=category&task=category&id=' . $category === $menu->link) {
                        return $menu->id;
                    }
                    // when multiple categories are selected in menu options
                    $params = new JRegistry($menu->params);
                    $categories = $params->get('categories', 0);
                    if(is_array($categories) && in_array($category, $categories)) {
                        return $menu->id;
                    }
                }
            }

            foreach($menus as $menu) {
                if(strpos($menu->link, 'view=itemlist&layout=category&task=category') !== false) {
                    return $menu->id;
                }
            }
            return;
        }
        else if($task == 'user') {
            foreach($menus as $menu) {
                if(strpos($menu->link, 'view=itemlist&layout=category&task=category') !== false) {
                    return $menu->id;
                }
            }
        }

        if($view == 'item') {
            // check to see if there is a direct menu link to k2 item
            foreach($menus as $menu) {
                if('index.php?option=com_k2&view=item&layout=item&id=' . $item->id === $menu->link) {
                    if($menu->language == '*' || $menu->language == $currentLanguage) {
                        return $menu->id;
                    }
                }
            }
            // get categories to which item belongs
            $categories = $this->getItemCategories($item->category);
        }
        else {
            // check to see if there is a direct menu link to k2 category
            foreach($menus as $menu) {
                if('index.php?option=com_k2&view=itemlist&layout=category&task=category&id=' . $item->id === $menu->link) {
                    if($menu->language == '*' || $menu->language == $currentLanguage) {
                        return $menu->id;
                    }
                }
            }
            // get categories to which category belongs
            $categories = $this->getItemCategories($item);
        }
        // no direct menu link, let's find menu link to the first belonging category
        foreach($categories as $category) {
            foreach($menus as $menu) {
                if('index.php?option=com_k2&view=itemlist&layout=category&task=category&id=' . $category === $menu->link) {
                    if($menu->language == '*' || $menu->language == $currentLanguage) {
                        return $menu->id;
                    }
                }
            }
        }
        // still nothing, let's check if there is a tag leading to an item
        if($view == 'item' && !empty($item->tags)) {
            foreach($menus as $menu) {
                foreach($item->tags as $tag) {
                    if('index.php?option=com_k2&view=itemlist&layout=tag&tag=' . $tag->name . '&task=tag' === $menu->link) {
                        return $menu->id;
                    }
                }
            }
        }

        // maybe additional categories plugin is installed
        if($view == 'item' && JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
            $categories = $this->getAdditionalCategories($item);
            if(!empty($categories)) {
                foreach($categories as $category) {
                    foreach($menus as $menu) {
                        if('index.php?option=com_k2&view=itemlist&layout=category&task=category&id=' . $category === $menu->link) {
                            return $menu->id;
                        }
                    }
                }
            }
        }
    }

    public function getK2Menus()
    {
        $query = $this->db->getQuery(true);
        $query
            ->select('menu.id, menu.link, menu.params, menu.language')
            ->from($this->db->quoteName('#__menu', 'menu'))
            ->join('INNER', $this->db->quoteName('#__extensions', 'extensions') . ' ON (extensions.extension_id = menu.component_id)')
            ->where('extensions.name="com_k2" AND extensions.type="component" AND menu.published=1 AND menu.access=1 AND menu.client_id=0');
        $this->db->setQuery($query);
        $result = $this->db->loadObjectList();
        return $result;
    }

    public function getItemCategories($category)
    {
        $query = $this->db->getQuery(true);
        $categories[] = $category->id;
        $catid = $category->parent;
        // traverse category tree to the top
        while($catid != 0) {
            $categories[] = $catid;
            $query = $this->db->getQuery(true);
            $query
                ->select('parent')
                ->from('#__k2_categories')
                ->where('id=' . $catid);
            $this->db->setQuery($query);
            $catid = $this->db->loadResult();
        }
        return $categories;
    }

    public function getTagCategories($tag)
    {
        $now = JFactory::getDate();
        $now = $now->toSql();
        $user = JFactory::getUser();
        $app = JFactory::getApplication();
        $languageFilter = $app->getLanguageFilter();
        $nullDate = $this->db->getNullDate();
        $query = 'SELECT item.catid
               FROM #__k2_tags AS tags
               INNER JOIN #__k2_tags_xref AS xref ON xref.tagID=tags.id 
               INNER JOIN #__k2_items AS item ON item.id=xref.itemID 
               WHERE tags.name = ' . $this->db->Quote($tag) . ' AND item.published = 1
               AND (item.publish_up = ' . $this->db->Quote($nullDate) . ' OR item.publish_up <= ' . $this->db->Quote($now) . ' )
               AND (item.publish_down = ' . $this->db->Quote($nullDate) . ' OR item.publish_down >= ' . $this->db->Quote($now) . ' )
               AND item.access IN(' . implode(",", $user->getAuthorisedViewLevels()) . ')' . ' AND item.trash = 0';
        if($languageFilter) {
            $languageTag = JFactory::getLanguage()->getTag();
            $query .= ' AND item.language IN (' . $this->db->quote($languageTag) . ',' . $this->db->quote('*') . ')';
        }
        $query .= ' LIMIT 1';
        $this->db->setQuery($query);
        $catid = $this->db->loadResult();
        // traverse category tree to the top
        $categories = array();
        while($catid != 0) {
            $categories[] = $catid;
            $query = $this->db->getQuery(true);
            $query
                ->select('parent')
                ->from('#__k2_categories')
                ->where('id=' . $catid);
            $this->db->setQuery($query);
            $catid = $this->db->loadResult();
        }
        return array_reverse($categories);
    }

    public function getAdditionalCategories($item)
    {
        $query = $this->db->getQuery(true);
        $query
            ->select('catid')
            ->from($this->db->quoteName('#__k2_additional_categories'))
            ->where('itemID=' . $item->id);
        $this->db->setQuery($query);
        $categories = $this->db->loadColumn();
        return $categories;
    }
}