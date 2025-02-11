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
<?php if (FH::isSiteAdmin()
			|| ($post->canEdit() && !$post->hasRevisionWaitingForApproval())
			|| ($post->isMine() && $this->acl->get('delete_entry'))
			|| ($post->canEdit() && $this->acl->get('publish_entry'))
			|| $this->acl->get('feature_entry')
			|| $this->acl->get('moderate_entry')
			|| $post->canFavourite()) { ?>
<div class="eb-post-admin">

	<?php if ($post->canFavourite()) { ?>
		<div class="eb-post-admin__item">
			<a class="eb-favourite-toggle<?php echo $post->isFavourited() ? ' is-favourited' : ''; ?>"
				href="javascript:void(0);"
				data-entry-favourite
				data-action="<?php echo $post->isFavourited() ? 'unfavourite' : 'favourite'; ?>"
				data-fd-tooltip
				data-fd-tooltip-title="<?php echo $post->isFavourited() ? JText::_('COM_EB_UNFAVOURITE_THIS_POST') : JText::_('COM_EB_FAVOURITE_THIS_POST');?>"
				data-fd-tooltip-placement="bottom">
				<i></i>
			</a>
		</div>
	<?php } ?>

	<?php if (FH::isSiteAdmin()
			|| ($post->canEdit() && !$post->hasRevisionWaitingForApproval())
			|| ($post->canEdit() && $this->acl->get('publish_entry'))
			|| ($post->isMine() && $this->acl->get('delete_entry'))
			|| $this->acl->get('feature_entry')
			|| $this->acl->get('moderate_entry')) {
		$showDivider = false;
	?>
		<div class="eb-post-admin__item">
			<div class="dropdown_" data-blog-tools>
				<a id="post-<?php echo $post->id;?>" data-bp-toggle="dropdown" href="javascript:void(0);" class="eb-post-admin__dropdown-toggle">
					<i class="fdi fa fa-pencil-alt"></i>
				</a>
				<ul class="dropdown-menu reset-list" role="menu" aria-labelledby="post-<?php echo $post->id;?>">

					<?php if ($post->canEdit() && !$post->hasRevisionWaitingForApproval()) { ?>
					<li>
						<a href="<?php echo EB::composer()->getComposeUrl(array('uid' => $post->id)); ?>"><?php echo JText::_('COM_EASYBLOG_ADMIN_EDIT_ENTRY'); ?></a>
					</li>
					<?php $showDivider = true; ?>
					<?php } ?>

					<?php if ($this->acl->get('feature_entry') && !$post->isPasswordProtected()) { ?>
					<li class="divider"></li>
					<li class="featured_add<?php echo $post->isFeatured ? ' hide' : '';?>">
						<a href="javascript:void(0);" data-entry-feature data-return="<?php echo base64_encode($return);?>"><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_THIS'); ?></a>
					</li>
					<li class="featured_remove<?php echo $post->isFeatured ? '' : ' hide';?>">
						<a href="javascript:void(0);" data-entry-unfeature data-return="<?php echo base64_encode($return);?>"><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_REMOVE'); ?></a>
					</li>
					<?php $showDivider = true; ?>
					<?php } ?>

					<?php if ($this->acl->get('moderate_entry') || FH::isSiteAdmin()) { ?>
						<?php if ($post->isArchived()) { ?>
						<li>
							<a href="javascript:void(0);" data-entry-unarchive data-id="<?php echo $post->id;?>" data-return="<?php echo base64_encode($return);?>">
								<?php echo JText::_('COM_EASYBLOG_UNARCHIVE_POST');?>
							</a>
						</li>
						<?php } else { ?>
						<li>
							<a href="javascript:void(0);" data-entry-archive data-id="<?php echo $post->id;?>" data-return="<?php echo base64_encode($return);?>">
								<?php echo JText::_('COM_EASYBLOG_ARCHIVE_THIS');?>
							</a>
						</li>
						<?php } ?>
						<?php $showDivider = true; ?>
					<?php } ?>

					<?php if ($post->isPublished() && (FH::isSiteAdmin() || ($post->isMine() && $this->acl->get('publish_entry')) || $this->acl->get('moderate_entry'))) { ?>
					<li class="unpublish">
						<a href="javascript:void(0);" data-entry-unpublish data-id="<?php echo $post->id;?>" data-return="<?php echo base64_encode($return);?>"><?php echo Jtext::_('COM_EASYBLOG_ADMIN_UNPUBLISH_ENTRY'); ?></a>
					</li>
					<?php $showDivider = true; ?>
					<?php } ?>

					<?php if (!$post->isPublished() && (FH::isSiteAdmin() || ($this->acl->get('publish_entry')) || $this->acl->get('moderate_entry'))) { ?>
					<li class="publish">
						<a href="javascript:void(0);" data-entry-publish data-id="<?php echo $post->id;?>" data-return="<?php echo base64_encode($return);?>"><?php echo Jtext::_('COM_EASYBLOG_ADMIN_PUBLISH_ENTRY'); ?></a>
					</li>
					<?php $showDivider = true; ?>
					<?php } ?>

					<?php if (FH::isSiteAdmin() || ($post->isMine() && $this->acl->get('delete_entry') ) || $this->acl->get('moderate_entry') ) { ?>
					<?php if ($showDivider) { ?>
					<li class="divider"></li>
					<?php } ?>

					<li class="delete">
						<a href="javascript:void(0);" data-entry-delete data-id="<?php echo $post->id;?>" data-return="<?php echo base64_encode(EBR::_('index.php?option=com_easyblog', false));?>">
							<?php echo Jtext::_('COM_EASYBLOG_TRASH'); ?>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	<?php } ?>

</div>
<?php } ?>
