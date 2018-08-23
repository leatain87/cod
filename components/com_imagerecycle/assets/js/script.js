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

/* Image Recycle */
jQuery.noConflict();
jQuery(document).ready(function ($) {

    initButtons = function () {

        //Optimize image via ajax
        $('.optimize.ir-action').unbind('click').bind('click', function (e) {

            e.preventDefault();
            var $this = $(e.target);
            var image = $this.data('image-realpath');
            if (!image || $this.hasClass('disabled')) {
                return false;
            }

            $.ajax({
                url: optimize_url,
                data: {image: image},
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    $this.addClass('disabled');
                    var $status = $this.closest('tr').find('.ir-status');
                    $status.html($("#ir-loader").html());
                },
                success: function (response) {

                    var $status = $this.closest('tr').find('.ir-status');
                    if (response.status === true) {
                        $status.addClass('msg-success');
                        $status.empty().text(response.datas.msg);
                        if (response.datas.newSize) {
                            $this.closest('tr').find('.filesize').empty().text(response.datas.newSize);
                        }
                        $this.removeClass('disabled optimize').addClass('revert').text('Revert to original');
                        initButtons();
                    }
                    else {
                        $status.addClass('msg-error');
                        $status.empty().text(response.datas.msg);
                        setTimeout(function () {
                            $this.removeClass('disabled');
                            $status.empty();
                        }, 5000);
                    }


                }
            });
        });

        $('.revert.ir-action').unbind('click').bind('click', function (e) {
            e.preventDefault();

            var $this = $(e.target);
            var image = $this.data('image-realpath');
            if (!image || $this.hasClass('disabled')) {
                return false;
            }

            $.ajax({
                url: revert_url,
                data: {image: image},
                //async : false,
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    $this.addClass('disabled');
                    var $status = $this.closest('tr').find('.ir-status');
                    $status.html($("#ir-loader").html());
                },
                success: function (response) {

                    var $status = $this.closest('tr').find('.ir-status');
                    if (response.status === true) {
                        $status.addClass('msg-success');
                        $status.empty().text(response.datas.msg);
                        if (response.datas.newSize) {
                            $this.closest('tr').find('.filesize').empty().text(response.datas.newSize);
                        }
                        $this.removeClass('disabled revert').addClass('optimize').text('Optimize');
                        initButtons();
                    }
                    else {
                        $status.addClass('msg-error');
                        $status.empty().text(response.datas.msg);
                        setTimeout(function () {
                            $this.removeClass('disabled');
                            $status.empty();
                        }, 5000);
                    }

                }
            });

        });

        //unqueue
        $('.queued.ir-action').unbind('click').bind('click', function (e) {
            e.preventDefault();

            var $this = $(e.target);
            var image = $this.data('image-realpath');
            if (!image || $this.hasClass('disabled')) {
                return false;
            }

            $.ajax({
                url: unqueue_url,
                data: {image: image},
                //async : false,
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    $this.addClass('disabled');
                    var $status = $this.closest('tr').find('.ir-status');
                    $status.html($("#ir-loader").html());
                },
                success: function (response) {

                    var $status = $this.closest('tr').find('.ir-status');
                    if (response.status === true) {
                        $status.addClass('msg-success');
                        $status.empty();
                        $this.removeClass('disabled queued').addClass('optimize').text('Optimize');
                        initButtons();
                    }
                    else {
                        $status.addClass('msg-error');
                        $status.empty().text(response.datas.msg);
                        setTimeout(function () {
                            $this.removeClass('disabled');
                            $status.empty();
                        }, 5000);
                    }

                }
            });
        })
    };
    initButtons();

    // Do bulk action
    $('.do-bulk-action').bind('click', function (e) {
        e.preventDefault();
        if ($('.ir-bulk-action').val() == '-1') {

            var ir_bulk_action = $('.ir-bulk-action');
            var border_color = '#DDD';
            ir_bulk_action.css('border-color', '#FF3300');
            setTimeout(function () {
                ir_bulk_action.css('border-color', border_color);
            }, 300);
        }
        else {

            if ($('.ir-checkbox:checked').length < 1) {
                alert("No image selected");
            } else {
                if ($('.ir-bulk-action').val() == 'optimize_selected') {
                    $('.ir-checkbox:checked').each(function (i) {
                        $(this).parents('tr').find('.ir-action.optimize').click();
                    });
                } else { //revert selected
                    $('.ir-checkbox:checked').each(function (i) {
                        $(this).parents('tr').find('.ir-action.revert').click();
                    });
                }
            }
        }
    });

    initOAButtons = function () {
        $('#dooptimizeall').unbind("click").click(function () {
            startOpimizeAll();
        })

        $('#stopoptimizeall').unbind("click").click(function () {
            stopOptimizeAll();
        });
    }
    initOAButtons();

    function stopOptimizeAll() {
        $.ajax({
            url: stop_optimizeall_url,
            data: {},
            type: 'post',
            dataType: 'json',
            success: function (response) {
                $("#stopoptimizeall").attr("id", "dooptimizeall").addClass("btn-success button-primary").prop('value', 'Optimize all');
                initOAButtons();
                clearTimeout(checkTimeRemainTm);
                //$("#dooptimizeall").after("Stopped");
                $("#progress_status").remove();
                window.location.href = window.location.href;
            }
        })
    }

    startOpimizeAll = function () {
        $("#dooptimizeall").attr("id", "stopoptimizeall").removeClass("btn-success button-primary").prop('value', 'Stop optimization');
        initOAButtons();

        $.ajax({
            url: start_optimizeall_url,
            data: {
                mode: "manual"
            },
            type: 'post',
            dataType: 'json',
            success: function (response) {
                window.location.href = window.location.href;
            }
        })
    }

    var checkTimeRemainTm;
    optimizeAll = function () {
        //init progress bar
        irBox = $("#progress_status");
        if (irBox.length === 0) {
            $("#dooptimizeall").after('<div id="progress_status" style="width: 300px; position: relative;float:left;"><section class="progress_wraper">' + $("#progress_init").html() + '</section></div>');
            $("#stopoptimizeall").after('<div id="progress_status" style="width: 300px; position: relative;float:left;"><section class="progress_wraper">' + $("#progress_init").html() + '</section></div>');

            irBox = $("#progress_status");

            $('progress').each(function () {
                var max = $(this).val();
                $(this).val(0).animate({value: max}, {duration: 2000});
            });
        }

        $.ajax({
            url: queue_count_url,
            data: {},
            type: 'post',
            dataType: 'json',
            success: function (response) {
                if (typeof (response.status) !== 'undefined' && response.status === true) {
                    if (response.datas.continue === true) {
                        totalOptimizedImages = 0;

                        if (typeof(response.datas.remainFiles) != 'undefined') {
                            curTime = parseInt(response.datas.curTime);
                            remainFiles = parseInt(response.datas.remainFiles);
                            totalOptimizedImages = totalImagesProcessing - remainFiles;
                            if (totalOptimizedImages > 0) {
                                remainTime = (curTime - startTime) / totalOptimizedImages * remainFiles;
                                remainTimeStr = toHHMMSS(Math.floor(remainTime));
                                $("#progress_status .progress_wraper .timeRemain").fadeOut();
                                $('#progress_status .progress_wraper .timeRemain').html(remainTimeStr + ' before finished');
                                $("#progress_status .progress_wraper .timeRemain").fadeIn();
                            }
                        }
                        $('#progress_status span').html('Processing ...' + totalOptimizedImages + ' / ' + totalImagesProcessing + ' images');

                        var percent = (totalOptimizedImages / totalImagesProcessing) * 100;
                        var oldVal = $('#progress_status progress').val();
                        $('#progress_status progress').val(percent);
                        $('#progress_status progress').val(oldVal).animate({value: percent}, {duration: 500});

                        checkTimeRemainTm = setTimeout(function () {
                            optimizeAll()
                        }, 3000);

                    } else {
                        window.location.href = window.location.href;
                    }
                } else {
                    if (typeof (response.datas) !== 'undefined') {
                        //alert(response.datas.errMsg);
                        $('#progress_status span').html("An error occurred: " + response.datas.errMsg).css('color', '#FF3300');
                        setTimeout(function () {
                            window.location.href = window.location.href;
                        }, 3000);
                    }
                }
            },
            error: function (xhr, txtStatus, thrownError) {
                if (thrownError != "") {
                    alert("An error occurred: " + thrownError);
                }
            }
        })
    }

    //if background process is running
    if ($("#stopoptimizeall").length > 0) {
        optimizeAll();
    }

    toHHMMSS = function (sec_num) {
        var hours = Math.floor(sec_num / 3600);
        var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
        var seconds = sec_num - (hours * 3600) - (minutes * 60);

        if (minutes < 10) {
            minutes = "0" + minutes;
        }
        if (seconds < 10) {
            seconds = "0" + seconds;
        }
        var time = '';
        if (hours == 0) {
            time = minutes + 'm ' + seconds;
        } else {
            if (hours < 10) {
                hours = "0" + hours;
            }
            time = hours + 'h ' + minutes + 'm ' + seconds + 's';
        }

        return time;
    }
});