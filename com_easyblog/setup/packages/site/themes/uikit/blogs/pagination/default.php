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

<ul class="uk-pagination uk-flex-center uk-margin-medium-top">
	<li class="<?php echo !$data->start->link ? '' : ' disabled';?>">
		<a href="<?php echo $data->start->link ? $data->start->link : 'javascript:void(0);';?>" class="">
			&nbsp; <i class="fdi fa fa-fast-backward"></i> &nbsp;
		</a>
	</li>

	<?php if($data->previous->link) { ?>
		<li class="disabled">
			<a href="<?php echo EB::uniqueLinkSegments($data->previous->link); ?>" rel="prev">
				<i class="fdi fa fa-chevron-left"></i> <?php echo JText::_('COM_EASYBLOG_PAGINATION_PREVIOUS');?>
			</a>	
		</li>
	<?php } else { ?>
		<li>
			<a href="javascript:void(0);" class="">
				<i class="fdi fa fa-chevron-left"></i> <?php echo JText::_('COM_EASYBLOG_PAGINATION_PREVIOUS');?>
			</a>	
		</li>
	<?php } ?>

	<?php foreach ($data->pages as $page) { ?>
		<?php if ($page->link) { ?>
			<li>
				<a href="<?php echo EB::uniqueLinkSegments($page->link); ?>"><?php echo $page->text;?></a>
			</li>
		<?php } else { ?>
			<li class="uk-active">
				<a class=""><?php echo $page->text;?></a>	
			</li>
			
		<?php } ?>
	<?php } ?>
	
	
	<?php if($data->next->link) { ?>
		<li class="disabled">
			<a href="<?php echo EB::uniqueLinkSegments( $data->next->link ); ?>" rel="next" >
				<?php echo JText::_('COM_EASYBLOG_PAGINATION_NEXT'); ?> <i class="fdi fa fa-chevron-right"></i>
			</a>	
		</li>
	<?php } else { ?>
		<li>
			<a href="javascript:void(0);" class="">
				<?php echo JText::_('COM_EASYBLOG_PAGINATION_NEXT');?><i class="fdi fa fa-chevron-right"></i>
			</a>	
		</li>
	<?php } ?>

	<li class="<?php echo !$data->end->link ? '' : ' disabled';?>">
		<a href="<?php echo $data->end->link ? $data->end->link : 'javascript:void(0);';?>" class=" ">
			&nbsp; <i class="fdi fa fa-fast-forward"></i> &nbsp;
		</a>	
	</li>
	
	

	<!-- <li><a href="#">1</a></li>
	<li class="uk-disabled"><span>...</span></li>
	<li><a href="#">5</a></li>
	<li><a href="#">6</a></li>
	<li class="uk-active"><span>7</span></li>
	<li><a href="#">8</a></li>
	 -->

	<!-- <li class=""><a href="#">Next <span class="uk-margin-small-left" uk-pagination-next></span></a></li> -->
</ul>

