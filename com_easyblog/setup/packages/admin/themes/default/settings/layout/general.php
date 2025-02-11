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
<div class="grid grid-cols-1 md:grid-cols-2 gap-md">
	<div class="">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_TITLE', 'COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'layout_nameformat', 'COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_NAME_FORMAT', [
					'name' => 'COM_EASYBLOG_REAL_NAME_OPTION',
					'nickname' => 'COM_EASYBLOG_NICKNAME_OPTION',
					'username' => 'COM_EASYBLOG_USERNAME_OPTION'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'layout_blogger_breadcrumb', 'COM_EASYBLOG_LAYOUT_BREADCRUMB_BLOGGER'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_DEFAULT_LIST_LIMIT', 'list_limit'); ?>

					<div class="col-md-7">
						<div class="checkbox" style="margin-top: 0;" data-list-length-wrapper>
							<input type="checkbox" id="inherit-joomla" name="listlength_inherit" value="1" <?php echo $this->config->get('layout_listlength') == 0 ? ' checked="checked"' : '';?> data-list-length-inherit />
							<label for="inherit-joomla">
								<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_USE_JOOMLA_LIST_LENGTH');?>
							</label>
						</div>

						<div class="row <?php echo $this->config->get('layout_listlength') == 0 ? 'hide' : '';?>" data-list-length-input>
							<div class="col-md-7">
								<div class="input-group">
									<input type="text" name="layout_listlength" value="<?php echo $this->config->get('layout_listlength');?>" class="form-control text-center" />
									<span class="input-group-addon">
										<?php echo JText::_('Items Per Page'); ?>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php echo $this->fd->html('settings.toggle', 'main_categories_hideempty', 'COM_EASYBLOG_SETTINGS_WORKFLOW_HIDE_EMPTY_CATEGORIES'); ?>

				<?php echo $this->fd->html('settings.toggle', 'layout_zero_as_plural', 'COM_EASYBLOG_LAYOUT_ZERO_AS_PLURAL'); ?>

				<?php echo $this->fd->html('settings.toggle', 'layout_css', 'COM_EB_INCLUDE_STYLESHEET_RENDERING', '', '', 'COM_EB_INCLUDE_STYLESHEET_RENDERING_NOTE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_typography', 'COM_EB_SETTINGS_ENABLE_TYPOGRAPHY'); ?>
				<?php echo $this->fd->html('settings.toggle', 'layout_dropcaps', 'COM_EB_SETTINGS_LAYOUT_CAPITALIZE_FIRST_PARAGRAPH'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_CUSTOM_FIELDS', 'COM_EASYBLOG_CUSTOM_FIELDS_INFO'); ?>

			<div class="panel-body">
				<?php
					$backwardSelected = null;
					$backwardValues = [
						'l, d F Y' => 'DATE_FORMAT_LC1',
						'l, d F Y H:i' => 'DATE_FORMAT_LC2',
						'd F Y' => 'DATE_FORMAT_LC3'
					];

					$currentDateFormat = $this->config->get('custom_field_date_format');

					// Backward Compatibility
					if (in_array($currentDateFormat, array_keys($backwardValues))) {
						$backwardSelected = $backwardValues[$currentDateFormat];

						$this->config->set('custom_field_date_format', $backwardSelected);
					}
				?>
				<?php echo $this->fd->html('settings.dropdown', 'custom_field_date_format', 'COM_EASYBLOG_FIELD_DATE_FORMAT', [
					'DATE_FORMAT_LC1' => JFactory::getDate()->format(JText::_('DATE_FORMAT_LC1')),
					'DATE_FORMAT_LC2' => JFactory::getDate()->format(JText::_('DATE_FORMAT_LC2')),
					'DATE_FORMAT_LC3' => JFactory::getDate()->format(JText::_('DATE_FORMAT_LC3'))
				]); ?>
			</div> 
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EB_SETTINGS_SCHEMA'); ?>

			<div class="panel-body">
				<div class="form-group" data-schema-logo-wrapper>
					<?php echo $this->fd->html('form.label', 'COM_EB_SETTINGS_SCHEMA_LOGO', 'schema_logo'); ?>

					<div class="col-md-7" data-schema-logo data-id="" data-default-schema-logo="<?php echo EB::getLogo('schema', true); ?>">
						<div class="mb-20">
							<div class="eb-img-holder">
								<div class="eb-img-holder__remove" data-schema-logo-restore-default-wrap <?php echo EB::hasOverrideLogo('schema') ? '' : 'style="display: none;'; ?>>
									<a href="javascript:void(0);" class="" data-schema-logo-restore-default-button>
										<i class="fdi fa fa-times"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_REMOVE'); ?>
									</a>
								</div>
								<img src="<?php echo EB::getLogo('schema'); ?>" width="120" data-schema-logo-image />
							</div>
						</div>
						<div>
							<input type="file" name="schema_logo" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EB_THIRD_PARTY_STYLESHEETS'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'css_fontawesome', 'COM_EB_CSS_LOAD_FONTAWESOME_LIBRARY'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATARS_TITLE'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'layout_avatar', 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_AVATARS', '', 'data-avatars-author'); ?>
				<div class="form-group <?php echo $this->config->get('layout_avatar') ? '' : 'hide';?>" data-avatars-author-settings>
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_LINK_AUTHOR_NAME', 'layout_avatar_link_name'); ?>
					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'layout_avatar_link_name', $this->config->get('layout_avatar_link_name')); ?>
					</div>
				</div>

				<?php echo $this->fd->html('settings.dropdown', 'layout_avatarIntegration', 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS', [
					'default' => 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_DEFAULT',
					'easysocial' => 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_EASYSOCIAL',
					'easydiscuss' => 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_EASYDISCUSS',
					'jfbconnect' => 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_JFBCONNECT',
					'communitybuilder' => 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_CB',
					'gravatar' => 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_GRAVATAR',
					'gravatar' => 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_JOMSOCIAL',
					'kunena' => 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_KUNENA',
					'k2' => 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_K2',
					'phpbb' => 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_PHPBB',
					'gravatar' => 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_GRAVATAR',
					'jomwall' => 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_JOMWALL'
				], '', 'data-avatar-source'); ?>

				<div class="form-group hidden" data-phpbb-path>
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_PHPBB_PATH', 'layout_phpbb_path'); ?>
					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'layout_phpbb_path', $this->config->get('layout_phpbb_path')); ?>
					</div>
				</div>

				<?php echo $this->fd->html('settings.toggle', 'layout_categoryavatar', 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CATEGORY_AVATARS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'layout_teamavatar', 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TEAMBLOG_AVATARS'); ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EB_SETTINGS_LAYOUT_STYLE_AVATAR', 'avatar_style'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.dropdown', 'layout_avatar_style', $this->config->get('layout_avatar_style'),
							[
								'rounded' => 'COM_EB_SETTINGS_LAYOUT_STYLE_AVATAR_OPTION_ROUNDED',
								'square' => 'COM_EB_SETTINGS_LAYOUT_STYLE_AVATAR_OPTION_SQUARE'
							]
						); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_ORDERING', 'COM_EASYBLOG_ORDERING_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'layout_postorder', 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING', [
					'modified' => 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_LAST_MODIFIED',
					'latest' => 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_LATEST',
					'alphabet' => 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_ALPHABET',
					'popular' => 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_HITS',
					'published' => 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_PUBLISHED'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'layout_postsort', 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING', [
					'desc' => 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING_OPTIONS_DESCENDING',
					'asc' => 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING_OPTIONS_ASCENDING'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'layout_sorting_category', 'COM_EASYBLOG_SETTINGS_LAYOUT_CATEGORIES_ORDERING', [
					'alphabet' => 'COM_EASYBLOG_SETTINGS_LAYOUT_CATEGORIES_ORDERING_OPTIONS_TITLE',
					'latest' => 'COM_EASYBLOG_SETTINGS_LAYOUT_CATEGORIES_ORDERING_OPTIONS_LATEST',
					'ordering' => 'COM_EASYBLOG_SETTINGS_LAYOUT_CATEGORIES_ORDERING_OPTIONS_ORDERING',
					'popular' => 'COM_EASYBLOG_SETTINGS_LAYOUT_CATEGORIES_ORDERING_OPTIONS_POPULAR'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'layout_categorypostorder', 'COM_EASYBLOG_SETTINGS_LAYOUT_CATEGORY_ORDERING', [
					'modified' => 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_LAST_MODIFIED',
					'created' => 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_LATEST',
					'title' => 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_ALPHABET',
					'published' => 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_PUBLISHED',
					'hits' => 'COM_EB_SETTINGS_LAYOUT_POSTS_ORDERING_VISITS'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'layout_bloggerorder', 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING', [
					'featured' => 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_FEATURED',
					'latest' => 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_LATEST',
					'alphabet' => 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_ALPHABET',
					'latestpost' => 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_LATESTPOST',
					'active' => 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_ACTIVE',
					'ordering' => 'COM_EB_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_ORDERING'
				]); ?>
			</div>
		</div>
	</div>
</div>
