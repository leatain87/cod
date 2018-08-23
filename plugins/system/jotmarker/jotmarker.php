<?php
/*
 * @version 6.0.2
 * @package JotCache
 * @category Joomla 3.7
 * @copyright (C) 2010-2017 Vladimir Kanich
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
class plgSystemJotmarker extends JPlugin {
protected static $rules;
public function onAfterInitialise() {
if (version_compare(JVERSION, '3.7', 'lt')) {
$app = JFactory::getApplication();
if ($app->isClient('administrator') || JDEBUG || $_SERVER['REQUEST_METHOD'] == 'POST') {
return;
}if (version_compare(JVERSION, '3.5', 'ge')) {
JLoader::register('JDocumentRendererHtmlModules', dirname(__FILE__) . '/modules.php', true);
} else {
JLoader::register('JDocumentRendererModules', dirname(__FILE__) . '/modules.php', true);
}}}public function onAfterDispatch() {
define('JOTCACHE_DISPATCH', 1);
}public static function onAfterRenderModules(&$buffer, &$params) {
if (!defined('JOTCACHE_DISPATCH')) {
return;
}$app = JFactory::getApplication();
if ($app->isClient('administrator') || JDEBUG || $_SERVER['REQUEST_METHOD'] == 'POST') {
return;
}$user = JFactory::getUser();
if (!$user->get('guest', false)) {
return;
}    if (empty(self::$rules)) {
$database = JFactory::getDBO();
$query = $database->getQuery(true);
$tpl_id = 1;
$query->select('value')
->from('#__jotcache_exclude')
->where($database->quoteName('type') . ' = 4')
->where($database->quoteName('name') . ' = ' . (int)$tpl_id);
$value = $database->setQuery($query)->loadResult();
self::$rules = unserialize($value);
}if (is_array(self::$rules) && is_array($params) && key_exists("name", $params) && key_exists($params["name"], self::$rules) && strlen($buffer) > 0) {
$prefix = '<jot ' . $params["name"] . ' s';
if (key_exists('style', $params)) {
$prefix .= ' style="' . $params["style"] . '"';
}if (count($params) > 2) {
foreach ($params as $key => $value) {
if ($key == 'name' || $key == 'style') {
continue;
} else {
$prefix .= ' ' . $key . '="' . $value . '"';
}}}$buffer = $prefix . '></jot>' . $buffer . '<jot ' . $params["name"] . ' e></jot>';
}}}