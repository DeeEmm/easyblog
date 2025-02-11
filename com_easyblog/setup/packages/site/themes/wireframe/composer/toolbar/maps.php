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
<div class="eb-comp-location-wrap" data-location-container data-google-apikey="<?php echo !empty($gMapkey) ? $gMapkey : '';?>">
	<div class="eb-comp-location <?php echo !empty($post->address) ? "has-location" : ""; ?>" data-location-preview>
		<div class="eb-comp-location__embed">
			<div class="eb-comp-location__embed-item" style="background-image: url('<?php echo $post->getLocationImage();?>');" data-location-map></div>
		</div>

		<div class="eb-comp-location__empty">
			<i class="fdi far fa-compass fa-fw"></i>
			<div class="t-lg-mt--lg"><?php echo JText::_('COM_EASYBLOG_COMPOSER_LOCATION_NOT_SET_YET');?></div>
		</div>
	</div>
	<div class="eb-comp-toolbar-dropdown-menu__input t-px--lg t-py--md" data-location-input-container>
		<div class="o-input-group">
			<input type="text" name="address" value="<?php echo $this->fd->html('str.escape', $post->address); ?>" class="o-form-control o-form-control--location" tabindex="-1" placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_ENTER_PLACE_TOWN_OR_CITY', true);?>"
				data-location-input
				data-placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_ENTER_PLACE_TOWN_OR_CITY', true);?>"
				data-placeholder-searching="<?php echo JText::_('COM_EASYBLOG_COMPOSER_DETECTING_CURRENT_LOCATION', true);?>"
			/>
			<div class="o-loader o-loader--sm"></div>
			<div class="o-input-group__btn">
				<a href="javascript:void(0);" class="btn btn-eb-default-o" data-location-detect>
					<i class="fdi fa fa-location-arrow"></i>
				</a>

				<a href="javascript:void(0);" class="btn btn-eb-default-o eb-comp-location-remove" data-location-remove>
					<i class="fdi fa fa-times t-text--danger"></i>
				</a>
			</div>
		</div>

		<input type="hidden" name="latitude" value="<?php echo $post->latitude;?>" data-location-latitude />
		<input type="hidden" name="longitude" value="<?php echo $post->longitude;?>" data-location-longitude />

		<div class="eb-composer-location-message" data-eb-composer-location-form-message></div>

		<div class="eb-composer-location-autocomplete" data-eb-composer-location-autocomplete>
			<s><s></s></s>
			<ul class="dropdown-menu eb-composer-location-places" data-eb-composer-location-places></ul>
		</div>
	</div>
</div>
