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
<div id="eb" class="eb-mod mod-easyblogwelcome<?php echo $modules->getWrapperClass();?>">
	<?php if (!$my->guest) { ?>
		<div class="mod-welcome-profile mod-table align-middle">
			<?php if ($params->get('display_avatar', true)) { ?>
			<div class="mod-cell cell-tight">
				<a href="<?php echo $author->getProfileLink();?>" class="mod-avatar">
					<img src="<?php echo $author->getAvatar();?>" class="avatar" width="50" height="50" />
				</a>
			</div>
			<?php } ?>

			<div class="mod-cell">
				<a href="<?php echo $author->getProfileLink();?>">
					<b><?php echo $author->getName();?></b>
				</a>

				<div>
					<a href="<?php echo EB::getEditProfileLink();;?>" class="small"><?php echo JText::_( 'MOD_EASYBLOGWELCOME_SETTINGS');?></a>
				</div>
			</div>
		</div>

		<div class="mod-welcome-action">
			<?php if ($acl->get('add_entry')) { ?>
				<?php if ($config->get('main_microblog')) { ?>
				<div class="eb-mod-item">
					<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost');?>">
						<span class="mod-cell">
							<i class="mod-muted fdi fa fa-bolt"></i>
						</span>
						<span class="mod-cell">
							<?php echo JText::_('MOD_EASYBLOGWELCOME_QUICK_SHARE');?>
						</span>
					</a>
				</div>
				<?php } ?>

				<div class="eb-mod-item">
					<a href="<?php echo EB::composer()->getComposeUrl(); ?>" target="_blank" data-eb-composer>
						<span class="mod-cell">
							<i class="mod-muted fdi fas fa-pencil-alt"></i>
						</span>
						<span class="mod-cell">
							<?php echo JText::_('MOD_EASYBLOGWELCOME_WRITE_NEW');?>
						</span>
					</a>
				</div>

				<div class="eb-mod-item">
					<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries');?>">
						<span class="mod-cell">
							<i class="mod-muted fdi far fa-file-alt"></i>
						</span>
						<span class="mod-cell">
							<?php echo JText::_('MOD_EASYBLOGWELCOME_MYBLOGS');?>
						</span>
					</a>
				</div>

				<?php if ((($config->get('comment_easyblog')) && $config->get('main_comment_multiple')) && $config->get('main_comment')) { ?>
				<div class="eb-mod-item">
					<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=comments');?>">
						<span class="mod-cell">
							<i class="mod-muted fdi fa fa-comments"></i>
						</span>
						<span class="mod-cell">
							<?php echo JText::_( 'MOD_EASYBLOGWELCOME_MYCOMMENTS');?>
						</span>
					</a>
				</div>
				<?php } ?>
			<?php } ?>

			<?php if ($config->get('main_favourite_post')) { ?>
			<div class="eb-mod-item">
				<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=favourites');?>">
					<span class="mod-cell">
						<i class="mod-muted fdi fa fa-heart"></i>
					</span>
					<span class="mod-cell">
						<?php echo JText::_( 'COM_EB_FAVOURITE_POSTS');?>
					</span>
				</a>
			</div>
			<?php } ?>

			<div class="eb-mod-item">
				<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=subscription');?>">
					<span class="mod-cell">
						<i class="mod-muted fdi fa fa-envelope"></i>
					</span>
					<span class="mod-cell">
						<?php echo JText::_('MOD_EASYBLOGWELCOME_MYSUBSCRIPTION');?>
					</span>
				</a>
			</div>

			<?php if ($params->get('enable_login', true)) { ?>
			<div class="eb-mod-item">
				<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&' . JSession::getFormToken() . '=1&return='.$return);?>">
					<span class="mod-cell">
						<i class="mod-muted fdi fas fa-sign-out-alt"></i>
					</span>
					<span class="mod-cell">
						<?php echo JText::_('MOD_EASYBLOGWELCOME_LOGOUT');?>
					</span>
				</a>
			</div>
			<?php } ?>
		</div>
	<?php } ?>

	<?php if ($my->guest && $params->get('enable_login', true)) { ?>
	<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure', false)); ?>" method="post" name="login" id="form-login">
		<div>
			<label for="eb-username"><?php echo JText::_('MOD_EASYBLOGWELCOME_USERNAME') ?></label>
			<input id="eb-username" type="text" name="username" class="mod-input" />
		</div>

		<div>
			<label for="eb-password"><?php echo JText::_('MOD_EASYBLOGWELCOME_PASSWORD') ?></label>
			<input id="eb-password" type="password" name="password" class="mod-input" />
		</div>

		<?php if ($hasTwoFactor) { ?>
		<div>
			<label for="eb-twofactor"><?php echo JText::_('JGLOBAL_SECRETKEY') ?></label>
			<input id="eb-twofactor" type="text" name="secretkey" class="mod-input" />
		</div>
		<?php } ?>

		<?php if (JPluginHelper::isEnabled('system', 'remember')) { ?>
		<div class="mod-checkbox">
			<input id="eb-remember" type="checkbox" name="remember" class="inputbox" value="yes" />
			<label for="eb-remember"><?php echo JText::_('MOD_EASYBLOGWELCOME_REMEMBER_ME'); ?></label>
		</div>
		<?php } ?>

		<div>
			<button class="mod-btn mod-btn-block mod-btn-primary">
				<?php echo JText::_('MOD_EASYBLOGWELCOME_LOGIN') ?>
			</button>
		</div>

		<br />

		<div>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo JText::_('MOD_EASYBLOGWELCOME_FORGOT_YOUR_PASSWORD'); ?></a>
		</div>
		<div>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
			<?php echo JText::_('MOD_EASYBLOGWELCOME_FORGOT_YOUR_USERNAME'); ?></a>
		</div>

		<?php if ($allowRegistration) { ?>
		<div>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
				<?php echo JText::_('MOD_EASYBLOGWELCOME_REGISTER'); ?>
			</a>
		</div>
		<?php } ?>

		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHTML::_('form.token'); ?>

		<?php if ($config->get('integrations_jfbconnect_login')) { ?>
			<?php echo EB::jfbconnect()->getTag();?>
		<?php } ?>
	</form>
	<?php } ?>
</div>
