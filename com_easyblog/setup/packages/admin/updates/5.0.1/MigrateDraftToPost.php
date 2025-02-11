<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptMigrateDraftToPost extends EasyBlogMaintenanceScript
{
	public static $title = "Migrate legacy draft to post.";
	public static $description = "Draft is deprecated in 5.0.0. This migrates old data to new format as post/revision.";

	public function main()
	{
		$db = EB::db();

		// check if draft table exists or not.
		if (! $this->isTableDraftsExists()) {
			// do nothing and return the true state.
			return true;
		}

		//check if we need to add the column or not.
		if (! $this->isColumnExists('#__easyblog_drafts', 'eb5_migrate')) {
			$alter = "ALTER TABLE `#__easyblog_drafts` ADD COLUMN `eb5_migrate` tinyint(1) NOT NULL default '0'";

			$db->setQuery($alter);
			$db->query();
		}

		// lets get drafts post from eb 3.9
		$query = "select * from `#__easyblog_drafts`";
		$query .= " where `eb5_migrate` = '0'";
		$query .= " order by `created`";

		$db->setQuery($query);
		$drafts = $db->loadObjectList();

		// Load the user. We know if this script is executed, the actor must be an admin of the site.
		$user = JFactory::getUser();

		if ($drafts) {
			foreach($drafts as $draft) {

				// lets gather the data before we pass to post lib
				$data = array();

				// there could be instances where the created_by is 0
				$data['created_by'] = ($draft->created_by) ? $draft->created_by : $user->id;
				$data['created'] = $draft->created;
				$data['modified'] = $draft->modified;
				$data['title'] = $draft->title;
				$data['permalink'] = $draft->permalink;
				$data['content'] = $draft->content;
				$data['intro'] = $draft->intro;
				$data['category_id'] = $draft->category_id;
				$data['categories'] = array($draft->category_id);
				$data['publish_up'] = $draft->publish_up;
				$data['publich_down'] = $draft->publish_down;
				$data['access'] = $draft->private;
				$data['allowcomment'] = $draft->allowcomment;
				$data['subscription'] = $draft->subscription;
				$data['frontpage'] = $draft->frontpage;
				$data['blogpassword'] = $draft->blogpassword;
				$data['tags'] = $draft->tags;
				$data['keywords'] = $draft->metakey;
				$data['description'] = $draft->metadesc;
				$data['send_notification_emails'] = $draft->send_notification_emails;
				$data['autopost'] = ($draft->autopost) ? explode(',', $draft->autopost) : '';
				$data['latitude'] = $draft->latitude;
				$data['longitude'] = $draft->longitude;
				$data['address'] = $draft->address;
				$data['robots'] = $draft->robots;
				// $data['copyrights'] = $draft->copyrights;
				$data['eb_language'] = $draft->language;
				$data['posttype'] = $draft->source;

				$data['image'] = '';
				if ($draft->image) {
					$imageObj = json_decode($draft->image);
					$newpath = $imageObj->place . $imageObj->path;

					$data['image'] = $newpath;
				}

				$data['published'] = EASYBLOG_POST_DRAFT;

				if ($draft->pending_approval) {
					$data['published'] = EASYBLOG_POST_PENDING;
				}

				$source_id = "0";
				$source_type = EASYBLOG_POST_SOURCE_SITEWIDE;

				if (! $draft->issitewide) {
					if ($draft->blog_contribute) {
						if (!$draft->external_source && !$draft->external_group_id) {
							// this is teamblog
							$source_id = $draft->blog_contribute;
							$source_type = EASYBLOG_POST_SOURCE_TEAM;
						} else {
							$source_id = $draft->external_group_id;
							$source_type = ($draft->external_source == 'jomsocial.event') ? EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT : EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP;
						}
					}
				}

				$data['source_id'] = $source_id;
				$data['source_type'] = $source_type;

				// now we got the data ready. let pass to post lib for further processing.
				$post = null;
				$postExists = false;

				if ($draft->entry_id) {
					$post = EB::post($draft->entry_id);

					// The draft is not tied to existings post
					if ($post->revision_id) {
						$postExists = true;

						//let set doctype here
						$post->doctype = 'legacy';
					}
				}

				// new post
				if (!$postExists) {
					$post = EB::post();
					$post->create(array('overrideDoctType' => 'legacy'));
				}

				// now let get the uid
				$data['uid'] = $post->uid;
				$data['revision_id'] = $post->revision->id;

				$post->bind($data, array());

				$saveOptions = array(
								'applyDateOffset' => false,
								'updateModifiedTime' => false,
								'validateData' => false
								);

				$post->save($saveOptions);

				// update this drafts so that it will not run again if admin re-run this script.
				$updateSQL = "update `#__easyblog_drafts` set `eb5_migrate` = '1' where `id` = " . $db->Quote($draft->id);
				$db->setQuery($updateSQL);
				$db->query();

			}
		}

		return true;
	}

	public function isTableDraftsExists() {
		$db = EB::db();

		$query = "SHOW TABLES LIKE '%_easyblog_drafts'";
		$db->setQuery($query);

		$result = $db->loadResult();

		return ($result) ? true : false;
	}

	public function isColumnExists($tbName, $colName)
	{
		$db = EB::db();
		$columns = $db->getTableColumns($tbName);

		return in_array($colName, $columns);
	}
}
