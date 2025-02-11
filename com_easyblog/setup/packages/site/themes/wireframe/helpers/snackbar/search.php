<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and destails.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<input type="text" name="<?php echo $element; ?>"
	class="form-control t-mr--md sm:t-mb--md sm:t-mr--no"
	<?php if ($placeholder) { ?>
	placeholder="<?php echo JText::_($placeholder);?>"
	<?php } ?>

	value="<?php echo $this->fd->html('str.escape', $value);?>"
/>

<a href="javascript:void(0);" data-eb-form-search <?php echo $buttonAttributes;?> class="btn btn-default t-d--flex t-align-items--c t-px--md t-justify-content--c">
	<i class="fdi fa fa-search"></i>
</a>
