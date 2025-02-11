<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
EasyBlog.ready(function($)
{
	$( '#event-blog-contribute' ).change(function()
	{
		$( '.blogContributeTeamRadio' ).attr( 'checked' , false );

		 eblog.dashboard.changeCollab('jomsocial.event');
		 $(".hidden_blog_contribute").val(this.value);
	});
});
</script>

<select name="blog_contribute" class="event-blog-contribute" id="event-blog-contribute" data-blog-contribute>
	<option>-- Select event --</option>
	<?php foreach( $events as $event ){ ?>
	<option <?php echo $external == $event->id && $blogSource == 'event' ? ' selected' : '';?> value="<?php echo $event->id;?>"><?php echo $event->title;?></option>
	<?php } ?>
</select>


