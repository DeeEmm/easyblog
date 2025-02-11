<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="fd">
<div class="o-pagination-wrapper px-xs py-2xs flex items-center justify-center mb-sm mt-sm" 
	data-fd-pagination 
	data-fd-extension="<?php echo $this->fd->getName();?>"
>
	<div class="o-pagination">
		<?php echo $this->output('pagination/footer/list', [
			'data' => $data, 
			'isLink' => $isLink
		]); ?>

		<?php if (!$isLink) { ?>
		<?php echo $this->fd->html('form.hidden', $pagination->prefix . 'limitstart', $pagination->limitstart, '', 'data-fd-pagination-limitstart'); ?>
		<?php } ?>
	</div>
</div>
</div>
