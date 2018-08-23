<?php
$baseUrl = str_replace('administrator', '', JUri::root());
$n = count($this->items);

require_once JPATH_ADMINISTRATOR . '/components/com_imagerecycle/classes/irQueue.php';
$irQueue = new irQueue(false);
?>
<tbody>
<?php foreach ($this->items as $i => $item) : $alternate = ($i % 2 == 0) ? 'even' : ''; ?>
    <tr class="<?php echo $alternate ?>">
        <td class="check-column">
            <input id="cb<?php echo $i; ?>" class="ir-checkbox" name="cid[]" value="<?php echo $item['filename'] ?>"
                   onclick="Joomla.isChecked(this.checked);" type="checkbox">
        </td>
        <td class="item-image">
            <?php if ($item['filetype'] == 'pdf') { ?>
                <img class="image-small"
                     src="<?php echo $baseUrl; ?>/components/com_imagerecycle/assets/images/pdf.png"/>
            <?php } else { ?>
                <img class="image-small" src="<?php echo $baseUrl . $item['filename'] ?>"/>
                <img class="image-origin" src="<?php echo $baseUrl . $item['filename'] ?>"/>
            <?php } ?>
        </td>
        <td><?php echo $item['filename'] ?></td>
        <td><span class="filesize"><?php echo number_format($item['size'] / 1000, 2, '.', ''); ?></span></td>
        <td><span class="date"><?php echo JHTML::_('date', $item['date'], JText::_('DATE_FORMAT_LC3')); ?></span></td>
        <td class="ir-status">
            <?php
            if ($item['optimized']) {
                $sizeBefore = $item['optimized_datas']['size_before'];
                echo '<span class="optimizationStatus">' . sprintf(Jtext::_('COM_IMAGERECYCLE_CTR_IMAGE_OPTIMIZED_PERCENT'), round(($sizeBefore - $item['size']) / $sizeBefore * 100, 2)) . '</span>';
            } elseif ($item['optimized_datas']['status'] == "R") {
            ?>
            <span class="optimizationStatus"><?php echo JText::_('COM_IMAGERECYCLE_CTR_IMAGE_REVERTED'); ?> <span>
    <?php } ?>

        </td>

        <td>
            <?php
            if ($item['optimized']) {
                if (isset($item['optimized_datas']['expired']) && $item['optimized_datas']['expired']) {
                    //nothing
                } else {
                    echo '<a class="revert ir-action btn flat-button" href="#" data-image-realpath="' . $item['filename'] . '">' . JText::_('COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_ACTION_REVERT') . '</a>';
                }
            } else {
                if ($irQueue->isFilePresent($item['filename'])) { ?>
                    <a class="queued ir-action btn flat-button" href="#"
                       data-image-realpath="<?php echo $item['filename'] ?>"><?php echo JText::_('COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_ACTION_QUEUED'); ?></a>
                <?php } else { ?>
                    <a class="optimize ir-action btn flat-button" href="#"
                       data-image-realpath="<?php echo $item['filename'] ?>"><?php echo JText::_('COM_IMAGERECYCLE_VIEW_IMAGERECYCLE_ACTION_OPTIMZE'); ?></a>
                <?php }
            } ?>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>

<tfoot>
<tr>
    <td colspan="6" style="text-align: center;padding-top:20px;">
        <?php echo $this->pagination->getListFooter(); ?>
    </td>
</tr>
</tfoot>