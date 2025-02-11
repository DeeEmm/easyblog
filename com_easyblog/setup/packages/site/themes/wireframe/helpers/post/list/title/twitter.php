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
<?php $screen_name = $post->getAsset('screen_name')->getValue();
	  $created_at = EB::date($post->getAsset('created_at')->getValue(), true)->format(JText::_('DATE_FORMAT_LC'));
?>
<div class="eb-post-headline">
	<h2 class="eb-post-title-tweet reset-heading">
		<?php echo $post->content;?>
	</h2>

	<?php if (!empty($screen_name) && !empty($created_at)) { ?>
	<div class="eb-post-headline-source">
			<?php echo '@'.$screen_name.' - '.$created_at; ?>
			&middot;
			<a href="<?php echo $post->getPermalink();?>">
				<?php echo JText::_('COM_EASYBLOG_LINK_TO_POST'); ?>
			</a>
	</div>
	<?php } ?>
</div>
