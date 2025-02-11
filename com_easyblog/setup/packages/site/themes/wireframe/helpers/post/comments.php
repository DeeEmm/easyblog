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
<span class="eb-post-comments">
	<?php if ($icon) { ?>
		<i class="fdi fa fa-comments"></i>
	<?php } ?>

	<span>
		<?php if ($this->config->get('comment_disqus')) { ?>
			<?php echo $total; ?>
		<?php } else { ?>
			<a href="<?php echo $permalink;?>">
				<?php echo $this->getNouns('COM_EASYBLOG_COMMENT_COUNT', $total, true); ?>
			</a>
		<?php } ?>
	</span>
</span>