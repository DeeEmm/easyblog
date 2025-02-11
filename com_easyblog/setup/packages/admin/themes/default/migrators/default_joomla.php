<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div id="config-document" style="padding:10px">
	<div id="page-joomla" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('articles');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-smartblog" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('smartblog');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-lyften" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('lyften');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-wordpress" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('wordpress');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-wordpressimport" class="tab">
	    <div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('wordpressimport');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-k2" class="tab">
		<div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('k2');?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="page-blogger" class="tab">
		<div>
			<table class="noshow">
				<tr>
					<td><?php echo $this->loadTemplate('bloggerimport');?></td>
				</tr>
			</table>
		</div>
	</div>
</div>
<div class="clr"></div>
