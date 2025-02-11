<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<br>
<br>
<form method="post" action="<?php echo JRoute::_('index.php');?>" data-eb-dashboard-posts>
<div class="row">
	<div class="col-md-6">
		<p class="muted text-small" style="text-transform: uppercase"><?php echo JText::_( 'COM_EASYBLOG_HISTORY_SAVED_DATE' );?></p>
		<div><?php echo $currentData->modified;?></div>
	</div>
	<div class="col-md-6">
		<a class="btn btn-default btn-sm pull-right" data-use-version data-version-id="<?php echo $versionId; ?>" data-blog-id="<?php echo $blogId; ?>"><?php echo JText::_('COM_EASYBLOG_HISTORY_USE_THIS_VERSION');?></a>
		<p class="muted text-small" style="text-transform: uppercase"><?php echo JText::_( 'COM_EASYBLOG_HISTORY_SAVED_DATE' );?></p>
		<div><?php echo $compareData->modified;?></div>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-md-6">
		<p class="muted text-small" style="text-transform: uppercase"><?php echo JText::_( 'COM_EASYBLOG_HISTORY_TITLE' );?></p>
		<div><b><?php echo $currentData->title;?></b></div>
	</div>
	<div class="col-md-6">
		<p class="muted text-small" style="text-transform: uppercase"><?php echo JText::_( 'COM_EASYBLOG_HISTORY_TITLE' );?></p>
		<div><b><?php echo $compareData->title;?></b></div>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-lg-6">
        <p class="muted text-small" style="text-transform: uppercase"><?php echo JText::_( 'COM_EASYBLOG_HISTORY_CONTENT' );?></p>
		<div class="eb-version-content">
			<?php echo $currentData->intro;?>
		</div>
	</div>

	<div class="col-lg-6">
		<p class="muted text-small" style="text-transform: uppercase"><?php echo JText::_( 'COM_EASYBLOG_HISTORY_CONTENT' );?></p>
		<div class="eb-version-content">
        	<?php //echo $diffArr['new'];?>

        	<?php echo $diff;?>
		</div>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-lg-6">
        <p class="muted text-small" style="text-transform: uppercase"><?php echo JText::_( 'COM_EASYBLOG_HISTORY_CATEGORY' );?></p>
		<div>
			<?php echo $dataArr['catOld'];?>
		</div>
	</div>

	<div class="col-lg-6">
		<p class="muted text-small" style="text-transform: uppercase"><?php echo JText::_( 'COM_EASYBLOG_HISTORY_CATEGORY' );?></p>
		<div>
        	<?php echo $dataArr['catNew'];?>
		</div>
	</div>
</div>

</form>
