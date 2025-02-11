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
<div class="eb-composer-templates eb-composer-templates--googleimport t-hidden" data-eb-googleimport
	data-islegacy="<?php echo $post->isLegacy() ? 1 : 0; ?>"
	data-postid="<?php echo $post->id; ?>" 
	data-url="<?php echo EB::oauth()->getClient(EBLOG_OAUTH_GOOGLE)->getLoginUrl(); ?>"
	data-return="<?php echo EB::oauth()->getClient(EBLOG_OAUTH_GOOGLE)->getReturnUrl(); ?>"
	data-retrieve-msg="<?php echo JText::_('COM_EB_GOOGLEIMPORT_RETRIEVE_FILE_MSG', true); ?>"
	data-importing-msg="<?php echo JText::_('COM_EB_GOOGLEIMPORT_IMPORTING_FILE_MSG', true); ?>"
	data-selectfile-msg="<?php echo JText::_('COM_EB_GOOGLEIMPORT_SELECT_FILE_MSG', true); ?>"
	data-authentication-msg="<?php echo JText::_('COM_EB_GOOGLEIMPORT_AUTHENTICATION_MSG', true); ?>"
>
	<div class="eb-composer-templates-in">
		<div class="eb-composer-templates-wrap eb-composer-templates-wrap--google-import">
			<div class="eb-composer-templates-header">
				<h4><?php echo JText::_('COM_EB_GOOGLEIMPORT_TITLE');?></h4>

				<div class="muted" data-eb-googleimport-message>
					<?php echo JText::_('COM_EB_GOOGLEIMPORT_AUTHENTICATION_MSG');?>
				</div>
				<a href="javascript:void(0);" class="eb-composer-templates-header__close" data-eb-googleimport-close-popup>
					<i class="fdi fa fa-times-circle"></i>
				</a>
			</div>

			<div class="eb-composer-templates-content eb-composer-templates-content--google-import" data-eb-googleimport-content>

				<div class="eb-composer-templates-content__main t-overflow--hidden t-px--no t-pt--no is-loading" data-eb-googleimport-container>
					<div class="eb-composer-templates-content__main eb-composer-templates-content__main--user-info t-overflow--hidden">

						<div data-eb-googleimport-userinfo-wrapper>
							<div data-eb-googleimport-userinfo-loader>
								<div class="o-loader o-loader--sm o-loader--inline is-active"></div>&nbsp;<?php echo JText::_('COM_EB_GOOGLEIMPORT_RETRIEVING_USERINFO'); ?>
							</div>
							<div data-eb-googleimport-userinfo></div>
						</div>
					</div>

					<div class="o-loader-wrapper">
						<div class="o-loader o-loader--inline"></div>
					</div>
					<div class="eb-googleimport-iframe-wrapper">
						<iframe class="t-hidden" src="about:blank" style="border:none;" width="100%" height="100%" scrolling="no" data-eb-googleimport-iframe></iframe>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
