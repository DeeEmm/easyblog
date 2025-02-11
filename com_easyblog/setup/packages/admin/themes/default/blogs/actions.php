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
<div id="toolbar-actions" class="btn-wrapper hide" data-actions>
	<div class="dropdown">
		<button type="button" class="btn btn-small dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown">
			<span class="icon-cog"></span> <?php echo JText::_('COM_EASYBLOG_OTHER_ACTIONS');?> &nbsp;<span class="caret"></span>
		</button>

		<ul class="dropdown-menu">
			<li>
				<a href="javascript:void(0);" data-action="admin/views/blogs/move" data-type="dialog">
					<?php echo JText::_('COM_EASYBLOG_MOVE'); ?>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);" data-action="blogs.copy" data-type="post">
					<?php echo JText::_('COM_EASYBLOG_COPY'); ?>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="javascript:void(0);" data-action="blogs.archive" data-type="post">
					<?php echo JText::_('COM_EASYBLOG_ARCHIVE_POSTS'); ?>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);" data-action="blogs.unarchive" data-type="post">
					<?php echo JText::_('COM_EASYBLOG_UNARCHIVE_POSTS'); ?>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="javascript:void(0);" data-action="blogs.lock" data-type="post">
					<?php echo JText::_('COM_EASYBLOG_TOOLBAR_LOCK'); ?>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);" data-action="blogs.unlock" data-type="post">
					<?php echo JText::_('COM_EASYBLOG_TOOLBAR_UNLOCK'); ?>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="javascript:void(0);" data-action="blogs.toggleFrontpage" data-type="post">
					<?php echo JText::_('COM_EASYBLOG_FRONTPAGE_TOOLBAR'); ?>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);" data-action="admin/views/blogs/authors" data-type="dialog">
					<?php echo JText::_('COM_EASYBLOG_CHANGE_AUTHOR'); ?>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="javascript:void(0);" data-action="blogs.resetHits" data-type="post">
					<?php echo JText::_('COM_EASYBLOG_RESET_HITS'); ?>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);" data-action="blogs.resetRatings" data-type="post">
					<?php echo JText::_('COM_EASYBLOG_RESET_RATINGS'); ?>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="javascript:void(0);" data-action="admin/views/blogs/massAssignTags" data-type="dialog">
					<?php echo JText::_('COM_EB_MASS_ASSIGN_TAGS'); ?>
				</a>
			</li>			
		</ul>
	</div>
</div>
