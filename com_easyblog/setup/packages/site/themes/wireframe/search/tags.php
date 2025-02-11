<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-dropdown-menu-hd">
	<b><?php echo JText::_('COM_EB_SEARCH_TAG_FILTER_LABEL'); ?></b>
</div>
<div class="t-px--xs t-py--md">
	<div class="l-cluster l-spaces--xs t-mb--lg">
		<div>
		<?php if ($tags) { ?>
			<?php foreach ($tags as $tag) { ?>
			<div class="t-min-width--0">
				<div class="t-d--flex t-text--truncate">
					<a href="javascript:void(0);" class="eb-filter-label <?php echo (in_array($tag->id, $activeTagIds)) ? 'is-active': ''; ?>" 
						data-eb-tags-filter data-id="<?php echo $tag->id; ?>">
						<?php echo $tag->title; ?>
					</a>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
		</div>
	</div>
	<div>
		<a href="javascript:void(0);" class="fd-link" data-eb-tags-clear><?php echo JText::_('COM_EB_SEARCH_TAG_CLEAR_SELECTION'); ?></a>
	</div>
</div>
