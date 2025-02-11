<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

if (!isset($classname)) {
	$classname = '';
} else {
	$classname = ' ' . $classname;
}
if (!isset($links)) $links = array();
?>
<div class="xeb-links <?php echo $classname; ?>" data-type="links" data-eb-links>
	<div class="eb-link-item-group" data-eb-link-item-group>
		<?php
			if (count($links) < 1) {
				// Output one empty link form
				echo $this->output('site/composer/fields/link', array('classname' => 'is-blank'));
			} else {
				foreach ($links as $link) {
					echo $this->output('site/composer/fields/link', $link);
				}
			}
		?>
	</div>
</div>
