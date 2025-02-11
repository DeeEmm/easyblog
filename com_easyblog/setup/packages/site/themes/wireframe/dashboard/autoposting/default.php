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
<form method="post" action="<?php echo JRoute::_('index.php');?>" class="form-horizontal" enctype="multipart/form-data">
	<?php if ($this->acl->get('update_twitter') && $this->config->get('integrations_twitter') && $this->config->get('integrations_twitter_centralized_and_own')) {?>
		<?php echo $this->output('site/dashboard/autoposting/twitter'); ?>
	<?php } ?>

	<?php if ($this->acl->get('update_linkedin') && $this->config->get('integrations_linkedin') && $this->config->get('integrations_linkedin_centralized_and_own')) {?>
		<?php echo $this->output('site/dashboard/autoposting/linkedin'); ?>
	<?php } ?>

	<div class="form-actions ">
		<div class="pull-right">
			<button class="btn btn-primary">
				<i class="fdi fa fa-save"></i>&nbsp; <?php echo JText::_('COM_EB_AUTOPOSTING_SAVE'); ?>
			</button>
		</div>
	</div>

	<?php echo $this->fd->html('form.action', 'autoposting.save'); ?>
</form>
