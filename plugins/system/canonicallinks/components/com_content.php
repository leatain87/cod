<?php
/**
 * @package      Canonical Links All in One
 * @copyright    Marko Dedovic / ManageCMS.com. All rights reserved.
 * @license      GNU GPLv2 <http://www.gnu.org/licenses/gpl.html> or later
 */
defined('_JEXEC') or die;

JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

class CanonicallinksCom_content extends PlgSystemCanonicallinks
{
    public $input;

    // override inherited constructor
    public function __construct()
    {
        $this->app = JFactory::getApplication();
        $this->input = JFactory::getApplication()->input;
    }

    public function setCanonical($parsed)
    {
        $canonical = $this->getCanonical($parsed);
        if($parsed['view'] == 'category' || $parsed['view'] == 'featured' || $parsed['view'] == 'archive') {
            $limitstart = 0;
            // set view to pass for pagination
            $view = $parsed['view'];
            if(!empty($parsed['limitstart'])) {
                $limitstart = $parsed['limitstart'];
            }

            if($parsed['view'] == 'category' && !empty($parsed['id'])) {
                $this->setPaginationLinks($limitstart, $canonical, $view, $parsed['id']);
            }
            else {
                parse_str(parse_url($canonical, PHP_URL_QUERY), $parsed);
                // unset from query as Joomla adds it to featured and archive links for some reason
                unset($parsed['limitstart']);
                unset($parsed['view']);
                $canonical = 'index.php?' . http_build_query($parsed);
                $this->setPaginationLinks($limitstart, $canonical, $view);
            }
            if(!empty($limitstart)) {
                if(self::sh404sefEnabled()) {
                    $canonical .= '&limitstart=' . $limitstart;
                }
                else {
                    $canonical .= '&start=' . $limitstart;
                }
            }
        }
        $this->addCanonicalLink($canonical);
    }

    public function setPaginationLinks($limitstart, $link, $view, $catId = null)
    {
        $params = $this->app->getParams();
        $menuParams = new JRegistry;
        if($menu = $this->app->getMenu()->getActive()) {
            $menuParams->loadString($menu->params);
        }
        $mergedParams = clone $menuParams;
        $mergedParams->merge($params);
        if($view == 'archive') {
            $limit = $mergedParams->get('display_num');
        }
        else {
            $limit = $mergedParams->get('num_leading_articles') + $mergedParams->get('num_intro_articles');
        }
        if($view == 'category') {
            $category = JTable::getInstance('category');
            $category->load($catId);
            $cparams = new JRegistry($category->params);
            $mergedParams->merge($cparams);
        }
        $total = $this->getTotal($mergedParams, $view, $catId);
        $this->addPaginationLinks($total, $limitstart, $limit, $link);
    }

    public function getCanonical($parsed)
    {
        JHtml::addIncludePath(JPATH_SITE . '/com_content/helpers');
        if($parsed['view'] == 'category' && !empty($parsed['id'])) {
            $canonical = ContentHelperRoute::getCategoryRoute($parsed['id']);
            // Joomla 3.8.7 introduced a bug when layout variable is added to the query. remove the variable from canonical links
            $parsedCanonical = parse_url($canonical);
            $query = [];
            if(!empty($parsedCanonical['query'])) {
                parse_str($parsedCanonical['query'], $query);
            }
            if(isset($query['layout'])) {
                unset($query['layout']);
                $canonical = 'index.php?' . http_build_query($query);
            }
        }
        else if($parsed['view'] == 'article' && !empty($parsed['id']) && is_numeric($parsed['id'])) {
            // if alias was renamed catid will be wrongly set in input variables, find correct id manually
            $catId = $this->findArticleCategory($parsed['id']);
            if(!empty($catId)) {
                if(!empty($parsed['lang'])) {
                    $app = JFactory::getApplication();
                    $menu = $app->getMenu();
                    $lang = JFactory::getLanguage();
                    if($menu->getActive() == $menu->getDefault($lang->getTag())) {
                        $canonical = ContentHelperRoute::getArticleRoute($parsed['id'], 0, $parsed['lang']);
                    }
                    else {
                        $canonical = ContentHelperRoute::getArticleRoute($parsed['id'], $catId, $parsed['lang']);
                    }

                }
                else {
                    $canonical = ContentHelperRoute::getArticleRoute($parsed['id'], $catId);
                }
            }
            // support for articles with pagination
            if(!empty($parsed['showall']) || !empty($parsed['start']) || !empty($parsed['limitstart'])) {
                if(strpos($canonical, '?') === false) {
                    $canonical .= '?';
                }
                else {
                    $canonical .= '&';
                }
                if(!empty($parsed['showall'])) {
                    $canonical .= 'showall=' . $parsed['showall'];
                    if(!empty($parsed['start']) || !empty($parsed['limitstart'])) {
                        $canonical .= '&';
                    }
                }
                if(!empty($parsed['start'])) {
                    $canonical .= 'start=' . $parsed['start'];
                }
                else if(!empty($parsed['limitstart'])) {
                    $canonical .= 'start=' . $parsed['limitstart'];
                }
            }
        }
        else if($parsed['view'] == 'categories' && !empty($parsed['id'])) {
            $canonical = 'index.php?' . http_build_query($parsed);
        }
        else {
            // custom views can make extra query variable, and since there is anyway itemId set it's not needed
            unset($parsed['view']);
            $canonical = 'index.php?' . http_build_query($parsed);
        }
        return $canonical;
    }

    // total rows for category/featued views, query has to be run since there is no access to original results
    public function getTotal($params, $view, $catId = null)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
            ->from('#__content AS a')
            ->where('a.access = 1 ');
        $nullDate = $db->Quote($db->getNullDate());
        $nowDate = $db->Quote(JFactory::getDate()->toSQL());
        $query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
        $query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
        if($view == 'category') {
            $includeSubcategories = $params->get('show_subcategory_content', '0');
            $categoryEquals = 'a.catid = ' . (int)$catId;
            if($includeSubcategories) {
                $levels = (int)$params->get('show_subcategory_content', '1');
                // Create a subquery for the subcategory list
                $subQuery = $db->getQuery(true);
                $subQuery->select('sub.id');
                $subQuery->from('#__categories as sub');
                $subQuery->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt');
                $subQuery->where('this.id = ' . (int)$catId);
                if($levels >= 0) {
                    $subQuery->where('sub.level <= this.level + ' . $levels);
                }
                // Add the subquery to the main query
                $query->where('(' . $categoryEquals . ' OR a.catid IN (' . $subQuery->__toString() . '))');
            }
            else {
                $query->where($categoryEquals);
            }
        }
        else if($view == 'featured') {
            $query->where('a.featured = 1');
            $featuredCategories = $params->get('featured_categories');
            if(is_array($featuredCategories)) {
                $allCategories = false;
                foreach($featuredCategories as $key => $value) {
                    // if "all categories" has also been selected in menu options for featured view, no need to filter by category
                    if(empty($featuredCategories[$key])) {
                        $allCategories = true;
                    }
                }
                $featuredCategories = implode(',', $featuredCategories);
                if(!empty($featuredCategories) && !$allCategories) {
                    $query->where('a.catid IN (' . $featuredCategories . ')');
                }
            }
        }
        if($view == 'archive') {
            $query->where('a.state = 2');
        }
        else {
            $query->where('a.state = 1');
        }
        if($this->app->getLanguageFilter()) {
            $query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
        }

        $db->setQuery($query);
        return $db->loadResult();
    }

    public function findArticleCategory($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('catid'))
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('id') . ' = ' . $db->quote($id));
        $db->setQuery($query);
        return $db->loadResult();
    }
}
