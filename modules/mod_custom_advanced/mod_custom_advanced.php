<?php
/**
 * @copyright	Copyright (C) 2011 JTricks.com.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$document = &JFactory::getDocument();

$moduleStyle = $params->get('moduleStyle');
if (strlen($moduleStyle) > 0)
    $attribs['style'] = $moduleStyle;

$cssOverride = $params->get('cssOverride');
if (strlen($cssOverride) > 0)
    $document->addStyleDeclaration($cssOverride);

$styleSheet = $params->get('styleSheet');
if (strlen($styleSheet) > 0)
    $document->addStyleSheet($styleSheet);

?>
<!-- BEGIN: Custom advanced (www.pluginaria.com) -->
<?php
$customHtml = $params->get('customHtml');
if (strlen($customHtml) > 0)
    echo $customHtml;
?>
<!-- END: Custom advanced (www.pluginaria.com) -->
