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
<div data-preview-toolbar class="eb-floating-toolbar-wrapper" data-spy="affix" data-offset-top="220">
	<div class="eb-floating-toolbar">
			<ul class="nav navbar-nav">
				<li>
					<a href="<?php echo EB::composer()->getComposeUrl(['uid' => $post->getUid()]); ?>" data-blog-preview-continue>
						<span><?php echo JText::_('COM_EASYBLOG_ENTRY_PREVIEW_CONTINUE_EDITING'); ?></span>
						<i class="fdi fa fa-edit"></i>
					</a>
				</li>
				<li class="eb-floating-toolbar-dropdown dropdown">
					<a href="javascript:void(0);" class="dropdown-toggle" id="revisions-drop" data-bp-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
						<span><?php echo JText::_('COM_EASYBLOG_REVISIONS'); ?></span>
						<i class="fdi fa fa-list"></i>
						<div class="eb-floating-toolbar-dropdown__drop"></div>
					</a>
					<ul class="eb-revisions dropdown-menu" aria-labelledby="revisions-drop">
						<div class="eb-revisions-title">
							<h5><?php echo JText::_('COM_EASYBLOG_REVISIONS'); ?></h5>
						</div>
						<?php foreach ($revisions as $revision) { ?>
						<li>
							<div class="eb-revisions-list">
								<div class="eb-revisions-item" data-revision-item data-id="<?php echo $revision->id; ?>">
									<div class="eb-revisions-item__notice">
										<span class="eb-revisions-figure"><?php echo $revision->ordering;?></span>
									</div>
									<div class="eb-revisions-item__content">
										<div class="row-table">
											<div class="col-cell">
											<?php echo $revision->getTitle();?>
											</div>
											<div class="col-cell cell-tight">
												<?php if ($revision->isCurrent($post) && !$post->isPending() && $revision->isFinalized()) { ?>
												<span class="eb-revision-published"><?php echo JText::_('COM_EASYBLOG_REVISION_PUBLISHED');?></span>
												<?php } ?>

												<?php if ($revision->id == $post->revision->id) { ?>
												<span class="eb-revision-current"><?php echo JText::_('COM_EASYBLOG_MM_CURRENTLY_VIEWING');?></span>
												<?php } ?>
											</div>
										</div>

										<ul class="eb-revisions-item-meta">
											<li><?php echo ucfirst($revision->getAuthor()->getName());?></li>
											<li><?php echo $revision->getCreationDate()->format(JText::_('d M Y, h:ia')); ?></li>

											<?php if ($revision->isCurrent($post) && $post->isUnpublished()) { ?>
												<li class="text-success"><?php echo JText::_('COM_EASYBLOG_REVISION_UNPUBLISHED');?></li>
											<?php } ?>

											<?php if ($revision->isPending()) { ?>
												<li class="text-info"><?php echo JText::_('COM_EASYBLOG_REVISION_PENDING');?></li class="text-info">
											<?php } ?>

											<?php if ($revision->isDraft()) { ?>
												<li><?php echo JText::_('COM_EASYBLOG_REVISION_DRAFT');?></li>
											<?php } ?>
										</ul>
										<?php if ($revision->id != $post->revision->id) { ?>
											<div class="eb-revisions-item-actions">
												<a class="btn btn-primary" href="<?php echo EBR::_('index.php?option=com_easyblog&view=composer&uid=' . $post->getUid() . '&compareid=' . $revision->id . '&tmpl=component');?>" data-blog-preview-comparerevision>
													<?php echo JText::_('COM_EASYBLOG_COMPOSER_COMPARE');?>
												</a>

												<?php if (($revision->id != $post->revision->id) || ($revision->isFinalized()) || (!$revision->isCurrent($post))) { ?>
													<div class="btn-group btn-group-xs">
														<?php if ($revision->id != $post->revision->id) { ?>
															<a class="btn btn-default" href="<?php echo EBR::_('index.php?option=com_easyblog&view=composer&uid=' . $post->id . '.' . $revision->id . '&tmpl=component');?>">
																<?php echo JText::_('COM_EASYBLOG_COMPOSER_OPEN_REVISION');?>
															</a>
														<?php } ?>

														<?php if ($revision->isFinalized() && $post->getCurrentRevisionId() != $revision->id) { ?>
															<a class="btn btn-default" href="javascript:void(0);" data-blog-preview-userevision>
																<?php echo JText::_('COM_EASYBLOG_COMPOSER_USE_REVISION');?>
															</a>
														<?php } ?>

														<?php if (!$revision->isCurrent($post)) { ?>
															<a class="btn btn-default" href="javascript:void(0);" data-blog-preview-deleterevision>
																<?php echo JText::_('COM_EASYBLOG_COMPOSER_DELETE_REVISION');?>
															</a>
														<?php } ?>
													</div>
												<?php } ?>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</li>
						<?php } ?>
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" data-blog-preview-trash>
						<span><?php echo JText::_('COM_EASYBLOG_ENTRY_PREVIEW_TRASH_POST'); ?></span>
						<i class="fdi fa fa-trash-o"></i>
					</a>
				</li>
			</ul>
			<?php if (($post->isBlank() || ($post->isNew() && !$post->isPending()) || $post->isUnpublished() || $post->isPostUnpublished()) && !$post->isScheduled() && $this->acl->get('publish_entry')) { ?>
				<ul class="nav navbar-nav navbar-right">
					<?php if (!$post->title) { ?>
						<span class="eb-floating-toolbar__alert-msg mr-10">
						<?php echo JText::_('COM_EASYBLOG_ENTRY_PREVIEW_REQUIRE_TITLE'); ?>
						</span>
					<?php } ?>
					<a href="javascript:void(0);" class="btn btn-primary" data-blog-preview-publish <?php echo $post->title ? '' : 'disabled' ?>><?php echo JText::_('COM_EASYBLOG_ENTRY_PREVIEW_PUBLISH_POST'); ?></a>
				</ul>
			<?php } ?>

			<?php if ((!$post->isBlank() || !$post->isPublished()) && !$this->acl->get('publish_entry')) { ?>
				<ul class="nav navbar-nav navbar-right">
					<a href="javascript:void(0);" class="btn btn-success" data-blog-preview-submit-approval><?php echo JText::_('COM_EASYBLOG_SUBMIT_POST_FOR_APPROVAL'); ?></a>
				</ul>
			<?php } ?>
	</div>
</div>