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
<div id="eb" class="eb-mod mod-easyblogquickpost mod-items-compact<?php echo $modules->getWrapperClass();?>">
	<div class="mod-welcome-action">
		<div class="eb-mod-item">
		   <a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=standard');?>">
				<span class="mod-cell">
					<i class="fdi fa fa-pencil-alt mod-muted"></i>
				</span>
				<span class="mod-cell"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_STANDARD');?></span>
			</a>
		</div>

		<div class="eb-mod-item">
			<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=photo');?>">
				<span class="mod-cell"><i class="fdi fa fa-camera mod-muted"></i></span>
				<span class="mod-cell"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_PHOTO');?></span>
			</a>
		</div>
		<div class="eb-mod-item">
			<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=video');?>">
				<span class="mod-cell"><i class="fdi fa fa-film mod-muted"></i></span>
				<span class="mod-cell"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_VIDEO');?></span>
			</a>
		</div>
		<div class="eb-mod-item">
			<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=quote');?>">
				<span class="mod-cell"><i class="fdi fa fa-quote-left mod-muted"></i></span>
				<span class="mod-cell"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_QUOTE');?></span>
			</a>
		</div>
		<div class="eb-mod-item">
			<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=link');?>">
				<span class="mod-cell"><i class="fdi fa fa-link mod-muted"></i></span>
				<span class="mod-cell"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_LINK');?></span>
			</a>
		</div>
	</div>
</div>

