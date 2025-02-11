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

class EasyBlogBlockHandlerCode extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-code';
	public $element = 'none';

	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();

		// We do not want to display the font attributes and font styles
		$meta->properties['fonts'] = false;

		return $meta;
	}

	/**
	 * Supplies the default data to the js part
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function data()
	{
		$data = new stdClass();

		// Default properties
		$data->mode = 'javascript';
		$data->theme = 'ace/theme/github';
		$data->show_gutter = true;
		$data->fontsize = 12;
		$data->height = 250;

		return $data;
	}

	/**
	 * Validates if the block contains any contents
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function validate($block)
	{
		// if no url specified, return false.
		if (!isset($block->data->code) || !$block->data->code) {
			return false;
		}

		return true;
	}

	/**
	 * Normalize the data to prevent broken html tags
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function normalizeData(&$data)
	{
		$data->code = str_ireplace('<html>', '&lt;html&gt;', $data->code);

		return $data;
	}

	private function getThemes()
	{
		static $themes = null;

		if (is_null($themes)) {
			$themes = [
				'Bright' => [
					"ace/theme/chrome" => "Chrome",
					"ace/theme/clouds" => "Clouds",
					"ace/theme/crimson_editor" => "Crimson Editor",
					"ace/theme/dawn" => "Dawn",
					"ace/theme/dreamweaver" => "Dreamweaver",
					"ace/theme/eclipse" => "Eclipse",
					"ace/theme/github" => "GitHub",
					"ace/theme/solarized_light" => "Solarized Light",
					"ace/theme/textmate" => "TextMate",
					"ace/theme/tomorrow" => "Tomorrow",
					"ace/theme/xcode" => "XCode",
					"ace/theme/kuroir" => "Kuroir",
					"ace/theme/katzenmilch" => "KatzenMilch"
				],
				'Dark' => [
					"ace/theme/ambiance" => "Ambiance",
					"ace/theme/chaos" => "Chaos",
					"ace/theme/clouds_midnight" => "Clouds Midnight",
					"ace/theme/cobalt" => "Cobalt",
					"ace/theme/idle_fingers" => "idle Fingers",
					"ace/theme/kr_theme" => "krTheme",
					"ace/theme/merbivore" => "Merbivore",
					"ace/theme/merbivore_soft" => "Merbivore Soft",
					"ace/theme/mono_industrial" => "Mono Industrial",
					"ace/theme/monokai" => "Monokai",
					"ace/theme/pastel_on_dark" => "Pastel on dark",
					"ace/theme/solarized_dark" => "Solarized Dark",
					"ace/theme/terminal" => "Terminal",
					"ace/theme/tomorrow_night" => "Tomorrow Night",
					"ace/theme/tomorrow_night_blue" => "Tomorrow Night Blue",
					"ace/theme/tomorrow_night_bright" => "Tomorrow Night Bright",
					"ace/theme/tomorrow_night_eighties" => "Tomorrow Night 80s",
					"ace/theme/twilight" => "Twilight",
					"ace/theme/vibrant_ink" => "Vibrant Ink"
				]
			];
		}

		return $themes;
	}

	private function getLanguages()
	{
		static $languages = null;

		if (is_null($languages)) {
			$languages = [
				"abap" => "ABAP",
				"actionscript" => "ActionScript",
				"ada" => "ADA",
				"apache_conf" => "Apache Conf",
				"asciidoc" => "AsciiDoc",
				"assembly_x86" => "Assembly x86",
				"autohotkey" => "AutoHotKey",
				"batchfile" => "BatchFile",
				"c9search" => "C9Search",
				"c_cpp" => "C/C++",
				"cirru" => "Cirru",
				"clojure" => "Clojure",
				"cobol" => "Cobol",
				"coffee" => "CoffeeScript",
				"coldfusion" => "ColdFusion",
				"csharp" => "C#",
				"css" => "CSS",
				"curly" => "Curly",
				"d" => "D",
				"dart" => "Dart",
				"diff" => "Diff",
				"dockerfile" => "Dockerfile",
				"dot" => "Dot",
				"erlang" => "Erlang",
				"ejs" => "EJS",
				"forth" => "Forth",
				"ftl" => "FreeMarker",
				"gherkin" => "Gherkin",
				"gitignore" => "Gitignore",
				"glsl" => "Glsl",
				"golang" => "Go",
				"groovy" => "Groovy",
				"haml" => "HAML",
				"handlebars" => "Handlebars",
				"haskell" => "Haskell",
				"haxe" => "haXe",
				"html" => "HTML",
				"html_ruby" => "HTML (Ruby)",
				"ini" => "INI",
				"jack" => "Jack",
				"jade" => "Jade",
				"java" => "Java",
				"javascript" => "JavaScript",
				"json" => "JSON",
				"jsoniq" => "JSONiq",
				"jsp" => "JSP",
				"jsx" => "JSX",
				"julia" => "Julia",
				"latex" => "LaTeX",
				"less" => "LESS",
				"liquid" => "Liquid",
				"lisp" => "Lisp",
				"livescript" => "LiveScript",
				"logiql" => "LogiQL",
				"lsl" => "LSL",
				"lua" => "Lua",
				"luapage" => "LuaPage",
				"lucene" => "Lucene",
				"makefile" => "Makefile",
				"matlab" => "MATLAB",
				"markdown" => "Markdown",
				"mel" => "MEL",
				"mysql" => "MySQL",
				"mushcode" => "MUSHCode",
				"nix" => "Nix",
				"objectivec" => "Objective-C",
				"ocaml" => "OCaml",
				"pascal" => "Pascal",
				"perl" => "Perl",
				"pgsql" => "pgSQL",
				"php" => "PHP",
				"powershell" => "Powershell",
				"prolog" => "Prolog",
				"properties" => "Properties",
				"protobuf" => "Protobuf",
				"python" => "Python",
				"r" => "R",
				"rdoc" => "RDoc",
				"rhtml" => "RHTML",
				"ruby" => "Ruby",
				"rust" => "Rust",
				"sass" => "SASS",
				"scad" => "SCAD",
				"scala" => "Scala",
				"smarty" => "Smarty",
				"scheme" => "Scheme",
				"scss" => "SCSS",
				"sh" => "SH",
				"sjs" => "SJS",
				"space" => "Space",
				"snippets" => "snippets",
				"soy_template" => "Soy Template",
				"sql" => "SQL",
				"stylus" => "Stylus",
				"svg" => "SVG",
				"tcl" => "Tcl",
				"tex" => "Tex",
				"text" => "Text",
				"textile" => "Textile",
				"toml" => "Toml",
				"twig" => "Twig",
				"typescript" => "Typescript",
				"vala" => "Vala",
				"vbscript" => "VBScript",
				"velocity" => "Velocity",
				"verilog" => "Verilog",
				"xml" => "XML",
				"xquery" => "XQuery",
				"yaml" => "YAML"
			];
		}

		return $languages;
	}

	/**
	 * Renders the fieldset of a block
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getFieldset($meta)
	{
		$languages = $this->getLanguages();
		$themes = $this->getThemes();

		$theme = EB::themes();
		$theme->set('languages', $languages);
		$theme->set('themes', $themes);
		$theme->set('block', $this);
		$theme->set('data', $meta->data);
		$theme->set('params', $this->table->getParams());

		return $theme->output('site/composer/blocks/handlers/' . $this->type . '/fieldset');
	}

	/**
	 * Standard method to format the output for displaying purposes
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false)
	{
		// If configured to display text only, nothing should appear at all for this block.
		if ($textOnly) {
			return;
		}

		$uid = uniqid();

		// If there's no codes, skip this
		if (!isset($block->data->code) || !$block->data->code) {
			return;
		}

		// Initialize default attributes
		if (!isset($block->data->show_gutter)) {
			$block->data->show_gutter = true;
		}

		if (!isset($block->data->read_only)) {
			$block->data->read_only = false;
		}

		if (!isset($block->data->fontsize)) {
			$block->data->fontsize = 12;
		}

		$theme = EB::themes();
		$theme->set('data', $block->data);
		$theme->set('uid', $uid);
		$theme->set('html', $block->html);

		$contents = $theme->output('site/blocks/code');

		return $contents;
	}
}
