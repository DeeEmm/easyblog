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
<form method="post" action="<?php echo JRoute::_('index.php');?>" data-eb-dashboard-posts>
	<div class="eb-head">
		<h2 class="reset-heading pull-left">
			<i class="fdi far fa-file-alt"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_POSTS');?>
		</h2>
	</div>
	<?php if(count($versions) > 1){ ?>
	<div class="eb-box eb-table">
		<div class="eb-table-body">
			<?php
			foreach ($versions as $post) {
			$obj = json_decode($post->params);
			?>
			<div class="row-table align-top" data-eb-post-item data-id="<?php echo $post->id;?>">
				<div class="col-cell cell-check">
				<?php if($post->current){ ?>
					<a>
						<i class="fdi fa fa-star check-star active" data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('This is the current version used.');?>" data-fd-tooltip-placement="top"></i>
					</a>
				<?php } ?>
				</div>

				<div class="col-cell">
					<b class="eb-table-title">
						<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=diff&id=' . $post->id);?>"><?php echo $post->title;?></a>
					</b>

					<div class="eb-table-content mt-10 mb-10 pb-10">
						<?php echo EBString::substr(strip_tags($obj->intro), 0, 250);?> <?php echo JText::_('COM_EASYBLOG_ELLIPSES');?>
					</div>

					<?php if(!$post->current){ ?>
					<div class="eb-table-toolbar btn-toolbar mt-15">
						<div class="btn-group btn-group-sm">
							<a class="btn btn-default" href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=diff&id=' . $post->id.'&post_id='.$post->post_id);?>" data-post-diff>
								<?php echo JText::_('Compare');?>
							</a>

						</div>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php } else { ?>

	<div class="eb-box is-empty text-center">
		<b><?php echo JText::_( 'COM_EASYBLOG_HISTORY_NO_VERSION' );?></b>
	</div>
	<?php } ?>
	<?php echo $this->fd->html('form.action'); ?>
</form>
