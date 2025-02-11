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

if (!isset($classname)) {
	$classname = '';
}

$actions = array(
	'bold' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_FONT_BOLD',
		'icon'   => 'fdi fa fa-bold',
		'format' => 'bold'
	),

	'italic' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_FONT_ITALIC',
		'icon'   => 'fdi fa fa-italic',
		'format' => 'italic'
	),

	'underline' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_FONT_UNDERLINE',
		'icon'   => 'fdi fa fa-underline',
		'format' => 'underline'
	),

	'hyperlink' => [
		'title' => 'Hyperlink',
		'icon' => 'fdi fa fa-link',
		'format' => 'hyperlink'
	],

	'strikethrough' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_FONT_STRIKETHROUGH',
		'icon'   => 'fdi fa fa-strikethrough',
		'format' => 'strikethrough'
	),

	'code' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_FONT_CODE',
		'icon'   => 'fdi fa fa-code',
		'format' => 'code'
	),

	'subscript' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_FONT_SUBSCRIPT',
		'icon'   => 'fdi fa fa-subscript',
		'format' => 'subscript'
	),

	'superscript' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_FONT_SUPERSCRIPT',
		'icon'   => 'fdi fa fa-superscript',
		'format' => 'superscript'
	),

	'alignleft' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_ALIGN_LEFT',
		'icon'   => 'fdi fa fa-align-left',
		'format' => 'alignleft'
	),

	'aligncenter' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_ALIGN_CENTER',
		'icon'   => 'fdi fa fa-align-center',
		'format' => 'aligncenter'
	),

	'alignright' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_ALIGN_RIGHT',
		'icon'   => 'fdi fa fa-align-right',
		'format' => 'alignright'
	),

	'justify' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_ALIGN_JUSTIFY',
		'icon'   => 'fdi fa fa-align-justify',
		'format' => 'justify'
	),

	'orderedlist' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_LIST_ORDERED',
		'icon'   => 'fdi fa fa-list-ol',
		'format' => 'orderedlist'
	),

	'unorderedlist' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_LIST_UNORDERED',
		'icon'   => 'fdi fa fa-list-ul',
		'format' => 'unorderedlist'
	),

	'indent' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_LIST_INDENT',
		'icon'   => 'fdi fa fa-indent',
		'format' => 'indent'
	),

	'outdent' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_LIST_OUTDENT',
		'icon'   => 'fdi fa fa-outdent',
		'format' => 'outdent'
	),

	'clear' => array(
		'title'  => 'COM_EASYBLOG_COMPOSER_CLEAR_FORMATTING',
		'icon'   => 'fdi fa fa-ban',
		'format' => 'clear'
	)
);
?>
<div class="o-form-group eb-font-formatting <?php echo $classname; ?>" data-type="font-formatting">
	<div class="eb-pills">
	<?php foreach ($layout as $itemgroup) { ?>
		<div class="eb-pill-group <?php echo $itemgroup['class']; ?>" data-font-format-<?php echo $itemgroup['class']; ?>>
			<div class="eb-pill">

				<?php if ($itemgroup['class'] == 'group-dropdown') { ?>
				<div class="eb-pill-item t-px--no">
					<div class="dropdown_">
						<div class="dropdown-toggle_ t-px--sm" data-bp-toggle="dropdown">
							<i class="fdi fa fa-caret-down"></i>
						</div>
						<div class="dropdown-menu dropdown-menu--font-formatting">
							<div class="t-d--flex t-flex-direction--c t-overflow--hidden">
							<?php
								foreach ($itemgroup['actions'] as $actionId) {
									$action = $actions[$actionId];
							?>
								<div class="font-formatting-dropdown-item" data-eb-font-format-option data-format="<?php echo $action['format']; ?>">
									<i class="t-mr--xs fa-fw <?php echo $action['icon']; ?>"></i><span class="t-text--truncate"><?php echo JText::_($action['title']); ?></span>
								</div>
							<?php } ?>
							</div>
						</div>
					</div>
				</div>
				<?php } else { ?>
					<?php
						foreach ($itemgroup['actions'] as $actionId) {
							$action = $actions[$actionId];
					?>
						<?php if ($actionId == 'hyperlink') { ?>
						<div class="eb-pill-item" data-eb-font-format-option data-format="<?php echo $action['format']; ?>">
							<div class="dropdown_">
								<div class="dropdown-toggle_" data-bp-toggle="dropdown">
									<i class="fa-fw <?php echo $action['icon']; ?>"></i>
								</div>
								<div class="dropdown-menu dropdown-menu--links-fields">
									<?php echo $this->output('site/composer/fields/links'); ?>
								</div>
							</div>
						</div>
						<?php } else { ?>
						<div class="eb-pill-item" data-eb-font-format-option data-format="<?php echo $action['format']; ?>">
							<i class="fa-fw <?php echo $action['icon']; ?>"></i><span><?php echo JText::_($action['title']); ?></span>
						</div>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	</div>
</div>
