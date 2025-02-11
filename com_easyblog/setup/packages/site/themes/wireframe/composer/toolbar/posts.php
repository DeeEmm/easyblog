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
<div class="btn-group" data-toolbar-view data-type="posts">
	<button type="button" class="btn eb-comp-toolbar__nav-btn dropdown-toggle_" data-eb-composer-insert-post
		data-eb-provide="tooltip"
		data-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_SIDEBAR_TITLE_POSTS');?><?php echo $this->html('composer.shortcut', ['shift', 'p']); ?>"
		data-html="1"
		data-placement="bottom">
		<i class="fdi far fa-file-alt fa-fw"></i>
	</button>

	<div class="dropdown-menu eb-comp-toolbar-dropdown-menu eb-comp-toolbar-dropdown-menu--posts" data-posts-container>
		<div class="eb-comp-toolbar-dropdown-menu__hd">
			<div class="eb-comp-toolbar-dropdown-menu__icon-container t-lg-mr--md">
				<i class="fdi far fa-file-alt fa-fw"></i>
			</div>
			<?php echo JText::_('COM_EASYBLOG_COMPOSER_SIDEBAR_TITLE_POSTS');?>
			<div class="eb-comp-toolbar-dropdown-menu__hd-action">
				<a href="javascript:void(0);" class="eb-comp-toolbar-dropdown-menu__close" data-toolbar-dropdown-close>
					<i class="fdi fa fa-times-circle"></i>
				</a>
			</div>
		</div>
		<div class="eb-comp-toolbar-dropdown-menu__bd">

			<div class="eb-comp-toolbar-dropdown-menu__search">
				<div class="o-form-control-icon t-text--500" data-fa-icon="&#xf002;">
					<input type="text" placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_SEARCH_POSTS');?>" data-posts-search class="o-form-control"/>
				</div>
			</div>

			<div class="eb-comp-toolbar-posts" data-posts-listing-container>
				<div class="o-empty">
					<div class="o-empty__content">
						<i class="o-empty__icon far fa-file-alt"></i>
						<div class="o-empty__text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_POSTS_EMPTY');?></div>
						<div class="o-empty__text" data-posts-empty-message><?php echo JText::_('COM_EASYBLOG_COMPOSER_NO_POSTS_FOUND'); ?></div>
					</div>
				</div>
				<div class="o-loader"></div>

				<div data-posts-listing>
				</div>
			</div>
		</div>
	</div>
</div>
