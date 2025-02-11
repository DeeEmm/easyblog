<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Libraries\Colors;

$color = new Colors($this->getThemeParams()->get('params_brand', '#134270'));
$background = new Colors($this->getThemeParams()->get('params_background', '#444444'));
$text = new Colors($this->getThemeParams()->get('params_text', '#aaaaaa'));
$toolbartext = new Colors($this->getThemeParams()->get('params_toolbartext', '#ffffff'));
$border = new Colors($this->getThemeParams()->get('params_border', '#333333'));
$darker = new Colors($color->darken(2));


?>
<style type="text/css">
#eb {
	--eb-divider-bg: <?php echo $background;?>;
}
#eb #fd {
	--fd-toolbar-text: <?php echo $toolbartext;?>;
}
#eb #fd .fd-toolbar__search {
	padding: 4px 8px;
}
#eb #fd .fd-toolbar{
	padding: 4px 8px;
	background-color: <?php echo $color;?>;
}
#eb #fd .fd-toolbar__link.is-composer {
	background: #<?php echo $color->darken(8); ?>;
}
#eb #fd .fd-toolbar__link:hover {
	background: #<?php echo $color->darken(4); ?>;
}

/*/Slider*/
#eb .eb-featured .eb-gallery-stage {
	background: #<?php echo $background->lighten(1); ?>;
}
#eb .eb-featured .eb-gallery-content {
	color: #<?php echo $text->getHex();?>;
}

#eb #fd .fd-toolbar .is-active .fd-toolbar__link:not(.no-active-state),
#eb #fd .fd-toolbar .is-active .fd-toolbar__submenu-link {
	/*color: <?php echo $color;?>;*/
	color: #fff;
	background: #<?php echo $color->darken(8); ?>;
}
#eb #fd .fd-toolbar .is-active .fd-toolbar__link:not(.no-active-state):hover,
#eb #fd .fd-toolbar .is-active .fd-toolbar__submenu-link:hover {
	color:  <?php echo $toolbartext;?>;
	color: #fff;
	background: #<?php echo $color->darken(4); ?>;
}

#eb .eb-header .eb-toolbar { background-color: <?php echo $color;?>; }

#eb .eb-toolbar__search .btn-search-submit,
#eb .eb-toolbar__search .o-select-group,
#eb .eb-header .eb-toolbar .o-nav__item.is-active .eb-toolbar__link,
#eb .eb-header .eb-toolbar .o-nav__item .eb-toolbar__link:hover,
#eb .eb-header .eb-toolbar .o-nav__item .eb-toolbar__link:focus,
#eb .eb-header .eb-toolbar .o-nav__item .eb-toolbar__link:active {
	background-color: #<?php echo $color->darken(4); ?> !important;
}



#eb .eb-comment-editor .markItUpContainer .markItUpExpanding .markItUpEditor,
#eb .eb-comment-editor .markItUpFooter,
#eb .eb-comment-form .markItUpHeader,
#eb .eb-entry,
#eb .form-control,
#eb .eb-calendar-tooltips,
#eb .eb-calendar,
#eb .eb-header,
#eb .eb-category,
#eb .eb-author,
#eb .eb-authors-head,
#eb .popbox-dropdown-nav__item,
#eb .eb-showcases,
#eb .eb-post-listing .eb-post,
#eb .eb-post-foot,
#eb .eb-comment,
#eb .eb-comment-form,
#eb .eb-comments-empty,
#eb .eb-table-filter,
#eb .eb-head,
#eb .eb-empty,
#eb .popbox-dropdown,
#eb .o-select-group,
#eb .dropdown-menu {
	background-color: <?php echo $background;?>;
}

#eb .eb-comment-editor .markItUpHeader ul > li > a,
#eb .eb-comment-editor .markItUpHeader ul > li > a:hover {
	background-color: <?php echo $background;?> !important;
}

#eb .eb-category .btn-default,
#eb .eb-author .btn-default,
#eb .btn-default,
#eb .btn-default:hover,
#eb .btn-default:focus,
#eb .btn-default:active,
#eb .btn-default.active,
#eb .dropdown-toggle_ .btn-default,
#eb .eb-toolbar__search-input,
#eb .eb-toolbar__search .btn-default {
	/*background-color: #<?php echo $color->lighten(1); ?> !important;*/
	background-color: <?php echo $color;?> !important;
}

#eb .eb-entry-author-recents,
#eb .eb-calendar tbody > tr > td.empty > small {
	background-color: #<?php echo $background->lighten(3); ?> !important;
}

#eb .eb-calendar tbody > tr > td.has-posts > div {
	background-color: #<?php echo $background->lighten(10); ?> !important;
}

#eb .eb-calendar tbody > tr > td:nth-child(5) > div .eb-calendar-tooltips::after, #eb .eb-calendar tbody > tr > td:nth-child(6) > div .eb-calendar-tooltips::after, #eb .eb-calendar tbody > tr > td:nth-child(7) > div .eb-calendar-tooltips::after {
	border-left-color: #<?php echo $background->darken(10); ?> ;
}
#eb .eb-calendar-tooltips::after {
	border-right-color: #<?php echo $background->darken(10); ?> ;
}
#eb .eb-calendar-tooltips > span {
	background-color: #<?php echo $background->darken(10); ?> !important;
}

#eb .eb-calendar-days > td,
#eb .eb-stats-nav > li .btn,
#eb .eb-stats-nav > li .btn:hover,
#eb .eb-stats-nav > li.active .btn,
#eb .eb-post-side,
#eb .eb-post-foot,
#eb .eb-entry-nav,
#eb .eb-entry-nav > div > a,
#eb .eb-pager {
	background-color: <?php echo $color;?> !important;
}

#eb .eb-pager > a::after,
#eb .eb-pager > div::after,
#eb .eb-post-foot::before,
#eb .eb-stats-nav > li.active > .btn::before {
	background-color: #<?php echo $color->lighten(10); ?>;
}

#eb .popbox-dropdown-nav__item:hover,
#eb .eb-tags-item,
#eb .table-striped > tbody > tr:nth-child(2n+1) > td,
#eb .table-striped > tbody > tr:nth-child(2n+1) > th {
	background-color: #<?php echo $background->lighten(2); ?>;
}


#eb .eb-entry-nav > div > a:before,
#eb .eb-entry-nav > div > a:hover {
	background-color: #<?php echo $color->lighten(2); ?>;
}

#eb .dropdown-menu,
#eb .eb-comment-editor .markItUpContainer .markItUpExpanding .markItUpEditor,
#eb .eb-entry,
#eb .o-select-group,
#eb .form-control,
#eb .eb-calendar tbody > tr > td.has-posts > div,
#eb .eb-calendar-tooltips,
#eb .popbox-dropdown-nav__name,
#eb .popbox-dropdown-nav__post-user-name,
#eb .popbox-dropdown-nav__item,
#eb .popbox-dropdown,
#eb .eb-elegant,
#eb .eb-authors-head
#eb .eb-showcase-title,
#eb .eb-showcases,
#eb .eb-toolbar__search-input,
#eb .eb-post-listing .eb-post,
#eb .eb-comments-empty,
#eb .eb-comment,
#eb .eb-comment-form,
#eb .eb-head,
#eb .eb-empty,
#eb .eb-header,
#eb .eb-header h1 {
	color: <?php echo $text->getHex();?>;
}

#eb .btn-default,
#eb .btn-default:hover,
#eb .btn-default:focus,
#eb .btn-default:active,
#eb .btn-default.active,
#eb .dropdown-toggle_ .btn-default,
#eb .eb-pager, #eb .eb-post-listing .eb-post,
#eb .eb-post-foot, #eb .eb-entry-nav-next,
#eb .eb-head, #eb .eb-table-filter,
#eb .table > thead > tr > th,
#eb .table > thead > tr > td,
#eb .table > tbody > tr > th,
#eb .table > tbody > tr > td,
#eb .table > tfoot > tr > th,
#eb .table > tfoot > tr > td,
#eb .eb-table tbody > tr td .post-title,
#eb .eb-toolbar__search .o-select-group .form-control,
#eb .markItUp .markItUpButton a::before,
#eb .eb-toolbar__search .btn-default {
	color: <?php echo $toolbartext;?> !important;
}


#eb .eb-post-side .fa,
#eb .eb-entry-nav > div > a .fa
{
	color: #<?php echo $color->lighten(40); ?>;
}

#eb .eb-post-hits,
#eb .eb-post-foot > div + div,
#eb .eb-post-foot,
#eb .eb-post-foot > div .fa,
#eb .eb-post-comments a,
#eb .eb-toolbar__link,
#eb .eb-toolbar__link:hover,
#eb .eb-toolbar__link.has-composer,
#eb .eb-toolbar__link.has-composer:hover {
	color: <?php echo $toolbartext;?>;
}

#eb .eb-showcases,
#eb .eb-calendar-days > td:first-child,
#eb .eb-calendar-days > td:last-child,
#eb .eb-calendar tbody > tr > td,
#eb .form-control,
#eb .popbox-dropdown-nav__item + .popbox-dropdown-nav__item,
#eb .popbox-dropdown__hd,
#eb .eb-toolbar__dropdown-menu,
#eb hr,
#eb .eb-pager,
#eb .eb-post-listing .eb-post,
#eb .eb-post-foot,
#eb .eb-entry-nav-next,
#eb .eb-head,
#eb .eb-table-filter,
#eb .table > thead > tr > th, #eb .table > thead > tr > td, #eb .table > tbody > tr > th,
#eb .table > tbody > tr > td, #eb .table > tfoot > tr > th, #eb .table > tfoot > tr > td,
#eb .eb-header .eb-toolbar,
#eb .eb-toolbar__item--search,
#eb .eb-entry,
#eb .eb-entry-nav {
	border-color: <?php echo $border;?>;
}
#eb .dropdown-menu.bottom-right .eb-arrow::after {
	border-bottom-color: <?php echo $border;?>;
}

#eb .btn-default,
#eb .btn-default:hover,
#eb .btn-default:focus,
#eb .btn-default:active {
	border-color: <?php echo $border;?> !important;
}

#eb .eb-category .btn-default, #eb .eb-author .btn-default,
#eb .eb-toolbar__search-input,
#eb .eb-toolbar__search .btn-default,
#eb .eb-toolbar__search .o-select-group .form-control {
	border-color: <?php echo $color;?> !important;
}
#eb .dropdown-menu.bottom-right .eb-arrow {
	border-bottom-color: <?php echo $background;?>;
}

#eb .o-select-group__drop {
	color: #<?php echo $text->getHex();?> transparent transparent  transparent !important;
}

#eb .eb-post-side:after,
#eb .eb-post-foot {
	border-top-color: <?php echo $color;?>;
}
#eb .eb-entry-helper > div + div,
#eb .eb-post-foot > div + div {
	border-left-color: #<?php echo $border->lighten(5); ?>;
}

#eb .eb-pager__fast-first-link,
#eb .eb-pager__fast-last-link,
#eb .eb-pager > div {
	border-color: #<?php echo $color->darken(5); ?>;
}
#eb .eb-reading-progress {background: <?php echo $this->config->get('main_reading_background');?> !important;color: <?php echo $this->config->get('main_reading_foreground');?> !important;}
#eb .eb-reading-progress::-webkit-progress-bar {background: <?php echo $this->config->get('main_reading_background');?> !important;}
#eb .eb-reading-progress__container {background-color: <?php echo $this->config->get('main_reading_background');?> !important;}
#eb .eb-reading-progress::-moz-progress-bar {background: <?php echo $this->config->get('main_reading_foreground');?> !important;}
#eb .eb-reading-progress::-webkit-progress-value {background: <?php echo $this->config->get('main_reading_foreground');?> !important;}
#eb .eb-reading-progress__bar {background: <?php echo $this->config->get('main_reading_foreground');?> !important;}
</style>
