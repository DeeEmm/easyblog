<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="popbox-dropdown">
	<div class="popbox-dropdown__hd">
		<div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EB_SOCIAL');?></div>
	</div>

	<div class="popbox-dropdown__bd">
		<div class="popbox-dropdown-nav">
			<div class="popbox-dropdown-nav__item ">
				<span class="popbox-dropdown-nav__link">
					<ol class="popbox-dropdown-nav__meta-lists">
						<?php echo $this->output('site/integrations/easysocial/toolbar.items'); ?>
					</ol>
				</span>
			</div>
		</div>
	</div>
</div>
