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
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_FEEDS_DETAILS', 'COM_EASYBLOG_FEEDS_DETAILS_INFO', 'administrators/configuration/feeds-importer'); ?>

				<div class="panel-body">
					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_TITLE', 'title'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'title', $feed->title, 'title'); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_URL', 'url'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'url', $feed->url, 'url'); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_PUBLISHED', 'published'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'published', $feed->published); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_CRON', 'cron'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'cron', $feed->cron); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_CRON_INTERVAL', 'interval'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'interval', $feed->get('interval'), '', [
								'postfix' => 'COM_EASYBLOG_MINUTES',
								'size' => 5,
								'class' => 'text-center'
							]); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_SHOW_AUTHOR', 'author'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'author', $feed->author); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_COPYRIGHT_TEXT', 'copyrights'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'copyrights', $params->get('copyrights', '')); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_INCLUDE_ORIGINAL_LINK', 'sourceLinks'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'sourceLinks', $params->get('sourceLinks')); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_AMOUNT', 'feedamount'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.text', 'feedamount', $params->get('feedamount', 10), '', [
								'postfix' => 'Items',
								'size' => 5,
								'class' => 'text-center'
							]); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_FEEDS_PUBLISHING_DETAILS', 'COM_EASYBLOG_FEEDS_PUBLISHING_DETAILS_INFO'); ?>

				<div class="panel-body">
					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_PUBLISH_ITEM', 'item_published'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.dropdown', 'item_published', $feed->item_published, [
								'1' => 'COM_EASYBLOG_PUBLISHED',
								'0' => 'COM_EASYBLOG_UNPUBLISHED',
								'4' => 'COM_EASYBLOG_PENDING'
							]); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_LANGUAGE', 'language'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.languages', 'language', $feed->language); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_PUBLISH_FRONTPAGE', 'item_frontpage'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'item_frontpage', $feed->item_frontpage); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EB_FEEDS_INSERT_CANONICAL_FEED_ITEM_URL', 'item_canonical'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'canonical', $params->get('canonical', false)); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_IMPORT_POST_COVER', 'cover'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'cover', $params->get('cover', false)); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_PUBLISH_AUTOPOST', 'autopost'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'autopost' ,$params->get('autopost')); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_PUBLISH_NOTIFY_USERS', 'notify'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'notify', $params->get('notify', true)); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_CATEGORY', 'item_category'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.browseCategory', 'item_category', $feed->item_category); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_AUTHOR', 'item_creator'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.user', 'item_creator', $feed->item_creator); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_TEAM', 'item_team'); ?>

						<div class="col-md-7">
							<?php echo $this->html('form.team', 'item_team', $feed->item_team); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_GET_FULL_TEXT', 'item_get_fulltext'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.toggler', 'item_get_fulltext',$feed->item_get_fulltext); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_STORE_CONTENT_TYPE', 'item_content'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.dropdown', 'item_content', $feed->item_content, [
								'intro' => 'COM_EASYBLOG_FEEDS_INTROTEXT',
								'content' => 'COM_EASYBLOG_FEEDS_MAINTEXT'
							]); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FEEDS_ALLOWED_TAGS', 'item_allowed_tags'); ?>

						<div class="col-md-7">
							<textarea name="item_allowed_tags" class="form-control"><?php echo $params->get( 'allowed' , '<img>,<a>,<br>,<table>,<tbody>,<th>,<tr>,<td>,<div>,<span>,<p>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>' ); ?></textarea>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->fd->html('form.label', 'COM_EB_FEEDS_ROBOTS', 'robots'); ?>

						<div class="col-md-7">
							<?php echo $this->fd->html('form.robots', 'robots', strtoupper($params->get('robots', '')));?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>


	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $feed->id;?>" />
</form>
