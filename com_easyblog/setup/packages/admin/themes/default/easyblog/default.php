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
<form action="index.php?option=com_easyblog" method="post" name="adminForm" id="adminForm">
	<div class="grid grid-cols-1 md:grid-cols-12 gap-md">


		<div class=" md:col-span-7 w-auto m-no">
			<?php if ($this->my->authorise('easyblog.manage.blog', 'com_easyblog')) { ?>

				<?php echo $this->fd->html('adminWidgets.statistics', 'Blog Statistics', 'Statistics of the blog on the site', [
					(object) [
						'url' => 'index.php?option=com_easyblog&view=blogs',
						'icon' => 'fdi far fa-file-alt',
						'title' => 'COM_EASYBLOG_STATS_POSTS',
						'count' => $totalPosts
					],
					(object) [
						'url' => 'index.php?option=com_easyblog&view=blogs&layout=pending',
						'icon' => 'fdi far fa-file',
						'title' => 'COM_EASYBLOG_STATS_PENDING',
						'count' => $totalPending
					],
					(object) [
						'url' => 'index.php?option=com_easyblog&view=feeds',
						'icon' => 'fdi fa fa-rss-square',
						'title' => 'COM_EASYBLOG_STATS_FEEDS',
						'count' => $totalFeeds
					],
					(object) [
						'url' => 'index.php?option=com_easyblog&view=comments',
						'icon' => 'fdi fa fa-comments',
						'title' => 'COM_EASYBLOG_STATS_COMMENTS',
						'count' => $totalComments
					],
					(object) [
						'url' => 'index.php?option=com_easyblog&view=categories',
						'icon' => 'fdi far fa-folder-open',
						'title' => 'COM_EASYBLOG_STATS_CATEGORIES',
						'count' => $totalCategories
					],
					(object) [
						'url' => 'index.php?option=com_easyblog&view=tags',
						'icon' => 'fdi fa fa-tags',
						'title' => 'COM_EASYBLOG_STATS_TAGS',
						'count' => $totalTags
					],
					(object) [
						'url' => 'index.php?option=com_easyblog&view=bloggers',
						'icon' => 'fdi fa fa-user-friends',
						'title' => 'COM_EASYBLOG_STATS_AUTHORS',
						'count' => $totalAuthors
					],
					(object) [
						'url' => 'index.php?option=com_easyblog&view=teamblogs',
						'icon' => 'fdi fa fa-users',
						'title' => 'COM_EASYBLOG_STATS_TEAMS',
						'count' => $totalTeams
					],
					(object) [
						'url' => 'index.php?option=com_easyblog&view=reactions',
						'icon' => 'fdi fa fa-smile-beam',
						'title' => 'COM_EASYBLOG_REACTIONS',
						'count' => $totalReactions
					]
				]);?>

				<?php echo $this->output('admin/easyblog/widgets/default');?>
			<?php } ?>
		</div>

		<div class=" md:col-span-5 w-auto m-no">
			<?php if ($this->my->authorise('easyblog.manage.maintenance', 'com_easyblog')) { ?>
				<?php echo $this->fd->html('adminwidgets.version', $this->config->get('main_apikey'), $localVersion, EB_SERVICE_VERSION, JURI::root() . 'administrator/index.php?option=com_easyblog&task=system.upgrade'); ?>

				<?php echo $this->fd->html('adminwidgets.news'); ?>
			<?php } ?>
		</div>
	</div>

	<input type="hidden" name="boxchecked" value="0" />
</form>
