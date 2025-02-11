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
		<div class="t-min-width--0 t-flex-grow--1 t-text--truncate">
			<div class="t-text--truncate">
				<i class="fdi far fa-file-alt text-muted t-mr--md"></i>
				<a href="<?php echo $post->getPermalink();?>" class="t-font-weight--bold t-text--800">
					<?php echo $post->title;?>
				</a>
			</div>
		</div>
		<div class="t-flex-shrink--0">
			<div class="text-muted t-ml--md">
				<?php echo $this->getNouns('COM_EASYBLOG_DASHBOARD_STATISTICS_TOTAL_COMMENTS', $post->totalcomments, true); ?>
			</div>
		</div>
	</div>
</div>
