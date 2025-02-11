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
<table width="100%">
	<tr>
		<td width="40%">&nbsp;</td>
		<td width="60%">&nbsp;</td>
	</tr>

	<!-- Account -->
	<tr>
		<td colspan="2" bgcolor="#a8a4a4">
			<?php echo JText::_('COM_EB_GDPR_ACCOUNT_DETAILS'); ?>
		</td>
	</tr>
	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_PROFILE_PICTURE'); ?>:</b></td>
		<td><img src="<?php echo $user->getAvatar(); ?>"/></td>
	</tr>
	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_REALNAME'); ?>:</b></td>
		<td><?php echo $this->escape($user->user->name); ?></td>
	</tr>
	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_WHAT_OTHERS_CALL_YOU'); ?>:</b></td>
		<td><?php echo $this->escape($user->nickname); ?></td>
	</tr>
	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_USERNAME'); ?>:</b></td>
		<td><?php echo $this->escape($user->user->username); ?></td>
	</tr>
	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_EMAIL'); ?>:</b></td>
		<td><?php echo $this->escape($user->user->email); ?></td>
	</tr>


	<!-- Other details -->
	<tr>
		<td colspan="2" bgcolor="#a8a4a4">
			<?php echo JText::_('COM_EB_GDPR_OTHER_DETAILS'); ?>
		</td>
	</tr>
	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_BLOG_TITLE'); ?>:</b></td>
		<td><?php echo $this->escape($user->title); ?></td>
	</tr>
	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_BLOG_DESC'); ?>:</b></td>
		<td><?php echo $this->escape($user->getDescription()); ?></td>
	</tr>
	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_CUSTOM_CSS'); ?>:</b></td>
		<td><?php echo $this->escape($user->custom_css); ?></td>
	</tr>
	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_BIOGRAPHICAL_INFO'); ?>:</b></td>
		<td><?php echo $this->escape($user->getBiography()); ?></td>
	</tr>
	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_WEBSITE'); ?>:</b></td>
		<td><?php echo $this->escape($user->url); ?></td>
	</tr>
	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_BLOGGER_PERMALINK'); ?>:</b></td>
		<td><?php echo $this->escape($user->permalink); ?></td>
	</tr>
	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_FACEBOOK_PROFILE_URL'); ?>:</b></td>
		<td><?php echo $this->escape($userParams->get('facebook_profile_url')); ?></td>
	</tr>

	<tr>
		<td class="left"><b><?php echo JText::_('COM_EB_FACEBOOK_PAGE_URL'); ?>:</b></td>
		<td><?php echo $this->escape($userParams->get('facebook_page_url')); ?></td>
	</tr>

	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEO_META_DESCRIPTION'); ?>:</b></td>
		<td><?php echo $this->escape($userMeta->description); ?></td>
	</tr>

	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEO_META_KEYWORDS'); ?>:</b></td>
		<td><?php echo $this->escape($userMeta->keywords); ?></td>
	</tr>

	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLEADS_CODE'); ?>:</b></td>
		<td><quote><?php echo $this->escape($userAdsense->code); ?></quote></td>
	</tr>

	<tr>
		<td class="left"><b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_FEEDBURNER_URL'); ?>:</b></td>
		<td><quote><?php echo $this->escape($userFeedburner->url); ?></quote></td>
	</tr>

</table>
