<?php
/*
 * @version 6.0.2
 * @package JotCache
 * @category Joomla 3.7
 * @copyright (C) 2010-2017 Vladimir Kanich
 * @license	GNU General Public License version 2
 */
defined('JPATH_BASE') or die;
require_once dirname(__FILE__) . '/JotcacheStorage.php';
require_once dirname(__FILE__) . '/JotcacheStorageBase.php';
class JotcacheMemcachedCache extends JotcacheStorage implements JotcacheStorageBase {
protected static $db = null;
protected $persistent = true;
protected $compress = 0;
protected static $lead = '';
public $connected = false;
public function __construct($params, $options = array()) {
parent::__construct($params, $options);
if (self::$db === null) {
$this->getConnection($params);
}}protected function getConnection($params) {
        if (!$params->exists('test') && !extension_loaded('memcached')) {
$this->debug(JText::_('JOTCACHE_MEMCACHED_PHP_EXTENSION_MISSING'));
return false;
}if (!class_exists('Memcached')) {
$this->debug(JText::_('JOTCACHE_MEMCACHED_CLASS_MISSING'));
return false;
}$this->persistent = $params->get('persistent', true);
$this->compress = $params->get('mcompress', false) == false ? 0 : Memcached::OPT_COMPRESSION;
$storage = $params->get('storage', null);
if (!isset($storage)) {
$this->loadLanguage();
throw new RuntimeException(JText::_('JOTCACHE_MEMCACHED_NO_SETTINGS'), 404);
}    if ($this->persistent) {
self::$db = new Memcached($this->hash);
self::$db->setOption(Memcached::OPT_REMOVE_FAILED_SERVERS, true);
$servers = self::$db->getServerList();
if ($servers && ($servers[0]['host'] != $storage->host || $servers[0]['port'] != $storage->port)) {
self::$db->resetServerList();
$servers = array();
}if (!$servers) {
self::$db->addServer($storage->host, $storage->port);
}} else {
self::$db = new Memcached();
}$stats = self::$db->getStats();
$this->connected = isset($stats[$storage->host . ":" . $storage->port]) and $stats[$storage->host . ":" . $storage->port]["pid"] > 0;
if (!$this->connected) {
$this->debug(JText::_('JOTCACHE_MEMCACHED_NO_CONNECT'), $storage->host . ":" . $storage->port);
return false;
}self::$db->setOption(Memcached::OPT_COMPRESSION, $this->compress);
    $hash = md5($this->hash);
    self::$lead = 'jotcache-' . substr(md5($this->hash), 0, 6) . '-';
    $this->connected = true;
return true;
}protected function loadLanguage() {
$lang = JFactory::getLanguage();
$lang->load('plg_system_jotcache', JPATH_ADMINISTRATOR, null, false, true);
}public function get() {
$data = self::$db->get(self::$lead . $this->fname);
return $data;
}public function store($data = null) {
if ($data) {
$cache_id = self::$lead . $this->fname;
if (!self::$db->replace($cache_id, $data, $this->lifetime)) {
self::$db->set($cache_id, $data, $this->lifetime);
}return true;
}return false;
}public function remove($path) {
return self::$db->delete(self::$lead . $this->fname);
}public function autoclean() {
}public function _getFilePath() {
return '';
}}