<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<script type="text/javascript">
	EasyBlog.require()
	.script('site/vendors/ratings')
	.done(function($) {
	    $('.blog-ratings [data-rating-form]').implement(EasyBlog.Controller.Ratings);
	});
</script>

<?php if (!$ajax) { ?>
	<script type="text/javascript">
		EasyBlog.ready(function($){
			window.readmoreBlogs = function(){
				var limit = $('#easyblog-limit').val();
				$.ajax({
					url: '<?php echo JURI::root();?>index.php?easyblog_external=1&no_html=1&tmpl=component&id=<?php echo $id;?>&showmoreBlogs=' + limit,
					success: function(data){
						var obj = $.parseJSON(data);

						if (!obj.hasMore) {
							$('#activity-more').hide();
						}

						if (obj.html != '') {
							$('ul.blog-items').append(obj.html);
						}

						// Update the limitstart
						$('#easyblog-limit').val(obj.limitstart);
					}
				});
			}
		});
	</script>

	<div class="joms-module__wrapper">
		<div class="joms-tab__bar active">
			<a class="active"><?php echo JText::_('GROUP_EASYBLOG_JS_GROUP_BLOGS');?></a>
		</div>

		<div class="joms-tab__content">
			<div class="app-box" id="community-group-blogs">

				<div class="app-box-content">
					<div id="ezblog-body">
						<ul class="blog-items reset-ul">
<?php } ?>

						<?php if ($showRss){ ?>
							<div class="blog-group-blog-rss">
								<a href="<?php echo EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=latest');?>&amp;jsevent=<?php echo $id;?>" class="subscribe-rss" target="_blank">
									<?php echo JText::_('GROUP_EASYBLOG_SUBSCRIBE_TO_RSS'); ?>
								</a>
							</div>
						<?php } ?>


						<?php if ($posts) { ?>
							<?php foreach ($posts as $post) { ?>
							<li class="js-blog-item">

								<?php if ($params->get('avatar', 1) == '1') { ?>
									<div class="js-blog-date">
										<div class="blog-date-d"><?php echo $post->day; ?></div>
										<div class="blog-date-m"><?php echo $post->month; ?></div>
									</div>
								<?php } ?>

								<?php if ($params->get('avatar', 1) == '2') { ?>
									<a href="<?php echo $post->getAuthor()->getPermalink();?>" class="js-blog-avatar">
										<img src="<?php echo $post->jomsocialUser->getAvatar(); ?>" alt="<?php echo $post->jomsocialUser->getDisplayName();?>" class="avatar" width="50" height="50" />
									</a>
								<?php } ?>

								<div class="js-blog-body">
									<div class="js-blog-head">
										<div class="js-blog-meta small">
										<?php if ($params->get('author')) { ?>
											<?php echo JText::_('COM_EASYBLOG_POSTED_BY');?>
											<a href="<?php echo $post->getAuthor()->getPermalink();?>"><?php echo $post->jomsocialUser->getDisplayName();?></a>
										<?php } ?>

										<?php if ($params->get('categories')) { ?>
											<?php echo JText::sprintf('COM_EASYBLOG_IN', $post->getPrimaryCategory()->getPermalink(), $post->getPrimaryCategory()->title ); ?>
										<?php } ?>

										<?php if ($params->get('avatar', 1) != '1') { ?>
											<?php echo JText::_('COM_EASYBLOG_ON');?> <?php echo $post->systemDate; ?>
										<?php } ?>

										</div>

										<h3 class="js-blog-title">
											<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
										</h3>
									</div>

									<?php if ($post->hasImage()) { ?>
										<a href="<?php echo $post->getPermalink();?>" title="<?php echo $this->escape($post->title);?>" class="blog-image float-l mrm mbm">
											<img src="<?php echo $post->getImage('medium');?>" alt="<?php echo $this->escape($post->title);?>" />
										</a>
									<?php } ?>

									<?php if ($params->get('contents')) { ?>
										<div class="js-blog-content">
											<?php echo $post->getIntro(); ?>
										</div>
									<?php } ?>

									<div class="js-blog-more">
										<a href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING'); ?></a>
									</div>
								</div>
							</li>
							<?php } ?>
						<?php } ?>
<?php if (!$ajax) { ?>
						</ul>

						<?php if ($total == 0) { ?>
							<div class="joms-tab__content">
								<?php echo JText::_('GROUP_EASYBLOG_NO_BLOGS_YET');?>
							</div>
						<?php } ?>

						<?php if ($total > $limit && $total != 0 && $limit != null) { ?>
							<div id="activity-more" class="joms-newsfeed-more">
								<a onclick="readmoreBlogs();" href="javascript:void(0);" class="more-activity-text"><?php echo JText::_('COM_EASYBLOG_MORE_BLOGS');?></a>
								<div class="loading"></div>
							</div>
						<?php } ?>
						<input type="hidden" name="easyblog-limit" id="easyblog-limit" value="<?php echo $limit; ?>" />
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
