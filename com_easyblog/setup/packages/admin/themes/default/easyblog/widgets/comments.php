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
<div class="db-stream-graph">
	<div data-chart-comments style="height: 200px; width: 100%;"></div>
	<div data-chart-comments-legend></div>
</div>

<?php echo $this->fd->html('adminwidgets.comments', $comments, 'COM_EASYBLOG_DASHBOARD_NO_COMMENTS_YET'); ?>
