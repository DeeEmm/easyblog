<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-compare" data-eb-composer-compare>
	<div class="eb-composer-viewport">
		<div class="eb-composer-viewport-content" data-scrolly-viewport>

			<div class="eb-composer-page">
				<div>
					<div class="compare-head">
						<div class="compare-category"><?php echo $target->getCategories(true);?></div>
						<div class="compare-title"><?php echo $target->getPost()->title;?></div>
						<div><?php echo $target->getPost()->permalink;?></div>
					</div>

					<div class="ebd-workarea">
						<div class="ebd">
							<?php if ($current->getPost()->isLegacy()) { ?>
								<?php echo '<div class="ebd-block">' . $current->getDiffContent($target) . '</div>'; ?>
							<?php } else { ?>
								<?php echo $current->getDocument()->getDiffContent($target->getDocument());?>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>