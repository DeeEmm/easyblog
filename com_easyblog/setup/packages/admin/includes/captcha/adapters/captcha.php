<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogCaptchaResponse
{
    public $success;
    public $errorCodes;
}

class EasyBlogCaptchaAdapterCaptcha
{
	public static function getHTML()
	{
		$captcha = EB::table('Captcha');
		$captcha->response = '';
		$captcha->created = EB::date()->toSql();
		$captcha->store();

		$theme = EB::themes();
		$theme->set('id', $captcha->id);

		return $theme->output('site/comments/captcha');
	}

	/**
	 * Verifies a captcha response
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function verify($response, $id)
	{
		$captcha = EB::table('Captcha');
		$captcha->load($id);

		//var_dump($id);exit;

		$captchaResponse = new EasyBlogCaptchaResponse();
		$captchaResponse->success = true;
		$captchaResponse->errorCodes = '';

		if (empty($response) || !$id) {
            $captchaResponse->success = false;
            $captchaResponse->errorCodes = JText::_('COM_EASYBLOG_RECAPTCHA_MISSING_INPUT');
		}

		if (!$captcha->verify($response)) {
			$captchaResponse->success = false;
            $captchaResponse->errorCodes = JText::_('COM_EASYBLOG_RECAPTCHA_INVALID_RESPONSE');
		}

		return $captchaResponse;
	}
}
