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
<div class="uk-label uk-margin-small-right" >
	<i class="fdi far fa-clock"></i>
	<b><?php echo $post->getReadingTime(); ?></b>
	<span class="eb-reading-indicator__count">(<?php echo JText::sprintf('COM_EB_TOTAL_WORDS', $post->getTotalWords()); ?>)</span>
</div>
