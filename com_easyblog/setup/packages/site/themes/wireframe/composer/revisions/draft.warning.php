<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-revisions-note-popup" data-draft-warning>
	<div class="eb-revisions-note">
		<div class="eb-revisions-note__title">
			<?php echo JText::_('COM_EASYBLOG_COMPOSER_DRAFT_WARNING_NOTICE'); ?>
		</div>
		<div class="eb-revisions-note__desc">
			<?php echo JText::_('COM_EASYBLOG_COMPOSER_DRAFT_WARNING'); ?>
		</div>
		<a href="<?php echo $draftEditLink; ?>" data-draft-continue class="btn btn-eb-primary btn--lg btn-block t-lg-mb--lg"><?php echo JText::_('COM_EASYBLOG_COMPOSER_DRAFT_CONTINUE_EDIT'); ?></a>
		<a href="javascript:void(0);" data-draft-discard class="btn btn-eb-danger btn--lg btn-block"><?php echo JText::_('COM_EASYBLOG_COMPOSER_DRAFT_DISCARD_REVISION'); ?></a>
	</div>
</div>
