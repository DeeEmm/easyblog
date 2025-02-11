<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogAdsense extends EasyBlog
{
	/**
	 * Generates the html codes for adsense
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function html(EasyBlogPost $post)
	{
		$result = new stdClass();
		$result->header = '';
		$result->beforecomments = '';
		$result->footer = '';

		// Standard code
		$code = $this->config->get('integration_google_adsense_code');

		// Responsive code
		$responsiveCode = $this->config->get('integration_google_adsense_responsive_code');

		// Determine whether the responsive code should be used or not
		$responsive = $this->config->get('integration_google_adsense_responsive');

		if ($responsive && $responsiveCode) {
			$code = $responsiveCode;
		}

		// Determines the location of the ads
		$location = $this->config->get('integration_google_adsense_display');

		// Ensure that adsense is enabled
		if (!$this->config->get('integration_google_adsense_enable')) {
			return $result;
		}

		// Determines who should we display the ads to
		$displayAccess = $this->config->get('integration_google_adsense_display_access');

		// If user is a guest and guest visibilty for ads are disabled, hide it.
		if ($displayAccess == 'members' && $this->my->guest) {
			return $result;
		}

		// If user is a guest, and settings is configured to be displayed to guests only, hide it.
		if ($this->config->get('integration_google_adsense_display_access') == 'guests' && !$this->my->guest) {
			return $result;
		}

		// Check if author enabled their own adsense
		$adsense = EB::table('Adsense');
		$adsense->load($post->getAuthor()->id);
		$userCode = $adsense->code;

		if ($adsense->code && $adsense->published) {
			$code = $userCode;
			$location = $adsense->display;
		}

		if ($location == 'userspecified') {
			return $result;
		}

		// If we can't find any adsense code, skip this
		if (!$code) {
			return $result;
		}

		$theme = EB::themes();
		$theme->set('code', $code);

		$namespace = 'site/adsense/responsive';

		// Author's adsense code will not using responsive code
		if (!$responsive || $userCode) {
			$namespace = 'site/adsense/code';
		}

		if ($location == 'both') {

			// Ensure that both html is different if there are more than one ads
			// hence we render the output twice. #1325
			$result->header = $theme->output($namespace);
			$result->footer = $theme->output($namespace);
		} else {
			$result->$location = $theme->output($namespace);
		}

		return $result;
	}

	/**
	 * Generates the AMP html codes for adsense
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function ampHtml(EasyBlogPost $post)
	{
		$result = new stdClass();
		$result->header = '';
		$result->beforecomments = '';
		$result->footer = '';

		// Standard code
		$code = $this->config->get('integration_google_adsense_code');
		$data = array();

		if ($code) {
			if (preg_match('/google_ad_client\s*=\s*"([^"]+)"\s*;/', $code, $m)) {
				$data['client'] = $m[1];
			}
			if (preg_match('/google_ad_slot\s*=\s*"([^"]+)"\s*;/', $code, $m)) {
				$data['slot'] = $m[1];
			}
		}

		// Responsive code
		$responsiveCode = $this->config->get('integration_google_adsense_responsive_code');

		if (!$code || $responsiveCode && $this->config->get('integration_google_adsense_responsive')) {
			$code = $responsiveCode;

			// We need to process the code to meet the AMP requirement
			preg_match_all('~ad-(?P<name>\w+)="(?P<val>[^"]*)"~', $code, $m);
			$data = array_combine($m['name'], $m['val']);
		}

		// Determines the location of the ads
		$location = $this->config->get('integration_google_adsense_display');

		// Ensure that adsense is enabled
		if (!$this->config->get('integration_google_adsense_enable')) {
			return $result;
		}

		// Determines who should we display the ads to
		$displayAccess = $this->config->get('integration_google_adsense_display_access');

		// If user is a guest and guest visibilty for ads are disabled, hide it.
		if ($displayAccess == 'members' && $this->my->guest) {
			return $result;
		}

		// If user is a guest, and settings is configured to be displayed to guests only, hide it.
		if ($this->config->get('integration_google_adsense_display_access') == 'guests' && !$this->my->guest) {
			return $result;
		}

		// Check if author enabled their own adsense
		$adsense = EB::table('Adsense');
		$adsense->load($post->getAuthor()->id);

		if ($adsense->code && $adsense->published) {
			$code = $adsense->code;
			$location = $adsense->display;
		}

		if ($location == 'userspecified') {
			return $result;
		}

		// If we can't find any adsense code, skip this
		if (!$code) {
			return $result;
		}

		$theme = EB::themes();
		$theme->set('data', $data);

		$html = $theme->output('site/adsense/amp');

		if ($location == 'both') {
			$result->header = $html;
			$result->footer = $html;
		} else {
			$result->$location = $html;
		}

		return $result;
	}

	/**
	 * Process adsense codes
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function process($content, $bloggerId)
	{
		// If there's no content, we should skip this altogether
		if (!$content || !$bloggerId) {
			return $content;
		}

		$pattern = '/\{eblogads(\sleft|\sright)?\}/i';

		preg_match_all($pattern, $content, $matches);

		$adscode = $matches[0];

		if (count($adscode) > 0) {

			foreach ($adscode as $code) {
				$codes = explode(' ', $code);
				$alignment = (isset($codes[1])) ? $codes[1] : '';
				$alignment = str_ireplace('}', '', $alignment);

				$html = $this->getAdsenseTemplate($bloggerId, $alignment);

				$content = str_ireplace($code, $html, $content);
			}
		}

		return $content;
	}

	/**
	 * Process adsense codes for instant article
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function processInstant($content, $bloggerId)
	{
		// If there's no content, we should skip this altogether
		if (!$content || !$bloggerId) {
			return $content;
		}

		$pattern = '/\{eblogads(\sleft|\sright)?\}/i';

		preg_match_all($pattern, $content, $matches);

		$adscode = $matches[0];

		if (count($adscode) > 0) {

			foreach ($adscode as $code) {

				$codes = explode(' ', $code);
				$alignment = (isset($codes[1])) ? $codes[1] : '';
				$alignment = str_ireplace('}', '', $alignment);

				$html = $this->getAdsenseTemplate($bloggerId, $alignment);

				// Wrap the code to follow instant article format
				$html = '<figure class="op-ad"><iframe height="50" width="320">' . $html . '</iframe></figure>';

				// For legacy gallery, it always be wrap in <p>. We need to take it out.
				$html = str_replace('<figure', '</p><figure', $html);
				$html = str_replace('</figure>', '</figure><p>', $html);

				$content = str_ireplace($code, $html, $content);
			}
		}

		return $content;
	}

	/**
	 * Strips all adsense codes
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function strip($content)
	{
		$pattern = '/\{eblogads.*\}/i';
		$content = preg_replace($pattern, '', $content);

		return $content;
	}

	/**
	 * Strip adsense codes
	 *
	 * @deprecated	4.0
	 * @access	public
	 */
	public function stripAdsenseCode($content)
	{
		return $this->strip($content);
	}

	/**
	 * Process adsense template for user specified adsense
	 *
	 * @since	5.2
	 * @access	public
	 */
	private function getAdsenseTemplate($bloggerId, $alignment = '')
	{
		$config = EB::getConfig();
		$my = JFactory::getUser();

		if ($config->get('integration_google_adsense_display_access') == 'members' && $my->id == 0) {
			return '';
		}

		if ($config->get('integration_google_adsense_display_access') == 'guests' && $my->id > 0) {
			return '';
		}

		if (!$config->get('integration_google_adsense_enable')) {
			return '';
		}

		//blogger adsense
		//now we check whether user enabled adsense or not.
		$bloggerAdsense = EB::table('Adsense');
		$bloggerAdsense->load($bloggerId);

		if (!empty($bloggerAdsense->code) && $bloggerAdsense->published) {
			$defaultCode = $bloggerAdsense->code;
			$defaultDisplay	= $bloggerAdsense->display;
		}

		if ($config->get('integration_google_adsense_centralized') || empty($defaultCode)) {
			$adminAdsenseCode = $config->get('integration_google_adsense_code');
			$adsenseResponsiveCode = $config->get('integration_google_adsense_responsive_code');
			$adminAdsenseDisplay = $config->get('integration_google_adsense_display');

			if (!empty($adminAdsenseCode) && !$config->get('integration_google_adsense_responsive')) {
				$defaultCode = $adminAdsenseCode;
				$defaultDisplay	= $adminAdsenseDisplay;
			} else {
				$defaultCode = $adsenseResponsiveCode;
				$defaultDisplay	= $adminAdsenseDisplay;
			}
		}

		if ($defaultDisplay != 'userspecified') {
			return '';
		}

		$responsive = $config->get('integration_google_adsense_responsive');

		$theme = EB::themes();
		$theme->set('code', $defaultCode);

		$align = '';

		if (!empty($alignment)) {
			$align = ($alignment == 'right') ? ' alignright' : ' alignleft';
		}

		$theme->set('alignment', $align);

		$namespace = 'site/adsense/responsive';

		if (!$responsive) {
			$namespace = 'site/adsense/code';
		}

		$adsenseHTML = $theme->output($namespace);

		return $adsenseHTML;
	}
}
