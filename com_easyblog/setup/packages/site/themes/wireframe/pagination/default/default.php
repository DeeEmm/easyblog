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
<div class="eb-pager <?php echo $this->isMobile() ? 'is-mobile' : ''; ?>">
	<a href="<?php echo $data->start->link ? $data->start->link : 'javascript:void(0);'; ?>" class="eb-pager__fast-first-link <?php echo $data->start->link ? '' : 'disabled';?>">
		&nbsp; <i class="fdi fa fa-fast-backward"></i> &nbsp;
	</a>

	<a href="<?php echo $data->previous->link ? EB::uniqueLinkSegments($data->previous->link) : 'javascript:void(0);';?>" <?php echo $data->previous->link ? 'rel="prev"' : ''; ?> class="eb-pager__pre-link <?php echo $data->previous->link ? '' : 'disabled'; ?>">
		<i class="fdi fa fa-chevron-left"></i><?php echo JText::_('COM_EASYBLOG_PAGINATION_PREVIOUS');?>
	</a>

	<a href="<?php echo $data->end->link ? $data->end->link : 'javascript:void(0);';?>" class="eb-pager__fast-last-link <?php echo $data->end->link ? '' : 'disabled';?>">
		&nbsp; <i class="fdi fa fa-fast-forward"></i> &nbsp;
	</a>

	<a href="<?php echo $data->next->link ? EB::uniqueLinkSegments($data->next->link) : 'javascript:void(0);';?>" <?php echo $data->next->link ? 'rel="next"' : ''; ?> class="eb-pager__next-link <?php echo $data->next->link ? '' : 'disabled'; ?>">
		<?php echo JText::_('COM_EASYBLOG_PAGINATION_NEXT');?><i class="fdi fa fa-chevron-right"></i>
	</a>

	<div class="eb-pager__link-list">
		<?php foreach ($data->pages as $page) { ?>
			<?php if ($page->link) { ?>
				<a href="<?php echo EB::uniqueLinkSegments($page->link); ?>"><?php echo $page->text;?></a>
			<?php } else { ?>
				<a class="disabled active"><?php echo $page->text;?></a>
			<?php } ?>
		<?php } ?>
	</div>
</div>