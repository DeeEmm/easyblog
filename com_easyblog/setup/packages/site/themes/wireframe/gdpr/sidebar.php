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
<ul class="gdpr-nav">
	<?php foreach ($tabs as $tab) { ?>
		<li><a href="<?php echo $tab . '.html'; ?>"><?php echo JText::_('COM_EB_GDPR_TAB_' . strtoupper($tab) . '_TITLE'); ?></a></li>
	<?php } ?>
</ul>
