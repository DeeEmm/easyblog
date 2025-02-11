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
<?php if ($item->type == 'folder') { ?>
<div class="eb-nmm-content-listing__item type-folder" data-mm-folder data-uri="<?php echo $item->uri;?>" data-key="<?php echo $item->key;?>">
	<div class="eb-nmm-media">
		<div class="eb-nmm-media__body">
			<div class="eb-nmm-media__icon-wrapper">
				<i class="eb-nmm-media__icon"></i>
			</div>
			<div class="eb-nmm-media__cover">
				<div class="eb-nmm-media__embed">
					<div class="eb-nmm-media__embed-item"></div>
				</div>
			</div>
		</div>

		<div class="eb-nmm-media__info">
			<div class="eb-nmm-media__info-txt" data-item-title><?php echo $item->filename;?></div>
		</div>
	</div>
</div>
<?php } else { ?>
<div class="eb-nmm-content-listing__item type-<?php echo $item->type; ?><?php echo empty($ext) ? '' : ' ext-' . $ext; ?>" data-mm-item data-key="<?php echo $item->key; ?>" data-uri="<?php echo $item->uri;?>">
	<div class="eb-nmm-media">
		<div class="eb-nmm-media__checkbox-wrap">
			<div class="o-checkbox eb-nmm-media__checkbox">
				<input type="checkbox" id="<?php echo $item->key; ?>" data-mm-item-checkbox-input />
				<label for="<?php echo $item->key; ?>" data-mm-item-checkbox></label>
			</div>
		</div>

		<div class="eb-nmm-media__body">
			<div class="eb-nmm-media__icon-wrapper">
				<i class="eb-nmm-media__icon"></i>
			</div>
			<div class="eb-nmm-media__cover">
				<div class="eb-nmm-media__embed">
					<div class="eb-nmm-media__embed-item" style="<?php echo $item->preview ? "background-image: url('" . $item->preview . "');" : '';?>">
						<?php if ($item->type != 'image') { ?>
						<i class="eb-nmm-media__embed-item-icon <?php echo $item->icon;?>"></i>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="eb-nmm-media__info">
			<div class="eb-nmm-media__info-txt" data-item-title><?php echo $item->title;?></div>
		</div>
	</div>
</div>
<?php } ?>
