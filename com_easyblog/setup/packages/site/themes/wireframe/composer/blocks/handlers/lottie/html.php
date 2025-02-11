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
<div class="eb-composer-placeholder eb-composer-link-placeholder text-center" data-lottie-wrapper>
	<div data-lottie-form>
		<?php echo $this->html('composer.block.placeholder', 'fdi fab fa-lottie', 'COM_EB_COMPOSER_BLOCKS_LOTTIE'); ?>

		<p class="eb-composer-placeholder-error t-text--danger t-hidden" data-lottie-error>
			<?php echo JText::_('COM_EB_COMPOSER_BLOCKS_LOTTIE_ERROR'); ?>
		</p>

		<div class="eb-composer-place-options">
			<div class="eb-composer-place-options__item" style="width: 70%;">
				<div class="o-input-group o-input-group--sm">
					<input type="text" class="o-form-control" type="text" data-lottie-input placeholder="<?php echo JText::_('COM_EB_COMPOSER_BLOCKS_LOTTIE_PLACEHOLDER', true);?>" />
					<span class="o-input-group__btn">
						<button type="button" class="btn btn-eb-primary btn--sm" data-lottie-embed>
							<?php echo JText::_('COM_EB_COMPOSER_BLOCKS_LOTTIE_EMBED');?>
						</button>
					</span>
				</div>
			</div>
			<div class="eb-composer-place-options__item">
				<button type="button" class="btn btn--sm btn-eb-primary" style="position: relative; z-index: 1;">
					<i class="fdi fa fa-upload"></i>&nbsp;
					<?php echo JText::_('COM_EB_LOTTIE_UPLOAD_BUTTON'); ?>

					<div style="position: absolute; top: 0px; left: 0px; width: 81px; height: 30px; overflow: hidden; z-index: 0;">
						<input type="file" name="lottie" accept=".json" data-lottie-file-input style="font-size: 999px; opacity: 0; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; cursor: pointer;">
					</div>
				</button>
			</div>
		</div>


	</div>
</div>
<div class="o-loader-wrapper">
	<div class="o-loader o-loader--inline"></div>
</div>
