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
<div class="tab-pane <?php echo $active=='quote' ? 'active' : ''; ?>" id="quote" data-quickpost-form data-type="quote">
	<form class="es-quick-quote form-horizontal">
		<div class="form-group">
			<div class="col-md-12">
				<textarea name="quote" class="form-control" placeholder="<?php echo $this->fd->html('str.escape', JText::_('COM_EASYBLOG_MICROBLOG_QUOTE_CONTENT_PLACEHOLDER', true));?>" rows="5" data-quickpost-content></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12">
				<input name="source" type="text" class="form-control" placeholder="<?php echo $this->fd->html('str.escape', JText::_('COM_EASYBLOG_MICROBLOG_QUOTE_SOURCE_PLACEHOLDER', true));?>" data-quickpost-source/>
			</div>
		</div>

		<?php echo $this->output('site/dashboard/quickpost/forms/more'); ?>
	</form>
</div>
