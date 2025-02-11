<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-social-button retweet">
	<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($url);?>&amp;text=<?php echo urlencode($title);?><?php echo $via ? '&amp;via=' . $via : '';?>" target="_blank" class="eb-share-twitter-btn">
		<i class="fdi fab fa-twitter"></i>
		<span class="btn-text"><?php echo JText::_('COM_EASYBLOG_SOCIALBUTTON_TWEET');?></span>
	</a>
</div>
