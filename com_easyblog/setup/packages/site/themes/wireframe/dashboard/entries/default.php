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
<form method="post" action="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries');?>" class="eb-dashboard-entries <?php echo !$posts ? 'is-empty' : '';?>" data-eb-dashboard-posts>
	<?php echo $this->html('dashboard.headers',
		$this->html('snackbar.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_POSTS', [
			'icon' => 'fdi fa fa-pencil-alt',
			'text' => 'COM_EASYBLOG_NEW_POST',
			'link' => EB::composer()->getComposeUrl(),
			'style' => 'primary'
		]),
		$this->fd->html('form.dropdown', 'templateActions', '', call_user_func(function() {
				$options = [
					'' => 'COM_EASYBLOG_BULK_ACTIONS',
					'posts.copy' => 'COM_EASYBLOG_COPY_SELECTED'
				];

				if ($this->acl->get('publish_entry')) {
					$options['posts.publish'] = 'COM_EASYBLOG_PUBLISH';
					$options['posts.unpublish'] = 'COM_EASYBLOG_UNPUBLISH';
				}

				if ($this->acl->get('delete_entry')) {
					$options['posts.trash'] = 'COM_EASYBLOG_TRASH';
				}
				return $options;
			}), ['attr' => 'data-eb-table-task']
		),
		$this->html('snackbar.search', 'post-search', $search),
		[
			$this->html('dashboard.filters', $state, [
				'all' => 'COM_EASYBLOG_FILTER_SELECT_FILTER',
				'publish' => 'COM_EASYBLOG_FILTER_PUBLISHED',
				'unpublish' => 'COM_EASYBLOG_FILTER_UNPUBLISHED',
				'pending' => 'COM_EASYBLOG_FILTER_UNDER_REVIEW',
				'scheduled' => 'COM_EASYBLOG_FILTER_SCHEDULED',
				'drafts' => 'COM_EASYBLOG_FILTER_DRAFT',
				'trash' => 'COM_EASYBLOG_FILTER_TRASHED'
			]),
			$categoryDropdown
		]
	);?>

	<?php echo $this->html('dashboard.emptyList', 'COM_EASYBLOG_DASHBOARD_EMPTY_POSTS', 'COM_EASYBLOG_DASHBOARD_EMPTY_POSTS_HINT', [
		'icon' => 'fdi fa fa-align-left',
		'button' => $this->fd->html('button.link', EB::composer()->getComposeUrl(), 'COM_EASYBLOG_NEW_POST', 'primary')
	]); ?>

	<table class="eb-table table table-striped table-hover" data-table>
		<thead>
			<tr>
				<td width="1%">
					<?php
						$disabled = false;
						if ($isPendingState && !$isModerator) {
							$disabled = true;
						}
					?>
					<?php echo $this->html('dashboard.checkall', $disabled); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?>
				</td>
				<td width="15%" class="text-center center narrow-hide">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATE');?>
				</td>
				<td width="10%" class="text-center narrow-hide">
					<?php echo $this->html('dashboard.sort', 'COM_EASYBLOG_TABLE_COLUMN_HITS', 'hits', $ordering, $sort); ?>
				</td>
				<td width="15%" class="text-center narrow-hide">
					<?php echo $this->html('dashboard.sort', 'COM_EASYBLOG_TABLE_COLUMN_DATE', 'created', $ordering, $sort); ?>
				</td>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($posts as $post) { ?>
			<tr data-eb-post-item data-id="<?php echo $post->id;?>" class="<?php echo $post->isPending() ? 'is-pending': ''; ?>">
				<td width="1%">
					<?php
						$disabled = false;
						if ($post->isPending() && !$isModerator) {
							$disabled = true;
						}
					?>

					<?php echo $this->html('dashboard.checkbox', 'ids[]', $post->id, array('disabled' => $disabled)); ?>
				</td>
				<td>
					<?php if ((!$post->isPending() && $post->canEdit()) || FH::isSiteAdmin()) { ?>
						<a href="<?php echo EB::composer()->getComposeUrl(array('uid' => $post->id . '.' . $post->revision_id));?>" class="post-title"><?php echo $post->getTitle();?></a>
					<?php } else { ?>
						<label class="post-title"><?php echo $post->getTitle();?></label>
					<?php } ?>

					<?php if ($post->isScheduled()) { ?>
					<div class="post-meta">
						<i><?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_ENTRIES_POST_IS_SCHEDULED_DESC', $post->getPublishDate(true)->format(JText::_('DATE_FORMAT_LC2'))); ?></i>
					</div>
					<?php } ?>

					<div class="post-meta">

						<?php if ($post->isTeamBlog()) { ?>
						<span>
							<a href="<?php echo $post->getBlogContribution()->getPermalink();?>"><?php echo $post->getBlogContribution()->getTitle();?></a>
						</span>
						<?php } ?>

						<span>
							<a href="<?php echo $post->creator->getPermalink();?>"><?php echo $post->getAuthorName();?></a>
						</span>

						<span>
							<?php foreach ($post->categories as $category) { ?>
								<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
							<?php } ?>
						</span>

						<?php if ($post->language != '*' && $post->language) { ?>
						<span>
							<i class="fdi fa fa-language"></i>&nbsp; <?php echo $post->language;?>
						</span>
						<?php } ?>

						<?php if ($post->isRejected) { ?>
						<span class="text-error">
							<?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_REJECTED_TITLE'); ?>
						</span>
						<?php } ?>
					</div>

					<?php if ($post->isRejected && $post->isRejected->message) { ?>
					<div class="mt-5">
						<b><u><?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_REJECTED_REASON');?></u></b>: <?php echo $post->isRejected->message;?>
					</div>
					<?php } ?>

					<?php if ($this->isMobile()) { ?>
						<?php if ($post->isPublished()) { ?>
							<span class="text-success"><?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?></span>
						<?php } ?>

						<?php if ($post->isUnpublished()) { ?>
							<span class="text-danger"><?php echo JText::_('COM_EASYBLOG_UNPUBLISHED'); ?></span>
						<?php } ?>

						<?php if ($post->isScheduled()) { ?>
							<span class="text-info"><?php echo JText::_('COM_EASYBLOG_SCHEDULED'); ?></span>
						<?php } ?>

						<?php if ($post->isPending()) { ?>
							<span class="text-warning"><?php echo JText::_('COM_EASYBLOG_UNDER_REVIEW'); ?></span>
						<?php } ?>

						<?php if ($post->isDraft()) { ?>
							<span><?php echo JText::_('COM_EASYBLOG_DRAFT'); ?></span>
						<?php } ?>

						<?php if ($post->isRejected) { ?>
								<p><span class="label label-danger"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_REJECTED_TITLE');?></span></p>
								<b><u><?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_REJECTED_REASON');?></u></b>
								<div><?php echo $post->isRejected->message;?></div>
						<?php } ?>

						<div><?php echo $post->getCreationDate(true)->format(JText::_('Y-m-d H:i'));?></div>
					<?php } ?>

					<ul class="post-actions" data-eb-actions data-id="<?php echo $post->id;?>">
						<?php if ($post->isPublished()) { ?>
						<li>
							<a href="<?php echo $post->getPermalink();?>" target="_blank" data-eb-action>
								<?php echo JText::_('COM_EASYBLOG_VIEW'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if ($post->isDraft() || $post->isPending()) { ?>
						<li>
							<a href="<?php echo $post->getPreviewLink();?>" target="_blank" data-eb-action>
								<?php echo JText::_('COM_EASYBLOG_PREVIEW'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if ($post->isPublished() && ($this->acl->get('publish_entry') || FH::isSiteAdmin())) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="posts.unpublish" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_UNPUBLISH'); ?>
								</a>
							</li>
						<?php } ?>

						<?php if ($post->isUnpublished() && ($this->acl->get('publish_entry') || FH::isSiteAdmin())) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="posts.publish" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_PUBLISH'); ?>
								</a>
							</li>
						<?php } ?>

						<?php if ($post->isFeatured() && $this->acl->get('feature_entry')) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="posts.unfeature" data-type="form">
								<?php echo JText::_('COM_EASYBLOG_FEATURED_UNFEATURE_POST'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if (!$post->isFeatured() && $this->acl->get('feature_entry')) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="posts.feature" data-type="form">
								<?php echo JText::_('COM_EASYBLOG_FEATURED_FEATURE_POST'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if (FH::isSiteAdmin()) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmNotify" data-type="dialog">
								<?php echo JText::_('COM_EASYBLOG_COMPOSER_NOTIFY_SUBSCRIBERS');?>
							</a>
						</li>
						<?php } ?>

						<?php if ($this->acl->get('delete_entry') && !$post->isPending()) { ?>
						<li>
							<a href="javascript:void(0);" class="text-danger" data-eb-action="posts.trash" data-type="form">
								<span class="eb-post-actions__text"><?php echo JText::_('COM_EASYBLOG_TRASH');?></span>
							</a>
						</li>
						<?php } ?>

						<?php if ($oauthClients) { ?>
							<?php foreach ($oauthClients as $oauth) { ?>
							<li class="dropdown-autopost">
								<a href="javascript:void(0);"
									class="<?php echo $oauth->isShared($post->id) ? ' active' : '';?>"
									data-post-autopost
									data-autopost-type="<?php echo $oauth->type;?>"
									data-eb-action
									data-fd-tooltip
									data-fd-tooltip-placement="top"

									<?php if ($oauth->isShared($post->id)) { ?>
									data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_TOOLTIP_' . strtoupper($oauth->type) . '_POSTED');?>"
									<?php } else { ?>
									data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_TOOLTIP_' . strtoupper($oauth->type));?>"
									<?php } ?>
								>
									<i class="fdi fa fa-check"></i>
									<?php echo $oauth->type;?>
								</a>
							</li>
							<?php } ?>
						<?php } ?>
					</ul>
				</td>


				<td class="text-center narrow-hide" width="15%">
					<?php if ($post->isPublished()) { ?>
						<span class="text-success"><?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?></span>
					<?php } ?>

					<?php if ($post->isUnpublished()) { ?>
						<span class="text-danger"><?php echo JText::_('COM_EASYBLOG_UNPUBLISHED'); ?></span>
					<?php } ?>

					<?php if ($post->isScheduled()) { ?>
						<span class="text-info"><?php echo JText::_('COM_EASYBLOG_SCHEDULED'); ?></span>
					<?php } ?>

					<?php if ($post->isPending()) { ?>
						<span class="text-warning"><?php echo JText::_('COM_EASYBLOG_UNDER_REVIEW'); ?></span>
					<?php } ?>

					<?php if ($post->isDraft()) { ?>
						<span><?php echo JText::_('COM_EASYBLOG_DRAFT'); ?></span>
					<?php } ?>
				</td>


				<td class="text-center narrow-hide" width="10%">
					<?php echo $post->hits;?>
				</td>

				<td class="text-center narrow-hide" width="15%">
					<?php echo $post->getCreationDate(true)->format(JText::_('Y-m-d H:i'));?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php if ($pagination) { ?>
	<div class="eb-box-pagination">
		<?php echo $pagination->getPagesLinks(); ?>
	</div>
	<?php } ?>

	<input type="hidden" name="return" value="<?php echo base64_encode(EBFactory::getURI(true));?>" data-table-grid-return />
	<input type="hidden" name="ids[]" value="" data-table-grid-id />
	<input type="hidden" name="sort" value="" />
	<input type="hidden" name="ordering" value="" />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="entries" />
	<?php echo $this->fd->html('form.action'); ?>
</form>
