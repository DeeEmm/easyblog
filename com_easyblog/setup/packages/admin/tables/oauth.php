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

require_once(__DIR__ . '/table.php');

class EasyBlogTableOauth extends EasyBlogTable
{
	public $id = null;
	public $user_id	= 0;
	public $type = '';
	public $auto = 0;
	public $request_token = '';
	public $access_token = '';
	public $message	= '';
	public $created	= '0000-00-00 00:00:00';
	public $private	= 0;
	public $params = '';
	public $system = null;
	public $expires = null;
	public $notify = null;

	public function __construct($db)
	{
		parent::__construct('#__easyblog_oauth', 'id', $db);
	}

	/**
	 * Deprecated. Use @load instead
	 *
	 * @deprecated	4.0
	 */
	public function loadSystemByType($type)
	{
		$db = $this->getDBO();

		$query = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote($this->_tbl) . ' '
				. 'WHERE ' . $db->nameQuote('type') . '=' . $db->Quote($type) . ' '
				. 'AND ' . $db->nameQuote('system') . '=' . $db->Quote(1);

		$db->setQuery($query);

		$result = $db->loadResult();

		if (empty($result)) {
			$this->id = 0;
			$this->type = $type;
			return $this;
		}

		return parent::load($result);
	}

	public function loadByUser($id, $type)
	{
		$db = EB::db();

		$query = 'SELECT * FROM ' . $db->quoteName($this->_tbl) . ' '
				. 'WHERE ' . $db->nameQuote('user_id') . '=' . $db->Quote($id) . ' '
				. 'AND ' . $db->nameQuote('type') . '=' . $db->Quote($type) . ' '
				. 'AND ' . $db->nameQuote('system') . '=' . $db->Quote(0) . ' '
				. 'LIMIT 1';
		$db->setQuery($query);

		$result = $db->loadObject();

		if (!$result) {
			return false;
		}

		return parent::bind($result);
	}

	/**
	 * Pushes to the oauth site
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function push(EasyBlogPost &$post, $system = true, $reposting = false, $force = false)
	{
		// When there is no access token set on this oauth record, we shouldn't do anything
		if (!$this->access_token) {
			$this->setError(JText::sprintf('No access token available for autoposting on %1$s', $this->type));
			return false;
		}

		$config = EB::config();

		if (!$reposting) {
			// Determines if this user is really allowed to auto post
			$author = $post->getAuthor();

			// Check the author's acl
			$acl = EB::acl($author->id);
			$rule = 'update_' . $this->type;

			if (!$acl->get($rule) && !EB::isSiteAdmin($post->created_by)) {
				$this->setError(JText::sprintf('No access to autopost on %1$s', $this->type));
				return false;
			}

			// we only check isShared if the autopost on blog edit is disabled. OR this is schedule autopost
			if ((!$config->get('integrations_' . $this->type . '_centralized_send_updates') || $post->autopost_date != EASYBLOG_NO_DATE) && !$force) {
				// Check if the blog post was shared before.
				if ($this->isShared($post->id)) {
					$this->setError(JText::sprintf('COM_EB_AUTOPOST_HAS_BEEN_SHARED', $post->id, $this->type));
					return false;
				}
			}
		}

		// Ensure that the oauth data has been set correctly
		$config = EB::config();
		$key = $config->get('integrations_' . $this->type . '_api_key');
		$secret = $config->get('integrations_' . $this->type . '_secret_key');

		// If either of this is empty, skip this
		if (!$key || !$secret) {
			return false;
		}

		// Set the callback URL
		$callback = JURI::base() . 'index.php?option=com_easyblog&task=oauth.grant&type=' . $this->type;

		// Now we do the real thing. Get the library and push
		$lib = EB::oauth()->getClient($this->type, $key, $secret, $callback);
		$lib->setAccess($this->access_token);

		// Try to share the post now
		$state = $lib->share($post, $this, $system, $reposting);

		if ($state === true) {
			$history = EB::table('OAuthPost');
			$history->load(array('oauth_id' => $this->id, 'post_id' => $post->id));

			$history->post_id = $post->id;
			$history->oauth_id = $this->id;
			$history->created = EB::date()->toSql();
			$history->modified = EB::date()->toSql();
			$history->sent = EB::date()->toSql();
			$history->store();

			return true;
		}

		return false;
	}

	/**
	 * Override parent's store method
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function store($updateNulls = false)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn($this->_tbl);
		$query[] = 'WHERE ' . $db->qn('user_id') . '=' . $db->q($this->user_id);
		$query[] = 'AND ' . $db->qn('type') . '=' . $db->q($this->type);
		$query[] = 'AND ' . $db->qn('system') . '=' . $db->q($this->system);

		$db->setQuery($query);

		$exists = $db->loadResult();

		if ($exists) {
			return $db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
		}

		return $db->insertObject($this->_tbl, $this, $this->_tbl_key);
	}

	/**
	 * Retrieves the expiry date
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getExpireDate()
	{
		// one of the situation Facebook will not return you the expired date if the user Facebook account already authenticated from the backend autopost setting.
		// this only happen on when the user authenticate with his Facebook account on their user profile page.
		if ($this->expires == '0000-00-00 00:00:00') {
			return false;
		}

		$date = EB::date($this->expires);

		return $date;
	}

	/**
	 * Retrieves a key value from the access token object.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getAccessTokenValue($key)
	{
		$param = EB::registry($this->access_token);

		return $param->get($key);
	}

	public function getMessage()
	{
		$config = EB::config();
		$message = !empty($this->message) ? $this->message : $config->get('main_' . $this->type . '_message');
		return $message;
	}

	/**
	 * Determines whether a blog post has been shared before.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function isShared($postId)
	{
		$model = EB::model('Oauth');

		return $model->isShared($postId, $this->id);
	}

	/**
	 * Add a backup of this current object record into temp table.
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function addBackup()
	{
		// check if we really need to backup this record or not.
		if ($this->hasPostsShared()) {

			// prepare data
			$data = $this->getProperties();
			$params = EB::registry($data);

			// lets add into tmp / backup table
			$table = EB::table('OauthTmp');
			$table->type = $this->type;
			$table->system = $this->system;
			$table->user_id = $this->user_id;
			$table->created = EB::date()->toSql();
			$table->params = $params->toString();

			$table->store();
		}

		return true;
	}

	/**
	 * Store backup from temp table and migrate data from oauth_posts
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function restoreBackup()
	{
		// check if there is a backup created for this client or not.
		$backup = $this->getBackup();
		if ($backup !== false) {
			$params = $backup->getParams();
			$oldId = $params->get('id');

			if ($oldId) {
				// now let migrate the oauth posts with the new id.
				$model = EB::model('Oauth');
				$state = $model->migrateOauthPosts($oldId, $this->id);

				if ($state) {
					// delete the backup
					$backup->delete();
				}
			}
		}

		return true;
	}

	/**
	 * Determine if there are any posts being shared under this oauth record.
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function hasPostsShared()
	{
		$model = EB::model('Oauth');
		return $model->hasPostsShared($this->id);
	}

	/**
	 * Return backup record created under this client.
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function getBackup()
	{
		$data = array('type' => $this->type, 'system' => $this->system);
		if (!$this->system) {
			$data['user_id'] = $this->user_id;
		}

		$table = EB::table('OauthTmp');
		$table->load($data);

		if ($table->id) {
			return $table;
		}

		return false;
	}
}
