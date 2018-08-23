<?php
/*
 * @version 6.0.2
 * @package JotCache
 * @category Joomla 3.7
 * @copyright (C) 2010-2017 Vladimir Kanich
 * @license	GNU General Public License version 2
 */
defined('JPATH_BASE') or die;
class JotcacheMemcached {
protected static $db = null;
protected $persistent = true;
protected $compress = 0;
protected static $lead = '';
public function __construct($pars) {
if (self::$db === null) {
$this->getConnection($pars);
}}protected function getConnection($pars) {
if ((extension_loaded('memcached') && class_exists('Memcached')) != true) {
return false;
}$this->persistent = $pars->storage->persistent == 1 ? true : false;
$this->compress = $pars->storage->mcompress == 1 ? Memcached::OPT_COMPRESSION : 0;
if (!isset($pars->storage->host)) {
throw new RuntimeException(JText::_('JOTCACHE_MEMCACHED_NO_SETTINGS'), 404);
}if ($this->persistent) {
$session = JFactory::getSession();
self::$db = new Memcached($session->getId());
} else {
self::$db = new Memcached;
}$memcachedtest = self::$db->addServer($pars->storage->host, $pars->storage->port);
if ($memcachedtest == false) {
$msg = sprintf(JText::_('JOTCACHE_MEMCACHED_NO_CONNECT'), $pars->storage->host, $pars->storage->port);
throw new RuntimeException($msg, 404);
}self::$db->setOption(Memcached::OPT_COMPRESSION, $this->compress);
$config = JFactory::getConfig();
$hash = md5($config->get('secret'));
self::$lead = 'jotcache-' . substr($hash, 0, 6) . '-';
return true;
}public function get($fname) {
$data = self::$db->get(self::$lead . $fname);
if ($data) {
$data = preg_replace('/^.*\n/', '', $data);
}return $data;
}public function getAllKeys() {
$list = array();
    $keys = self::$db->getAllKeys();
foreach ($keys AS $key) {
if (strlen($key) == 48) {
if (substr($key, 0, 16) == self::$lead) {
$list[] = substr($key, 16);
}}}return $list;
}public function remove($key) {
return self::$db->delete(self::$lead . $key);
}public function autoclean() {
}}