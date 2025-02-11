<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
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

del.diffmod,
.diffdel,
.diffdel > p,
.diff-html-removed,
.deleted {
	background: #ffe9e9 !important;
}

ins.diffmod,
.diffins,
.diffins > p,
.diff-html-added,
.added {
	background: #e9ffe9 !important;
}

.block-compare {
	width: 100% !important;
}
</style>

<?php echo $html; ?>
