<?php

/**
 * ImageRecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package ImageRecycle
 * @copyright Copyright (C) 2014 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die;

$params = JComponentHelper::getParams('com_imagerecycle');
$selected_folders = explode(',', $params->get('include_folders', 'images,media,templates'));
?>
<div>
    <div class="pull-left">
        <div id="jao"></div>
    </div>
    <div class="pull-right">
        <button class="btn btn-primary" type="button"
                onclick="jSelectFolders()"><?php echo JText::_('JTOOLBAR_SAVE') ?></button>
        <button class="btn" type="button"
                onclick="window.parent.jModalClose();"><?php echo JText::_('JCANCEL') ?></button>
    </div>
</div>
<script>
    var curFolders = <?php echo json_encode($selected_folders);?>;

    jQuery(document).ready(function ($) {
        var sdir = '/';
        jSelectFolders = function () {

            var fchecked = [];
            curFolders.sort();
            for (i = 0; i < curFolders.length; i++) {
                curDir = curFolders[i];
                valid = true;
                for (j = 0; j < i; j++) {
                    if (curDir.indexOf(curFolders[j]) == 0) {
                        valid = false;
                    }
                }
                if (valid) {
                    fchecked.push(curDir);
                }
            }

            data = {};
            data.folders = fchecked.join(',');
            $.ajax({
                url: 'index.php?option=com_imagerecycle&task=connector.setFolders',
                type: "POST",
                data: data
            }).done(function (result) {
                window.parent.jModalClose();
            });
            window.parent.document.getElementById('jform_include_folders').value = fchecked.join(',');
            window.parent.document.getElementById('jform_include_folders_id').value = fchecked.join(',');
        }
        $('#jao').jaofiletree({
            script: 'index.php?option=com_imagerecycle&task=connector.listdir&tmpl=component',
            usecheckboxes: true,
            showroot: '/',
            oncheck: function (elem, checked, type, file) {
                var dir = file;
                if (file.substring(file.length - 1) == sdir) {
                    file = file.substring(0, file.length - 1);
                }
                if (file.substring(0, 1) == sdir) {
                    file = file.substring(1, file.length);
                }
                if (checked) {
                    if (file != "" && curFolders.indexOf(file) == -1) {
                        curFolders.push(file);
                    }
                } else {
                    if (file != "" && !$(elem).next().hasClass('pchecked')) {
                        temp = [];
                        for (i = 0; i < curFolders.length; i++) {
                            curDir = curFolders[i];
                            if (curDir.indexOf(file) !== 0) {
                                temp.push(curDir);
                            }
                        }
                        curFolders = temp;
                    } else {
                        var index = curFolders.indexOf(file);
                        if (index > -1) {
                            curFolders.splice(index, 1);
                        }
                    }

                }

            }
        });

        jQuery('#jao').bind('afteropen', function () {
            jQuery(jQuery('#jao').jaofiletree('getchecked')).each(function () {
                curDir = this.file;
                if (curDir.substring(curDir.length - 1) == sdir) {
                    curDir = curDir.substring(0, curDir.length - 1);
                }
                if (curDir.substring(0, 1) == sdir) {
                    curDir = curDir.substring(1, curDir.length);
                }
                if (curFolders.indexOf(curDir) == -1) {
                    curFolders.push(curDir);
                }
            })
            spanCheckInit();

        })

        spanCheckInit = function () {
            $("span.check").unbind('click');
            $("span.check").bind('click', function () {
                $(this).removeClass('pchecked');
                $(this).toggleClass('checked');
                if ($(this).hasClass('checked')) {
                    $(this).prev().prop('checked', true).trigger('change');
                    ;
                } else {
                    $(this).prev().prop('checked', false).trigger('change');
                    ;
                }
                setParentState(this);
                setChildrenState(this);
            });
        }

        setParentState = function (obj) {
            var liObj = $(obj).parent().parent(); //ul.jaofiletree
            var noCheck = 0, noUncheck = 0, totalEl = 0;
            liObj.find('li span.check').each(function () {

                if ($(this).hasClass('checked')) {
                    noCheck++;
                } else {
                    noUncheck++;
                }
                totalEl++;
            })

            if (totalEl == noCheck) {
                liObj.parent().children('span.check').removeClass('pchecked').addClass('checked');
                liObj.parent().children('input[type="checkbox"]').prop('checked', true).trigger('change');
            } else if (totalEl == noUncheck) {
                liObj.parent().children('span.check').removeClass('pchecked').removeClass('checked');
                liObj.parent().children('input[type="checkbox"]').prop('checked', false).trigger('change');
            } else {
                liObj.parent().children('span.check').removeClass('checked').addClass('pchecked');
                liObj.parent().children('input[type="checkbox"]').prop('checked', false).trigger('change');
            }

            if (liObj.parent().children('span.check').length > 0) {
                setParentState(liObj.parent().children('span.check'));
            }
        }

        setChildrenState = function (obj) {
            if ($(obj).hasClass('checked')) {
                $(obj).parent().find('li span.check').removeClass('pchecked').addClass("checked");
                $(obj).parent().find('li input[type="checkbox"]').prop('checked', true).trigger('change');
            } else {
                $(obj).parent().find('li span.check').removeClass("checked");
                $(obj).parent().find('li input[type="checkbox"]').prop('checked', false).trigger('change');
            }

        }
    })
</script>        