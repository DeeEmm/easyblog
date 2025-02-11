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
<div class="dropdown_ eb-comp-toolbar__dropdown eb-comp-toolbar__revisions" data-toolbar-view data-type="revisions">
	<span><?php echo JText::_('COM_EASYBLOG_COMPOSER_HISTORY');?> /</span>

	<button type="button" class="eb-comp-toolbar__btn-revision dropdown-toggle_ t-text--800" data-composer-toolbar-revisions
		data-eb-provide="tooltip"
		data-html="1"
		data-placement="bottom"
		title="<?php echo JText::_('COM_EB_POST_REVISIONS');?><?php echo $this->html('composer.shortcut', ['shift', 'h']); ?>"
	>
		Initial Post <i class="fdi fa fa-chevron-down t-lg-ml--md"></i>
	</button>

	<div class="dropdown-menu eb-comp-toolbar-dropdown-menu eb-comp-toolbar-dropdown-menu--revisions" data-revisions-container>
		<div class="eb-comp-toolbar-dropdown-menu__hd">
			<div class="eb-comp-toolbar-dropdown-menu__icon-container t-lg-mr--md">
				<i class="fdi far fa-clock fa-fw"></i>
			</div>
			<?php echo JText::_('COM_EASYBLOG_COMPOSER_HISTORY');?>
			<div class="eb-comp-toolbar-dropdown-menu__hd-action">
				<?php if ($post->canPurgeRevisions() && $post->getRevisionCount('all') > 1) { ?>
					<a href="javascript:void(0);" class="btn btn-eb-default btn--xs" data-eb-revision-purge><?php echo JText::_('COM_EASYBLOG_CLEAR_HISTORY');?></a>
				<?php } ?>

				<a href="javascript:void(0);" class="eb-comp-toolbar-dropdown-menu__close" data-toolbar-dropdown-close>
					<i class="fdi fa fa-times-circle"></i>
				</a>
			</div>

		</div>
		<div class="eb-comp-toolbar-dropdown-menu__bd">
			<div class="eb-comp-revisions-posts">
				<div class=" eb-revisions-list-field" data-eb-revisions-list-field>
					<div class="eb-revision-listing" data-eb-revisions-list>
						<?php echo $this->output('site/composer/revisions/list', array('revisions' => $revisions)); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
