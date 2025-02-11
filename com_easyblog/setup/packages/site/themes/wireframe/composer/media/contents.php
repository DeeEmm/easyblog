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
<?php foreach ($folder->contents as $group => $items) { ?>
<div class="eb-nmm-group eb-nmm-group--<?php echo $group;?>" data-group data-type="<?php echo $group;?>">
	<div class="eb-nmm-group__head">
		<div class="eb-nmm-group__title">
			<?php echo JText::_('COM_EASYBLOG_MM_FILEGROUP_TYPE_' . strtoupper($group)); ?>
		</div>
	</div>

	<div class="eb-nmm-group__body">
		<div class="eb-nmm-content-listing" data-group-list>
			<?php // This doesn't use site/composer/media/file.php is because it is faster this way // ?>
			<?php if ($items) { ?>
				<?php echo $this->output('site/composer/media/items', ['uri' => $folder->uri, 'items' => $items, 'nextPage' => 2]); ?>
			<?php } ?>
		</div>
	</div>
</div>
<?php } ?>
