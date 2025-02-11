<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Libraries\Colors;

$color = new Colors($this->getThemeParams()->get('params_brand', '#BED274'));
?>
<style type="text/css">

#eb {
	--eb-divider-bg: #<?php echo $color->darken(2); ?>;
}
#eb #fd {
	--fd-toolbar-text: #fff;
}

#eb.eb-pastel {
	background-color: #<?php echo $color->lighten(26.5); ?>;
}
#eb #fd .fd-toolbar__search,
#eb #fd .fd-toolbar {
	padding:  4px 8px;
	border-radius: 4px;
}
#eb #fd .fd-toolbar{
	background-color: <?php echo $color;?>;
}
#eb #fd .fd-toolbar__link.is-composer {
	background: #<?php echo $color->darken(14); ?>;
}

#eb #fd .fd-toolbar .is-active .fd-toolbar__link:not(.no-active-state),
#eb #fd .fd-toolbar .is-active .fd-toolbar__submenu-link {
	color: #fff;
	background: #<?php echo $color->darken(14); ?>;
}
#eb #fd .fd-toolbar .is-active .fd-toolbar__link:not(.no-active-state):hover,
#eb #fd .fd-toolbar .is-active .fd-toolbar__submenu-link:hover {
	color: #fff;
	background: #<?php echo $color->darken(4); ?>;
}
#eb #fd .fd-toolbar__link:hover {
	background: #<?php echo $color->darken(4); ?>;
}
#fd .fd-toolbar__link-bubble {
	/*background: #<?php echo $color->darken(20); ?>;*/
}

#eb .eb-comment-form,
#eb .eb-comment,
#eb .eb-calendar__item:hover,
#eb .form-actions,
#eb .eb-calendar tbody > tr > td.has-posts > div,
#eb .eb-authors-head,
#eb .eb-empty {
	background-color: #<?php echo $color->lighten(30); ?>;
}

#eb .eb-stats-nav > li.active > .btn::before,
#eb .eb-stats-nav > li .btn:hover:before,
#eb .eb-stats-nav > li .btn:focus::before,
#eb .eb-comment-editor .markItUpHeader ul > li.markItUpSeparator {
	background-color: #<?php echo $color->darken(14); ?> !important;
}

#eb .eb-comment-editor .markItUpHeader ul > li > a {
	background-color: #<?php echo $color->lighten(24); ?> !important;
}

#eb .eb-mag-header-title::after,
#eb .eb-comment-editor .markItUpFooter,
#eb .eb-comment-editor .markItUpHeader,
/*#eb .eb-comments-empty,*/
#eb .eb-entry-author-recents,
#eb .eb-entry-nav > div > a:hover,
#eb .eb-pager,
#eb .eb-reactions__options,
#eb .eb-calendar tbody > tr > td.empty > small,
#eb .eb-calendar-days > td,
#eb .eb-category-profile {
	background-color: #<?php echo $color->lighten(24); ?>;
}
#eb .eb-category-bio {
	border-color: #<?php echo $color->lighten(24); ?>;
}

#eb .eb-toolbar__search,
#eb .eb-stats-nav > li.active > .btn::before,
#eb .eb-toolbar {
	background-color: <?php echo $color;?>;
}

#eb .eb-toolbar__search .btn-search-submit,
#eb .eb-toolbar__search .o-select-group,
#eb .o-nav__item.is-active .eb-toolbar__link,
#eb .eb-toolbar__link:hover,
#eb .eb-toolbar__link:active,
#eb .eb-toolbar__link:focus {
	background-color: #<?php echo $color->darken(15); ?> !important;
}

#eb .eb-toolbar__link.has-composer,
#eb .eb-toolbar__link.has-composer:hover,
#eb .eb-toolbar__link.has-composer:focus,
#eb .eb-toolbar__link.has-composer:active {
	background-color: #<?php echo $color->darken(22); ?>;
}

#eb .eb-toolbar-profile__bd,
#eb .form-actions,
#eb .eb-category-subscribe > span + span,
#eb .eb-posts-search .eb-post-meta,
#eb .eb-comment-editor .markItUpContainer,
#eb .eb-comment-editor .markItUpFooter,
#eb .eb-comment-editor .markItUpHeader,
#eb .eb-comment,
#eb .eb-comment-form,
#eb .eb-comments-empty,
#eb .eb-entry-author-recents > div + div,
#eb .eb-entry-author-recents,
#eb .eb-section-heading,
#eb .eb-entry-nav,
#eb .eb-entry-nav-next,
#eb .popbox-dropdown__hd,
#eb .popbox-dropdown-nav__item + .popbox-dropdown-nav__item,
#eb .dropdown-menu,
#eb .eb-calendar-tooltips,
#eb .eb-calendar-tooltips > div + div,
#eb .eb-calendar-tooltips > span,
#eb .eb-calendar-days > td:first-child,
#eb .eb-calendar-days > td:last-child,
#eb .eb-calendar tbody > tr > td,
#eb .eb-empty,
#eb .eb-simple-posts > div + div,
#eb .eb-stats-nav > li .btn,
#eb .eb-authors-subscribe > span + span,
#eb .eb-author-filter,
#eb .eb-showcases,
#eb .eb-post,
#eb .eb-post + .eb-post,
#eb .eb-entry,
#eb .eb-post-foot,
#eb .eb-pager,
#eb .eb-pager__fast-first-link,
#eb .eb-pager__fast-last-link,
#eb .eb-pager > div,
#eb .eb-toolbar__search .btn-default,
#eb .eb-tags-item {
	/*#e1ebbd*/
	border-color: #<?php echo $color->lighten(19.5); ?> !important;
}
#eb .dropdown-menu.bottom-right .eb-arrow::after {
	border-bottom-color: #<?php echo $color->lighten(19.5); ?>;
}

#eb .eb-calendar tbody > tr > td:nth-child(5) > div .eb-calendar-tooltips::after, #eb .eb-calendar tbody > tr > td:nth-child(6) > div .eb-calendar-tooltips::after, #eb .eb-calendar tbody > tr > td:nth-child(7) > div .eb-calendar-tooltips::after {
	border-left-color: #<?php echo $color->lighten(15); ?> ;
}
#eb .eb-calendar-tooltips::after {
	border-right-color: #<?php echo $color->lighten(15); ?> ;
}
#eb .eb-calendar-tooltips > span {
	background-color: #<?php echo $color->lighten(15); ?> !important;
}
#eb .eb-reading-progress {background: <?php echo $this->config->get('main_reading_background');?> !important;color: <?php echo $this->config->get('main_reading_foreground');?> !important;}
#eb .eb-reading-progress::-webkit-progress-bar {background: <?php echo $this->config->get('main_reading_background');?> !important;}
#eb .eb-reading-progress__container {background-color: <?php echo $this->config->get('main_reading_background');?> !important;}
#eb .eb-reading-progress::-moz-progress-bar {background: <?php echo $this->config->get('main_reading_foreground');?> !important;}
#eb .eb-reading-progress::-webkit-progress-value {background: <?php echo $this->config->get('main_reading_foreground');?> !important;}
#eb .eb-reading-progress__bar {background: <?php echo $this->config->get('main_reading_foreground');?> !important;}
</style>
