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
<div class="eb-entry-author uk-margin-small-bottom">
	<h4 class="uk-heading-divider">
		<?php echo JText::_('COM_EASYBLOG_ABOUT_THE_AUTHOR');?>
	</h4>

	<div class="eb-entry-author-bio cell-top uk-margin-small-bottom">
		<?php if ($this->entryParams->get('post_author_box_avatar', true)) { ?>
		<div class="col-cell pr-15">
			<?php echo $this->html('avatar.user', $post->getAuthor()); ?>
		</div>
		<?php } ?>

		<div class="col-cell">
			<?php if ($params->get('post_author_box_title', true)) { ?>
			<h3 class="eb-authors-name reset-heading">
				<a href="<?php echo $post->getAuthorPermalink();?>"><?php echo $post->getAuthorName();?></a>
			</h3>
			<?php } ?>

			<?php if (EB::points()->hasIntegrations()) { ?>
			<div class="eb-points">
				<?php echo EB::points()->html($post->creator); ?>
			</div>
			<?php } ?>

			<div class="eb-entry-author-meta muted fd-cf">

				<?php if ($post->creator->getWebsite() && $params->get('post_author_box_website', true)) { ?>
				<span class="eb-authors-url">
					<a href="<?php echo $this->escape($post->creator->getWebsite()); ?>" target="_blank" class="author-url" rel="nofollow"
						data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EB_VISIT_WEBSITE', true);?>" data-fd-tooltip-placement="top"
					>
						<i class="fdi fa fa-globe"></i>
					</a>
				</span>
				<?php } ?>

				<?php if ($this->acl->get('allow_subscription') && $this->config->get('main_bloggersubscription') && $post->getAuthor()->getAcl()->get('add_entry')) { ?>
				<span>
					<a class="<?php echo $subscribed ? 'hide' : ''; ?>" href="javascript:void(0);" data-blog-subscribe data-type="blogger" data-id="<?php echo $post->getAuthor()->id;?>"
						data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_BLOGGER', true);?>" data-fd-tooltip-placement="top"
					>
						<i aria-hidden="true" class="fdi fa fa-envelope"></i>
						<span class="sr-only"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_BLOGGER'); ?></span>
					</a>
					<a class="<?php echo $subscribed ? '' : 'hide'; ?>" href="javascript:void(0);" data-blog-unsubscribe data-type="blogger" data-subscription-id="<?php echo $subscribed;?>"
						data-fd-tooltip data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_TO_BLOGGER', true);?>" data-fd-tooltip-placement="top"
					>
						<i aria-hidden="true" class="fdi fa fa-envelope"></i>
						<span class="sr-only"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_TO_BLOGGER'); ?></span>
					</a>
				</span>
				<?php } ?>

				<?php if ($params->get('post_author_box_view_profile', true)) { ?>
				<span>
					<a href="<?php echo $post->getAuthorPermalink();?>">
						<i class="fdi fa fa-user"></i>
					</a>
				</span>
				<?php } ?>

				<?php if (!$this->my->guest && EB::messaging()->hasMessaging($post->creator->id)) { ?>
				<span>
					<?php echo EB::messaging()->html($post->creator);?>
				</span>
				<?php } ?>
			</div>

			<?php if (EB::achievements()->hasIntegrations()) { ?>
			<div class="eb-achievements mt-10">
				<?php echo EB::achievements()->html($post->creator); ?>
			</div>
			<?php } ?>
		</div>

		<?php if ($post->creator->getBioGraphy() && $params->get('post_author_box_biography', true)) { ?>
		<div class="eb-entry-author-details">
			<?php echo $post->creator->getBioGraphy(); ?>
		</div>
		<?php } ?>
	</div>

	<?php if ($params->get('post_author_recent', true) && $recentPosts) { ?>
	<div class="uk-section uk-section-muted uk-padding">
		<div class="uk-container">
			<div class="uk-grid">
				<div class="uk-width-expand@m uk-first-column">
					<h5 class="uk-h3"><?php echo JText::_('COM_EASYBLOG_AUTHOR_RECENT_POSTS');?></h5>
				</div>
				<div class="uk-width-auto@m">
					<?php if ($params->get('post_author_box_more_posts', true)) { ?>
					<span class="col-cell text-right">
						<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $post->creator->id);?>"><?php echo JText::_('COM_EASYBLOG_ABOUT_AUTHOR_VIEW_MORE_POSTS');?></a>
					</span>
					<?php } ?>
				</div>
			</div>

			<ul class="uk-list uk-list-divider">
			<?php foreach ($recentPosts as $recentPost) { ?>
				<li>
					<div class="uk-grid-small uk-grid-match" uk-grid>
						<div class="uk-width-expand@m" uk-leader>
							<a href="<?php echo $recentPost->getPermalink();?>">
								<span uk-icon="icon: file-text" class="uk-margin-small-right"></span>
								<span><?php echo $recentPost->title;?></span>
							</a>
						</div>
						<div>
							<time ><?php echo $recentPost->getDisplayDate($params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?></time>
						</div>

					</div>
				</li>
			<?php } ?>
			</ul>
		</div>
	</div>
	<?php } ?>
</div>
