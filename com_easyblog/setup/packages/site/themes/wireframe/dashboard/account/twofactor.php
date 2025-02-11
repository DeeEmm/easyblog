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
<div class="eb-dashboard-form-section">
	<?php echo $this->html('dashboard.miniHeading', 'COM_USERS_PROFILE_TWO_FACTOR_AUTH', 'COM_EB_TWOFACTOR_DESC'); ?>

	<div class="eb-dashboard-form-section__form">
		<div class="form-horizontal">
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_USERS_PROFILE_TWOFACTOR_LABEL', 'user_editor'); ?>

				<div class="col-md-8">
					<?php echo JHtml::_('select.genericlist', $twoFactorMethods, 'jform[twofactor][method]', ['onchange' => 'Joomla.twoFactorMethodChange()', 'class' => 'form-control'], 'value', 'text', $otpConfig->method, 'jform_twofactor_method', false); ?>
				</div>
			</div>

			<div id="com_users_twofactor_forms_container" style="margin-top: 15px;">
				<?php foreach ($twoFactorForms as $form) { ?>
					<?php $style = $form['method'] == $otpConfig->method ? 'display: block' : 'display: none'; ?>
					<div id="com_users_twofactor_<?php echo $form['method']; ?>" style="<?php echo $style; ?>">
						<?php echo $form['form']; ?>
					</div>
				<?php } ?>

				<fieldset>
					<legend>
						<?php echo JText::_('COM_USERS_PROFILE_OTEPS'); ?>
					</legend>
					<div class="alert alert-info">
						<?php echo JText::_('COM_USERS_PROFILE_OTEPS_DESC'); ?>
					</div>
					<?php if (empty($otpConfig->otep)) { ?>
						<div class="alert alert-warning">
							<?php echo JText::_('COM_USERS_PROFILE_OTEPS_WAIT_DESC'); ?>
						</div>
					<?php } else { ?>
						<?php foreach ($otpConfig->otep as $otep) { ?>
							<span class="span3">
								<?php echo substr($otep, 0, 4); ?>-<?php echo substr($otep, 4, 4); ?>-<?php echo substr($otep, 8, 4); ?>-<?php echo substr($otep, 12, 4); ?>
							</span>
						<?php } ?>
						<div class="clearfix"></div>
					<?php } ?>
				</fieldset>
			</div>
		</div>
	</div>

</div>


<script type="text/javascript">
Joomla.twoFactorMethodChange = function(e) {
	var selectedPane = 'com_users_twofactor_' + jQuery('#jform_twofactor_method').val();

	jQuery.each(jQuery('#com_users_twofactor_forms_container>div'), function(i, el) {
		if (el.id != selectedPane)
		{
			jQuery('#' + el.id).hide(0);
		}
		else
		{
			jQuery('#' + el.id).show(0);
		}
	});
}
</script>
