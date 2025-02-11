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
<div>
	<div class="t-d--flex">
		<div class="t-flex-shrink--0 t-pr--md">
			<?php echo $this->html('avatar.user', $comment->getAuthor(), 'default', $comment->created_by != 0); ?>
		</div>
		<div class="t-min-width--0 t-flex-grow--1 l-stack l-spaces--xs">
			<div>
				<?php echo $comment->comment;?>
			</div>
			<div class="fd-inline-list">
				<div>
					<?php echo $comment->getCreated()->format('DATE_FORMAT_LC2'); ?>
				</div>
				<div fd-breadcrumb="·">
					<?php echo $comment->getAuthor()->getName();?>
				</div>
				<div fd-breadcrumb="·">
					<a href="<?php echo $comment->getPermalink();?>" class="t-text--500"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_STATISTICS_VIEW_COMMENT');?></a>
				</div>
			</div>
		</div>
	</div>
</div>
