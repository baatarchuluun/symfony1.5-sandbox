<?php
define( 'CKFINDER_DEFAULT_BASEPATH', '/ckfinder/' ) ;
class CKFinder
{
	var $BasePath ;
	var $Width ;
	var $Height ;
	var $SelectFunction ;
	var $SelectFunctionData ;
	var $SelectThumbnailFunction ;
	var $SelectThumbnailFunctionData ;
	var $DisableThumbnailSelection = false ;
	var $ClassName = '' ;
	var $Id = '' ;
	var $ResourceType ;
	var $StartupPath ;
	var $RememberLastFolder = true ;
	var $StartupFolderExpanded = false ;

	// PHP 4 Constructor
	function CKFinder( $basePath = CKFINDER_DEFAULT_BASEPATH, $width = '100%', $height = 400, $selectFunction = null )
	{
		$this->BasePath			= empty( $basePath ) ? CKFINDER_DEFAULT_BASEPATH : $basePath ;
		$this->Width			= empty( $width ) ? '100%' : $width ;
		$this->Height			= empty( $height ) ? 400 : $height ;
		$this->SelectFunction	= $selectFunction ;
		$this->SelectThumbnailFunction	= $selectFunction ;
	}

	// Renders CKFinder in the current page.
	function Create()
	{
		echo $this->CreateHtml() ;
	}

	// Gets the HTML needed to create a CKFinder instance.
	function CreateHtml()
	{
		$className = $this->ClassName ;
		if ( !empty( $className ) )
			$className = ' class="' . $className . '"' ;

		$id = $this->Id ;
		if ( !empty( $id ) )
			$id = ' id="' . $id . '"' ;

		return '<iframe src="' . $this->_BuildUrl() . '" width="' . $this->Width . '" ' .
			'height="' . $this->Height . '"' . $className . $id . ' frameborder="0" scrolling="no"></iframe>' ;
	}

	function _BuildUrl( $url = "" )
	{
		if ( !$url )
			$url = $this->BasePath ;

		$qs = "" ;

		if ( empty( $url ) )
			$url = CKFINDER_DEFAULT_BASEPATH ;

		if ( $url[ strlen( $url ) - 1 ] != '/' )
			$url = $url . '/' ;

		$url .= 'ckfinder.html' ;

		if ( !empty( $this->SelectFunction ) )
			$qs .= '?action=js&amp;func=' . $this->SelectFunction ;

		if ( !empty( $this->SelectFunctionData ) )
		{
			$qs .= $qs ? "&amp;" : "?" ;
			$qs .= 'data=' . rawurlencode($this->SelectFunctionData) ;
		}

		if ( $this->DisableThumbnailSelection )
		{
			$qs .= $qs ? "&amp;" : "?" ;
			$qs .= "dts=1" ;
		}
		else if ( !empty( $this->SelectThumbnailFunction ) || !empty( $this->SelectFunction ) )
		{
			$qs .= $qs ? "&amp;" : "?" ;
			$qs .= 'thumbFunc=' . ( !empty( $this->SelectThumbnailFunction ) ? $this->SelectThumbnailFunction : $this->SelectFunction ) ;

			if ( !empty( $this->SelectThumbnailFunctionData ) )
				$qs .= '&amp;tdata=' . rawurlencode( $this->SelectThumbnailFunctionData ) ;
			else if ( empty( $this->SelectThumbnailFunction ) && !empty( $this->SelectFunctionData ) )
				$qs .= '&amp;tdata=' . rawurlencode( $this->SelectFunctionData ) ;
		}

		if ( !empty( $this->StartupPath ) )
		{
			$qs .= ( $qs ? "&amp;" : "?" ) ;
			$qs .= "start=" . urlencode( $this->StartupPath . ( $this->StartupFolderExpanded ? ':1' : ':0' ) ) ;
		}

		if ( !empty( $this->ResourceType ) )
		{
			$qs .= ( $qs ? "&amp;" : "?" ) ;
			$qs .= "type=" . urlencode( $this->ResourceType ) ;
		}

		if ( !$this->RememberLastFolder )
		{
			$qs .= ( $qs ? "&amp;" : "?" ) ;
			$qs .= "rlf=0" ;
		}

		if ( !empty( $this->Id ) )
		{
			$qs .= ( $qs ? "&amp;" : "?" ) ;
			$qs .= "id=" . urlencode( $this->Id ) ;
		}

		return $url . $qs ;
	}

	// Static "Create".
	function CreateStatic( $basePath = CKFINDER_DEFAULT_BASEPATH, $width = '100%', $height = 400, $selectFunction = null )
	{
		$finder = new CKFinder( $basePath, $width, $height, $selectFunction ) ;
		$finder->Create() ;
	}

	// Static "SetupFCKeditor".
	function SetupFCKeditor( &$editorObj, $basePath = CKFINDER_DEFAULT_BASEPATH, $imageType = null, $flashType = null )
	{
		if ( empty( $basePath ) )
			$basePath = CKFINDER_DEFAULT_BASEPATH ;

		// If it is a path relative to the current page.
		if ( $basePath[0] != '/' )
		{
			$basePath = substr( $_SERVER[ 'REQUEST_URI' ], 0, strrpos( $_SERVER[ 'REQUEST_URI' ], '/' ) + 1 ) .
				$basePath ;
		}

		$ckfinder = new CKFinder( $basePath ) ;
		$ckfinder->SetupFCKeditorObject( $editorObj, $imageType, $flashType );
	}

	// Non-static method of attaching CKFinder to FCKeditor
	function SetupFCKeditorObject( &$editorObj, $imageType = null, $flashType = null )
	{
		$url = $this->BasePath ;

		// If it is a path relative to the current page.
		if ( isset($url[0]) && $url[0] != '/' )
		{
			$url = substr( $_SERVER[ 'REQUEST_URI' ], 0, strrpos( $_SERVER[ 'REQUEST_URI' ], '/' ) + 1 ) . $url ;
		}

		$url = $this->_BuildUrl( $url ) ;
		$qs = ( strpos($url, "?") !== false ) ? "&amp;" : "?" ;

		if ( $this->Width !== '100%' && is_numeric( str_replace( "px", "", strtolower( $this->Width ) ) ) )
		{
			$width = intval( $this->Width );
			$editorObj->Config['LinkBrowserWindowWidth'] = $width ;
			$editorObj->Config['ImageBrowserWindowWidth'] = $width ;
			$editorObj->Config['FlashBrowserWindowWidth'] = $width ;
		}
		if ( $this->Height !== 400 && is_numeric( str_replace( "px", "", strtolower( $this->Height ) ) ) )
		{
			$height = intval( $this->Height );
			$editorObj->Config['LinkBrowserWindowHeight'] = $height ;
			$editorObj->Config['ImageBrowserWindowHeight'] = $height ;
			$editorObj->Config['FlashBrowserWindowHeight'] = $height ;
		}

		$editorObj->Config['LinkBrowserURL'] = $url ;
		$editorObj->Config['ImageBrowserURL'] = $url . $qs . 'type=' . ( empty( $imageType ) ? 'Images' : $imageType ) ;
		$editorObj->Config['FlashBrowserURL'] = $url . $qs . 'type=' . ( empty( $flashType ) ? 'Flash' : $flashType ) ;

		$dir = substr( $url, 0, strrpos( $url, "/" ) + 1 ) ;
		$editorObj->Config['LinkUploadURL'] = $dir . urlencode( 'core/connector/php/connector.php?command=QuickUpload&type=Files' ) ;
		$editorObj->Config['ImageUploadURL'] = $dir . urlencode( 'core/connector/php/connector.php?command=QuickUpload&type=') . ( empty( $imageType ) ? 'Images' : $imageType ) ;
		$editorObj->Config['FlashUploadURL'] = $dir . urlencode( 'core/connector/php/connector.php?command=QuickUpload&type=') . ( empty( $flashType ) ? 'Flash' : $flashType ) ;
	}

	// Static "SetupCKEditor".
	function SetupCKEditor( &$editorObj, $basePath = CKFINDER_DEFAULT_BASEPATH, $imageType = null, $flashType = null )
	{
		if ( empty( $basePath ) )
			$basePath = CKFINDER_DEFAULT_BASEPATH ;

		$ckfinder = new CKFinder( $basePath ) ;
		$ckfinder->SetupCKEditorObject( $editorObj, $imageType, $flashType );
	}

	// Non-static method of attaching CKFinder to CKEditor
	function SetupCKEditorObject( &$editorObj, $imageType = null, $flashType = null )
	{
		$url = $this->BasePath ;

		// If it is a path relative to the current page.
		if ( isset($url[0]) && $url[0] != '/' )
		{
			$url = substr( $_SERVER[ 'REQUEST_URI' ], 0, strrpos( $_SERVER[ 'REQUEST_URI' ], '/' ) + 1 ) . $url ;
		}

		$url = $this->_BuildUrl( $url ) ;
		$qs = ( strpos($url, "?") !== false ) ? "&" : "?" ;

		if ( $this->Width !== '100%' && is_numeric( str_ireplace( "px", "", $this->Width ) ) )
		{
			$width = intval( $this->Width );
			$editorObj->config['filebrowserWindowWidth'] = $width ;
		}
		if ( $this->Height !== 400 && is_numeric( str_ireplace( "px", "", $this->Height ) ) )
		{
			$height = intval( $this->Height );
			$editorObj->config['filebrowserWindowHeight'] = $height ;
		}

		$editorObj->config['filebrowserBrowseUrl'] = $url ;
		$editorObj->config['filebrowserImageBrowseUrl'] = $url . $qs . 'type=' . ( empty( $imageType ) ? 'Images' : $imageType ) ;
		$editorObj->config['filebrowserFlashBrowseUrl'] = $url . $qs . 'type=' . ( empty( $flashType ) ? 'Flash' : $flashType ) ;

		$dir = substr( $url, 0, strrpos( $url, "/" ) + 1 ) ;
		$editorObj->config['filebrowserUploadUrl'] = $dir . 'core/connector/php/connector.php?command=QuickUpload&type=Files' ;
		$editorObj->config['filebrowserImageUploadUrl'] = $dir . 'core/connector/php/connector.php?command=QuickUpload&type=' . ( empty( $imageType ) ? 'Images' : $imageType ) ;
		$editorObj->config['filebrowserFlashUploadUrl'] = $dir . 'core/connector/php/connector.php?command=QuickUpload&type=' . ( empty( $flashType ) ? 'Flash' : $flashType ) ;
	}
}

/*
* Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
* For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * \brief CKEditor class that can be used to create editor
 * instances in PHP pages on server side.
 * @see http://ckeditor.com
 *
 * Sample usage:
 * @code
 * $CKEditor = new CKEditor();
 * $CKEditor->editor("editor1", "<p>Initial value.</p>");
 * @endcode
 */
class CKEditor
{
	/**
	 * The version of %CKEditor.
	 * \private
	 */
	var $version = '3.4.1';
	/**
	 * A constant string unique for each release of %CKEditor.
	 * \private
	 */
	var $_timestamp = 'A8LE4JO';

	/**
	 * URL to the %CKEditor installation directory (absolute or relative to document root).
	 * If not set, CKEditor will try to guess it's path.
	 *
	 * Example usage:
	 * @code
	 * $CKEditor->basePath = '/ckeditor/';
	 * @endcode
	 */
	var $basePath;
	/**
	 * An array that holds the global %CKEditor configuration.
	 * For the list of available options, see http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html
	 *
	 * Example usage:
	 * @code
	 * $CKEditor->config['height'] = 400;
	 * // Use @@ at the beggining of a string to ouput it without surrounding quotes.
	 * $CKEditor->config['width'] = '@@screen.width * 0.8';
	 * @endcode
	 */
	var $config = array();
	/**
	 * A boolean variable indicating whether CKEditor has been initialized.
	 * Set it to true only if you have already included
	 * &lt;script&gt; tag loading ckeditor.js in your website.
	 */
	var $initialized = false;
	/**
	 * Boolean variable indicating whether created code should be printed out or returned by a function.
	 *
	 * Example 1: get the code creating %CKEditor instance and print it on a page with the "echo" function.
	 * @code
	 * $CKEditor = new CKEditor();
	 * $CKEditor->returnOutput = true;
	 * $code = $CKEditor->editor("editor1", "<p>Initial value.</p>");
	 * echo "<p>Editor 1:</p>";
	 * echo $code;
	 * @endcode
	 */
	var $returnOutput = false;
	/**
	 * An array with textarea attributes.
	 *
	 * When %CKEditor is created with the editor() method, a HTML &lt;textarea&gt; element is created,
	 * it will be displayed to anyone with JavaScript disabled or with incompatible browser.
	 */
	var $textareaAttributes = array( "rows" => 8, "cols" => 60 );
	/**
	 * A string indicating the creation date of %CKEditor.
	 * Do not change it unless you want to force browsers to not use previously cached version of %CKEditor.
	 */
	var $timestamp = "A8LE4JO";
	/**
	 * An array that holds event listeners.
	 * \private
	 */
	var $_events = array();
	/**
	 * An array that holds global event listeners.
	 * \private
	 */
	var $_globalEvents = array();

	/**
	 * Main Constructor.
	 *
	 *  @param $basePath (string) URL to the %CKEditor installation directory (optional).
	 */
	function CKEditor($basePath = null) {
		if (!empty($basePath)) {
			$this->basePath = $basePath;
		}
	}

	/**
	 * Creates a %CKEditor instance.
	 * In incompatible browsers %CKEditor will downgrade to plain HTML &lt;textarea&gt; element.
	 *
	 * @param $name (string) Name of the %CKEditor instance (this will be also the "name" attribute of textarea element).
	 * @param $value (string) Initial value (optional).
	 * @param $config (array) The specific configurations to apply to this editor instance (optional).
	 * @param $events (array) Event listeners for this editor instance (optional).
	 *
	 * Example usage:
	 * @code
	 * $CKEditor = new CKEditor();
	 * $CKEditor->editor("field1", "<p>Initial value.</p>");
	 * @endcode
	 *
	 * Advanced example:
	 * @code
	 * $CKEditor = new CKEditor();
	 * $config = array();
	 * $config['toolbar'] = array(
	 *     array( 'Source', '-', 'Bold', 'Italic', 'Underline', 'Strike' ),
	 *     array( 'Image', 'Link', 'Unlink', 'Anchor' )
	 * );
	 * $events['instanceReady'] = 'function (ev) {
	 *     alert("Loaded: " + ev.editor.name);
	 * }';
	 * $CKEditor->editor("field1", "<p>Initial value.</p>", $config, $events);
	 * @endcode
	 */
	function editor($name, $value = "", $config = array(), $events = array())
	{
		$attr = "";
		foreach ($this->textareaAttributes as $key => $val) {
			$attr.= " " . $key . '="' . str_replace('"', '&quot;', $val) . '"';
		}
		$out = "<textarea name=\"" . $name . "\"" . $attr . ">" . htmlspecialchars($value) . "</textarea>\n";
		if (!$this->initialized) {
			$out .= $this->init();
		}

		$_config = $this->configSettings($config, $events);

		$js = $this->returnGlobalEvents();
		if (!empty($_config))
			$js .= "CKEDITOR.replace('".$name."', ".$this->jsEncode($_config).");";
		else
			$js .= "CKEDITOR.replace('".$name."');";

		$out .= $this->script($js);

		if (!$this->returnOutput) {
			print $out;
			$out = "";
		}

		return $out;
	}

	/**
	 * Replaces a &lt;textarea&gt; with a %CKEditor instance.
	 *
	 * @param $id (string) The id or name of textarea element.
	 * @param $config (array) The specific configurations to apply to this editor instance (optional).
	 * @param $events (array) Event listeners for this editor instance (optional).
	 *
	 * Example 1: adding %CKEditor to &lt;textarea name="article"&gt;&lt;/textarea&gt; element:
	 * @code
	 * $CKEditor = new CKEditor();
	 * $CKEditor->replace("article");
	 * @endcode
	 */
	function replace($id, $config = array(), $events = array())
	{
		$out = "";
		if (!$this->initialized) {
			$out .= $this->init();
		}

		$_config = $this->configSettings($config, $events);

		$js = $this->returnGlobalEvents();
		if (!empty($_config)) {
			$js .= "CKEDITOR.replace('".$id."', ".$this->jsEncode($_config).");";
		}
		else {
			$js .= "CKEDITOR.replace('".$id."');";
		}
		$out .= $this->script($js);

		if (!$this->returnOutput) {
			print $out;
			$out = "";
		}

		return $out;
	}

	/**
	 * Replace all &lt;textarea&gt; elements available in the document with editor instances.
	 *
	 * @param $className (string) If set, replace all textareas with class className in the page.
	 *
	 * Example 1: replace all &lt;textarea&gt; elements in the page.
	 * @code
	 * $CKEditor = new CKEditor();
	 * $CKEditor->replaceAll();
	 * @endcode
	 *
	 * Example 2: replace all &lt;textarea class="myClassName"&gt; elements in the page.
	 * @code
	 * $CKEditor = new CKEditor();
	 * $CKEditor->replaceAll( 'myClassName' );
	 * @endcode
	 */
	function replaceAll($className = null)
	{
		$out = "";
		if (!$this->initialized) {
			$out .= $this->init();
		}

		$_config = $this->configSettings();

		$js = $this->returnGlobalEvents();
		if (empty($_config)) {
			if (empty($className)) {
				$js .= "CKEDITOR.replaceAll();";
			}
			else {
				$js .= "CKEDITOR.replaceAll('".$className."');";
			}
		}
		else {
			$classDetection = "";
			$js .= "CKEDITOR.replaceAll( function(textarea, config) {\n";
			if (!empty($className)) {
				$js .= "	var classRegex = new RegExp('(?:^| )' + '". $className ."' + '(?:$| )');\n";
				$js .= "	if (!classRegex.test(textarea.className))\n";
				$js .= "		return false;\n";
			}
			$js .= "	CKEDITOR.tools.extend(config, ". $this->jsEncode($_config) .", true);";
			$js .= "} );";

		}

		$out .= $this->script($js);

		if (!$this->returnOutput) {
			print $out;
			$out = "";
		}

		return $out;
	}

	/**
	 * Adds event listener.
	 * Events are fired by %CKEditor in various situations.
	 *
	 * @param $event (string) Event name.
	 * @param $javascriptCode (string) Javascript anonymous function or function name.
	 *
	 * Example usage:
	 * @code
	 * $CKEditor->addEventHandler('instanceReady', 'function (ev) {
	 *     alert("Loaded: " + ev.editor.name);
	 * }');
	 * @endcode
	 */
	function addEventHandler($event, $javascriptCode)
	{
		if (!isset($this->_events[$event])) {
			$this->_events[$event] = array();
		}
		// Avoid duplicates.
		if (!in_array($javascriptCode, $this->_events[$event])) {
			$this->_events[$event][] = $javascriptCode;
		}
	}

	/**
	 * Clear registered event handlers.
	 * Note: this function will have no effect on already created editor instances.
	 *
	 * @param $event (string) Event name, if not set all event handlers will be removed (optional).
	 */
	function clearEventHandlers($event = null)
	{
		if (!empty($event)) {
			$this->_events[$event] = array();
		}
		else {
			$this->_events = array();
		}
	}

	/**
	 * Adds global event listener.
	 *
	 * @param $event (string) Event name.
	 * @param $javascriptCode (string) Javascript anonymous function or function name.
	 *
	 * Example usage:
	 * @code
	 * $CKEditor->addGlobalEventHandler('dialogDefinition', 'function (ev) {
	 *     alert("Loading dialog: " + ev.data.name);
	 * }');
	 * @endcode
	 */
	function addGlobalEventHandler($event, $javascriptCode)
	{
		if (!isset($this->_globalEvents[$event])) {
			$this->_globalEvents[$event] = array();
		}
		// Avoid duplicates.
		if (!in_array($javascriptCode, $this->_globalEvents[$event])) {
			$this->_globalEvents[$event][] = $javascriptCode;
		}
	}

	/**
	 * Clear registered global event handlers.
	 * Note: this function will have no effect if the event handler has been already printed/returned.
	 *
	 * @param $event (string) Event name, if not set all event handlers will be removed (optional).
	 */
	function clearGlobalEventHandlers($event = null)
	{
		if (!empty($event)) {
			$this->_globalEvents[$event] = array();
		}
		else {
			$this->_globalEvents = array();
		}
	}

	/**
	 * Prints javascript code.
	 * \private
	 *
	 * @param string $js
	 */
	function script($js)
	{
		$out = "<script type=\"text/javascript\">";
		$out .= "//<![CDATA[\n";
		$out .= $js;
		$out .= "\n//]]>";
		$out .= "</script>\n";

		return $out;
	}

	/**
	 * Returns the configuration array (global and instance specific settings are merged into one array).
	 * \private
	 *
	 * @param $config (array) The specific configurations to apply to editor instance.
	 * @param $events (array) Event listeners for editor instance.
	 */
	function configSettings($config = array(), $events = array())
	{
		$_config = $this->config;
		$_events = $this->_events;

		if (is_array($config) && !empty($config)) {
			$_config = array_merge($_config, $config);
		}

		if (is_array($events) && !empty($events)) {
			foreach ($events as $eventName => $code) {
				if (!isset($_events[$eventName])) {
					$_events[$eventName] = array();
				}
				if (!in_array($code, $_events[$eventName])) {
					$_events[$eventName][] = $code;
				}
			}
		}

		if (!empty($_events)) {
			foreach($_events as $eventName => $handlers) {
				if (empty($handlers)) {
					continue;
				}
				else if (count($handlers) == 1) {
					$_config['on'][$eventName] = '@@'.$handlers[0];
				}
				else {
					$_config['on'][$eventName] = '@@function (ev){';
					foreach ($handlers as $handler => $code) {
						$_config['on'][$eventName] .= '('.$code.')(ev);';
					}
					$_config['on'][$eventName] .= '}';
				}
			}
		}

		return $_config;
	}

	/**
	 * Return global event handlers.
	 * \private
	 */
	function returnGlobalEvents()
	{
		static $returnedEvents;
		$out = "";

		if (!isset($returnedEvents)) {
			$returnedEvents = array();
		}

		if (!empty($this->_globalEvents)) {
			foreach ($this->_globalEvents as $eventName => $handlers) {
				foreach ($handlers as $handler => $code) {
					if (!isset($returnedEvents[$eventName])) {
						$returnedEvents[$eventName] = array();
					}
					// Return only new events
					if (!in_array($code, $returnedEvents[$eventName])) {
						$out .= ($code ? "\n" : "") . "CKEDITOR.on('". $eventName ."', $code);";
						$returnedEvents[$eventName][] = $code;
					}
				}
			}
		}

		return $out;
	}

	/**
	 * Initializes CKEditor (executed only once).
	 * \private
	 */
	function init()
	{
		static $initComplete;
		$out = "";

		if (!empty($initComplete)) {
			return "";
		}

		if ($this->initialized) {
			$initComplete = true;
			return "";
		}

		$args = "";
		$ckeditorPath = $this->ckeditorPath();

		if (!empty($this->timestamp) && $this->timestamp != "%"."TIMESTAMP%") {
			$args = '?t=' . $this->timestamp;
		}

		// Skip relative paths...
		if (strpos($ckeditorPath, '..') !== 0) {
			$out .= $this->script("window.CKEDITOR_BASEPATH='". $ckeditorPath ."';");
		}

		$out .= "<script type=\"text/javascript\" src=\"" . $ckeditorPath . 'ckeditor.js' . $args . "\"></script>\n";

		$extraCode = "";
		if ($this->timestamp != $this->_timestamp) {
			$extraCode .= ($extraCode ? "\n" : "") . "CKEDITOR.timestamp = '". $this->timestamp ."';";
		}
		if ($extraCode) {
			$out .= $this->script($extraCode);
		}

		$initComplete = $this->initialized = true;

		return $out;
	}

	/**
	 * Return path to ckeditor.js.
	 * \private
	 */
	function ckeditorPath()
	{
		if (!empty($this->basePath)) {
			return $this->basePath;
		}

		/**
		 * The absolute pathname of the currently executing script.
		 * Note: If a script is executed with the CLI, as a relative path, such as file.php or ../file.php,
		 * $_SERVER['SCRIPT_FILENAME'] will contain the relative path specified by the user.
		 */
		if (isset($_SERVER['SCRIPT_FILENAME'])) {
			$realPath = dirname($_SERVER['SCRIPT_FILENAME']);
		}
		else {
			/**
			 * realpath — Returns canonicalized absolute pathname
			 */
			$realPath = realpath( './' ) ;
		}

		/**
		 * The filename of the currently executing script, relative to the document root.
		 * For instance, $_SERVER['PHP_SELF'] in a script at the address http://example.com/test.php/foo.bar
		 * would be /test.php/foo.bar.
		 */
		$selfPath = dirname($_SERVER['PHP_SELF']);
		$file = str_replace("\\", "/", __FILE__);

		if (!$selfPath || !$realPath || !$file) {
			return "/ckeditor/";
		}

		$documentRoot = substr($realPath, 0, strlen($realPath) - strlen($selfPath));
		$fileUrl = substr($file, strlen($documentRoot));
		$ckeditorUrl = str_replace("ckeditor_php5.php", "", $fileUrl);

		return $ckeditorUrl;
	}

	/**
	 * This little function provides a basic JSON support.
	 * http://php.net/manual/en/function.json-encode.php
	 * \private
	 *
	 * @param mixed $val
	 * @return string
	 */
	function jsEncode($val)
	{
		if (is_null($val)) {
			return 'null';
		}
		if ($val === false) {
			return 'false';
		}
		if ($val === true) {
			return 'true';
		}
		if (is_scalar($val))
		{
			if (is_float($val))
			{
				// Always use "." for floats.
				$val = str_replace(",", ".", strval($val));
			}

			// Use @@ to not use quotes when outputting string value
			if (strpos($val, '@@') === 0) {
				return substr($val, 2);
			}
			else {
				// All scalars are converted to strings to avoid indeterminism.
				// PHP's "1" and 1 are equal for all PHP operators, but
				// JS's "1" and 1 are not. So if we pass "1" or 1 from the PHP backend,
				// we should get the same result in the JS frontend (string).
				// Character replacements for JSON.
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
				array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));

				$val = str_replace($jsonReplaces[0], $jsonReplaces[1], $val);

				return '"' . $val . '"';
			}
		}
		$isList = true;
		for ($i = 0, reset($val); $i < count($val); $i++, next($val))
		{
			if (key($val) !== $i)
			{
				$isList = false;
				break;
			}
		}
		$result = array();
		if ($isList)
		{
			foreach ($val as $v) $result[] = $this->jsEncode($v);
			return '[ ' . join(', ', $result) . ' ]';
		}
		else
		{
			foreach ($val as $k => $v) $result[] = $this->jsEncode($k).': '.$this->jsEncode($v);
			return '{ ' . join(', ', $result) . ' }';
		}
	}
}

class sfWidgetFormCKEditor extends sfWidgetFormTextarea {
  
  protected $_editor;
  protected $_finder;
  
  protected function configure($options = array(), $attributes = array())
  {
    $editorClass = 'CKEditor';
	  if (!class_exists($editorClass))
	  {
   	  throw new sfConfigurationException(sprintf('CKEditor class not found'));    
	  }
    $this->_editor = new $editorClass();
    $this->_editor->basePath = sfConfig::get('app_ckeditor_basePath');
    $this->_editor->returnOutput = true;
    if(sfConfig::get('app_ckfinder_active', false) == 'true')
    {
      $finderClass = 'CKFinder';
  	  if (!class_exists($finderClass))
  	  {
     	  throw new sfConfigurationException(sprintf('CKFinder class not found'));    
  	  }      
      $this->_finder = new $finderClass();
      $this->_finder->BasePath = sfConfig::get('app_ckfinder_basePath');
      $this->_finder->SetupCKEditorObject($this->_editor);
    }
    if(isset($options['jsoptions']))
    {
      $this->addOption('jsoptions', $options['jsoptions']);    
    }
    parent::configure($options, $attributes);
  }
  
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $jsoptions = $this->getOption('jsoptions');
    if($jsoptions)
    {   
      $this->setJSOptions($name, $value, $attributes, $errors);
    }
    return parent::render($name, $value, $attributes, $errors).$this->_editor->replace($name); 

  }
  protected function setJSOptions($name, $value = null, $attributes = array(), $errors = array())
  {
    $jsoptions = $this->getOption('jsoptions');
    foreach($jsoptions as $k => $v)
    {
      $this->_editor->config[$k] = $v;
    }
  }
  public function getEditor()
  {
    return $this->_editor;
  }  
  public function getFinder()
  {
    return $this->_finder;
  }  
}