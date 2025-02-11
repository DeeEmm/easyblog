<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class=" <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
	
	<h2 class="uk-h2 uk-heading-divider"><?php echo JText::_('COM_EASYBLOG_ARCHIVE_HEADING');?></h2>
	
</div>

<div class="eb-archives">

	<?php if ($posts) { ?>
		<ul class="uk-list uk-list-divider uk-margin-small">
			<?php foreach ($posts as $post) { ?>
				<li>
					<div class="uk-grid-small" uk-grid>
						<div class="uk-width-expand" uk-leader>
							<a href="<?php echo $post->getPermalink();?>">
								<span uk-icon="icon: file-text" class="uk-margin-small-right"></span> 
								<span><?php echo $post->title;?></span>
							</a>
						</div>
						<div>
							<time ><?php echo $this->fd->html('str.date', $post->created, JText::_('DATE_FORMAT_LC1')); ?></time>
						</div>
					</div>
				</li>
			<?php } ?>
		</ul>
	<?php } else { ?>
		<div class="eb-empty">
			<i class="fdi fa fa-archive"></i>
			<?php echo JText::_('COM_EASYBLOG_NO_ARCHIVES_YET'); ?>
		</div>
	<?php } ?>
</div>

<?php if($pagination) {?>
	<!-- @module: easyblog-before-pagination -->
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<!-- Pagination items -->
	<?php echo $pagination;?>

	<!-- @module: easyblog-after-pagination -->
	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
