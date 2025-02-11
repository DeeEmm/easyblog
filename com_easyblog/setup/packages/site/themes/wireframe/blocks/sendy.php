<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="sendy-block" data-eb-sendy-wrapper>
	<form action="<?php echo $data->url;?>/subscribe"method="post" data-sendy-form>
		<div class="eb-sendy-form">
			<div class="form-group">
				<?php if ($data->title) { ?>
				<label for="eb-post-subscribe-email"><?php echo $data->title_text;?></label>
				<?php } ?>

				<?php if ($data->info) { ?>
				<p><?php echo $data->info_text;?></p>
				<?php } ?>
			</div>

			<div class="form-group">
				<input name="email" type="email" class="form-control" placeholder="<?php echo $data->email_placeholder;?>" />
			</div>

			<?php if ($data->name) { ?>
			<div class="form-group">
				<input name="name" type="text" class="form-control" placeholder="<?php echo $data->name_placeholder;?>" />
			</div>
			<?php } ?>

			<button class="btn btn-primary btn-block"><?php echo $data->button;?></button>
		</div>
		<input type="hidden" name="list" value="<?php echo $data->list_id;?>" />
		<input type="hidden" name="subform" value="yes" />
	</form>
</div>