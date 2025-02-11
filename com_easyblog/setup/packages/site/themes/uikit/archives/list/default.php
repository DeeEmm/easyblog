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
<?php echo $this->html('snackbar.standard', 'COM_EASYBLOG_ARCHIVE_HEADING'); ?>

<div class="eb-archives t-mt--lg">
	<?php if ($posts) { ?>
	<ul class="uk-list uk-list-divider uk-margin-small">
		<?php foreach ($posts as $post) { ?>
			<?php echo $this->html('post.list.simple', $post, 'created', 'DATE_FORMAT_LC1'); ?>
		<?php } ?>
	</ul>
	<?php } ?>

	<?php if (!$posts) { ?>
		<?php echo $this->html('post.list.emptyList', 'COM_EASYBLOG_NO_ARCHIVES_YET', 'fdi fa fa-archive'); ?>
	<?php } ?>
</div>

<?php if($pagination) {?>
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<?php echo $pagination;?>

	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
