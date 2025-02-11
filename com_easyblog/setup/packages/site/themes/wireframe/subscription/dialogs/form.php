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
<dialog>
	<width><?php echo $this->config->get('main_subscription_agreement') ? 550 : 500; ?></width>
	<height>220</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{form}" : "[data-form-response]",
		"{submitButton}" : "[data-submit-button]",

		"{email}" : "[data-subscribe-email]",
		"{name}"  : "[data-subscribe-name]",

		"{agreement}": "[data-subscription-agreement]",
		"{agreementInput}": "[data-subscription-agreement] input[type=checkbox]",

		"{alert}": "[data-subscribe-alert]",
		"{alertMessage}": "[data-subscribe-alert-message]",
		"{alertDismiss}": "[data-subscribe-alert-dismiss]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},

		"{alertDismiss} click": function() {
			this.alert().addClass('hide');
		},
		
		"{submitButton} click" : function(el) {
			el = $(el);
			this.alert().addClass('hide');

			<?php if ($this->config->get('main_subscription_agreement')) { ?>
			var agreed = this.agreementInput().is(':checked');

			if (!agreed) {
				this.agreement().addClass('text-error');
				return false;
			}
			<?php } ?>

			// Show the loader
			el.addClass('is-loading');

			EasyBlog.ajax('site/views/subscription/subscribe', {
				"type" : "<?php echo $type;?>",
				"email" : this.email().val(),
				"name"  : this.name().val(),
				"uid" : "<?php echo $id;?>",
				"userId" : "<?php echo $this->my->id;?>"
			}).done(function(output, id) {
				// Search for any subscribe button on the site
				var subscribeButton = $('[data-blog-subscribe]').filter('[data-id="<?php echo $id ?>"]');
				var unsubscribeButton = subscribeButton.siblings('[data-blog-unsubscribe]');

				// if the subscription process type is double opt-in, do not perform any action
				<?php if (!$isDoubleOptIn) { ?>
					subscribeButton.addClass('hide');
					unsubscribeButton.removeClass('hide');
					unsubscribeButton.attr('data-subscription-id', id);
				<?php } ?>

				// Append the output
				EasyBlog.dialog({
					content: output
				})
			}).fail(function(message) {
				$('[data-subscribe-alert-message]').html(message);
				$('[data-subscribe-alert]').removeClass('hide');

				// Remove the loader
				el.removeClass('is-loading');
			});
		}
	}
	</bindings>
	<title>
		<?php echo $title;?>
	</title>
	<content>
		<div class="eb-alert row-table alert alert-danger hide" data-subscribe-alert>
			<div class="col-cell cell-tight cell-sign">
				<i class="fdi fa fa-check-circle"></i>
			</div>
			<div class="col-cell cell-text" data-subscribe-alert-message></div>

			<div class="col-cell cell-tight cell-close" data-subscribe-alert-dismiss>
				<b class="fdi fa fa-times"></b>
			 </div>
		</div>
		<p style="padding: 10px 0;"><?php echo $desc;?></p>

		<form method="post" action="<?php echo JRoute::_('index.php');?>" data-form-response class="pl-10 pr-10">
			<div class="form-group">
				<label class="col-cell control-label"><?php echo JText::_('COM_EASYBLOG_FULLNAME'); ?></label>
				<div class="col-cell">
					<input class="form-control input-sm" type="text" id="esfullname" name="esfullname" size="45" value="<?php echo $this->fd->html('str.escape', $this->my->name);?>" data-subscribe-name />
				</div>
			</div>

			<div class="form-group">
				<label class="col-cell control-label"><?php echo JText::_('COM_EASYBLOG_EMAIL'); ?></label>
				<div class="col-cell">
					<input type="text" id="title" name="title" class="form-control input-sm" size="45" value="<?php echo $this->fd->html('str.escape', $this->my->email); ?>" data-subscribe-email />
				</div>
			</div>

			<?php if ($this->config->get('main_subscription_agreement')) { ?>
			<div class="form-group" data-subscription-agreement style="margin-top: 20px;">
				<div class="col-cell control-label">&nbsp;</div>
				<div class="col-cell">
					<div class="eb-checkbox">
						<input type="checkbox" id="agreement" name="agreement" value="1" />
						<label for="agreement">
							<?php echo JText::_($this->config->get('main_subscription_agreement_message')); ?>
						</label>
					</div>
				</div>
			</div>
			<?php } ?>
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_EASYBLOG_SUBSCRIBE_BUTTON'); ?>
	</buttons>
</dialog>
