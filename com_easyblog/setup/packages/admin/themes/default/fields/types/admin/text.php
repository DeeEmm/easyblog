<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="form-group">
	<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FIELDS_PLACEHOLDER_VALUE', 'params[placeholder]'); ?>

	<div class="col-md-7">
		<?php echo $this->fd->html('form.text', 'params[placeholder]', $params->get('placeholder'), 'params[placeholder]'); ?>
	</div>
</div>
