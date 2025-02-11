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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-eb-form>
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->fd->html('panel.head', 'COM_EASYBLOG_SUBSCRIPTION_SETTINGS'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SUBSCRIPTION_TYPE', 'type'); ?>

							<div class="col-md-7">
								<?php echo $this->fd->html('form.dropdown', 'type', $subscription->utype, [
									'site' => 'COM_EASYBLOG_SUBSCRIPTION_TYPE_SITE',
									'category' => 'COM_EASYBLOG_SUBSCRIPTION_TYPE_CATEGORY',
									'entry' => 'COM_EASYBLOG_SUBSCRIPTION_TYPE_BLOG_POST',
									'blogger' => 'COM_EASYBLOG_SUBSCRIPTION_TYPE_BLOGGER',
									'team' => 'COM_EASYBLOG_SUBSCRIPTION_TYPE_TEAM_BLOG'
								], ['attr' => 'data-subscription-type']); ?>
							</div>
						</div>

						<div class="form-group <?php echo $subscription->utype != 'category' ? 'hide' : '';?>" data-subscriptions="category">
								<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_FILTER_SELECT_CATEGORY', 'type'); ?>
							<div class="col-md-7">
								<?php echo $this->html('form.browseCategory', 'cid_category', $subscription->utype == 'category' ? $subscription->uid : null) ?>
							</div>
						</div>

						<div class="form-group <?php echo $subscription->utype != 'entry' ? 'hide' : '';?>" data-subscriptions="entry">
								<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SUBSCRIPTION_SELECT_ENTRY', 'type'); ?>
							<div class="col-md-7">
								<?php echo $this->html('form.browseBlog', 'cid_entry', $subscription->utype == 'entry' ? $subscription->uid : null) ?>
							</div>
						</div>

						<div class="form-group <?php echo $subscription->utype != 'blogger' ? 'hide' : '';?>" data-subscriptions="blogger">
								<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SUBSCRIPTION_SELECT_BLOGGER', 'type'); ?>
							<div class="col-md-7">
								<?php echo $this->fd->html('form.user', 'cid_blogger', $subscription->utype == 'blogger' ? $subscription->uid : null) ?>
							</div>
						</div>

						<div class="form-group <?php echo $subscription->utype != 'team' ? 'hide' : '';?>" data-subscriptions="team">
								<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SUBSCRIPTION_SELECT_TEAM', 'type'); ?>
							<div class="col-md-7">
								<?php echo $this->html('form.team', 'cid_team', $subscription->utype == 'team' ? $subscription->uid : null) ?>
							</div>
						</div>

						<div class="form-group">
								<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SUBSCRIPTION_NAME', 'name'); ?>
							<div class="col-md-7">
								<input type="text" class="form-control" id="fullname" name="fullname" size="55" maxlength="255" value="<?php echo $subscription->fullname;?>" />
							</div>
						</div>

						<div class="form-group">
								<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_SUBSCRIPTION_EMAIL', 'email'); ?>
							<div class="col-md-7">
								<input type="text" class="form-control" id="email" name="email" size="55" maxlength="255" value="<?php echo $subscription->email;?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="id" value="<?php echo $subscription->id;?>" />
	<?php $controller = $subscription->id ? 'subscriptions.save' : 'subscriptions.create'; ?>
	<?php echo $this->fd->html('form.action', $controller); ?>
</form>
