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
<div class="eb-composer-fieldset eb-composer-fieldset--accordion <?php echo !$isPanelPreferencesEnabled || $panelPreferences->get('quickpost_link', true) ? 'is-open' : ''; ?>" data-name="quickpost_link" data-eb-composer-block-section>
	<?php echo $this->html('composer.panel.header', 'COM_EB_COMPOSER_QUICKPOST_LINK'); ?>

	<div class="eb-composer-fieldset-content">
		<?php echo $this->html('composer.panel.help', 'COM_EB_COMPOSER_QUICKPOST_LINK_HELP'); ?>

		<div class="o-form-horizontal">
			<input class="form-control" type="text" name="link" value="<?php echo $post->getAsset('link')->getValue();?>" />
		</div>
	</div>
</div>