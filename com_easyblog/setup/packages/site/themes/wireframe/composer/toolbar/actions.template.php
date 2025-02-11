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
<div class="btn-group t-lg-ml--lg" data-composer-toolbar-actions data-toolbar-view="actions">
	<button type="button" class="btn btn-eb-primary dropdown-toggle_ eb-comp-toolbar__btn-publish" data-composer-save-template>
		<span data-template-button-save class="<?php echo $postTemplate->id ? 't-hidden' : ''; ?>"><?php echo JText::_('COM_EASYBLOG_SAVE_TEMPLATE_BUTTON'); ?></span>
		<span data-template-button-update class="<?php echo $postTemplate->id ? '' : 't-hidden'; ?>"><?php echo JText::_('COM_EB_UPDATE_POST_TEMPLATE'); ?></span>
	</button>
</div>
