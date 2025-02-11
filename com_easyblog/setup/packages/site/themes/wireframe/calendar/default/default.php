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
<div class="t-mb--lg">
	<?php echo $this->html('snackbar.standard', $date->format(JText::_('COM_EB_CALENDAR_HEADING')), [
		call_user_func(function() use ($listViewUrl) {

		ob_start();
		?>
		<a href="<?php echo $listViewUrl;?>" class="eb-calendar-topbar__toggle" data-calendar-toggle-view><?php echo JText::_('COM_EASYBLOG_SWITCH_TO_LIST_VIEW'); ?></a>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();

			return $contents;
		})
	]);?>
</div>

<div class="eb-calendar <?php echo $this->isMobile() ? 'is-mobile' : '';?>" data-calendar-container></div>

<div style="display: none;" data-calendar-loader-template>
	<div class="eb-empty eb-calendar-loader" data-calender-loader>
		<i class="fdi fa fa-sync fa-spin"></i> <span><?php echo JText::_('COM_EASYBLOG_CALENDAR_LOADING_CALENDAR'); ?></span>
	</div>
</div>
