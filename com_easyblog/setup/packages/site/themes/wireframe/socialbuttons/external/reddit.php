<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<script type="text/javascript">
reddit_url = "<?php echo $url;?>";
reddit_title = "<?php echo $title;?>";
</script>

<?php if ($size == 'small') { ?>
<script type="text/javascript" src="//www.redditstatic.com/button/button1.js"></script>
<?php } ?>

<?php if ($size == 'large') { ?>
<script type="text/javascript" src="https://www.reddit.com/static/button/button3.js"></script>
<?php } ?>