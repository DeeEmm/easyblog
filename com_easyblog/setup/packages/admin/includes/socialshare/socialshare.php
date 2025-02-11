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

class EasyBlogSocialShare
{
	public function __construct()
	{
		$this->app = JFactory::getApplication();
		$this->config = EB::config();
	}

	//kiv for the time being
	public static function getLink($type, $id)
	{
		if(empty($type) || empty($id))
		{
			return false;
		}

		$oauth = EB::table('Oauth');
		$oauth->loadByUser($id, $type);

		$param 	= EB::registry($oauth->params);

		$screenName = $param->get( 'screen_name', '');

		$acl 	= EB::acl($id);
		$rule	= 'update_'.$type;

		if (!$acl->get($rule)) {
			return false;
		}

		switch($type)
		{
			case 'twitter':
				$link = empty($screenName)? '' : 'http://twitter.com/'.$screenName;
				break;
			case 'facebook':
				$link = '';
				break;
			case 'linkedin':
				$link = '';
				break;
		}

		return $link;
	}

	/**
	 * Determines if the user has enabled the auto updates settings.
	 *
	 * @param	int		$userId		The subject user.
	 * @param	string	$type		The type of social sharing.
	 */
	public function hasAutoPost($userId, $type)
	{
		$social	= EB::table('Oauth');
		$social->loadByUser($userId, constant('EBLOG_OAUTH_' . strtoupper($type)));

		return $social->auto;
	}

 /*
	 * Determines whether the selected user has associated their accounts or not
	 *
	 * @param	int		$userId		The subject user.
	 * @param	string	$type		The type of social sharing.
	 */
	public function isAssociated( $userId , $type )
	{
		//check if centralized, then use centralized.
		$config		= EB::config();

		$allowed	= $config->get( 'integrations_' . strtolower( $type ) . '_centralized_and_own' );

		if( !$allowed )
		{
			return false;
		}

		$oauth	= EB::table('Oauth');
		return $oauth->loadByUser( $userId , constant( 'EBLOG_OAUTH_' . strtoupper( $type ) ) );
	}
}
