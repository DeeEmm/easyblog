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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_GENERAL_ATTRIBUTES'); ?>

	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html('composer.field', 'composer.field.toggler', 'citation', 'COM_EASYBLOG_BLOCKS_QUOTES_CITATION', $data->citation, array('data-quotes-citation')); ?>
	</div>
</div>

<div class="eb-composer-fieldset eb-composer-fieldset--accordion is-open" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EASYBLOG_BLOCKS_QUOTES_STYLE'); ?>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group" data-eb-composer-block-quotes-style>
			<div class="eb-swatch swatch-grid">
				<div class="row">
					<?php foreach ($styles as $style) { ?>
					<div class="col-sm-12">
						<div class="eb-swatch-item eb-composer-quote-preview" data-style="<?php echo $style['classname']; ?>">
							<div class="eb-swatch-preview">
								<blockquote class="eb-quote <?php echo $style['classname']; ?>">
									<p><?php echo JText::_('COM_EASYBLOG_BLOCKS_QUOTES_PREVIEW_CONTENT');?></p>
									<cite><?php echo JText::_('COM_EASYBLOG_BLOCKS_QUOTES_PREVIEW_CITE');?></cite>
								</blockquote>
							</div>
							<div class="eb-swatch-label">
								<span><?php echo $style['label']; ?></span>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
