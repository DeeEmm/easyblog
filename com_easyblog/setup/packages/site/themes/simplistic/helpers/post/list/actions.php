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
<?php if ($this->config->get('main_ratings') && $this->params->get('post_ratings', true)) { ?>
<div class="eb-post-actions">
	<div class="eb-post-rating">
		<?php echo $this->output('site/ratings/frontpage', array('post' => $post, 'locked' => $this->config->get('main_ratings_frontpage_locked'))); ?>
	</div>
</div>
<?php } ?>
