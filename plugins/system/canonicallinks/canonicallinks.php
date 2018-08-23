<?php
/**
 * @package      Canonical Links All in One
 * @copyright    Marko Dedovic / ManageCMS.com. All rights reserved.
 * @license      GNU GPLv2 <http://www.gnu.org/licenses/gpl.html> or later
 */
defined('_JEXEC') or die;
jimport('joomla.html.pagination');

class PlgSystemCanonicallinks extends JPlugin
{
    public $itemId;
    public $app;
    public $input;
    public $supported = array('com_content', 'com_k2', 'com_virtuemart', 'com_easyblog');
    public static $pluginParams;
    public static $currentLink;
    public static $languageCode;
    public static $rootUrl;
    public static $sh404sefEnabled;

    public function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);

        $this->app = JFactory::getApplication();
        $this->input = JFactory::getApplication()->input;
        self::$pluginParams = new JRegistry($params['params']);
    }

    // remove double canonical link from Joomla, only show the last one
    public function onBeforeRender()
    {
        $document = JFactory::getDocument();
        if($this->app->isAdmin() || $document->getType() != 'html') {
            return;
        }

        $router = $this->app->getRouter();
        $uri = clone JUri::getInstance();
        $parsed = $router->parse($uri);
        // if url is accessed like http://localhost/joomla/en/component/content/article?id=2, then router parser will overwrite 'id' with 'article' for some reason
        // put manually id back to the parsed array
        $queryId = $this->input->get('id', 0, 'INT');
        if(!empty($queryId)) {
            $parsed['id'] = $queryId;
        }
        // in url https://www.sfweb.it/faq/cms/prestashop/index.php?option=com_k2&view=itemlist&layout=category&task=category&id=25&Itemid=710&amp=1
        // router parser will overwrite 'itemlist' with 'item' for some reason, it could be due to some local preprocessing router
        // put manually itemlist back to the parsed array
        $queryView = $this->input->get('view', '', 'STRING');
        if(!empty($queryView)) {
            $parsed['view'] = $queryView;
        }

        // sometimes forms override option via post, it's not detected when parsing uri
        if(!empty($parsed['option']) && in_array($parsed['option'], $this->supported) && (empty($_POST['option']) || in_array($_POST['option'], $this->supported)) && !empty($parsed['view'])) {
            self::$currentLink = $this->getCurrentLink();
            self::$languageCode = $this->getLanguageCode();
            $overrideCanonicals = self::$pluginParams->get('override_canonicals', '');
            $removeCanonicals = self::$pluginParams->get('remove_canonicals', '');
            $removeSlashes = function($value) {
                return rtrim($value, '/');
            };
            $currentLink = rtrim(self::$currentLink, '/');
            if(!empty($removeCanonicals)) {
                $removeCanonicals = array_map($removeSlashes, array_map('trim', preg_split("/\\r\\n|\\r|\\n/", $removeCanonicals)));
                foreach($removeCanonicals as $removeCanonical) {
                    if($removeCanonical != rawurldecode($removeCanonical)) {
                        $removeCanonicals[] = rawurldecode($removeCanonical);
                    }
                }
                if(array_search($currentLink, $removeCanonicals) !== false) {
                    $this->unsetCanonicalTag();
                    return;
                }
                // check if wildcard is used
                $wildCards = array_keys(array_filter($removeCanonicals, function($var) {
                    return strpos($var, '/*') !== false;
                }));
                if(!empty($wildCards)) {
                    foreach($wildCards as $wildCard) {
                        if(strpos(rtrim($currentLink, '/') . '/', rtrim($removeCanonicals[$wildCard], '*')) === 0) {
                            $this->unsetCanonicalTag();
                            return;
                        }
                    }
                }
            }
            if(!empty($overrideCanonicals)) {
                $overrideCanonicals = array_map('trim', preg_split("/\\r\\n|\\r|\\n/", $overrideCanonicals));
                foreach($overrideCanonicals as $overrideCanonical) {
                    $override = explode('|', $overrideCanonical);
                    if(count($override) == 2) {
                        $duplicate = rtrim($override[0], '/');
                        $canonical = $override[1];
                        if($duplicate == $currentLink || rawurldecode($duplicate) == $currentLink) {
                            $this->addCanonicalLink($canonical, 'canonical', true);
                            return;
                        }
                    }
                }
            }
            // sh404sef will already output correct paginated links, so no need for us to do anything.
            // plus, sh404sef will make them with /page-1, /page-2 etc, which we don't manage to do because they are routed before we can attach
            // limit and limitstart, and routed link afterwards doesn't look right. so, leave the ones from sh404sef, they are fine anyway
            // and if we try to set the canonical link on a paginated page, it will be with ?start=xx instead of /page-xx, so better do not output anything there
            $sh404sefEnabled = self::sh404sefEnabled();
            if($sh404sefEnabled && !empty($parsed['limitstart'])) {
                //return;
            }
            require_once('components/' . $parsed['option'] . '.php');
            $className = 'Canonicallinks' . ucfirst($parsed['option']);
            $model = new $className;
            $model->setCanonical($parsed);
        }
    }

    public function addCanonicalLink($link, $type = 'canonical', $routed = false)
    {
        $uri = clone JUri::getInstance();
        $homepage = rtrim(JUri::root(), '/');
        $router = $this->app->getRouter();
        $parsed = $router->parse($uri);
        $domain = self::$pluginParams->get('domain');
        $slashedRedirect = $ignoreRedirect = false;
        $nonCanonicalDomain = false;
        if(!empty($this->itemId)) {
            $link .= '&Itemid=' . $this->itemId;
        }
        if(empty($domain)) {
            $domain = $uri->toString(array('scheme', 'host', 'port'));
        }
        else {
            if(rtrim($domain, '/') != rtrim($uri->toString(array('scheme', 'host', 'port'), '/'))) {
                $nonCanonicalDomain = true;
            }
        }
        if(!$routed) {
            $link = rtrim($domain, '/') . JRoute::_($link, false);
        }
        if(rawurldecode(rtrim($link, '/')) == rtrim(self::$currentLink, '/') && rawurldecode($link) != self::$currentLink) {
            $slashedRedirect = true;
        }
        if(self::$pluginParams->get('remove_trailing_slash', 1)) {
            $link = rtrim($link, '/');
        }
        if($nonCanonicalDomain || (rawurldecode($link) != self::$currentLink) && ($homepage != rtrim($link, '/') || strpos(self::$currentLink, 'productsublayout=') !== false)) {
            $onCanonicalPage = false;
        }
        else {
            $onCanonicalPage = true;
        }
        $ignoreSpecificQueries = array_map('trim', explode(',', self::$pluginParams->get('ignore_specific_queries', '')));
        $currentQuery = $uri->getQuery();
        if(!empty($ignoreSpecificQueries) && !empty($currentQuery)) {
            parse_str($currentQuery, $currentQueries);
            foreach($ignoreSpecificQueries as $ignoreSpecificQuery) {
                if(array_key_exists($ignoreSpecificQuery, $currentQueries)) {
                    $ignoreRedirect = true;
                }
            }
        }
        if(self::$pluginParams->get('redirect', 0) && $type == 'canonical' && (self::$pluginParams->get('redirect_slashed', 0) || !$slashedRedirect) && !$onCanonicalPage && !$ignoreRedirect && strpos($link, 'cf_id') === false && (empty($parsed['tmpl']) || (!empty($parsed['tmpl']) && $parsed['tmpl'] !== 'component'))) {
            $jVersion = new JVersion();
            if(substr($jVersion->getShortVersion(), 0, 1) == "3") {
                $this->app->redirect($link, true);
            }
            else {
                $this->app->redirect($link, '', '', true);
            }
        }
        if($onCanonicalPage && self::$pluginParams->get('unset_self_canonical', 0)) {
            $this->unsetCanonicalTag();
        }
        else {
            $this->addHeadLink($link, $type);
        }
    }

    public function unsetCanonicalTag()
    {
        $document = JFactory::getDocument();
        $header = $document->getHeadData();
        foreach($header['links'] as $key => $array) {
            if($array['relation'] == 'canonical') {
                unset($document->_links[$key]);
            }
        }
    }

    public function addHeadLink($link, $type = 'canonical')
    {
        $document = JFactory::getDocument();
        $header = $document->getHeadData();
        foreach($header['links'] as $key => $array) {
            if($array['relation'] == $type) {
                unset($document->_links[$key]);
            }
        }
        $document->addHeadLink($this->encodeUrl(rawurldecode($link)), $type);
    }

    public function getCurrentLink()
    {
        // check to see if we need to add language prefix
        $uri = clone JUri::getInstance();
        // JUri instance path may already be without lang tag sometimes, in which case skip this so it's not added twice
        $lang = $uri->getVar('lang');
        if(!empty(self::$languageCode) && !empty($lang)) {
            $uri->delVar('lang');
            $uri->setPath('/' . self::$languageCode . '/' . $uri->getPath());
        }
        if(self::$pluginParams->get('ignore_query', 1) == 1) {
            $start = $uri->getVar('start');
            $link = rawurldecode($uri->toString(array('scheme', 'host', 'port', 'path')));
            // don't kill pagination
            if(empty($start)) {
                $start = $uri->getVar('limitstart');
            }
            if(!empty($start)) {
                $link .= '?start=' . $start;
            }
            // k2 tools uses catid for filtering, include it
            $router = $this->app->getRouter();
            $uri = clone JUri::getInstance();
            $parsed = $router->parse($uri);
            $queryVariables = array();
            if(!empty($parsed['option']) && $parsed['option'] == 'com_k2' && !empty($parsed['task']) && $parsed['task'] == 'date') {
                $catid = JFactory::getApplication()->input->get('catid', 0);
                if(!empty($catid)) {
                    $queryVariables[] = 'catid=' . $catid;
                }
            }
            if(!empty($parsed['option']) && $parsed['option'] == 'com_k2') {
                // enable ChronoForms support
                $cf = $this->input->get('cf_id');
                if(!empty($cf)) {
                    $queryVariables[] = 'cf_id=' . $cf;
                }
            }
            $productsublayout = $this->input->get->get('productsublayout', null);
            if(!empty($parsed['option']) && $parsed['option'] == 'com_virtuemart' && isset($productsublayout)) {
                // virtuemart homepage can end up in a redirection loop without this, default value for productlayout is 0
                $queryVariables[] = 'productsublayout=' . $productsublayout;
            }
            if(strpos($link, '?') === false && !empty($queryVariables)) {
                $link .= '?';
            }
            if(!empty($queryVariables)) {
                $link .= implode('&', $queryVariables);
            }
            return $link;
        }
        else {
            return rawurldecode($uri->toString(array('scheme', 'host', 'port', 'path', 'query')));
        }
    }

    public function addPaginationLinks($total, $limitstart, $limit, $link, $routed = false)
    {
        $pagination = new JPagination($total, $limitstart, $limit);
        $pages = $pagination->getData();
        $baseLink = $link;
        if(strpos($link, '?') === false) {
            if(self::sh404sefEnabled()) {
                $link .= '?limit=' . $limit . '&limitstart=';
            }
            else {
                $link .= '?start=';
            }
        }
        else {
            if(self::sh404sefEnabled()) {
                $link .= '&limit=' . $limit . '&limitstart=';
            }
            else {
                $link .= '&start=';
            }
        }
        if(!is_null($pages->next->base)) {
            $this->addCanonicalLink($link . $pages->next->base, 'next', $routed);
        }
        if(!is_null($pages->previous->base)) {
            if($pages->previous->base == 0) {
                $this->addCanonicalLink($baseLink, 'prev', $routed);
            }
            else {
                $this->addCanonicalLink($link . $pages->previous->base, 'prev', $routed);
            }
        }
    }

    public static function sh404sefEnabled()
    {
        if(!isset(self::$sh404sefEnabled)) {
            self::$sh404sefEnabled = false;
            // we cannot use API call for JComponentHelper::getComponent('com_sh404sef', true) immediately since for Joomla older than 3.8.2 it would show alert error when
            // component not installed, so check manually if it's installed first
            $db = JFactory::getDbo();
            $result = (int) $db->setQuery(
                $db->getQuery(true)
                    ->select('COUNT(' . $db->quoteName('extension_id') . ')')
                    ->from($db->quoteName('#__extensions'))
                    ->where($db->quoteName('element') . ' = ' . $db->quote('com_sh404sef'))
                    ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
            )->loadResult();
            if(!empty($result)) {
                $sh404sefInstalled = JComponentHelper::getComponent('com_sh404sef', true);
                if(!empty($sh404sefInstalled->enabled) && $sh404sefInstalled->params->get('Enabled')) {
                    self::$sh404sefEnabled = true;
                }
            }
        }
        return self::$sh404sefEnabled;
    }

    private function getLanguageCode()
    {
        $plugin = JPluginHelper::getPlugin('system', 'languagefilter');
        if(isset($plugin->params)) {
            $modeSef = JFactory::getConfig()->get('sef', 0);
            $langCodes = JLanguageHelper::getLanguages('lang_code');
            $defaultLang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
            $language = JFactory::getLanguage();
            if(!empty($language)) {
                $languageTag = $language->getTag();
            }
            $params = new JRegistry($plugin->params);
            if(isset($languageTag) && isset($langCodes[$languageTag])) {
                $sef = $langCodes[$languageTag]->sef;
            }
            else {
                $sef = $langCodes[$defaultLang]->sef;
            }
            if($modeSef && (!$params->get('remove_default_prefix', 0) || $languageTag != $defaultLang)) {
                return $sef;
            }
        }
    }

    private function encodeUrl($link)
    {
        $rootUrl = $this->getRootUrl();
        $slashedLink = false;
        if(substr($link, -1) == '/') {
            $slashedLink = true;
        }
        // remove root domain and language code, if it's encoded JDocument messes up link when added to the head
        $link = str_replace($rootUrl, '', rtrim($link, '/'));
        // remove it so that there are no empty values in the array, but do not remove it in the previous step because on the homepage result would be wrong
        $link = parse_url(ltrim($link, '/'));
        if(!empty($link['path'])) {
            // encode url parts separately and reconstruct the link
            $path = implode('/', array_map('rawurlencode', explode('/', $link['path'])));
        }
        if(!empty($link['query'])) {
            $parts = explode('&', $link['query']);
            $query = array();
            foreach($parts as $part) {
                $part = explode('=', $part);
                if(count($part) == 2) {
                    $query[] = rawurlencode($part[0]) . '=' . rawurlencode($part[1]);
                }
            }
            // encode url parts separately and reconstruct the link
            $query = implode('&', $query);
        }
        $encodedLink = $rootUrl;
        // on the homepage $path would be empty, return link without slash
        if(!empty($path)) {
            if(!empty($query)) {
                $encodedLink = $rootUrl . '/' . $path . '?' . $query;
            }
            else {
                $encodedLink = $rootUrl . '/' . $path;
            }
        }
        if($slashedLink) {
            $encodedLink .= '/';
        }
        return $encodedLink;
    }

    private function getRootUrl()
    {
        $base = ltrim(JUri::base(true), '/');
        $uri = clone JUri::getInstance();
        $hostname = $uri->toString(array('scheme', 'host', 'port'));
        if(empty(self::$rootUrl)) {
            $jVersion = new JVersion();
            if(substr($jVersion->getShortVersion(), 0, 1) == "3") {
                $urlRewriting = JFactory::getApplication()->get('sef_rewrite', 0);
            }
            else {
                $urlRewriting = JFactory::getConfig()->get('sef_rewrite', 0);
            }
            $domain = self::$pluginParams->get('domain');
            if(empty($domain)) {
                self::$rootUrl = rtrim($hostname, '/');
            }
            else {
                self::$rootUrl = rtrim($domain, '/');
            }
            if(!empty($base)) {
                self::$rootUrl .= '/' . $base;
            }
            if(!$urlRewriting) {
                self::$rootUrl .= '/index.php';
            }
            if(!empty(self::$languageCode)) {
                self::$rootUrl .= '/' . self::$languageCode;
            }
        }
        return self::$rootUrl;
    }
}
