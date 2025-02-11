<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/table.php');

class EasyBlogTableFieldGroup extends EasyBlogTable
{
	public $id = null;
	public $title = '';
	public $description = null;
	public $created = null;
	public $state = null;
	public $read = '';
	public $write = '';
	public $params = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_fields_groups', 'id', $db);
	}

	/**
	 * Retrieve the group title
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTitle()
	{
		return JText::_($this->title);
	}

	/**
	 * Publishes a field group
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$this->state = 1;

		return $this->store();
	}


	/**
	 * Unpublishes a field group
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function unpublish()
	{
		$this->state = 0;

		return $this->store();
	}

	/**
	 * Deletes a custom field group
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function delete($pk = null)
	{
		// Require the parent to delete the group
		$state = parent::delete($pk);

		// Remove custom fields associated with the group
		$model = EB::model('Fields');
		$model->deleteGroupAssociation($this->id);

		return $state;
	}

	/**
	 * Retrieves the total number of fields
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTotalFields()
	{
		$db 	= EB::db();

		$query		= array();
		$query[]	= 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_fields');
		$query[]	= 'WHERE ' . $db->quoteName('group_id') . '=' . $db->Quote($this->id);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Determines if there are any values for fields within this group for a particular post.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function hasValues(EasyBlogPost $post)
	{
		static $result = array();

		$idx = $this->id.$post->id;

		if (!isset($result[$idx])) {

			if (EB::cache()->exists($post->id, 'posts')) {

				$data = EB::cache()->get($post->id, 'posts');

				if (isset($data['customfields'])) {
					$fCount = 0;

					foreach ($data['customfields'] as $fields) {
						foreach($fields as $fieldvalue) {
							if ($fieldvalue->value) {
								$fCount++;
							}
						}
					}

					$result[$idx] = $fCount;
					return $result[$idx];
				}
			}
		}

		// if still empty, let run the sql
		if (!isset($result[$idx])) {

			$model = EB::model('FieldGroups');
			$result[$idx] = $model->hasValues($post->id, $this->id);
		}

		return $result[$idx];
	}

	public function getAcl($type)
	{
		$aclGroup = $this->$type;

		return json_decode($aclGroup);
	}

	public function store($updateNulls = false)
	{
		parent::store($updateNulls);

		// Clear the existsing data
		$model = EB::model('fieldGroupAcl');
		$model->clearAcl($this->id);

		// Store the acl
		if ($this->write) {
			$write = json_decode($this->write);

			foreach ($write as $aclId) {
				$table = EB::table('fieldGroupAcl');
				$table->group_id = $this->id;
				$table->acl_id = $aclId;
				$table->acl_type = 'write';

				$table->store();
			}
		}

		if ($this->read) {
			$read = json_decode($this->read);

			foreach ($read as $aclId) {
				$table = EB::table('fieldGroupAcl');
				$table->group_id = $this->id;
				$table->acl_id = $aclId;
				$table->acl_type = 'read';

				$table->store();
			}
		}

		return true;
	}
}
