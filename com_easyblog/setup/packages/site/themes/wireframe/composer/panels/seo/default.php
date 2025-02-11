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
<div class="eb-composer-panel" data-eb-composer-panel data-id="seo">
	<div class="eb-composer-panel-content">
		<div data-eb-composer-panel-content-viewport data-scrolly-viewport>
			<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('page_title', true) ? 'is-open' : ''; ?>" data-name="page_title" data-eb-composer-block-section>
				<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_CUSTOM_PAGE_TITLE'); ?>

				<div class="eb-composer-fieldset-content o-form-horizontal">
					<input class="o-form-control" type="text" id="custom_title" name="custom_title" value="<?php echo $this->fd->html('str.escape', $post->custom_title);?>"
						placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_CUSTOM_PAGE_TITLE_PLACEHOLDER', true);?>" />

					<?php echo $this->html('composer.panel.help', 'COM_EB_CUSTOM_PAGE_TITLE_HELP'); ?>
				</div>
			</div>

			<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('canonical', true) ? 'is-open' : ''; ?>" data-name="canonical" data-eb-composer-block-section>
				<?php echo $this->html('composer.panel.header', 'COM_EB_CANONICAL_LINK'); ?>

				<div class="eb-composer-fieldset-content o-form-horizontal">
					<input class="o-form-control" type="text" id="canonical" name="canonical" value="<?php echo $this->fd->html('str.escape', $post->canonical);?>"
						placeholder="https://site.com/to/another/article" />

					<?php echo $this->html('composer.panel.help', 'COM_EB_CANONICAL_LINK_HELP'); ?>
				</div>
			</div>

			<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('meta_desc', true) ? 'is-open' : ''; ?>" data-name="meta_desc" data-eb-composer-block-section>
				<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_META_DESCRIPTION', true, [
					'counter' => 0,
					'counterAttr' => 'data-meta-counter',
					'counterText' => 'COM_EASYBLOG_COMPOSER_CHARACTERS'
				]); ?>

				<div class="eb-composer-fieldset-content o-form-horizontal">
					<div class="eb-composer-textarea">
						<textarea class="o-form-control" name="description" rows="3"
							data-meta-description
							placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_META_DESCRIPTION_PLACEHOLDER');?>"><?php echo $post->description; ?></textarea>
					</div>

					<?php echo $this->html('composer.panel.help', 'COM_EB_COMPOSER_META_DESCRIPTION_HELP'); ?>
				</div>
			</div>

			<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('keywords', true) ? 'is-open' : ''; ?>" data-name="keywords" data-eb-composer-block-section>
				<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_KEYWORDS', true, [
					'counter' => 0,
					'counterAttr' => 'data-keyword-counter',
					'counterText' => 'COM_EASYBLOG_COMPOSER_KEYWORDS'
				]); ?>

				<div class="eb-composer-fieldset-content o-form-horizontal">

					<div class="eb-composer-textarea">
						<div class="eb-composer-textboxlist o-form-control" data-eb-composer-seo-keywords-textboxlist>
							<input type="text" class="textboxlist-textField" data-textboxlist-textField
								placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_KEYWORDS_PLACEHOLDER');?>"
								autocomplete="off" />
						</div>

						<?php if ($this->config->get('main_apikey')) { ?>
						<div class="eb-composer-textarea-footer eb-composer-seo-keywords-actions">
							<button data-eb-composer-seo-keywords-autofill-button="" class="btn btn--xs btn-eb-default pull-right" type="button"><i class="fdi fa fa-bolt"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOFILL');?></button>
							<b class="eb-loader-o pull-right" style="margin: 0 5px;"></b>
						</div>
						<?php } ?>

						<textarea style="display:none;" data-eb-composer-keywords-jsondata><?php echo json_encode($post->getKeywords()); ?></textarea>
					</div>

					<?php echo $this->html('composer.panel.help', 'COM_EB_COMPOSER_KEYWORDS_HELP'); ?>

					<div class="hide" data-keyword-template>
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
				</div>
				<input type="hidden" name="keywords" value="" data-eb-keywords />
			</div>

			<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('robots', true) ? 'is-open' : ''; ?>" data-name="robots" data-eb-composer-block-section>
				<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_ROBOTS'); ?>

				<div class="eb-composer-fieldset-content o-form-horizontal">
					<?php echo $this->fd->html('form.robots', 'robots', strtoupper($post->robots), ['baseClass' => 'o-form-control']); ?>

					<?php echo $this->html('composer.panel.help', 'COM_EB_COMPOSER_ROBOTS_HELP'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
