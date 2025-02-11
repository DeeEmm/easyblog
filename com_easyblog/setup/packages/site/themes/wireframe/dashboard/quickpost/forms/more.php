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

$readonly = ($this->acl->get('create_tag')) ? '' : 'data-eb-composer-tags-readonly';
$maxTags  = $this->config->get('max_tags_allowed');
?>
<div data-quickpost-extended>
	<div class="eb-quick-text-more t-hidden" data-quickpost-extended-panel>
		<div class="form-group">
			<div class="col-md-6">
				<?php echo $this->html('form.category', 'category_id', 'category_id', $defaultCategory, ' data-quickpost-category'); ?>
			</div>
			<div class="col-md-6">
				<?php if ($this->acl->get('enable_privacy')) { ?>
					<?php echo $this->fd->html('form.dropdown', 'access', $this->config->get('main_blogprivacy'), EB::privacy()->getOptions(), ["attributes" => "data-quickpost-privacy"]); ?>
				<?php } ?>
			</div>
		</div>

		<div class="eb-composer-fieldset-content o-form-horizontal">
			<div class="eb-composer-tags" data-eb-composer-tags <?php echo $readonly; ?> data-eb-composer-tags-max="<?php echo $maxTags; ?>">
				<div class="eb-composer-textboxlist o-form-control" data-eb-composer-tags-textboxlist>
					<input type="text"
						class="textboxlist-textField" data-textboxlist-textField
						placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_TAGS_INSTRUCTIONS');?>"
						autocomplete="off" />
				</div>

				<div class="eb-composer-tags-suggestions is-empty">
					<div class="eb-composer-tags-selection">
						<s></s>
						<small class="empty-tags"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_TAGS_AVAILABLE'); ?></small>
						<div class="eb-composer-tags-selection-itemgroup"></div>
					</div>
					<div class="eb-composer-tags-actions">
						<small class="pull-left eb-composer-tags-toggle" data-eb-composer-tags-toggle-button>
							<i class="fdi fa fa-tags"></i>
							<span>
								<span data-eb-composer-tags-total>0</span>
							</span>
						</small>
					</div>
				</div>
				<input type="hidden" name="tags" value="" data-quickpost-tags />
			</div>
		</div>
	</div>



	<div class="form-group form-action">
		<div class="col-md-3 eb-quick-more-options">
			<button type="button" class="btn btn-default btn-options-less" data-quickpost-extended-toggle>
				<?php echo JText::_('COM_EASYBLOG_LESS_OPTIONS'); ?>
			</button>
			<button type="button" class="btn btn-default btn-options-more" data-quickpost-extended-toggle>
				<?php echo JText::_('COM_EASYBLOG_MICROBLOG_MORE_OPTIONS'); ?>
			</button>
		</div>

		<div class="col-md-9 eb-quick-actions">
			<?php if ($twitter || $linkedin) { ?>
			<div class="eb-quick-autopost">
				<label class="text-muted"><?php echo JText::_('COM_EASYBLOG_QUICKPOST_AUTO_POST_TO');?></label>

				<?php if ($twitter) { ?>
				<label data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('Automatically posts on Twitter as soon as the post is published.');?>" data-fd-tooltip-placement="bottom">
					<input name="autoposting[]" value="twitter" type="checkbox" data-autopost-item />
					<i class="fdi fab fa-twitter-square"></i>
				</label>
				<?php } ?>

				<?php if ($linkedin) { ?>
				<label data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('Automatically publishes on LinkedIn as soon as the post is published.');?>" data-fd-tooltip-placement="bottom">
					<input name="autoposting[]" value="linkedin" type="checkbox" data-autopost-item />
					<i class="fdi fa fa-linkedin-square"></i>
				</label>
				<?php } ?>
			</div>
			<?php } ?>

			<a href="javascript:void(0);" class="btn btn-primary" data-quickpost-publish>
				<?php echo JText::_('COM_EASYBLOG_PUBLISH_STORY_BUTTON');?>
			</a>
		</div>
	</div>
</div>
