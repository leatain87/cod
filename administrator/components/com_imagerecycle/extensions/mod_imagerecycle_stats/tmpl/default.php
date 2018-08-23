<?php
/**
 * ImageRecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@imagerecycle.com *
 * @package ImageRecycle
 * @copyright Copyright (C) 2014 ImageRecycle (http://www.imagerecycle.com). All rights reserved.
 * @copyright Copyright (C) 2014 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die;
?>
<ul class="list-striped list-condensed ir-stats-module<?php echo $moduleclass_sfx ?>">
    <?php foreach ($list as $item) : ?>
        <li><span class="title" title="<?php echo $item->title; ?>"><?php echo $item->title; ?></span>
            <span class="data"><?php echo $item->data; ?></span></li>
    <?php endforeach; ?>
</ul>

<style>
    .ir-stats-module li span.title {
        width: 250px;
        display: inline-block;
    }
</style>    
