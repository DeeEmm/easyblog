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

class EasyBlogCompiler
{
	static $instance = null;
	public $version;
	public $cli = false;

	// These script files should be rendered externally and not compiled together
	// Because they are either too large or only used in very minimal locations.
	public $excludePatterns = [
		"moment",
		"plupload2",

		//jquery.ui
		"vendors/ui/autocomplete.js",
		"vendors/ui/core.js",
		"vendors/ui/draggable.js",
		"vendors/ui/droppable.js",
		"vendors/ui/menu.js",
		"vendors/ui/mouse.js",
		"vendors/ui/position.js",
		"vendors/ui/resizable.js",
		"vendors/ui/sortable.js",
		"vendors/ui/widget.js",

		// Vendors
		"vendors/ace",
		"vendors/accordion.js",
		"vendors/audiojs.js",
		"vendors/datetimepicker.js",
		"vendors/videojs",
		"vendors/videojs.js",
		"vendors/imgareaselect.js",
		"vendors/masonry.js",
		"vendors/imagesloaded.js",
		"vendors/swiper.js",
		"vendors/leaflet.js", // Maps
		"vendors/textboxlist.js",
		"vendors/scrollTo.js",
		"vendors/jquery.js", // Already included in the core files
		"vendors/daterangepicker.js", // Not sure where this is used

		// Composer files
		"composer/vendors/redactor10.js",

		// Shared libraries
		"shared/comparison.js",
		"shared/datetime.js",
		"shared/mobile.js",
		"shared/usertags.js",
		"shared/nearest.js",

		// Admin scripts
		"admin/vendors/flot.js",
		"admin/vendors/select2.js",
		'admin/vendors/resize.js',
		'admin/vendors/mousewheel.js',
		'admin/vendors/event/drag.js',

		// Site scripts
		"site/mmenu.js",
		"site/markerclusterer.js",
		"site/location.js",
		"site/teamblogs.js",
		"site/polls.js",
		"site/comments.js",
		"site/search/filters.js",
		"site/vendors/markitup.js",
		"site/vendors/ticker.js",
		"site/vendors/webcam.js", // Used in photos quickpost
		"site/dashboard/filters.js",
		"site/dashboard/table.js",
		"site/dashboard/teamblogs.js",
		"site/dashboard/quickpost",
		"site/author/suggest.js" // Used in teamblogs on the dashboard
	];

	// Excluded files
	public $excluded = [];

	// Excluded files that should be minified
	public $excludedMinify = [];

	// Contains a list of files which we have already minified so that we don't try to compile
	// the excluded file more than once.
	static $minifiedExcludedFiles = [];

	/**
	 * Allows caller to compile a script file on the site, given the section
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function compile($section = 'admin', $minify = true, $jquery = true)
	{
		// Get the file name that should be used after compiling the scripts
		$fileName = EB::scripts()->getFileName($section, $jquery);

		$files = $this->getFiles($section, $jquery);

		// Include the bootloader
		$contents = $this->compileBootloader();

		// 1. Core file contents needs to be placed at the top
		$contents .= $this->compileCoreFiles($files->core);

		// 2. Libraries should be appended next
		$contents .= $this->compileLibraries($files->libraries);

		// 3. Compile the normal scripts
		$contents .= $this->compileScripts($files->scripts);

		$result = new stdClass();
		$result->section = $section;
		$result->minify = $minify;

		// Store the uncompressed version
		$standardPath = EBLOG_SCRIPTS . '/' . $fileName . '.js';

		$this->write($standardPath, $contents);

		$result->standard = $standardPath;
		$result->minified = false;

		// Compress the script and minify it
		if ($minify) {
			$closure = $this->getClosure();

			// 1. Minify the main library
			$contents = $closure->minify($contents);

			// Store the minified version
			$minifiedPath = EBLOG_SCRIPTS . '/' . $fileName . '.min.js';
			$this->write($minifiedPath, $contents);

			// 2. Since excluded files are running on their own, we would need to minify them so that it
			// runs on the compressed version rather than the uncompressed version
			if (defined('EASYBLOG_COMPONENT_CLI')) {
				$compileExcludedFiles = true;
			}

			if (!defined('EASYBLOG_COMPONENT_CLI')) {
				$compileExcludedFiles = JFactory::getApplication()->input->get('compileExcludedFiles', true, 'bool');
			}

			if ($compileExcludedFiles) {
				$excludedFiles = $this->getExcludedFilesToMinify();

				foreach ($excludedFiles as $excludedFile) {

					$targetPath = str_ireplace('.js', '.min.js', $excludedFile);

					// If the file already exists, we just skip it
					if (in_array($targetPath, self::$minifiedExcludedFiles)) {
						continue;
					}

					$excludedContents = file_get_contents($excludedFile);

					$excludedContents = $closure->minify($excludedContents);

					$this->write($targetPath, $excludedContents);

					// Ensure that this script do not get compiled again
					self::$minifiedExcludedFiles[] = $targetPath;
				}
			}

			$result->minified = $minifiedPath;
		}

		return $result;
	}

	/**
	 * Determine files that should be excluded from being minified
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function isExcludedFromMinify($file)
	{
		// As there are too many acejs files, it's not ideal to recompile this on the fly.
		// Since these files hardly change, we just store these minified versions in the repository.
		if ($this->isAceFile($file)) {
			return true;
		}

		// As there are too many momentjs language files, it's not ideal to recompile this on the fly.
		// Since these files hardly change, we just store these minified versions in the repository.
		if ($this->isMomentLanguageFile($file)) {
			return true;
		}

		// Dependency files should not need to be minified as they are already included into the core
		if ($this->isDependencyFile($file)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the file pattern is a moment language file
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function isMomentLanguageFile($file)
	{
		if (stristr($file, EB_SCRIPTS . '/vendors/moment/') !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the file pattern is a moment language file
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function isAceFile($file)
	{
		if (stristr($file, EB_SCRIPTS . '/vendors/ace/') !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the file pattern is a dependency file
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function isDependencyFile($file)
	{
		$dependencies = EB::scripts()->getDependencies();

		$pattern = implode('|', array_map('preg_quote', $dependencies));
		$pattern = str_ireplace('/', '\/', $pattern);
		$pattern = '/' . $pattern . '/i';

		if (preg_match($pattern, $file)) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves a list of excluded script files that needs to be minified
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getExcludedFilesToMinify()
	{
		$files = [];

		foreach ($this->excludedMinify as $excluded) {

			// Determines if there are any script files which we should exclude from minifying to speed up the compiling process
			if ($this->isExcludedFromMinify($excluded)) {
				continue;
			}

			$files[] = $excluded;
		}

		return $files;
	}

	/**
	 * Retrieves contents from the bootloader file
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function compileBootloader()
	{
		$file = EB_SCRIPTS . '/bootloader.js';

		$contents = file_get_contents($file);

		return $contents;
	}

	/**
	 * Compiles core files used in EasyBlog
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function compileCoreFiles($files)
	{
		$contents = '';

		foreach ($files as $file) {
			$contents .= file_get_contents($file);
		}

		return $contents;
	}

	/**
	 * Compiles all libraries
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function compileLibraries($files)
	{
		$modules = [];

		$prefix = EB_SCRIPTS . '/vendors';

		foreach ($files as $file) {
			$fileName = ltrim(str_ireplace($prefix, '', $file), '/');
			$modules[] = str_ireplace('.js', '', $fileName);
		}

		$modules = json_encode($modules);

ob_start();
?>
FD50.plugin("static", function($) {
	$.module(<?php echo $modules;?>);

	// Now we need to retrieve the contents of each files
	<?php foreach ($files as $file) { ?>
		<?php echo $this->getContents($file); ?>
	<?php } ?>
});
<?php
$contents = ob_get_contents();
ob_end_clean();

		return $contents;
	}

	/**
	 * Compiles script files
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function compileScripts($files)
	{
		$modules = [];

		foreach ($files as $file) {
			$namespace = str_ireplace(EB_SCRIPTS, 'easyblog', $file);

			$modules[] = str_ireplace('.js', '', $namespace);
		}

		$modules = json_encode($modules);
ob_start();
?>
// Prepare the script definitions
FD50.installer('EasyBlog', 'definitions', function($) {
	$.module(<?php echo $modules;?>);
});

// Prepare the contents of all the scripts
FD50.installer('EasyBlog', 'scripts', function($) {
	<?php foreach ($files as $file) { ?>
		<?php echo $this->getContents($file); ?>
	<?php } ?>
});
<?php
$contents = ob_get_contents();
ob_end_clean();

		return $contents;
	}

	/**
	 * Retrieves the contents of a particular file
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getContents($file)
	{
		$contents = file_get_contents($file);

		return $contents;
	}

	/**
	 * Retrieves the closure compiler
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getClosure()
	{
		require_once(__DIR__ . '/closure.php');
		$closure = new EasyBlogCompilerClosure();

		return $closure;
	}

	/**
	 * Retrieves a list of files for specific sections
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getFiles($section, $jquery = true)
	{
		$files = new stdClass();

		// Get a list of core files
		$files->core = $this->getCoreFiles($jquery);

		// Get a list of libraries
		$files->libraries = $this->getLibraryFiles();

		// Get a list of shared scripts that is used across sections
		$scriptFiles = array_merge([], $this->getSharedFiles());

		// Get script files from the particular section
		$scriptFiles = array_merge($scriptFiles, $this->getScriptFiles($section));
		$files->scripts = $scriptFiles;

		return $files;
	}

	/**
	 * Retrieves a list of core files used on the site
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getCoreFiles($jquery = true)
	{
		$files = EB::scripts()->getDependencies(true, $jquery);

		// $this->debug($files);

		return $files;
	}

	/**
	 * Retrieves a list of library files used on the site
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getLibraryFiles()
	{
		$path = EB_SCRIPTS . '/vendors';
		$files = JFolder::files($path, '.js$', true, true);

		$files = $this->reduceFiles($files, []);

		// $this->debug($files);

		return $files;
	}

	/**
	 * Retrieves list of shared files that is used across all sections
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getSharedFiles()
	{
		$files = JFolder::files(EB_SCRIPTS . '/shared', '.', false, true);

		// $this->debug($files);

		$files = $this->reduceFiles($files, []);

		// $this->debug($files);

		return $files;
	}

	/**
	 * Retrieves list of scripts that is only used in the particular section
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getScriptFiles($section)
	{
		$path = EB_SCRIPTS . '/' . $section;
		$files = JFolder::files($path, '.js$', true, true);

		// $this->debug($files);

		$files = $this->reduceFiles($files, [], true, $section);

		// $this->debug($files);

		return $files;
	}

	/**
	 * Given a list of files, remove exclusions
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function reduceFiles($files, $exclusions, $minifyExcludedFiles = true, $section = '', $debug = false)
	{
		$dependencies = EB::scripts()->getDependencies();
		$exclusions = array_merge($this->excludePatterns, $exclusions, $dependencies);

		// Add exclusion files
		foreach ($exclusions as $exclusion) {
			// Excluded files may also contain a .min.js
			$exclusions[] = str_ireplace('.js', '.min.js', $exclusion);
		}

		$files = array_filter($files, function($file) use (&$exclusions, &$minifyExcludedFiles, $section, $debug) {
			$pattern = implode('|', array_map('preg_quote', $exclusions));
			$pattern = str_ireplace('/', '\/', $pattern);
			$pattern = '/' . $pattern . '/i';

			if (preg_match($pattern, $file)) {
				// Do not include these files that needs to be minified
				if ($this->isExcludedFromMinify($file)) {
					return false;
				}

				// We don't want to generate duplicate excluded files since excluded files will need to be minified as well
				if (stristr($file, '.min.js') === false) {
					$this->excluded[] = $file;

					if ($minifyExcludedFiles) {
						$this->excludedMinify[] = $file;
					}
				}

				return false;
			}

			return true;
		});

		return $files;
	}

	/**
	 * Saves the contents into a file
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function write($path, $contents)
	{
		if (JFile::exists($path)) {
			JFile::delete($path);
		}

		return JFile::write($path, $contents);
	}

	/**
	 * For debugging purposes only. @dump does not display everything
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function debug($items)
	{
		$debug = [];
		$totalSize = 0;

		foreach ($items as &$item) {
			$file = new stdClass();
			$file->file = $item;

			$size = round(filesize($item) / 1024);

			if ($size > 50) {
				$file->size = "<span style='background:red;color: #fff;'>" . $size . "kb</span>";
			} else {
				$file->size = $size . 'kb';
			}

			$debug[] = $file;

			$totalSize += $size;
		}

		echo "Total Size: " . $totalSize . 'kb';

		usort($debug, function($a, $b) {
			$sizeA = str_ireplace('kb', '', $a->size);
			$sizeB = str_ireplace('kb', '', $b->size);
			return $sizeA < $sizeB;
		});

		echo '<pre>';
		print_r($debug);
		echo '</pre>';
		exit;
	}
}
