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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('author', true) ? 'is-open' : ''; ?>" data-name="author" data-eb-composer-block-section>

	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_COMPOSER_PANEL_AUTHOR'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group o-form-group--eb-style-bordered">
			<div class="eb-sub-author current-user t-hidden" data-eb-composer-current-author>
				<div class="t-d--flex">
					<div class="o-grid__cell o-grid__cell--auto-size">
						<img src="<?php echo $user->getAvatar();?>" class="avatar" width="50" height="50" />
					</div>

					<div class="o-grid__cell t-flex-grow--1">
						<div class="eb-sub-author-details">
							<div class="authorship-title text-muted text-uppercase text-small">
								<?php echo JText::_('COM_EASYBLOG_COMPOSER_PANEL_AUTHOR_YOU_ARE');?>
							</div>
							<div><?php echo $user->getName();?></div>
						</div>
					</div>
				</div>
			</div>

			<div class="eb-sub-author current-author t-p--sm" data-eb-composer-author>

				<div class="t-d--flex">
					<div class="o-grid__cell o-grid__cell--auto-size">
						<img src="<?php echo $post->getAuthor()->getAvatar();?>" class="avatar" width="50" height="50" data-eb-composer-authoravatar />

						<?php if ($post->isStandardSource() && !$contribution) { ?>
						<img width="30" height="30" class="avatar hide" data-eb-composer-associateavatar />
						<?php } ?>

						<?php if ($post->isStandardSource() && $contribution) { ?>
						<img width="30" height="30" class="avatar" data-eb-composer-associateavatar
							src="<?php echo $contribution->getAvatar();?>" />
						<?php } ?>

						<?php if (!$post->isStandardSource() && $post->getBlogContribution()) { ?>
						<img width="30" height="30" class="avatar" data-eb-composer-associateavatar
							src="<?php echo $post->getBlogContribution()->getAvatar();?>" />
						<?php } ?>
					</div>

					<div class="o-grid__cell t-flex-grow--1">
						<div class="eb-sub-author-details">
							<div class="authorship-title text-muted text-uppercase text-small">
								<?php echo JText::_('COM_EASYBLOG_COMPOSER_PANEL_AUTHOR_POSTING_UNDER');?>
							</div>
							<div data-eb-composer-authorname><?php echo $post->getAuthor()->getName();?></div>

							<?php if ($post->isStandardSource() && !$contribution) { ?>
							<div class="text-muted" data-eb-composer-source-name data-eb-composer-associatename></div>
							<?php } ?>

							<?php if ($post->isStandardSource() && $contribution) { ?>
							<div class="text-muted" data-eb-composer-source-name data-eb-composer-associatename>
								<?php echo $contribution->getTitle();?>
							</div>
							<?php } ?>

							<?php if (!$post->isStandardSource() && $post->getBlogContribution()) { ?>
							<div class="text-muted" data-eb-composer-source-name data-eb-composer-associatename>
								<?php echo $post->getBlogContribution()->getTitle();?>
							</div>
							<?php } ?>

							<input type="hidden" name="created_by" id="created_by" value="<?php echo !$post->getAuthor()->id ? $user->id : $post->getAuthor()->id;?>" data-eb-composer-authorid />
							<input type="hidden" name="source_id" id="source_id" value="<?php echo $post->source_id;?>" data-eb-composer-associateid />
							<input type="hidden" name="source_type" id="source_type" value="<?php echo $post->source_type;?>" data-eb-composer-associatetype />
						</div>
					</div>

					<?php if (FH::isSiteAdmin() || $this->acl->get('moderate_entry') || $post->getAuthor()->hasTeams() || $post->getAuthor()->hasAssociations()) { ?>
					<div class="o-grid__cell o-grid__cell--auto-size eb-sub-author-switch">
						<a href="javascript:void(0);" data-eb-composer-switch-author><i class="fdi fa fa-pencil-alt"></i></a>
					</div>
					<?php } ?>
				</div>
			</div>

			<div class="eb-action-pick eb-composer-pick-author" data-eb-composer-author-picker>
				<div class="eb-action-pick-finder">

					<ul class="eb-action-pick-tabs">
						<?php if (FH::isSiteAdmin() || $this->acl->get('moderate_entry')) { ?>
						<li class="active" data-author-type data-type="authors">
							<a href="javascript:void(0);">
								<?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTHORS');?>
							</a>
						</li>
						<?php } ?>

						<li class="<?php echo !FH::isSiteAdmin() && !$this->acl->get('moderate_entry') ? 'active' : '';?>" data-author-type data-type="associates">
							<a href="javascript:void(0);">
								<?php echo JText::_('COM_EASYBLOG_COMPOSER_ASSOCIATES');?>
							</a>
						</li>
					</ul>

					<div class="eb-action-pick-content tab-content">

						<?php if (FH::isSiteAdmin() || $this->acl->get('moderate_entry')) { ?>
						<div class="tab-pane in active" data-tab-content data-type="authors">
							<div class="eb-composer-pick-list" data-eb-composer-author-list>
								<div class="loading-authors">
									<i class="fdi fa fa-circle-o-notch fa-spin"></i>
								</div>
							</div>
							<div class="t-px--md t-py--sm t-border-top--1">
								<div>
									<div class="o-form-control-icon t-text--500" data-fa-icon="&#xf002;">
										<input type="text" placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_PLACEHOLDER_FIND_A_PERSON');?>" data-author-search class="o-form-control"/>
										<a href="javascript:void(0);" class="o-form-control-icon__remove t-text--500" data-author-search-cancel>
											<i class="fdi fa fa-times"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>

						<div class="tab-pane<?php echo !FH::isSiteAdmin() && !$this->acl->get('moderate_entry') ? ' active' : '';?>" data-tab-content data-type="associates">
							<div class="eb-composer-pick-list" data-eb-composer-associates-list>
								<div class="loading-authors">
									<i class="fdi fa fa-circle-o-notch fa-spin"></i>
								</div>
							</div>
							<div class="t-px--md t-py--sm t-border-top--1">
								<div>
									<div class="o-form-control-icon t-text--500" data-fa-icon="&#xf002;">
										<input type="text" placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_FIND_ASSOCIATES');?>" data-associates-search class="o-form-control"/>
										<a href="javascript:void(0);" class="o-form-control-icon__remove t-text--500" data-associates-search-cancel>
											<i class="fdi fa fa-times"></i>
										</a>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php if (FH::isSiteAdmin() || $this->acl->get('moderate_entry') || $post->getAuthor()->hasTeams() || $post->getAuthor()->hasAssociations()) { ?>
			<?php echo $this->html('composer.panel.help', 'COM_EB_COMPOSER_PANEL_AUTHOR_INFO'); ?>
		<?php } ?>
	</div>
</div>

<?php if ($this->config->get('reviewer_fact_checker_enabled')) { ?>
<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('reviewer', true) ? 'is-open' : ''; ?>" data-name="reviewer" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EB_COMPOSER_REVIEWER_SECTION'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group">
			<?php echo $this->html('composer.field.text', 'params[reviewer_name]', $post->getReviewerName(), ['placeholder' => 'COM_EB_COMPOSER_REVIEWER_NAME_FIELD_PLACEHOLDER']); ?>
		</div>

		<div class="o-form-group">
			<?php echo $this->html('composer.field.text', 'params[reviewer_link]', $post->getReviewerLink(), ['placeholder' => 'COM_EB_COMPOSER_REVIEWER_LINK_FIELD_PLACEHOLDER']); ?>
		</div>

		<?php echo $this->html('composer.panel.help', 'COM_EB_COMPOSER_REVIEWER_HELP'); ?>
	</div>
</div>

<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('fact_checker', true) ? 'is-open' : ''; ?>" data-name="fact_checker" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EB_COMPOSER_FACT_CHECKER_SECTION'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group">
			<?php echo $this->html('composer.field.text', 'params[fact_checker_name]', $post->getFactCheckerName(), ['placeholder' => 'COM_EB_COMPOSER_FACT_CHECKER_NAME_FIELD_PLACEHOLDER']); ?>
		</div>

		<div class="o-form-group">
			<?php echo $this->html('composer.field.text', 'params[fact_checker_link]', $post->getFactCheckerLink(), ['placeholder' => 'COM_EB_COMPOSER_FACT_CHECKER_LINK_FIELD_PLACEHOLDER']); ?>
		</div>

		<?php echo $this->html('composer.panel.help', 'COM_EB_COMPOSER_FACT_CHECKER_HELP'); ?>
	</div>
</div>
<?php } ?>