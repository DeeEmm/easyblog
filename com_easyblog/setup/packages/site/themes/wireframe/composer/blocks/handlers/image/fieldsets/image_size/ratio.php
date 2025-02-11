<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$ratioList = array(
	array(
		'name'      => JText::_('COM_EASYBLOG_COMPOSER_RATIO_WIDE'),
		'caption'   => '16:9',
		'value'     => '16:9',
		'padding'   => '56.25%',
		'classname' => 'ar-wide'
	),
	array(
		'name'      => JText::_('35mm'),
		'caption'   => '3:2',
		'value'     => '3:2',
		'padding'   => '66.666667%',
		'classname' => 'ar-35mm'
	),
	array(
		'name'      => JText::_('COM_EASYBLOG_COMPOSER_RATIO_NORMAL'),
		'caption'   => '4:3',
		'value'     => '4:3',
		'padding'   => '75%',
		'classname' => 'ar-photo'
	),
	array(
		'name'      => JText::_('COM_EASYBLOG_COMPOSER_RATIO_SQUARE'),
		'caption'   => '1:1',
		'value'     => '1:1',
		'padding'   => '100%',
		'classname' => 'ar-square'
	),
	array(
		'name'      => JText::_('COM_EASYBLOG_COMPOSER_RATIO_ORIGINAL'),
		'caption'   => 'Original',
		'value'     => '0',
		'padding'   => '100%',
		'classname' => 'ar-original'
	),
	array(
		'name'      => JText::_('COM_EASYBLOG_COMPOSER_RATIO_UNLOCKED'),
		'caption'   => '<i class="fdi fa fa-unlock-alt"></i>',
		'value'     => '0',
		'padding'   => '100%',
		'classname' => 'ar-unlocked'
	)
);
?>

<div class="eb-composer-fieldgroup eb-image-ratio-field">
	<div class="eb-composer-fieldgroup-content">
		<div class="">
			<div class="t-lg-mb--lg">
				<?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_SELECT_ASPECT_RATIO'); ?>
			</div>
			<div class="eb-swatch swatch-grid eb-image-ratio-swatch">
				<div class="row">
					<?php foreach ($ratioList as $ratio) { ?>
					<div class="col-sm-3">
						<div class="eb-swatch-item eb-image-ratio-selection <?php echo $ratio['classname']; ?>" data-eb-image-ratio-selection data-value="<?php echo $ratio['value']; ?>">
							<div class="eb-swatch-preview is-responsive">
								<div><div>
									<div class="eb-image-ratio-preview <?php echo $ratio['classname']; ?>" data-eb-image-ratio-preview>
										<div style="padding-top: <?php echo $ratio['padding']; ?>">
											<div><span><?php echo $ratio['caption']; ?></span></div>
										</div>
									</div>
								</div></div>
							</div>
							<div class="eb-swatch-label">
								<span><?php echo $ratio['name']; ?></span>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
			<div class="eb-image-ratio-actions">
				<button type="button" class="btn btn-sm btn-primary" data-eb-image-ratio-customize-button>
					<span><?php echo JText::_('COM_EASYBLOG_COMPOSER_CUSTOMIZE_BUTTON'); ?></span>
				</button>
				<button type="button" class="btn btn-sm btn-default" data-eb-image-ratio-cancel-button>
					<span><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></span>
				</button>
			</div>
		</div>
	</div>
</div>

<div class="eb-composer-fieldgroup eb-image-ratio-custom-field">
	<div class="eb-composer-fieldgroup-content">
		<div class="">
			<div class="t-lg-mb--lg">
				<?php echo JText::_('COM_EASYBLOG_USE_CUSTOM_ASPECT_RATIO'); ?>
			</div>
			<input type="text" class="o-form-control eb-image-ratio-input" placeholder="16:9 or 1.77" data-eb-image-ratio-input/>
			<div class="eb-image-ratio-actions">
				<button type="button" class="btn btn-sm btn-primary" data-eb-image-ratio-use-custom-button>
					<span><?php echo JText::_('COM_EASYBLOG_USE_ASPECT_RATIO_BUTTON'); ?></span>
				</button>
				<button type="button" class="btn btn-sm btn-default" data-eb-image-ratio-cancel-custom-button>
					<span><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></span>
				</button>
			</div>
		</div>
	</div>
</div>
