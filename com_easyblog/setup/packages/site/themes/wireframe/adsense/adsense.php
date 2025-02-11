<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

$align = (isset($alignment)) ? $alignment : '';

if (!empty($align)) {
    $align = ($align == 'right') ? ' pull-right' : ' pull-left';
}

?>

<div class="adsense-wrap <?php echo $align; ?>">
	<script type="text/javascript"><!--
	<?php echo html_entity_decode("$adsense\n"); ?>
	//--></script>
	<script type="text/javascript" src="https://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
</div>