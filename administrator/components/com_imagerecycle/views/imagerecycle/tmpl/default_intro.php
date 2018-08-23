<?php
/**
 * ImageRecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@imagerecycle.com *
 * @package ImageRecycle
 * @copyright Copyright (C) 2014 ImageRecycle (http://www.imagerecycle.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die;

$adminUser = JFactory::getUser();
$adminName = urlencode($adminUser->get('username'));
$adminMail = urlencode($adminUser->get('email'));

$urlImagerecycle = "https://www.imagerecycle.com";
$urlToList = 'index.php?option=com_imagerecycle&iomess=accountCreated';// admin_url("upload.php?page=wp-image-recycle-page&iomess=accountCreated");
$urlToCreateAccount = "https://www.imagerecycle.com/index.php?option=com_ioa&view=register&adminName={$adminName}&adminMail={$adminMail}&TB_iframe=true&width=600&height=550";
$urlToLoginAccount = "https://www.imagerecycle.com/index.php?option=com_ioa&view=register&layout=connect&TB_iframe=true&width=600&height=550";

JHTML::_('behavior.modal', 'a.modal-button');
?>
<div class="main-presentation"
     style="margin: 30px auto; max-width: 1200px; background-color:#f0f1f4;font-family: helvetica,arial,sans-serif;">
    <div class="main-textcontent"
         style="margin: 0px auto; min-height: 300px; border-left: 1px dotted #d2d3d5; border-right: 1px dotted #d2d3d5; width: 840px; background-color:#fff;border-top: 5px solid #544766;"
         cellspacing="0" cellpadding="0" align="center">
        <a href="https://www.imagerecycle.com/" target="_blank"> <img
                src="https://www.imagerecycle.com/images/Notification-mail/logo-image-recycle.png"
                alt="logo image recycle" width="500" height="84" class="CToWUd"
                style="display: block; outline: medium none; text-decoration: none; margin-left: auto; margin-right: auto; margin-top:15px;">
        </a>
        <p style="background-color: #ffffff; color: #445566; font-family: helvetica,arial,sans-serif; font-size: 24px; line-height: 24px; padding-right: 10px; padding-left: 10px;"
           align="center"><strong>Welcome on board!<br></strong></p>
        <p style="background-color: #ffffff; color: #445566; font-family: helvetica,arial,sans-serif; font-size: 14px; line-height: 22px; padding-left: 20px; padding-right: 20px; text-align: center;">
            ImageRecycle will help you to optimize automatically your website images &amp; PDF<br>In order to start the
            optimization process, you can create in one click a 15 days free trial account<br>to use our external hosted
            webservice.<br>This way, it will require less resources to your server.<br/>You can also use existing
            credentials. Enjoy!</p>
        <p></p>
        <p>
            <a style="width: 250px; float: left; background: #554766; font-size: 12px; line-height: 18px; text-align: center;  margin-left:20px;color: #fff;font-size: 14px;text-decoration: none; text-transform: uppercase; padding: 8px 20px; font-weight:bold;"
               href="<?php echo $urlToLoginAccount; ?>" class="modal-button"
               rel="{handler: 'iframe', size: {x: 600, y: 500}}">Use existing account</a></p>
        <p>
            <a style="width: 250px; float: right; background: #554766; font-size: 12px; line-height: 18px; text-align: center;  margin-right:20px;color: #fff;font-size: 14px;text-decoration: none; text-transform: uppercase; padding: 8px 20px; font-weight:bold;"
               href="<?php echo $urlToCreateAccount; ?>" class="modal-button"
               rel="{handler: 'iframe', size: {x: 600, y: 500}}">Create a trial account</a></p>
        <p style="background-color: #ffffff; color: #445566; font-family: helvetica,arial,sans-serif; font-size: 24px; line-height: 24px; padding-right: 10px; padding-left: 10px; padding-top: 40px;"
           align="center"><strong><br>Why ImageRecycle?<br></strong></p>
        <p style="background-color: #ffffff; color: #445566; font-family: helvetica,arial,sans-serif; font-size: 14px; line-height: 22px; padding-left: 20px; padding-right: 20px; text-align: center;">
            Images represent 60% to 70% of website page weight. Image optimization is good for your users and for your
            server.<br> You won't find any other service that compress in the same time .pdf .jpeg and .png images
            keeping the original quality. We are using a unique algorithm to achieve that. Each compression script is
            executed on an optimized server to serve you as fast as possible.</p>
        <p style="font-size: 130%;">
            <a href="https://www.imagerecycle.com/plans/free-trial-plan?group_id=0" target="_blank">Free trial</a> -
            <a href="https://www.imagerecycle.com/prices" target="_blank">Prices</a> -
            <a href="https://www.imagerecycle.com/uploader" target="_blank">Uploader</a>
        </p>
        <p style="text-align: center; color: #9da2a8; font-size: 11px; line-height: 12px; padding: 15px;"><br> This is a
            welcome screen to help you with ImageRecycle configuration. Once you have registered your API key this
            message will no longer be displayed.<br><br><a href="https://www.facebook.com/imagerecycle" target="_blank"><img
                    src="https://www.imagerecycle.com/images/Notification-mail/facebook.png" alt="facebook" width="24"
                    height="24" class="CToWUd"></a> &nbsp; <a href="https://twitter.com/ImageRecycle" target="_blank"
                                                              style="margin: 0 5px;"><img
                    src="https://www.imagerecycle.com/images/Notification-mail/twitter.png" alt="twitter" width="24"
                    height="24" class="CToWUd"></a> &nbsp; <a href="https://plus.google.com/104235881525310825230/posts"
                                                              target="_blank"><img
                    src="https://www.imagerecycle.com/images/Notification-mail/google.png" alt="google" width="24"
                    height="24" class="CToWUd"></a></p>
    </div>
</div>
<!--Configuration form-->
<form style="display:none" action="<?php echo JRoute::_('index.php?option=com_imagerecycle'); ?>" method="post"
      name="configForm" id="configForm">
    <div class="row-fluid">
        <div class="span3"><?php //echo JText::_('COM_IMAGERECYCLE_CONFIG_VIEW_CONFIG_LABEL');?></div>
        <div class="span3">
            <div class="control-group">
                <div class="control-label">
                    <label id="jform_api_key-lbl" for="jform_api_key" class="">
                        <?php echo JText::_('COM_IMAGERECYCLE_CONFIG_API_KEY_LABEL'); ?></label>
                </div>
                <div class="controls">
                    <input name="jform[api_key]" id="jform_api_key" value="" class="input-xlarge" size="100"
                           type="text">
                </div>
            </div>
        </div>
        <div class="span3">
            <div class="control-group">
                <div class="control-label">
                    <label id="jform_api_secret-lbl" for="jform_api_secret" class="">
                        <?php echo JText::_('COM_IMAGERECYCLE_CONFIG_API_SECRET_LABEL'); ?></label>
                </div>
                <div class="controls">
                    <input name="jform[api_secret]" id="jform_api_secret" value="" class="input-xlarge" size="100"
                           type="text">
                </div>
            </div>
        </div>
        <div class="span3">
            <div class="control-group">
                <div class="control-label">
                    <label id="jform_api_secret-lbl" for="jform_api_secret" class="">
                        <br/>
                    </label>
                </div>
                <div class="controls">
                    <input type="button" class="btn btn-success flat-button" name="btnSave" id="btnSaveConfig"
                           value="<?php echo JText::_('JSUBMIT'); ?>"/>
                </div>
            </div>
        </div>
    </div>
</form>

<div id="errorMsg" class="alert alert-error" style="display: none"></div>
<script>

    window.addEventListener("message",
        function (e) {
            if (e.origin !== "<?=$urlImagerecycle ?>") {
                return;
            }
            var accountData = JSON.parse(e.data);
            jQuery.ajax({
                url: 'index.php?option=com_imagerecycle&task=image.saveConfig',
                data: {api_key: accountData.key, api_secret: accountData.secret},
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (response) {
                    if (response.status === true) {
                        window.location = '<?=$urlToList ?>';
                    }
                    else {
                        alert("Error, try again");
                    }
                }
            });

        },
        false);

    jQuery(document).ready(function ($) {
        $("#btnSaveConfig").click(function (e) {
            e.preventDefault();
            var api_key = $("#jform_api_key").val();
            var api_secret = $("#jform_api_secret").val();
            console.log(api_key, api_secret, "clicked");
            if (api_key == "" || api_secret == "") {
                $("#errorMsg").html("<p>Invalid api key or secret</p>").show();
                return false;
            }

            $.ajax({
                url: 'index.php?option=com_imagerecycle&task=image.saveConfig',
                data: {api_key: api_key, api_secret: api_secret},
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (response) {
                    if (response.status === true) {
                        $("#errorMsg").html("<?php echo JText::_("COM_IMAGERECYCLE_CONFIG_SAVE_SUCCESS");?>").show();
                        $("#errorMsg").removeClass('alert-error').addClass('alert-success');
                        $(".main-presentation").fadeOut();
                        $("#configForm").fadeOut();
                    }
                    else {
                        $("#errorMsg").removeClass('alert-success').addClass('alert-error');
                        $("#errorMsg").empty().html("Save fail");
                        setTimeout(function () {

                        }, 5000);
                    }

                }
            });
        })
    });
</script>    
