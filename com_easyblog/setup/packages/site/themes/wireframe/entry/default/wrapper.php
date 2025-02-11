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
<div data-eb-posts>
	<div data-eb-posts-wrapper>
		<?php echo $this->output('site/entry/default/default' . ($protected ? '.protected' : '')); ?>
	</div>

	<?php if ($previousPostId) { ?>
	<div>
		<a class="btn btn-default btn-block" href="javascript:void(0);" data-eb-pagination-loadmore data-post-id="<?php echo $previousPostId; ?>">
			<i class="fdi fa fa-sync"></i>&nbsp;<?php echo JText::_('COM_EB_LOADMORE'); ?>
		</a>
	</div>
	<input type="hidden" name="pagination_exclude" data-eb-pagination-exclusion value="<?php echo $exclude; ?>" />
	<?php } ?>
</div>
