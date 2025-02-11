<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-nmm-google" data-eb-googleimport-list-wrapper>

	<div class="t-lg-p--lg t-hidden" data-eb-googleimport-sub-message>
		<?php echo JText::_('COM_EB_GOOGLEIMPORT_IMPORTING_FILE_INSTRUCTION'); ?>
	</div>

	<div class="o-loader-wrapper">
		<div class="o-loader o-loader--inline"></div>
	</div>

	<div class="eb-comp-toolbar-dropdown-menu__search" data-eb-googleimport-list-search>
		<form name="googleSearch" method="POST" action="<?php echo JRoute::_('index.php');?>" data-eb-googleimport-search-form>
			<div class="t-d--flex t-align-items--c">
				<div class="t-flex-grow--1 t-pr--md">
					<div class="o-form-control-icon t-text--500" data-fa-icon="&#xf002;">
						<input type="text" name="search" data-eb-googleimport-search-input value="<?php echo $search; ?>" placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_SEARCH_POSTS');?>" class="o-form-control"/>
					</div>
				</div>
				<button type="button" class="btn btn-primary btn-search-submit" data-eb-googleimport-search-button><?php echo JText::_('COM_EASYBLOG_SEARCH'); ?></button>
			</div>
			<input type="hidden" name="layout" value="googleFileList" />
			<input type="hidden" name="tmpl" value="component" />
			<input type="hidden" name="nextPageToken" value="" data-eb-googleimport-token-input />
			<?php echo $this->fd->html('form.action', '', '', 'composer'); ?>
		</form>
	</div>

	<div data-eb-googleimport-list-contents class="eb-comp-googleimport-posts <?php echo $files ? '' : ' is-empty'; ?>">
		<div class="o-empty">
			<div class="o-empty__content">
				<i class="o-empty__icon far fa-file-alt"></i>
				<div class="o-empty__text" data-eb-googleimport-list-emptymsg>
					<?php echo $search ? JText::_('COM_EB_GOOGLEIMPORT_SEARCH_NOT_FOUND') : JText::_('COM_EB_GOOGLEIMPORT_FILE_NOT_AVAILABLE');?>
				</div>
			</div>
		</div>

		<div class="o-loader-wrapper">
			<div class="o-loader o-loader--inline"></div>
		</div>

		<?php if ($files) { ?>
			<div class="eb-composer-googleimport-lists" data-eb-googleimport-list-items>
				<?php echo $this->output('site/googleimport/default/default.filelist'); ?>
			</div>

			<?php if ($nextPage) { ?>
			<div class="eb-pagination t-px--xs t-pt--sm" data-eb-googleimport-pagination>
				<a href="javascript:void(0);" data-eb-googleimport-loadmore data-token="<?php echo $nextPage; ?>" class="btn btn-eb-default-o btn-block">
					<?php echo JText::_('COM_EB_LOADMORE'); ?>
				</a>
			</div>
			<?php } ?>

		<?php } ?>
	</div>
</div>
