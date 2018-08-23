<?php
/**
 * Imagerecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@imagerecycle.com *
 * @package Imagerecycle
 * @copyright Copyright (C) 2014 ImageRecycle (http://www.imagerecycle.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die;
?>
<h4>You need support?</h4>
<p>
    Please read first the documentation available in the <a href="https://www.imagerecycle.com/cms/joomla"
                                                            target="_blank">download section</a><br/>
</p>

<h4>You still have a problem?</h4>
<p>
    You can contact our support team here : <a href="https://www.imagerecycle.com/my-account/support-ticket/"
                                               target="_blank">Support</a>
</p>

<h4>Please give use this informations when you open a new ticket :</h4>


<p><i>Joomla version : </i><?php echo imagerecycleBase::getJoomlaVersion(); ?></p>

<p>
    <i>Imagerecycle version : </i><?php echo imagerecycleBase::getExtensionVersion('com_imagerecycle'); ?><br/>
</p>

<p>
    <i>Php version : </i><?php echo phpversion(); ?><br/>
</p>

<h4>Save time in your ticket resolution</h4>
<p>
    We may need an admin access to your website, in order to save time you can create an admin access an give us the <i>username</i>,
    the <i>password</i> and the <i>site url</i><br/>
    Be as precise as possible in your explanations.<br/>
    You can add screenshots to your ticket, it always helps to understand your problem.
</p>
