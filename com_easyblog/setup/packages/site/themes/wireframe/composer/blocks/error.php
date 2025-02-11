<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-placeholder-fail">
	<div class="row-table">
		<div class="col-cell">
			<p data-eb-mm-upload-error-text></p>
			<a href="javascript:void(0);" class="btn btn--sm btn-eb-primary" data-eb-mm-upload-error-retry><?php echo JText::_('COM_EASYBLOG_TRY_AGAIN_BUTTON');?></a>
		</div>
	</div>
</div>