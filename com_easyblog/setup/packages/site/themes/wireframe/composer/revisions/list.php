<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php foreach ($revisions as $revision) { ?>
<div class="eb-composer-revision-item row-table
	<?php echo $revision->id == $post->revision->id ? ' is-current' : '';?>
	<?php echo $revision->id == $post->getCurrentRevisionId() ? ' is-published' : '';?>
	<?php echo $revision->isPending() ? ' is-pending' : '';?>
	<?php echo $revision->isDraft() ? ' is-draft' : '';?>"
	data-id="<?php echo $revision->id;?>"
	data-uid="<?php echo $post->id;?>.<?php echo $revision->id;?>"
	data-eb-composer-revisions-item
>
	<div class="col-cell cell-tight eb-revision-numbers t-hidden">
		<div class="eb-revision-number"><?php echo $revision->ordering;?></div>
	</div>
	<div class="col-cell">
		<div class="eb-revision-title">
			<div class="row-table">
				<div class="col-cell">
					<b><?php echo $revision->getTitle();?></b>
				</div>
				<div class="col-cell cell-tight">
					<?php if ($revision->isCurrent($post) && !$post->isPending() && $revision->isFinalized()) { ?>
					<span class="eb-revision-published"
						data-eb-provide="tooltip" data-html="1" data-title="<?php echo JText::_('COM_EASYBLOG_REVISION_PUBLISHED');?>" data-placement="top"
					>
						<i class="fdi far fa-check-circle fa-fw"></i>
					</span>
					<?php } ?>

					<?php if ($revision->id == $post->revision->id) { ?>
					<span class="eb-revision-current"
						data-eb-provide="tooltip" data-html="1" data-title="<?php echo JText::_('COM_EASYBLOG_MM_CURRENTLY_EDITING');?>" data-placement="top"
					>
						<i class="fdi fa fa-pencil-alt fa-fw"></i>
					</span>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="eb-revision-meta text-muted">
			<div class="row-table">
				<div class="col-cell cell-ellipse">
					<?php if ($revision->isPending()) { ?>
						<span><?php echo JText::_('COM_EASYBLOG_REVISION_PENDING');?></span>
					<?php } ?>
					<?php if ($revision->isCurrent($post) && $post->isUnpublished()) { ?>
						<span><?php echo JText::_('COM_EASYBLOG_REVISION_UNPUBLISHED');?></span>
						&middot;
					<?php } ?>

					<?php if ($revision->isDraft()) { ?>
						<span><?php echo JText::_('COM_EASYBLOG_REVISION_DRAFT');?></span>
						&middot;
					<?php } ?>

					<span><?php echo ucfirst($revision->getAuthor()->getName());?></span>
						&middot;
					<span><?php echo $revision->getCreationDate()->format(JText::_('d M Y, h:ia')); ?></span>
				</div>
				<div class="col-cell cell-tight">
				</div>
			</div>
		</div>
		<?php if ($revision->id != $post->revision->id) { ?>
		<div class="eb-revision-options btn-toolbar">
			<div class="btn-group btn-group-xs">
				<a class="btn btn-eb-primary btn--xs" href="javascript:void(0);" data-eb-composer-revisions-compare>
					<?php echo JText::_('COM_EASYBLOG_COMPOSER_COMPARE');?>
				</a>
			</div>

			<?php if (($revision->id != $post->revision->id) || ($revision->isFinalized()) || (!$revision->isCurrent($post))) { ?>
			<div class="btn-group btn-group-xs">
				<a href="<?php echo $post->getPreviewLink(); ?>" class="btn btn-eb-default-o btn--xs" target="_blank">
					<?php echo JText::_('COM_EASYBLOG_PREVIEW');?>
				</a>

				<?php if ($revision->id != $post->revision->id) { ?>
					<a class="btn btn-eb-default-o btn--xs" href="<?php echo EBR::_('index.php?option=com_easyblog&view=composer&uid=' . $post->id . '.' . $revision->id . '&tmpl=component');?>">
						<?php echo JText::_('COM_EASYBLOG_COMPOSER_OPEN_REVISION');?>
					</a>
				<?php } ?>

				<?php if ($revision->isFinalized() && $post->getCurrentRevisionId() != $revision->id) { ?>
					<a class="btn btn-eb-default-o btn--xs" href="javascript:void(0);" data-eb-composer-revisions-use>
						<?php echo JText::_('COM_EASYBLOG_COMPOSER_USE_REVISION');?>
					</a>
				<?php } ?>

				<?php if (!$revision->isCurrent($post)) { ?>
					<a class="btn btn-eb-default-o btn--xs" href="javascript:void(0);" data-eb-composer-revisions-delete>
						<?php echo JText::_('COM_EASYBLOG_COMPOSER_DELETE_REVISION');?>
					</a>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
</div>
<?php } ?>
