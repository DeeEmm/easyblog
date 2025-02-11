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

// Get preferred variation
$preferredVariations = ['system/thumbnail', 'system/original'];

foreach ($preferredVariations as $variationName) {
	if (isset($variations[$variationName])) {
		$variation = $variations[$variationName];
		break;
	}
}

$ratio = $variation->width / $variation->height;

$blockData = array(
	'url' => $preview,
	'uri' => $file->uri,
	'variation' => $variationName,
	'mode' => 'simple',
	'ratio' => $ratio,
	'ratio_lock' => true,
	'natural_ratio' => $ratio
);
?>
<div class="ebd-block is-standalone" data-type="image">
	<div class="ebd-block-viewport" data-ebd-block-viewport>
		<div class="ebd-block-content" data-ebd-block-content>
			<div class="eb-image">
				<div class="eb-image-figure">
					<a class="eb-image-viewport"><img src="<?php echo $preview; ?>"></a>
				</div>
			</div>
		</div>
	</div>
</div>
<textarea data-block><?php echo json_encode($blockData); ?></textarea>
