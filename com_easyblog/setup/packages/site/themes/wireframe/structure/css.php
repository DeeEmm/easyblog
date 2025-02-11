<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<style type="text/css">

#eb .eb-header .eb-toolbar,
#eb .eb-toolbar__search { background-color: <?php echo $this->config->get('layout_toolbarcolor', '#333333');?>;}

#eb .eb-header .eb-toolbar,
#eb .eb-toolbar__item--search,
#eb .eb-toolbar__search .o-select-group .form-control {border-color: <?php echo $this->config->get('layout_toolbarbordercolor', '#333333');?>; }

#eb .eb-toolbar__search-close-btn > a,
#eb .eb-toolbar__search-close-btn > a:hover,
#eb .eb-toolbar__search-close-btn > a:focus,
#eb .eb-toolbar__search-close-btn > a:active,
#eb .eb-header .eb-toolbar .eb-toolbar__search-input,
#eb .eb-header .eb-toolbar .o-nav__item .eb-toolbar__link,
#eb .eb-toolbar__search .o-select-group .form-control,
#eb .eb-toolbar .btn-search-submit { color: <?php echo $this->config->get('layout_toolbartextcolor', '#FFFFFF')?> !important; }
#eb .eb-toolbar__search .o-select-group__drop {
	color: <?php echo $this->config->get('layout_toolbartextcolor', '#FFFFFF')?>;
}
#eb .eb-toolbar__search .o-select-group,
#eb .eb-header .eb-toolbar .o-nav__item.is-active .eb-toolbar__link,
#eb .eb-header .eb-toolbar .o-nav__item .eb-toolbar__link:hover,
#eb .eb-header .eb-toolbar .o-nav__item .eb-toolbar__link:focus,
#eb .eb-header .eb-toolbar .o-nav__item .eb-toolbar__link:active { background-color: <?php echo $this->config->get('layout_toolbaractivecolor', '#5c5c5c')?>; }


#eb .eb-toolbar__link.has-composer,
#eb .eb-toolbar .btn-search-submit {background-color: <?php echo $this->config->get('layout_toolbarcomposerbackgroundcolor', '#428bca')?> !important; }
#eb .eb-reading-progress {background: <?php echo $this->config->get('main_reading_background');?> !important;color: <?php echo $this->config->get('main_reading_foreground');?> !important;}
#eb .eb-reading-progress::-webkit-progress-bar {background: <?php echo $this->config->get('main_reading_background');?> !important;}
#eb .eb-reading-progress__container {background-color: <?php echo $this->config->get('main_reading_background');?> !important;}
#eb .eb-reading-progress::-moz-progress-bar {background: <?php echo $this->config->get('main_reading_foreground');?> !important;}
#eb .eb-reading-progress::-webkit-progress-value {background: <?php echo $this->config->get('main_reading_foreground');?> !important;}
#eb .eb-reading-progress__bar {background: <?php echo $this->config->get('main_reading_foreground');?> !important;}

<?php if ($this->config->get('layout_composer_enforce_fontstyle')) { ?>
#eb .ebd-block[data-type="text"] p,
#eb [data-blog-post-content] p {
	font-size: <?php echo $this->config->get('layout_composer_fontsize'); ?>px;
	line-height: <?php echo $this->config->get('layout_composer_lineheight', '1.2'); ?>;
	<?php echo $this->config->get('layout_composer_fontfamily') !== 'default' ? 'font-family:' . $this->config->get('layout_composer_fontfamily') . ';' : ''; ?>
}
<?php } ?>
</style>
