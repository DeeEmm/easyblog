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
defined("_JEXEC") or die("Unauthorized Access");
?>
<div class="eb-socialbuttons mt-20">
	<span class="st_sharethis_large" st_url="<?php echo $post->getPermalink(false, true);?>" st_title="<?php echo $post->title;?>" displayText="ShareThis"></span>
	<span class="st_facebook_large" st_url="<?php echo $post->getPermalink(false, true);?>" st_title="<?php echo $post->title;?>" displayText="Facebook"></span>
	<span class="st_twitter_large" st_url="<?php echo $post->getPermalink(false, true);?>" st_title="<?php echo $post->title;?>" displayText="Tweet"></span>
	<span class="st_linkedin_large" st_url="<?php echo $post->getPermalink(false, true);?>" st_title="<?php echo $post->title;?>" displayText="LinkedIn"></span>
	<span class="st_pinterest_large" st_url="<?php echo $post->getPermalink(false, true);?>" st_title="<?php echo $post->title;?>" displayText="Pinterest"></span>
	<span class="st_email_large" st_url="<?php echo $post->getPermalink(false, true);?>" st_title="<?php echo $post->title;?>" displayText="Email"></span>
</div>
