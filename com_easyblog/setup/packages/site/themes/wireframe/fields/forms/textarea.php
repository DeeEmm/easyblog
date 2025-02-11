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
<textarea class="o-form-control"
		name="fields[<?php echo $field->id;?>]"
		placeholder="<?php echo $params->get('placeholder');?>"
		cols="<?php echo $params->get('cols', 10);?>"
		rows="<?php echo $params->get('rows', 5);?>"
		data-field-class-input-textarea
	><?php echo $value;?>
</textarea>
