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
<?php if ($giphies) { ?>
	<div class="l-cluster">
		<div class="">
		<?php foreach ($giphies as $giphy) { ?>
			<div>
				<a href="javascript:void(0);" class="eb-gif-holder"
					data-type="<?php echo $type; ?>"
					data-giphy-item
					data-original="<?php echo $giphy->images->original->url; ?>"
					style="background-image: url('<?php echo $giphy->images->fixed_width->url; ?>');">
				</a>
			</div>
		<?php } ?>
		</div>
	</div>
<?php } ?>