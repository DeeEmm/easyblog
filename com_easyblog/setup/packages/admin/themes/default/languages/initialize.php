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
<form name="adminForm" id="adminForm" method="post" data-table-grid>
	<div class="panel languages-loader">
		<div class="panel-body">
			<i class="fdi fa fa-sync fa-spin"></i>
			&nbsp;
			<?php echo JText::_('COM_EASYBLOG_INITIALIZING_LANGUAGE_LIST');?>
			<div class="alert alert-danger hide" data-initialize-error style="margin-top:50px;"></div>
		</div>
	</div>
</form>
