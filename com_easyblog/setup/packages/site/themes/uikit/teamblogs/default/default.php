<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-authors-team">
	<?php if ($teams) { ?>
		<?php foreach ($teams as $team) { ?>
			<div class="eb-author <?php echo $this->isMobile() ? 'is-mobile' : '';?>" data-team-item data-id="<?php echo $team->id;?>">

				<?php echo $this->html('headers.team', $team); ?>

				<div class="eb-authors-stats">
					<ul class="uk-child-width-expand t-mb--lg" uk-tab>
						<li class="active">
							<a class="" href="#team-posts-<?php echo $team->id;?>" data-bp-toggle="tab">
								<?php echo JText::_('COM_EASYBLOG_TEAMBLOG_TOTAL_POSTS');?>
							</a>
						</li>
						<li>
							<a href="#team-authors-<?php echo $team->id;?>" data-bp-toggle="tab">
								<?php echo JText::_('COM_EASYBLOG_TEAMBLOG_TOTAL_AUTHORS');?>
							</a>
						</li>
					</ul>

					<div class="eb-stats-content">
						<div class="tab-pane eb-stats-posts active" id="team-posts-<?php echo $team->id;?>">
							<?php if ($team->blogs) { ?>
								<ul class="uk-list uk-list-divider uk-margin-small">
									<?php foreach ($team->blogs as $post) { ?>
										<?php echo $this->html('post.list.simple', $post, 'created'); ?>
									<?php } ?>
								</ul>

								<a href="<?php echo $team->getPermalink();?>" class="uk-button uk-button-link uk-button-small uk-width-1-1">
									<?php echo JText::_('COM_EASYBLOG_VIEW_ALL_POSTS');?>
								</a>

							<?php } else { ?>
								<div class="eb-empty">
									<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_NO_POSTS_YET');?>
								</div>
							<?php } ?>
						</div>

						<div class="tab-pane eb-labels eb-stats-authors" id="team-authors-<?php echo $team->id;?>">
							<?php if ($team->members) { ?>
								<ul class="uk-list uk-list-divider uk-margin-small">
								<?php foreach ($team->members as $member) { ?>
									<li>
										<div class="uk-grid-small" uk-grid>
											<div class="uk-width-expand" uk-leader>
												<a href="<?php echo $member->getPermalink();?>">
													<?php echo $member->getName();?>
												</a>
												<div class="uk-margin-small-right">
													<?php echo $this->html('avatar.user', $member); ?>
												</div>
											</div>
											<div>
												<?php $pCnt = isset($member->postCount) ? $member->postCount : $member->getTotalPosts() ; ?>
												<?php echo $this->getNouns('COM_EASYBLOG_AUTHOR_POST_COUNT', $pCnt, true); ?>
											</div>
										</div>
									</li>
									
								<?php } ?>
								</ul>

								<?php if ($team->memberCount > count($team->members)) { ?>
									<a href="javascript:void(0);" data-view-member class="uk-button uk-button-link uk-button-small uk-width-1-1"><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_VIEW_ALL_MEMBERS');?></a>
								<?php } ?>


							<?php } else { ?>
								<div class="eb-empty">
									<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_NO_AUTHORS_YET');?>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } else { ?>
		<div class="eb-empty"><?php echo JText::_('COM_EASYBLOG_NO_TEAMBLOGS_FOUND'); ?></div>
	<?php } ?>

	<?php if ($pagination) { ?>
		<?php echo $pagination; ?>
	<?php } ?>
</div>
