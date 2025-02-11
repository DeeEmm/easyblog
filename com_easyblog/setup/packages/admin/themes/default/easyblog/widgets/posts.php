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
<div class="db-stream-graph">
	<div data-chart-posts style="height: 200px; width: 100%;"></div>
	<div data-chart-posts-legend></div>
</div>

<?php if ($posts) { ?>
	<div class="divide-y divide-solid divide-gray-200">
		<?php foreach ($posts as $post) { ?>
			<div class="dash-stream py-sm leading-sm">
				<div class="dash-stream-content flex overflow-hidden">
					<div class="dash-stream-headline flex-grow min-w-0 overflow-hidden truncate whitespace-nowrap">
						<a href="<?php echo EB::composer()->getComposeUrl(array('uid' => $post->uid . '.' . $post->revision_id));?>" class="fd-link block w-full overflow-hidden truncate whitespace-nowrap text-sm font-bold"><?php echo $post->title;?></a>
					</div>
					<div class="text-gray-500 ml-auto flex-shrink-0 pl-md">
						<span>
							<i class="fdi fa fa-user"></i>&nbsp; <?php echo $post->getAuthor()->getName();?>
						</span>
						<span class="ml-sm">
							<i class="fdi far fa-clock"></i>&nbsp; <?php echo $this->fd->html('str.date', $post->created, JText::_('Y-m-d H:i'));?>
						</span>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
<?php } else { ?>
<div class="o-empty block">
	<div class="o-empty__content">
		<div class="o-empty__text">
			<?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_POSTS_YET');?>
		</div>
	</div>
</div>
<?php } ?>
