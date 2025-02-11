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
<div class="eb-composer-templates eb-composer-templates--template">
	<div class="eb-composer-templates-in">
	<div class="eb-composer-templates-wrap">
		<div class="eb-composer-templates-header">
			<h4><?php echo JText::_('COM_EASYBLOG_COMPOSER_TEMPLATES_HEADING');?></h4>
			<div class="muted">
				<?php echo JText::_('COM_EASYBLOG_COMPOSER_TEMPLATES_HEADING_INFO');?>
			</div>
			<a href="javascript:void(0);" class="eb-composer-templates-header__close" data-template-close-popup>
				<i class="fdi fa fa-times-circle"></i>	
			</a>
		</div>
		<div class="eb-composer-templates-content">
			<div class="eb-composer-templates-content__main">

				<?php if ($postTemplates) { ?>
					<?php foreach ($postTemplates as $postTemplate) { ?>
					<div class="template text-center"
							data-template-item
							data-uid="<?php echo $postTemplate->id;?>"
							data-blank="<?php echo $postTemplate->isBlank() ? '1' : '0';?>"
						>
						<div class="template-thumb" style="background-image: url(<?php echo $postTemplate->getThumbnails(true);?>);">
							<div class="template-control">
								<a href="javascript:void(0);" class="template-pick" title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_TEMPLATES_SELECT_TEMPLATE');?>">
									<i class="fdi fa fa-check"></i>
								</a>
							</div>

						</div>
						<div class="template-name">
							<?php echo JText::_($postTemplate->title);?>
						</div>
						<div class="template-dob text-muted">&nbsp;</div>
					</div>
					<?php } ?>
				<?php } ?>
			</div>
			<?php if ($googleImportEnabled) { ?>
			<div class="eb-composer-templates-content__sub">
				<div class="">
					<div class="t-mb--sm t-text--center t-text--500"><?php echo JText::_('COM_EB_GOOGLEIMPORT_COMPOSER_TITLE'); ?></div>

					<?php echo $this->fd->html('button.google', 'COM_EB_GOOGLEIMPORT_COMPOSER_TITLE_INFO', [
						'attributes' => 'data-template-googleimport'
					]); ?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	</div>
</div>
