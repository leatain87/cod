<?php
@set_time_limit(0);
if (defined('IR_PROCESSING_QUEUE')) {
    die();
}
define('IR_PROCESSING_QUEUE', true);

if (ob_get_length() > 0) {
    ob_end_clean();
}
header("Connection: close");
ignore_user_abort(true); // just to be safe
ob_start();
//echo('connected');
$size = ob_get_length();
header("Content-Length: $size");
ob_end_flush(); // Strange behaviour, will not work
flush(); // Unless both are called !

// Do processing here 
sleep(1);
if (!defined('_JEXEC')) {
    /** Set up Joomla environment */

    define('_JEXEC', 1); //  This will allow to access file outside of joomla.
    //defined( '_JEXEC')  or die( 'Restricted access' );// Use this if you wanna access file only in Joomla.
    define('DS', DIRECTORY_SEPARATOR);
    define('JPATH_BASE', realpath(dirname(dirname(dirname(__FILE__))) . '/'));
    require_once(JPATH_BASE . DS . 'includes' . DS . 'defines.php');
    require_once(JPATH_BASE . DS . 'includes' . DS . 'framework.php');
}

// Register helper class
JLoader::register('ImagerecycleHelper', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/helpers/imagerecycle.php');
// Register helper class
JLoader::register('ImagerecycleComponentHelper', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/helpers/component.php');

$helper = new ImagerecycleHelper();
$helper->optimizeInQueue();

die();
?>