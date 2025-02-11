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
<style amp-custom>
body {
	font-family: 'Heebo', sans-serif;
	font-size: 18px;
	line-height: 1.6;
}
header {
	background-color: #222;
	color: #fff;
	padding: 10px;
	position: relative;
	
}
.heading > h1 > a {
	text-decoration: none;
	color: #1b69b6;
}
.toggle-btn {
	position: absolute;
	color: #fff;
	top: 8px;
	<?php echo $isRtl ? 'left: 8px;' : 'right: 8px;'; ?>
}

h1 {
	font-size: 30px;
}
h2 {
	font-size: 26px;
}
h3 {
	font-size: 22px;
}
h4 {
	font-size: 20px;
}
h1, h2, h3, h4 {
	margin-bottom: 10px;
	padding: 0 0 10px;
}

p {	
	color: #222;
	background-color: #fff;
	margin-bottom: 10px;
	padding: 0 0 10px;
}
hr {
	margin-top: 18px;
	margin-bottom: 18px;
	border: 0;
	border-top: 1px solid #eeeeee;
}
table {
	table-layout: fixed;
	border-collapse: collapse;
	border-spacing: 0;
	font-size: 16px;
}
.table td {
	vertical-align: top;
	padding: 6px;
}
.table-bordered {
	border: 1px solid #ddd;
}
.table-bordered > thead > tr > th,
.table-bordered > thead > tr > td,
.table-bordered > tbody > tr > th,
.table-bordered > tbody > tr > td,
.table-bordered > tfoot > tr > th,
.table-bordered > tfoot > tr > td {
  border: 1px solid #ddd;
}
.table-bordered > thead > tr > th,
.table-bordered > thead > tr > td {
  border-bottom-width: 2px;
}
.table-striped > tbody > tr:nth-child(odd) > td,
.table-striped > tbody > tr:nth-child(odd) > th {
  background-color: #f9f9f9;
}
amp-sidebar {
	width: 150px;
	position: relative;
	background: #fff;
	
}
amp-sidebar .close-btn {
	font-size: 30px;
	position: absolute;
	right: 0;
	top: 0;
	width: 40px;
	height: 30px;
	line-height: 1;
	padding: 0;
}
.sidebar-nav {
	position: relative;
	top: 40px;
}
nav ul {
	margin: 0;
	padding: 0;
	border-top: 1px solid #ccc;
}
nav li {
	list-style: none;
	padding: 0;
	margin: 0;
}
nav li a {
	padding: 10px;
	text-decoration: none;
	color: #666666;
	background: #fff;
	border-bottom: 1px solid #ccc;
	display: block;
	font-size: 14px;
}

.blog-content {
	padding: 16px;
}
.blog-meta {
	font-size: 13px;
	padding:  16px;
	color: #666;
	border-bottom: 1px solid #ccc;
}
.blog-meta a {
	text-decoration: none;
	color: #1b69b6;
}
.blog-meta a:hover,
.blog-meta a:focus {
	color: #134a81;
}
.blog-meta__author {
	margin-bottom: 8px;
}

.carousel .slide > amp-img > img {
	object-fit: contain;
}

.related {
	background-color: #f5f5f5;
	margin: 10px;
	display: block;
	color: #111;
	height: 75px;
	padding: 0;
	text-decoration: none;
	display: flex;
}
.related__img {

}
.related__body {
	padding: 10px;
	flex: 0 1 auto;
	display: flex;
	overflow: hidden;
	width: 100%;
}
.related__title {
	display: block;
	overflow: hidden;

}
.related__date {
	color: #888;
	font-size: 13px;
}
.related__link {
	text-decoration: none;
	display: block;
	align-self: center;
	width: 100%;
	height: 100%;
	overflow: hidden;
	color: #1155CC;
}

.btn-eb {
	display: inline-block;
	margin-bottom: 0;
	font-weight: normal;
	text-align: center;
	vertical-align: middle;
	background-image: none;
	border: 1px solid transparent;
	white-space: nowrap;
	padding: 6px 12px;
	font-size: 14px;
	line-height: 1.428571429;
	border-radius: 4px;
	text-decoration: none;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	-o-user-select: none;
	user-select: none;

	color: #333;
	background-color: #fff;
	border-color: #ccc;
}
.btn-eb-comment {
	margin: 0 16px 16px;
	display: block;
}
</style>
