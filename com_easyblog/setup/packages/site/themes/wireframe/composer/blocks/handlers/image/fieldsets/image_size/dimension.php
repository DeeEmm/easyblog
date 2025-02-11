<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-fieldgroup eb-image-dimension-field">
	<div class="eb-composer-fieldgroup-content">
		<?php echo $this->output('site/composer/fields/numslider', array(
				'name'   => 'image-width',
				'type'   => 'image-width',
				'label'  => JText::_('COM_EASYBLOG_COMPOSER_FIELDS_WIDTH'),
				'toggle' => false,
				'units'  => array('pixel', 'percent'),
				'defaultUnit' => 'percent'
			)); ?>

		<?php echo $this->output('site/composer/fields/numslider', array(
				'name'   => 'image-height',
				'type'   => 'image-height',
				'label'  => JText::_('COM_EASYBLOG_COMPOSER_FIELDS_HEIGHT'),
				'toggle' => false,
				'units'  => array('pixel')
			)); ?>

		<?php echo $this->output('site/composer/fields/checkbox', array(
				'classname' => 'eb-image-ratio-lock',
				'attributes' => 'data-eb-image-ratio-lock',
				'checked' => true
			)); ?>

		<div class="eb-image-ratio-toggle">
			<div>
				<button type="button" class="btn btn-eb-default-o eb-image-ratio-button" data-eb-image-ratio-button>
					<i class="fdi fa fa-lock"></i>
					<i class="fdi fa fa-unlock-alt"></i>
					<span class="eb-image-ratio-label" data-eb-image-ratio-label>16:9</span>
				</button>
			</div>
		</div>
	</div>
</div>