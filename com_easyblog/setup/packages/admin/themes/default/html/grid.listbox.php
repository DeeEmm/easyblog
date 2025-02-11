<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-manage-tabs <?php echo $options['classes']; ?>" <?php echo $options['attributes']; ?>
    data-listbox
    data-listbox-sortable="<?php echo (int) $options['sortable']; ?>"
    data-listbox-toggleDefault="<?php echo (int) $options['toggleDefault']; ?>"
    data-listbox-allowAdd="<?php echo (int) $options['allowAdd']; ?>"
    data-listbox-allowRemove="<?php echo (int) $options['allowRemove']; ?>"
    data-listbox-itemTitle="<?php echo $options['itemTitle']; ?>"
    data-listbox-max="<?php echo $options['max']; ?>"
    data-listbox-min="<?php echo $options['min']; ?>"
>
    <?php $i = 0; ?>
    <?php foreach ($items as $item) { ?>
    <div class="eb-composer-manage-tab row-table <?php if ($i === $options['default']) { ?>is-default<?php } ?>" data-listbox-item>
        <div class="col-cell eb-composer-manage-tab-handler">
            <?php if ($options['sortable']) { ?>
            <i class="fdi fa fa-bars"></i>
            <?php } ?>
            <?php if ($options['toggleDefault']) { ?>
            <i class="fdi fa fa-star" data-listbox-button-default></i>
            <?php } ?>
        </div>
        
        <div class="col-cell eb-composer-manage-tab-name" data-listbox-item-content>
            <?php echo $item; ?>
        </div>

        <?php if ($options['allowRemove']) { ?>
        <div class="col-cell eb-composer-manage-tab-remove" data-listbox-button-remove>
            &times;
        </div>
        <?php } ?>
    </div>
    <?php } ?>

    <?php if ($options['allowAdd']) { ?>
    <div class="t-d--flex t-justify-content--fe t-align-items--c t-mt--md is-add" data-listbox-button-add-wrapper>
		<div data-listbox-button-add>
			<a href="javascript:void(0);" class="t-no-focus-outline">&plus; <?php echo $options['addTitle']; ?></a>
		</div>
	</div>
    <?php $i++; ?>
    <?php } ?>

    <div data-listbox-custom-html style="display:none"><?php echo $options['customHTML']; ?></div>
</div>
