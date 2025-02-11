<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<span class="small">(
<?php if( $user->getPoints() > 0 ){ ?>
<a href="<?php echo FRoute::points( array( 'id' => $user->id , 'layout' => 'history' ) );?>"><strong><?php echo $user->getPoints();?> <?php echo JText::_( 'COM_EASYBLOG_POINTS' );?></strong></a>
<?php } else { ?>
<?php echo JText::_( 'COM_EASYBLOG_NO_POINTS_YET' ); ?>
<?php } ?>
)</span>
