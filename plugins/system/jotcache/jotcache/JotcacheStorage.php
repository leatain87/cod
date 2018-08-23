<?php
/*
 * @version 6.0.2
 * @package JotCache
 * @category Joomla 3.7
 * @copyright (C) 2010-2017 Vladimir Kanich
 * @license	GNU General Public License version 2
 */
defined('JPATH_BASE') or die;
class JotcacheStorage {
public $fname;
public $options;
public $params;
protected $id;
protected $application;
protected $hash;
protected $language;
protected $root;
protected $now;
protected $lifetime;
protected $group;
protected $locking;
public function __construct($params, $options = array()) {
    $this->options = $options;
$this->params = $params;
$config = JFactory::getConfig();
    $this->root = $config->get('cache_path', JPATH_ROOT . '/cache');
    $this->language = $config->get('language', 'en-GB');
$this->options['language'] = $this->language;
    $this->hash = $config->get('secret');
$this->group = $options['defaultgroup'];
$this->locking = (isset($options['locking'])) ? $options['locking'] : true;
$this->lifetime = (isset($options['lifetime'])) ? $options['lifetime'] : null;
$this->now = (isset($options['now'])) ? $options['now'] : time();
if (empty($this->lifetime)) {
$this->lifetime = 60;
}$this->id = md5($options['uri'] . '-' . $options['browser'] . $options['cookies'].$options['sessionvars']);
    $this->fname = md5($this->application . '-' . $this->id . '-' . $this->hash . '-' . $this->language);
}public function setLifeTime($lt) {
$this->lifetime = $lt;
}public function debug($message, $value = '', $level = 2) {
if ($this->params->get('cachedebug', '0') >= $level) {
if (is_null($value)) {
$value = 'null';
} elseif (is_bool($value)) {
$value = $value ? 'true' : 'false';
}if ($value) {
$message .= " [$value]";
}JLog::add($message, JLog::DEBUG, 'jotcache_debug');
}}}