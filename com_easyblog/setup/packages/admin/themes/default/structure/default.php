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
<div id="fd" class="is-joomla-backend">
	<div id="eb" class="eb-component eb-admin si-theme--light <?php echo FH::isJoomla4() ? 'is-loading' : '';?>" data-eb-structure data-fd-structure>

		<?php if (FH::isJoomla4()) { ?>
			<?php echo $this->fd->html('loader.block'); ?>
		<?php } ?>

		<?php if ($this->config->get('show_outdated_message')) { ?>
			<?php echo $this->fd->html('admin.outdated', 'COM_EASYBLOG_OUTDATED_VERSION', JRoute::_('index.php?option=com_easyblog&task=system.upgrade'), 'COM_EASYBLOG_UPDATE_NOW'); ?>
		<?php } ?>

		<?php if ($this->config->get('toolbar_installation_failed')) { ?>
			<?php echo $this->fd->html('admin.notice', 'COM_EB_TOOLBAR_PACKAGE_NOT_INSTALLED', 'warning' , null, [
				'attributes' => 'data-eb-toolbar-notice',
				'dismissible' => true,
				'dismissAttribute' => 'data-eb-close-toolbarnotice'
			]); ?>
		<?php } ?>

		<?php if ($fbTokenExpiring) { ?>
			<?php echo $this->fd->html('admin.notice', 'COM_EASYBLOG_FACEBOOK_TOKEN_EXPIRING', 'info', (object) [
				'text' => JText::_('COM_EASYBLOG_FACEBOOK_RENEW_TOKEN'),
				'url' => JRoute::_('index.php?option=com_easyblog&view=autoposting&layout=facebook'),
				'type' => 'default'
			], [
				'icon' => 'fdi fab fa-facebook'
			]); ?>
		<?php } ?>

		<?php if ($tmpl != 'component') { ?>
		<div class="app <?php echo FH::isJoomla4() ? 't-hidden' : '';?>" data-fd-body>
			<?php echo $sidebar; ?>

			<div class="app-content <?php echo !$heading ? 't-pt--no' : '';?>">

				<?php echo $info->html();?>

				<?php if ($heading || $desc) { ?>
					<?php echo $this->fd->html('admin.headers', $heading, $desc); ?>
				<?php } ?>

				<div class="app-body">
					<?php echo $output; ?>
				</div>
			</div>
		</div>

		<div class="btn-float-wrap">
			<a href="<?php echo JURI::base();?>index.php?option=com_easyblog&view=tags&layout=form" class="btn-float btn-float--category">
				<i class="fdi fa fa-tag"></i>
				<span><?php echo JText::_('COM_EB_NEW_TAG');?></span>
			</a>
			<a href="<?php echo JURI::base();?>index.php?option=com_easyblog&view=categories&layout=form" class="btn-float btn-float--category">
				<i class="fdi far fa-folder-open"></i>
				<span><?php echo JText::_('COM_EASYBLOG_NEW_CATEGORY');?></span>
			</a>
			<a href="<?php echo EB::composer()->getComposeUrl(); ?>" class="btn-float btn-float--post">
				<i class="fdi far fa-file-alt"></i>
				<span><?php echo JText::_('COM_EASYBLOG_NEW_POST');?></span>
			</a>
			<a href="javascript:void(0);" class="btn-float btn-float--default">
				<i class="fdi fa fa-plus"></i>
			</a>
		</div>

		<?php echo $this->fd->html('admin.toolbarSaveGroup'); ?>

		<?php if ($help) { ?>
			<?php echo $this->fd->html('admin.toolbarHelp', $help); ?>
		<?php } ?>

		<?php } else { ?>
			<?php echo $output; ?>
		<?php } ?>

		<?php if ($jscripts) { ?>
		<div data-eb-scripts>
			<?php echo $jscripts;?>
		</div>
		<?php } ?>


	</div>
</div>

<?php echo $this->fd->html('html.popover'); ?>

<?php if (\FH::isJoomla4()) { ?>
<style>
body.com_easyblog.is-joomla-4 .page-title [class^="icon-"],
body.com_easyblog.is-joomla-4 .page-title [class*=" icon-"] {
	background-image: url('<?php echo rtrim(JURI::root(), '/');?>/media/com_easyblog/images/easyblog-48x48.png');
}
</style>
<?php } ?>
