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
<script type="text/javascript">
EasyBlog.require()
.script('site/vendors/ticker')
.done(function($) {
	$('[data-eb-module-ticker] [data-mod-ticker-items]').ticker({
		titleText: "<?php echo JText::_('MOD_EASYBLOGTICKER_HEADLINE_TEXT', true);?>",
		direction: "<?php echo $modules->doc->getDirection() == 'rtl' ? 'rtl' : '';?>"
	});
});
</script>
<div id="eb" class="eb-mod mod_easyblogticker<?php echo $params->get('moduleclass_sfx'); ?>" data-eb-module-ticker>
	<ul id="js-ticker" class="js-hidden" data-mod-ticker-items>
		<?php foreach ($posts as $post) { ?>
			<li class="news-item">
				<a href="<?php echo $post->getPermalink();?>">
					<?php if (!$truncateTitle || JString::strlen($post->title) <= $truncateTitle) { ?>
						<?php echo $post->title; ?>
					<?php } else { ?>
						<?php echo JString::substr($post->title, 0, $truncateTitle) . JText::_('COM_EASYBLOG_ELLIPSES'); ?>
					<?php } ?>
				</a>
			</li>
		<?php } ?>
	</ul>
</div>
