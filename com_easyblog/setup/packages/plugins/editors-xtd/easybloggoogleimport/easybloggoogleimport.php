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

class plgButtonEasyBlogGoogleimport extends JPlugin
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

		$uid = $input->getVar('uid', null);
		if ($uid) {
			// this is not a blank post.
			// stop here.
			return;
		}

		// check if google import enabled from EB or not.
		$client = EB::oauth()->getClient(EBLOG_OAUTH_GOOGLE);
		if (!$client->isEnabled()) {
			return;
		}

		// Load its plugin language
		JPlugin::loadLanguage('plg_editors-xtd_easybloggoogleimport');

		$url = $client->getLoginUrl();

		$script = $this->getScript($name, $url);

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($script);

		$button = new JObject;
		$button->modal = false;
		$button->onclick = 'displayEasyBlogGoogleImport();return false;';
		$button->text = JText::_('PLG_EB_GOOGLEIMPORT_BUTTON', true);
		$button->name = 'google';
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
	private function getScript($editorName, $url)
	{
ob_start();
?>
			var editor = "<?php echo $editorName;?>";
			window.displayEasyBlogGoogleImport = function() {
				EasyBlog.ready(function($) {

					var gController = EasyBlog.Composer.googleimport;

					if (gController !== undefined) {
						gController.initGimport();
						gController.initOauthPopUp();
					}
				});
			};
<?php
$contents = ob_get_contents();
ob_end_clean();

		return $contents;
	}
}
