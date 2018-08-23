<?php
/**
 * Imagerecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@imagerecycle.com *
 * @package Imagerecycle
 * @copyright Copyright (C) 2012 ImageRecycle (http://www.imagerecycle.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.form.formrule');

/**
 * Form Rule class for the Joomla Framework.
 *
 * @package        Joomla.Framework
 * @since          1.6
 */
class JFormRuleInteger extends JFormRule
{
    /**
     * Method to test the username for uniqueness.
     *
     * @param    object $element The JXMLElement object representing the <field /> tag for the
     *                                 form field object.
     * @param    mixed $value The form field value to validate.
     * @param    string $group The field name group control value. This acts as as an array
     *                                 container for the field. For example if the field has name="foo"
     *                                 and the group value is set to "bar" then the full field name
     *                                 would end up being "bar[foo]".
     * @param    object $input An optional JRegistry object with the entire data set to validate
     *                                 against the entire form.
     * @param    object $form The form object for which the field is being tested.
     *
     * @return   boolean               True if the value is valid, false otherwise.
     * @since    1.6
     * @throws   JException on invalid rule.
     */
    public function test(& $element, $value, $group = null, & $input = null, & $form = null)
    {
        if ($value == "" || $value == null || $this->is_integer($value)) {
            return true;
        }
        return false;
    }

    private function is_integer($v)
    {
        $i = intval($v);
        if ("$i" == "$v") {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}