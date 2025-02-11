<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$readonly = ($this->acl->get('create_tag')) ? '' : 'data-eb-composer-tags-readonly';
$maxTags  = $this->config->get('max_tags_allowed');

if (!is_array($post->tags)) {

	if (empty($post->tags)) {
		$tagCount = 0;
	} else {
		$post->tags = explode(',', $post->tags);
		$tagCount = count($post->tags);
	}
} else {
	$tagCount = count($post->tags);
}
?>
<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('tags', true) ? 'is-open' : ''; ?>" data-name="tags" data-eb-composer-block-section>

	<div class="eb-composer-fieldset-header" data-eb-composer-block-section-header>
		<strong><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_TAGS_HEADING'); ?></strong>
		<small>
			<span class="eb-composer-tags-count" data-eb-composer-tags-count><?php echo $tagCount; ?></span><?php if ($maxTags > 0) { ?>/<?php echo $maxTags; ?><?php } ?> <?php echo JText::_("COM_EASYBLOG_TAGS"); ?>
		</small>
		<i class="eb-composer-fieldset-header__icon" data-panel-icon=""></i>
	</div>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<div class="eb-composer-tags" data-eb-composer-tags <?php echo $readonly; ?> data-eb-composer-tags-max="<?php echo $maxTags; ?>">
			<div class="eb-composer-textboxlist o-form-control" data-eb-composer-tags-textboxlist>
				<input type="text"
					class="textboxlist-textField" data-textboxlist-textField
					placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_ENTER_TAG');?>"
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

					<?php if ($this->config->get('main_apikey')) { ?>
					<a class="eb-composer-tags-autofill-indicator pull-left">
						<i class="fdi fa fa-sync fa-spin"></i>
					</a>

					<button type="button" class="btn btn--xs btn-eb-default pull-right" data-eb-composer-tags-autofill-button>
						<i class="fdi fa fa-bolt"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOFILL');?>
					</button>

					<b class="eb-loader-o pull-right" style="margin: 0 5px;"></b>
					<?php } ?>

				</div>
			</div>

			<input type="hidden" name="tags" value="" data-eb-tags />
		</div>
		<textarea style="display:none;" data-eb-composer-tags-jsondata><?php echo json_encode($tags); ?></textarea>
		<textarea style="display:none;" data-eb-composer-default-tags><?php echo json_encode($defaultTags); ?></textarea>
	</div>
</div>

<div class="hide" data-tag-template>
	<div class="textboxlist-item[%== (this.locked) ? ' is-locked' : '' %]" data-textboxlist-item>
		<span class="textboxlist-itemContent" data-textboxlist-itemContent>[%== html %]</span>
		[% if (!this.locked) { %]
		<div class="textboxlist-itemRemoveButton" data-textboxlist-itemRemoveButton>
			<i class="fdi fa fa-times"></i>
		</div>
		[% } else { %]
			<i class="fdi fa fa-lock"></i>
		[% } %]
	</div>
</div>
