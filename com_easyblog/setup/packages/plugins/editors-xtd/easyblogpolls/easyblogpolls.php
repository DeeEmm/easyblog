<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class plgButtonEasyBlogPolls extends JPlugin
{
	/**
	 * Renders the button when editor is rendered
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function onDisplay($name)
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		if ($input->get('option', '', 'string') !== 'com_easyblog') {
			return;
		}

		if (!$this->exists()) {
			return;
		}

		// Load its plugin language
		JPlugin::loadLanguage('plg_editors-xtd_easyblogpolls');

		$script = $this->getScript($name);

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($script);

		$button = new JObject;
		$button->modal = false;
		$button->onclick = 'displayEasyBlogPoll();return false;';
		$button->text = JText::_('PLG_EB_POLLS_BUTTON');
		$button->name = 'bars';
		$button->link = '#';

		return $button;
	}

	private function exists()
	{
		static $exists = null;

		if (is_null($exists)) {

			$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
			$exists = JFile::exists($file);

			if (!$exists) {
				return false;
			}

			require_once($file);

			if (!EB::isFoundryEnabled()) {
				$exists = false;
			}
		}

		return $exists;
	}

	/**
	 * Renders the button's script
	 *
	 * @since	6.0.0
	 * @access	private
	 */
	private function getScript($editorName)
	{
ob_start();
?>
			var editor = "<?php echo $editorName;?>";

			window.displayEasyBlogPoll = function() {
				EasyBlog.ready(function($) {
					var isSaving = false;

					EasyBlog.dialog({
						"content": EasyBlog.ajax('site/views/polls/form', {
							'pollId': 0,
							'isComposer': 1
						}),
						"bindings": {
							'{saveButton} click': function(el, event) {

								if (isSaving) {
									return;
								}

								isSaving = true;

								var footer = $(el).closest('.eb-dialog-footer');
								var modal = footer.closest('.eb-dialog-modal');
								var content = modal.find('.eb-dialog-content');
								var form = content.find('[data-eb-poll-form]');
								var errorWrapper = content.find('[data-poll-error]');

								var selectPollOption = form.find('[data-eb-poll-form-select]');
								var createPollOption = form.find('[data-eb-poll-form-create]');
								var postData = {};

								// Just in case because when there is no poll to select, no options will be shown
								var formOption = 'savePoll';

								if (selectPollOption.is(':checked')) {
									var selectedPollId = form.find('[data-unassociated-post-polls-list]').val();

									formOption = 'selectPoll';
									postData.selectedPollId = selectedPollId;
								}

								// This can be create/update poll
								if (formOption == 'savePoll' || createPollOption.is(':checked')) {
									var title = form.find('[data-eb-poll-form-title]').val();
									var multiple = form.find('[data-eb-poll-form-multiple]').find('input[type="hidden"]').val();
									var unvote = form.find('[data-eb-poll-form-unvote]').find('input[type="hidden"]').val();
									var expiry_date = form.find('[data-eb-poll-form-expiration]').find('[data-datetime]').val();
									var itemsWrapper = form.find('[data-eb-poll-form-item]');

									// Format items into an array with objects
									var items = [];

									$.each(itemsWrapper, function(index, item) {
										var _el = $(item);
										var itemId = _el.data('id');
										var value = _el.find('input').val();

										var obj = {
											'id': itemId ? itemId : 0,
											'content': value
										};

										items.push(obj);
									});

									formOption = 'savePoll';

									postData.title = title;
									postData.items = items;
									postData.multiple = parseInt(multiple);
									postData.unvote = parseInt(unvote);
									postData.expiry_date = expiry_date;
								}

								// Always hide the error
								errorWrapper.addClass('t-hidden');

								// Display the loader on the button
								$(el).addClass('is-loading');

								EasyBlog.ajax('site/views/polls/save', {
									'formOption': formOption,
									'pollId': 0,
									'postData': JSON.stringify(postData)
								}).done(function(poll) {
									var placeholder = poll.placeholder;
									var text = '<p><span style="color: white; background: #99A3A4; padding: 40px; text-align: center; font-size: 18px; display: block;" data-eb-poll-id="' + poll.id + '">' + placeholder + '</span></p><p></p>';

									if (window.parent.Joomla && window.parent.Joomla.editors && window.parent.Joomla.editors.instances && window.parent.Joomla.editors.instances.hasOwnProperty(editor)) {
										window.Joomla.editors.instances[editor].replaceSelection(text);
									} else {
										window.jInsertEditorText(text, editor);
									}

									EasyBlog.dialog().close();
								}).fail(function(msg) {
									errorWrapper.find('[data-fd-alert-message]').html(msg);

									// Display the error
									errorWrapper.removeClass('t-hidden');
								}).always(function() {
									// Remove the loader
									$(el).removeClass('is-loading');

									isSaving = false;
								});
							}
						}
					});
				});
			};
<?php
$contents = ob_get_contents();
ob_end_clean();

		return $contents;
	}
}