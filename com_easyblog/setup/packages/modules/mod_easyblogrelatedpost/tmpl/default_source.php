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
<?php if ($params->get('showauthor', true) || $params->get('showdate', true)) { ?>
<div class="mod-small mod-muted" >
	<?php if ($params->get('showauthor', true)) { ?>
		<div class="mod-cell eb-mod-media-body">
			<strong><a href="<?php echo $post->creator->getProfileLink(); ?>" class="eb-mod-media-title"><?php echo $post->creator->getName();?></a></strong>
		</div>
	<?php } ?>

	<?php if ($params->get('showdate' , true)) { ?>
		<div class="mod-muted mod-small">
			<?php echo $post->getDisplayDate($params->get('sourcedate', ''))->format($params->get('dateformat', JText::_('DATE_FORMAT_LC3'))); ?>
		</div>
	<?php } ?>
</div>
<?php } ?>
