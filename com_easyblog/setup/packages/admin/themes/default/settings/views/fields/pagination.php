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

$options = [
	'-2' => 'COM_EASYBLOG_INHERIT_FROM_SETTINGS',
	'-3' => 'COM_EASYBLOG_INHERIT_FROM_JOOMLA'
];

for ($i = 5; $i <= 100; $i += 5) {
	$options[$i] = $i;
}

?>
<?php echo $this->fd->html('form.dropdown', $configKey, $this->config->get($configKey), $options); ?>
