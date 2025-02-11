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
<?php if ($associates['teams'] || $associates['groups'] || $associates['events'] || $associates['pages']) { ?>
	<div class="eb-composer-pick-item"
		data-id="0"
		data-type="<?php echo EASYBLOG_POST_SOURCE_SITEWIDE;?>"
		data-associates-item
	>
		<div class="eb-radio">
			<input type="radio" name="radio-associates" id="radio-easyblog-sitewide-0" value="0" data-associates-checkbox <?php echo $source_id == 0 && $source_type == EASYBLOG_POST_SOURCE_SITEWIDE ? ' checked="checked"' : '';?>/>
			<label for="radio-easyblog-sitewide-0" class="row-table">
				<div class="col-cell" style="line-height: 20px;">
					<?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTHORSHIP_NONE'); ?>
				</div>
			</label>
		</div>
	</div>

	<?php if (isset($associates['teams']) && !empty($associates['teams'])) { ?>
		<p class="eb-composer-pick-label"><?php echo JText::_('COM_EASYBLOG_COMPOSER_TEAMS');?></p>

		<?php foreach ($associates['teams'] as $team) { ?>
			<?php echo $this->output('site/composer/panels/post/author/associates.list', ['associate' => $team]); ?>
		<?php } ?>
	<?php } ?>


	<?php if (isset($associates['groups']) && !empty($associates['groups'])) { ?>
	<p class="eb-composer-pick-label"><?php echo JText::_('COM_EASYBLOG_COMPOSER_GROUPS');?></p>

		<?php foreach ($associates['groups'] as $group) { ?>
			<?php echo $this->output('site/composer/panels/post/author/associates.list', ['associate' => $group]); ?>
		<?php } ?>
	<?php } ?>


	<?php if (isset($associates['events']) && !empty($associates['events'])) { ?>
	<p class="eb-composer-pick-label"><?php echo JText::_('COM_EASYBLOG_COMPOSER_EVENTS');?></p>

		<?php foreach ($associates['events'] as $event) { ?>
			<?php echo $this->output('site/composer/panels/post/author/associates.list', ['associate' => $event]); ?>
		<?php } ?>
	<?php } ?>

	<?php if (isset($associates['pages']) && !empty($associates['pages'])) { ?>
	<p class="eb-composer-pick-label"><?php echo JText::_('COM_EASYBLOG_COMPOSER_PAGES');?></p>

		<?php foreach ($associates['pages'] as $page) { ?>
			<?php echo $this->output('site/composer/panels/post/author/associates.list', ['associate' => $page]); ?>
		<?php } ?>
	<?php } ?>
<?php } else { ?>
	<div class="note-empty">
		<div class="row-table">
			<div class="col-cell"><?php echo JText::_('COM_EASYBLOG_COMPOSER_ASSOCIATES_EMPTY');?></div>
		</div>
	</div>
<?php } ?>
