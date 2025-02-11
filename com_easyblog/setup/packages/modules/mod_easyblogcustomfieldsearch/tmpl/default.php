<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="eb" class="eb-mod mod-items-compact mod_easyblogcustomfieldsearch<?php echo $modules->getWrapperClass();?>" data-eb-module-custom-field>

	<?php echo $params->get('description'); ?>

	<div class="mt-10">
		<?php if (in_array($params->get('buttonposition'), array('top', 'both'))) { ?>
			<?php require(JModuleHelper::getLayoutPath('mod_easyblogcustomfieldsearch', 'default_buttons')); ?>
		<?php } ?>

		<div class="mt-5 text-center hidden" data-filter-saved>
			<?php echo JText::_('MOD_EASYBLOG_FILTER_SAVED'); ?>
		</div>
	</div>

	<div class="eb-mod-head mod-table align-middle">
		<div class="mod-cell cell-tight">
			<div class="eb-mod-media-title mb-10 mt-10"><?php echo $group->getTitle(); ?></div>
		</div>
	</div>

	<form action="<?php echo JRoute::_('index.php');?>" method="post">
		<?php foreach ($fields as $field) { ?>
		<div class="mod-item">
			 <div class="mod-table cell-top">
				<div class="mod-cell cell-tight">
					<i class="eb-mod-media-thumb fdi fa fa-folder mod-muted mr-10"></i>
				</div>

				<div class="mod-cell">
					<div class="mod-table">
						<div class="mod-cell">
							<?php echo $field->title; ?>
						</div>

						<a class="mod-cell cell-tight mod-muted" data-eb-toggle="collapse" href="#eb-field-<?php echo $field->id; ?>">
							<i class="fdi fa eb-mod-chevron"></i>
						</a>
					</div>

					<div id="eb-field-<?php echo $field->id; ?>" class="in">
						<?php $i = 0; ?>
						<?php foreach ($field->options as $option) { ?>
							<?php if ($option->title && $option->value) { ?>
								<div class="mt-10">
									<div class="eb-checkbox<?php echo ($limit && $i >= $limit) ? ' hide' : '';?>" <?php echo ($limit && $i >= $limit) ? 'data-item-hide' : '';?>>
										<input type="checkbox" id="<?php echo $field->id . '-' . $option->value; ?>" name="field-<?php echo $field->id;?>[]" value="<?php echo EB::string()->escape($option->value);?>" <?php echo $option->checked ? 'checked' : ''; ?> data-checkbox-option/>
										<label for="<?php echo $field->id . '-' . $option->value;?>"><?php echo JText::_($option->title);?></label>
									</div>
								</div>
							<?php $i++; ?>
							<?php } ?>
						<?php } ?>
						<?php if ($limit && count($field->options) > $limit) { ?>
							<a href="javascript:void(0);" class="mod-small" data-show-all-options><?php echo JText::_('MOD_EASYBLOG_SHOW_ALL_OPTIONS'); ?> &raquo;</a>
							<a href="javascript:void(0);" class="mod-small hide" data-show-less-options><?php echo JText::_('MOD_EASYBLOG_SHOW_LESS_OPTIONS'); ?> &raquo;</a>
						<?php } ?>
					</div>
				</div>
			 </div>
		</div>
		<?php } ?>

		<div>
			<input type="submit" value="<?php echo JText::_('COM_EASYBLOG_BLOGS_FILTER');?>"  class="mod-btn mod-btn-block mod-btn-primary mt-20<?php echo $submitOnClick ? ' hide' : ''; ?>" data-submit/>
		</div>

		<input type="hidden" name="option" value="com_easyblog" />
		<input type="hidden" name="view" value="<?php echo $view;?>">
		<input type="hidden" name="layout" value="<?php echo $layout;?>">
		<input type="hidden" name="filtermode" value="<?php echo $filterMode;?>">
		<input type="hidden" name="strictmode" value="<?php echo $strictMode;?>">
		<input type="hidden" name="task" value="posts.filterField" />

		<?php if ($catid) { ?>
			<input type="hidden" name="id" value="<?php echo $catid;?>">
		<?php } ?>

		<?php if ($catinclusion) { ?>
			<input type="hidden" name="inclusion" value="<?php echo $catinclusion;?>">
		<?php } ?>

		<?php echo JHTML::_('form.token'); ?>
	</form>

	<div class="mt-20">
		<?php if (in_array($params->get('buttonposition'), array('bottom', 'both'))) { ?>
			<?php require(JModuleHelper::getLayoutPath('mod_easyblogcustomfieldsearch', 'default_buttons')); ?>
		<?php } ?>

		<div class="mt-5 text-center hidden" data-filter-saved>
			<?php echo JText::_('MOD_EASYBLOG_FILTER_SAVED'); ?>
		</div>
	</div>
</div>
<?php include_once(__DIR__ . '/default_scripts.php'); ?>
