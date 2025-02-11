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

if( $this->input->get( 'external', '' ) != '' )
{
	$groupId	= $external;
}

?>
<script type="text/javascript">
EasyBlog.ready(function($)
{
	$( '#group-blog-contribute' ).change(function()
	{
		$( '.blogContributeTeamRadio' ).attr( 'checked' , false );

		eblog.dashboard.changeCollab('jomsocial');
		$(".hidden_blog_contribute").val(this.value);
	});

	<?php if (isset($groupId) && $groupId) { ?>
		eblog.dashboard.changeCollab('jomsocial');
	<?php } ?>
});
</script>

<select name="blog_contribute" class="group-blog-contribute" id="group-blog-contribute" data-blog-contribute>
	<option>-- Select group --</option>
	<?php foreach( $groups as $group ){ ?>
	<option <?php echo $groupId == $group->id && $blogSource == 'group' ? ' selected' : '';?> value="<?php echo $group->id;?>"><?php echo $group->title;?></option>
	<?php } ?>
</select>
