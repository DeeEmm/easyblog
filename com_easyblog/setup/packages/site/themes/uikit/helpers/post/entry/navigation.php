<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ((!empty($navigation->prev) || !empty($navigation->next)) && $this->entryParams->get('post_navigation', true)) { ?>
	<ul class="uk-pagination">
		<?php if (!empty($navigation->next)) { ?>
		<li class="">
			<a href="<?php echo $navigation->next->link;?>"> 
				<span class="uk-margin-small-right" uk-pagination-previous></span>
				<?php echo $navigation->next->title;?>
			</a>
		</li>
		<?php } ?>
		<?php if (!empty($navigation->prev)) { ?>
		<li class="uk-margin-auto-left">
			<a href="<?php echo $navigation->prev->link;?>">
				<?php echo $navigation->prev->title;?>
				<span class="uk-margin-small-left" uk-pagination-next></span>
			</a>
		</li>
		<?php } ?>
	</ul>
<?php } ?>