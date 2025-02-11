<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

class EasyBlogExceptionUpload
{
	public static function filter($file)
	{
		$code = $file['error'];

	    switch ($code) {

	        case UPLOAD_ERR_INI_SIZE:
	            $message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_INI_SIZE';
	            break;

	        case UPLOAD_ERR_FORM_SIZE:
	            $message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_FORM_SIZE';
	            break;

	        case UPLOAD_ERR_PARTIAL:
	            $message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_PARTIAL';
	            break;

	        case UPLOAD_ERR_NO_FILE:
	            $message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_NO_FILE';
	            break;

	        case UPLOAD_ERR_NO_TMP_DIR:
	            $message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_NO_TMP_FILE';
	            break;

	        case UPLOAD_ERR_CANT_WRITE:
	            $message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_CANT_WRITE';
	            break;

	        case UPLOAD_ERR_EXTENSION:
	            $message = 'COM_EASYBLOG_EXCEPTION_UPLOAD_EXTENSION';
	            break;

	        default:
	        	return null;
	        	break;
	    }

	    return array(
	    	'message' => $message,
	    	'type'    => SOCIAL_MSG_ERROR
	    );
	}
}
