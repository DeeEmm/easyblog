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
<div id="eb" class="mod-eb mod-eb-nav">
	<div class="mod-eb-menu-bar">
		<div class="o-nav">
			<?php if ($acl->get('add_entry')) {?>
			<div class="o-nav__item" data-eb-provide="tooltip" data-placement="top" data-original-title="<?php echo JText::_('COM_EASYBLOG_TOOLBAR_NEW_POST_TIPS');?>">
				<a class="o-nav__link mod-eb-menu-bar__icon-link has-composer" href="<?php echo EB::composer()->getComposeUrl(); ?>">
					<i class="fdi fa fa-pencil-alt"></i>
				</a>
			</div>
			<?php } ?>

			<?php if ($acl->get('add_entry') && $config->get('main_microblog')) { ?>
			<div class="o-nav__item" data-original-title="<?php echo JText::_('COM_EASYBLOG_TOOLBAR_QUICK_POST');?>" data-placement="top" data-eb-provide="tooltip">
				<a class="o-nav__link mod-eb-menu-bar__icon-link" href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost'); ?>">
					<i class="fdi fa fa-bolt"></i>
				</a>
			</div>
			<?php } ?>

			<?php if ($config->get('main_sitesubscription') && $acl->get('allow_subscription')) { ?>
			<div class="o-nav__item <?php echo $subscription->id ? 'hide' : ''; ?>" data-blog-subscribe data-type="site" data-original-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_SITE');?>" data-placement="top" data-eb-provide="tooltip">
				<a class="o-nav__link mod-eb-menu-bar__icon-link" href="javascript:void(0);">
					<i class="fdi fa fa-envelope"></i>
				</a>
			</div>
			<div class="o-nav__item <?php echo $subscription->id ? '' : 'hide'; ?>" data-blog-unsubscribe data-subscription-id="<?php echo $subscription->id;?>" data-return="<?php echo base64_encode(EBFactory::getURI(true));?>" data-original-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_TO_SITE');?>" data-placement="top" data-eb-provide="tooltip">
				<a class="o-nav__link mod-eb-menu-bar__icon-link" href="javascript:void(0);">
					<i class="fdi fa fa-envelope"></i>
				</a>
			</div>
			<?php } ?>

			<?php if ($config->get('layout_login') && $guest) { ?>
			<div class="o-nav__item dropdown_">
				<a href="javascript:void(0);" class="o-nav__link mod-eb-menu-bar__icon-link dropdown-toggle_" data-bp-toggle="dropdown">
					<i class="fdi fa fa-lock"></i>
				</a>
				<div class="eb-toolbar__dropdown-menu eb-toolbar__dropdown-menu--signin dropdown-menu <?php echo ($params->get('flip', true)) ? : 'pull-right'; ?>" data-eb-toolbar-dropdown >
					<div class="eb-arrow"></div>
					<div class="popbox-dropdown">
						<div class="popbox-dropdown__hd">
							<div class="o-flag o-flag--rev">
								<div class="o-flag__body">
									<div class="popbox-dropdown__title"><?php echo JText::_('COM_EB_SIGN_IN');?></div>
									<?php if (EB::isRegistrationEnabled()) { ?>
									<div class="popbox-dropdown__meta">
										<?php echo JText::sprintf('COM_EB_TOOLBAR_IF_YOU_ARE_NEW_HERE', '<a href="' . EB::getRegistrationLink() . '">' . JText::_('COM_EASYBLOG_REGISTER') . '</a>');?>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
						<div class="popbox-dropdown__bd">
							<form class="popbox-dropdown-signin" action="<?php echo JRoute::_('index.php');?>" method="post">

								<?php echo $theme->fd->html('form.floatinglabel', 'COM_EASYBLOG_USERNAME', 'username', 'text', '', 'eb-mod-nav-btn-username'); ?>

								<?php echo $theme->fd->html('form.floatinglabel', 'COM_EASYBLOG_PASSWORD', 'password', 'password', '', 'eb-mod-nav-btn-password'); ?>

								<div class="popbox-dropdown-signin__action">
									<div class="popbox-dropdown-signin__action-col">
										<?php if (JPluginHelper::isEnabled('system', 'remember')) { ?>
											<div class="eb-checkbox">
												<input id="eb-mod-nav-btn-remember-me" type="checkbox" name="remember" value="1" class="rip" tabindex="33"/>
												<label for="eb-mod-nav-btn-remember-me"><?php echo JText::_('COM_EASYBLOG_REMEMBER_ME') ?></label>
											</div>
										<?php } ?>
									</div>
									<div class="popbox-dropdown-signin__action-col">
										<button class="btn btn-primary" tabindex="34"><?php echo JText::_('COM_EASYBLOG_LOGIN') ?></button>
									</div>
								</div>
								<input type="hidden" value="com_users"  name="option">
								<input type="hidden" value="user.login" name="task">
								<input type="hidden" name="return" value="<?php echo $return; ?>" />
								<?php echo $theme->fd->html('form.token'); ?>

								<?php if ($config->get('integrations_jfbconnect_login')) { ?>
									<?php echo EB::jfbconnect()->getTag();?>
								<?php } ?>
							</form>
						</div>
						<div class="popbox-dropdown__ft">
							<ul class=" popbox-dropdown-signin__ft-list g-list-inline g-list-inline--dashed t-text--center">
								<li>
									<a href="<?php echo EB::getRemindUsernameLink();?>"><?php echo JText::_('COM_EASYBLOG_FORGOTTEN_USERNAME');?></a>
								</li>
								<li>
									<a href="<?php echo EB::getResetPasswordLink();?>" class=""><?php echo JText::_('COM_EB_RESET_PASSWORD');?></a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

			<?php if (!$guest) { ?>
			<div class="o-nav__item is-signin dropdown_" data-original-title="<?php echo JText::_('COM_EB_TOOLBAR_MORE_SETTINGS');?>" data-placement="top" data-eb-provide="tooltip">
				<a href="javascript:void(0);" class="o-nav__link mod-eb-menu-bar__icon-link dropdown-toggle_" data-bp-toggle="dropdown">
					<i class="fdi fa fa-cog"></i>
				</a>
				<div id="more-settings" role="menu" class="mod-eb-menu-bar__dropdown-menu dropdown-menu <?php echo ($params->get('flip', true)) ? : 'pull-right'; ?>">
					<div class="eb-arrow"></div>
					<div class="popbox-dropdown">
						<div class="popbox-dropdown__hd">
							<div class="popbox-dropdown__hd-flag">
								<div class="popbox-dropdown__hd-body">
									<?php if ($acl->get('add_entry')) { ?>
										<a href="<?php echo $profile->getPermalink();?>" class="eb-user-name"><?php echo $profile->getName();?></a>
									<?php } else { ?>
										<?php echo $profile->getName();?>
									<?php } ?>

									<?php if ($config->get('layout_dashboardmain') && $acl->get('add_entry')) { ?>
									<div class="mt-5">
										<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard');?>" class="text-muted">
											<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_OVERVIEW');?>
										</a>
									</div>
									<?php } ?>
								</div>
								<div class="popbox-dropdown__hd-image">
									<?php if ($acl->get('add_entry')) { ?>
									<a href="<?php echo $profile->getPermalink();?>" class="o-avatar o-avatar--sm">
										<img src="<?php echo $profile->getAvatar();?>" alt="<?php echo $fd->html('str.escape', $profile->getName());?>" width="24" height="24" />
									</a>
									<?php } else { ?>
										<img src="<?php echo $profile->getAvatar();?>" alt="<?php echo $fd->html('str.escape', $profile->getName());?>" width="24" height="24" />
									<?php } ?>
								</div>
							</div>
						</div>

						<div class="popbox-dropdown__bd">
							<div class="popbox-dropdown-nav">

								<?php if (FH::isSiteAdmin() || $allowManage) { ?>
								<div class="popbox-dropdown-nav__item ">
									<span class="popbox-dropdown-nav__link">

										<div class="popbox-dropdown-nav__name mb-5">
											<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_MANAGE');?>
										</div>

										<ol class="popbox-dropdown-nav__meta-lists">
											<?php if ($acl->get('add_entry')) { ?>
											<li>
												<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries');?>">
													<?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_POSTS');?>
												</a>
											</li>
											<?php } ?>

											<?php if ($acl->get('create_post_templates')) { ?>
											<li>
												<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=templates');?>">
													<?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_POST_TEMPLATES');?>
												</a>
											</li>
											<?php } ?>

											<?php if ($acl->get('create_block_templates')) { ?>
											<li>
												<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=blockTemplates');?>">
													<?php echo JText::_('COM_EB_DASHBOARD_HEADING_BLOCK_TEMPLATES');?>
												</a>
											</li>
											<?php } ?>


											<?php if ((FH::isSiteAdmin() || $acl->get('moderate_entry')) && $totalPending) { ?>
											<li>
												<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=moderate');?>">
													<i class="fdi fa fa-ticket"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_PENDING');?>
													<?php if ($totalPending) { ?>
														<span class="popbox-dropdown-nav__indicator ml-5"></span>
														<span class="popbox-dropdown-nav__counter"><?php echo $totalPending;?></span>
													<?php } ?>
												</a>
											</li>
											<?php } ?>

											<?php if ($acl->get('manage_comment') && EB::comment()->isBuiltin()) { ?>
											<li>
												<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=comments');?>">
													<i class="fdi fa fa-comments"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_COMMENTS');?>
													<?php if ($totalPendingComments) { ?>
													<span class="popbox-dropdown-nav__indicator ml-5"></span>
													<span class="popbox-dropdown-nav__counter"><?php echo $totalPendingComments; ?></span>
													<?php } ?>
												</a>
											</li>
											<?php } ?>

											<?php if ($acl->get('create_category')) { ?>
											<li>
												<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=categories');?>">
													<i class="fdi far fa-folder"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_CATEGORIES');?>
												</a>
											</li>
											<?php } ?>

											<?php if ($acl->get('create_tag')) { ?>
											<li>
												<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=tags');?>">
													<i class="fdi fa fa-tags"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_TAGS');?>
												</a>
											</li>
											<?php } ?>

											<?php if ($config->get('layout_teamblog') && $acl->get('create_team_blog')) { ?>
											<li>
												<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs');?>">
													<?php echo JText::_('COM_EASYBLOG_TOOLBAR_TEAMBLOGS');?>
												</a>
											</li>
											<?php } ?>

											<?php if ((EB::isTeamAdmin() || FH::isSiteAdmin()) && $config->get('toolbar_teamrequest') && $acl->get('create_team_blog') && $totalTeamRequests){ ?>
											<li>
												<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=requests');?>">
													<i class="fdi fa fa-users"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_TEAM_REQUESTS');?>
													<?php if ($totalTeamRequests) { ?>
													<span class="popbox-dropdown-nav__indicator ml-5"></span>
													<span class="popbox-dropdown-nav__counter"><?php echo $totalTeamRequests;?></span>
													<?php } ?>
												</a>
											</li>
											<?php } ?>
										</ol>
									</span>
								</div>
								<?php } ?>

								<div class="popbox-dropdown-nav__item ">
									<span class="popbox-dropdown-nav__link">
										<div class="popbox-dropdown-nav__name mb-5">
											<?php echo JText::_('COM_EASYBLOG_YOUR_ACCOUNT'); ?>
										</div>
										<ol class="popbox-dropdown-nav__meta-lists">
											<?php if ($acl->get('allow_subscription')) { ?>
											<li>
												<a href="<?php echo EB::_('index.php?option=com_easyblog&view=subscription');?>">
													<i class="fdi fa fa-envelope"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_SUBSCRIPTIONS');?>
												</a>
											</li>
											<?php } ?>

											<?php if ($config->get('toolbar_editprofile')){ ?>
											<li>
												<a href="<?php echo EB::getEditProfileLink();?>">
													<?php echo JText::_('COM_EASYBLOG_TOOLBAR_EDIT_PROFILE');?>
												</a>
											</li>
											<?php } ?>

											<?php if ($config->get('toolbar_logout')){ ?>
											<li>
												<a href="javascript:void(0);" data-blog-toolbar-logout>
													<i class="fdi fa fa-power-off"></i> <?php echo JText::_('COM_EASYBLOG_TOOLBAR_SIGN_OUT');?>
												</a>
											</li>
											<?php } ?>
										</ol>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
