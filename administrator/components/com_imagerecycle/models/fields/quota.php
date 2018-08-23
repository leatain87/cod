<?php
/**
 * Dropfiles
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Dropfiles
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die;

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 */
class JFormFieldQuota extends JFormField
{

    protected $type = 'Quota';

    /**
     */
    protected function getInput()
    {
        $componentParams = JComponentHelper::getParams('com_imagerecycle');
        if ($componentParams->get('api_key') == '' || $componentParams->get('api_secret') == '') {
            return '<style>#jform_quota_consumption-lbl {display:none}</style>';
        }

        include_once(JPATH_ADMINISTRATOR . '/components/com_imagerecycle/classes/ioa.class.php');
        $ioa = new ioaphp($componentParams->get('api_key'), $componentParams->get('api_secret'));
        $return = $ioa->getAccountInfos();
        $percentQuota = 0;
        $consumption_text = "";
        if ($return && (floatval($return->quota_allowed) > 0)) {
            $consumption_text = JText::_('COM_IMAGERECYCLE_CONFIG_CONSUMMATED_QUOTA') . " " . date('d F Y', $return->quota_start) . " " .
                JText::_('COM_IMAGERECYCLE_CONFIG_TO') . " " . date('d F Y', $return->quota_end) . ": " .
                "<b>" . $this->formatBytes(floatval($return->quota_current)) . " / " .
                $this->formatBytes(floatval($return->quota_allowed)) . "</b>";
            $percentQuota = number_format(min(($return->quota_current / $return->quota_allowed), 1) * 100, 2);
        } else if ($return) {
            $consumption_text = JText::_('COM_IMAGERECYCLE_CONFIG_CONSUMMATED_QUOTA') . " " . date('d F Y', $return->quota_start) . " " .
                JText::_('COM_IMAGERECYCLE_CONFIG_TO') . " " . date('d F Y', $return->quota_end) . ": " .
                "<b>" . $this->formatBytes(floatval($return->quota_current)) . "</b>";
        }
        ?>

        <?php if ($percentQuota > 0) { ?>
        <?php echo $consumption_text; ?>
        <div style="float: left;width:100%;clear:both">

            <div class="ir-progress-wrap" style="float: left">
                <div class="ir-progress-bar" style="width: <?php echo $percentQuota; ?>%;min-width:0.5%"></div>
                <span><?php echo $percentQuota; ?>%</span>
            </div>

            <div style="float: left;margin:10px 10px 10px 20px">
                <a href="https://www.imagerecycle.com/prices" target="_blank" id="getQuota"
                   class="btn btn-success"><?php echo JText::_('COM_IMAGERECYCLE_CONFIG_GET_QUOTA_USAGE'); ?></a>
            </div>
        </div>
    <?php } else { //unlimited quota ?>
        <span style="line-height: 25px;"><?php echo $consumption_text; ?></span>
        <a href="https://www.imagerecycle.com/prices" target="_blank" id="getQuota"
           class="btn btn-success"><?php echo JText::_('COM_IMAGERECYCLE_CONFIG_GET_QUOTA_USAGE'); ?></a>
    <?php } ?>

        <style>

            .ir-progress-wrap {
                display: block !important;
                width: 50%;
                max-width: 100%;
                background: #8ac0fd;
                line-height: 1 !important;
                margin: 10px 0 15px 0;
                position: relative !important;
                box-shadow: 0px 0px 5px 1px rgba(0, 0, 0, 0.03) inset !important;
                -moz-box-shadow: 0px 0px 5px 1px rgba(0, 0, 0, 0.03) inset !important;
                -webkit-box-shadow: 0px 0px 5px 1px rgba(0, 0, 0, 0.03) inset !important;
            }

            .ir-progress-bar {
                width: 0%;
                display: block !important;
                background: #308ffa;
                height: 30px !important;
                box-sizing: border-box !important;
                -webkit-box-sizing: border-box !important;
                -moz-box-sizing: border-box !important;
            }

            .ir-progress-wrap span {
                position: absolute !important;
                left: 10px !important;
                top: 10px !important;
                font-size: 12px !important;
                color: #fff;
                line-height: 1 !important;
            }

            #getQuota.btn.btn-success {
                vertical-align: top;
                height: 26px;
                line-height: 26px;
                padding: 0px 10px;
                color: #FFF;
                text-align: center;
                border-radius: 2px;
                text-shadow: 0px 1px 2px rgba(0, 0, 0, 0.25);
                background: none repeat scroll 0% 0% #3498DB;
                background-color: #3498DB;
                border-width: 0px 0px 2px;
                border-style: none none solid;
                border-color: #3498DB;
                border-image: none;
                cursor: pointer;
            }

            #getQuota.btn.btn-success:hover {
                color: #FFF;
                background: none repeat scroll 0% 0% #3498DB;
                text-decoration: none;
                box-shadow: 1px 1px 12px #ccc;
            }
        </style>
        <?php
        return '';

    }

    function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        //$bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

}
