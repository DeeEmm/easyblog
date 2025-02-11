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

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldModal_MultiBloggers extends JFormField
{
	protected $type = 'Modal_MultiBloggers';

	protected function getInput()
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

		if (!EB::isFoundryEnabled()) {
			return;
		}

		// @task: Load Joomla's modal dialog
		JHTML::_( 'behavior.modal' );

		// @task: Load easyblog's language here.
		JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT . '/administrator');

		$app = JFactory::getApplication();
		$profiles = array();

		if ($this->value) {
			$ids = explode(',', trim($this->value));

			if ($ids && count($ids) > 0) {
				EB::user($ids);

				foreach ($ids as $id) {
					$author = EB::user($id);
					$profiles[] = $author;
				}
			}
		}

		ob_start();
		?>

<script type="text/javascript">
EasyBlog.ready(function($){

	window.insertAuthor = function( id , name , uid ){

		// @task: Insert the result
		window.insertResult( id );

		// @task: Hide the Select / Change button after it has been selected.
		$( '.uid-' + uid ).find( '.select-author' ).remove();

		// @task: Set the name for the input.
		$( '.uid-' + uid ).find( '.author-name' ).val( name );

		// @task: Set the id
		$( '.uid-' + uid ).find( '.author-id' ).val( id );

		// @task: Once item is inserted, hide the modal dialog.
		SqueezeBox.close();
	}

	window.duplicateAuthor	= function( name , id ){
		var row	= $( 'table .dummy-item' ).clone(),
			uid = $.uid();

		// @task: Remove dummy-item
		$( row ).removeClass( 'dummy-item' );

		// @task: Set the name on the input
		$( row ).find( '.author-name' ).val( name );

		// @task: Set the uid class to the row.
		$( row ).addClass( 'uid-' + uid );

		// @task: Now we need to hack the url of the modal so that it includes the proper uid.
		$( row ).find( '.modal' ).attr( 'href' , $( row ).find( '.modal' ).attr( 'href' ) + '&uid=' + uid );

		// @task: Add a new row into the table.
		$( '#blog-authors .add-author' ).before( $(row).show() );

		// @task: If the name is not empty, then it means that it has already been selected.
		if( name != '' && name != '<?php echo JText::_( 'COM_EASYBLOG_SELECT_A_USER' , true ); ?>' )
		{
			$( row ).find( '.select-author' ).remove();
		}

		if( id != '' )
		{
			$( row ).find( '.author-id' ).val( id );
		}

		// @task: Reinitialize Joomla's squeeze box.
		SqueezeBox.initialize({});
		SqueezeBox.assign($$('a.modal'), { parse: 'rel' });
	}

	/**
	 * Removes an author record.
	 */
	window.removeAuthor = function(element){

		var id 	= $( element ).parents( 'tr' ).find( '.author-id' ).val();

		$( element ).parents( 'tr' ).remove();

		window.removeResult( id );
	}

	window.insertResult = function( currentId ){
		var currentResult 	= $( '#<?php echo $this->id;?>_id' ).val(),
			currentResult	= currentResult == '' ? new Array() : currentResult.split( ',' );

		currentResult.push( currentId );

		window.updateResult( currentResult );
	}

	window.updateResult = function( result ){

		var unique = result.filter( function( item , i , a ){
			return i == a.indexOf( item );
		});

		$( '#<?php echo $this->id;?>_id' ).val( unique.join(',') );
	}

	/**
	 * Removes a value from the real result
	 */
	window.removeResult = function( currentId ){

		if( currentId == '' )
		{
			return false;
		}

		var currentResult 	= $( '#<?php echo $this->id;?>_id' ).val(),
			currentResult	= currentResult.split( ',' );

		for(var i=0; i<currentResult.length; i++)
		{
			if( currentResult[ i ] == currentId )
			{
				currentResult.splice( i , 1 );
				break;
			}
		}

		window.updateResult( currentResult );
	}

	// @task: If there's no value or empty value, let's just show a single row
	<?php if( !$this->value ){ ?>
		window.duplicateAuthor( '<?php echo JText::_( 'COM_EASYBLOG_SELECT_A_USER' , true ); ?>' );
	<?php } ?>

	<?php if( $profiles ){ ?>
		<?php foreach( $profiles as $profile ){ ?>
			window.duplicateAuthor( '<?php echo $profile->getName();?>' , '<?php echo $profile->id;?>');
		<?php } ?>
	<?php } ?>
});
</script>

		<table id="blog-authors">
			<tr class="dummy-item" style="display:none;">
				<td>
					<div style="float:left;">
						<input type="text" class="input author-name" readonly="readonly" value="" disabled="disabled" style="background: #ffffff;width: 200px;" />
					</div>
					<div class="button2-left select-author">
						<div class="blank">
							<a rel="{handler: 'iframe', size: {x: 750, y: 475}}" href="<?php echo JRoute::_( 'index.php?option=com_easyblog&view=bloggers&tmpl=component&browse=1&browsefunction=insertAuthor' );?>" class="modal"><?php echo JText::_( 'COM_EASYBLOG_SELECT_OR_CHANGE' ); ?></a>
						</div>
					</div>
					<div class="button2-left remove-author">
						<div class="blank">
							<a href="javascript:void(0);" onclick="window.removeAuthor(this);"><?php echo JText::_( 'COM_EASYBLOG_REMOVE');?></a>
						</div>
						<input type="hidden" class="author-id" value="" />
					</div>
				</td>
			</tr>
			<tr class="add-author">
				<td>
					<div class="button2-left remove-author">
						<div class="blank">
							<a href="javascript:void(0);" onclick="window.duplicateAuthor('');"><?php echo JText::_( 'COM_EASYBLOG_ADD');?></a>
						</div>
					</div>
				</td>
			</tr>
		</table>


		<input type="hidden" id="<?php echo $this->id;?>_id" name="<?php echo $this->name; ?>" value="<?php echo $this->value;?>" />
		<?php
		$output		= ob_get_contents();
		ob_end_clean();

		return $output;
	}
}
