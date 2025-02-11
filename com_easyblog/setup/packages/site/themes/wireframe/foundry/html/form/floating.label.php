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
<div class="form-group form-group--ifta" data-fd-label="<?php echo $this->fd->getName();?>">

	<?php echo $this->fd->html('form.' . $type, $name, $value, $id, [
		'class' => 'form-control',
		'attributes' =>'autocomplete="off"'
	]); ?>
	<label class="form-label" for="<?php echo $id;?>"><?php echo $label;?></label>
</div>
