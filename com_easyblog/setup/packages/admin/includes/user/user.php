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

class EasyBlogUser
{
	public $type = 'joomla';
	static $userInstances = [];
	protected $helper = null;

	public function __construct($id = null , $debug = false)
	{
		$item = self::loadUsers($id, $debug);

		return $item;
	}

	/**
	 * Object initialisation for the class to fetch the appropriate user
	 * object.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function factory($ids = null, $debug = false)
	{
		$items	= self::loadUsers($ids, $debug);

		return $items;
	}

	/**
	 * Initializes the guest user object
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function createGuestObject()
	{
		// Set guest property
		if (!isset(EasyBlogUserStorage::$users[0])) {

			$profile = EB::table('Profile');

			EasyBlogUserStorage::$users[0] = $profile;
		}
	}

	/**
	 * Load a list of users to pre-cache them
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function loadUsers( $ids = null , $debug = false )
	{
		// Determine if the argument is an array.
		$argumentIsArray	= is_array($ids);

		// If it is null or 0, the caller wants to retrieve the current logged in user.
		if (is_null($ids) || (is_string($ids) && $ids == '')) {
			$ids 	= array(JFactory::getUser()->id);
		}

		// Always create the guest objects first.
		self::createGuestObject();

		// Ensure that id's are always an array
		if (!is_array($ids)) {
			$ids = array($ids);
		}

		// Reset the index of ids so we don't load multiple times from the same user.
		$ids = array_values($ids);

		// Total needs to be computed here before entering iteration as it might be affected by unset.
		$total = count($ids);

		// Placeholder for items that are already loaded.
		$loaded = array();

		// @task: We need to only load user's that aren't loaded yet.
		for( $i = 0; $i < $total; $i++) {
			
			if (empty($ids)) {
				break;
			}

			if (!isset($ids[$i]) && empty($ids[$i])) {
				continue;
			}

			$id		= $ids[ $i ];

			// If id is null, we know we want the current user.
			if( is_null( $id ) )
			{
				$ids[ $i ] 	= JFactory::getUser()->id;
			}

			// The parsed id's could be an object from the database query.
			if( is_object( $id ) && isset( $id->id ) ) {
				$id			= $id->id;

				// Replace the current value with the proper value.
				$ids[ $i ]	= $id;
			}

			if (isset(EasyBlogUserStorage::$users[$id])) {
				$loaded[]	= $id;
				unset($ids[$i]);
			}

		}

		// @task: Reset the ids after it was previously unset.
		$ids	= array_values( $ids );

		// Place holder for result items.
		$result	= array();

		foreach ($loaded as $id) {
			$result[]	= EasyBlogUserStorage::$users[$id];
		}

		if (!empty($ids)) {
			
			// @task: Now, get the user data.
			$model = EB::model('Users');
			$users = $model->getUsersMeta($ids);

			if ($users) {

				// @task: Iterate through the users list and add them into the static property.
				foreach ($users as $user) {

					$obj = EB::table('Profile');

					if (empty($user->eb_id)) {
						// the load method will create a new records, and assign juser into class property 'user'
						$obj->load($user->id);
					} else {
						$data = array();
						$data['id'] = $user->eb_id;
						$data['nickname'] = $user->nickname;
						$data['avatar'] = $user->avatar;
						$data['description'] = $user->description;
						$data['url'] = $user->url;
						$data['params'] = $user->eb_params;
						$data['published'] = $user->eb_published;
						$data['title'] = $user->eb_title;
						$data['biography'] = $user->biography;
						$data['permalink'] = $user->permalink;
						$data['custom_css'] = $user->custom_css;
						$data['ordering'] = $user->ordering;

						$obj->bind($data);

						// juser binding
						// unset data from easyblog_users table
						unset($user->eb_id);
						unset($user->nickname);
						unset($user->avatar);
						unset($user->description);
						unset($user->url);
						unset($user->eb_params);
						unset($user->eb_published);
						unset($user->eb_title);
						unset($user->biography);
						unset($user->permalink);
						unset($user->custom_css);
						unset($user->ordering);

						$data = get_object_vars($user);

						// JFactory::getUser() might load extra data that may not used in easyblog profile object. for now, we will jz use binding to bind the require data only.
						$obj->user = JFactory::getUser($user->id);
					}

					EasyBlogUserStorage::$users[$user->id] = $obj;

					$result[] = EasyBlogUserStorage::$users[$user->id];
				}
			} else {

				foreach ($ids as $id) {
					// Since there are no such users, we just use the guest object.
					EasyBlogUserStorage::$users[$id] = EasyBlogUserStorage::$users[0];

					$result[] = EasyBlogUserStorage::$users[$id];
				}
			}
		}

		// If the argument passed in is not an array, just return the proper value.
		if( !$argumentIsArray && count( $result ) == 1 )
		{
			return $result[0];
		}

		return $result;
	}
}

/**
 * This class would be used to store all user objects
 *
 */
class EasyBlogUserStorage
{
	static $users = [];
}
