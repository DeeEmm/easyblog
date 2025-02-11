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
<div class="eb-composer-placeholder eb-composer-link-placeholder text-center" data-sendy-placeholder>
	<?php echo $this->html('composer.block.placeholder', 'fdi far fa-paper-plane', 'COM_EB_BLOCKS_SENDY'); ?>
</div>


<div class="sendy-block t-hidden" data-eb-sendy-wrapper>
	<div data-sendy-form>
		<div class="eb-sendy-form">

			<div class="form-group">
				<label for="eb-post-subscribe-email" data-sendy-title></label>
				<p data-sendy-info></p>
			</div>

			<div class="form-group">
				<input type="email" class="form-control" id="eb-post-subscribe-email" placeholder="" data-sendy-email />
			</div>

			<div class="form-group">
				<input type="text" class="form-control" id="eb-post-subscribe-name" placeholder="" data-sendy-name />
			</div>

			<a href="javascript:void(0);" class="btn btn-primary btn-block" data-sendy-button></a>
		</div>
		<input type="hidden" name="list" value="" data-sendy-id />
		<input type="hidden" name="subform" value="yes" />
	</div>
</div>
