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
<div class="o-alert o-alert--<?php echo $class;?>" data-fd-alert>
	<div class="t-d--flex t-align-items--c">
		<div class="t-flex-grow--1">
			<?php echo $content; ?>
		</div>
		<div class="t-flex-shrink--0 t-pl--md">
			<a href="javascript:void(0);" class="o-alert__close" data-fd-dismiss="alert">×</a>
		</div>
	</div>
</div>
