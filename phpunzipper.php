<?php
ob_start();

/**
 License: MIT
 
 Author: Kamil "nameczanin" Dabrowski aka "Namek"
 Date: 2009-07-08 17:34
 Version: 0.1
 
 Functionality: unpacks ZIP file in the selected (through GUI) directory.
 
*/

/****************************** Configuration **********************************/
//                                                                             //
 
	// Whether to show other files than .ZIP
	$DEFAULT_SHOW_OTHER_FILES = true;
 
//                                                                             //
/**************************** End Configuration ********************************/
 
/********************************* Startup *************************************/
//
	define("LIST_FILES", 1);
	define("SELECT_UNPACK_FOLDER", 2);
	define("UNPACK_FILE", 3);
	define("GET_IMAGE", 4);
	define("GET_CSS", 5);
	define("SHOW_SETTINGS", 6);
	define("CHANGE_SETTING", 7);
 
	$Action = (empty($_GET['action'])) ? LIST_FILES : $_GET['action'];
	$Filename = empty($_GET['filename']) ? null : $_GET['filename'];
	$Folder = (empty($_GET['folder'])) ? "." : $_GET['folder'];
 
	session_start();
 
	if (!isset($_SESSION["SHOW_OTHER_FILES"])) {
		$_SESSION["SHOW_OTHER_FILES"] = $DEFAULT_SHOW_OTHER_FILES;
	}
 
/******************************* End Startup ***********************************/  
 
/******************************** Functions ************************************/

	// Returns array of files
	function listFiles($folder, $list_subfolders = true, $list_zips = true, $show_others = true) {
		$files = array();
		$folders = array();
		$others = array();
 
		if ($handle = opendir($folder)) {
			while (($filename = readdir($handle)) !== false) {
				if ($filename != '.') {
					if ($list_zips && stripos($filename, ".zip") !== false) {
						array_push($files, $filename);
					}
					else if ($list_subfolders && is_dir($folder.'/'.$filename)) {
						array_push($folders, $filename);
					}
					else if ($show_others && $_SESSION["SHOW_OTHER_FILES"]) {
						array_push($others, $filename);
					}
				}
			}
 
			closedir($handle);
		}
 
		sort($folders);
		sort($files);
		sort($others);

		return array_merge($folders, $files, $others);
	}
 
	// Shows one of images
	function showImage($imageName) {
		$data = '';
 
		if ($imageName == "archive")
			$data = "iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAA3XAAAN1wFCKJt4AAAAB3RJTUUH1wkcEQM5vfwe7QAAAalJREFUOMutkjFoVEEQhr/Z2913d8k9YgxBrhARBJEgSSOKTcTGUgkoh41YKJYRCzuvSCkepBGUFLEJaBUsBEUPtRT0mohibIyYIipRouFd3tuxeDkVI3hRp5nZgf+b2ZmBfzQBaNTMMPBsk9qR8ZnQkkbNNIHRs1cX9P38YxVbNqAAGCO5F0g+L2JKAxlpIi+e3jNzD6cB6hbg9OVWliy/MbOTp8Q7B8DaWhsfRQiCcw5TcPi4Wjh28Q5Lb5+z+8AYr1v3Ry2wQ4yYkKViCp405P1FvQMAVPqrIAaA6q59tFfeEShQ2bINDdmwBfqSlQ+8fHKbkxemQLMNn1UFUAht0o+vSFY/4YoxABYoPro5IYePnoP0C2SroAHVDEKKash9WAdrYM/e/TyYvQ5o0QJ+cPuQYozgK0Dlx3pQJK+9/qYzX4ZGDrIw14ykUTOXgPpfnkG9cwd65MR5luebXaninYe4e+sK4zNBbCfpozI98dauAM5H3+OfACWI+7sCmKi4EeBKPdguAan7XQdxleB7/yDN9xGC/AKw5WvTE8fPbGb8XxO9wf+wb+DSh8wTKaW0AAAAAElFTkSuQmCC";
 
		else if ($imageName == "folder")
			$data = "iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9kHBQ0fGkT7r9IAAABBdEVYdENvbW1lbnQAQ1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2NjIpLCBxdWFsaXR5ID0gOTMKm2gLUAAAAwlJREFUOMttkU1vG1UUht9z586Mx+OPhjq2E1MnAaVAIlqEWBQWRCyjlkrdsuA39DfwD1gh/gAgsUEskCplX0UQCBKfMqGJm4TEX+NJ5sMzc+89LLARC971eV89Og8pZYQgI0FWiSn1i3ispdeJCJyBDDOIBJGjs+mNdHJYjY0VtTs7EwAZAJZE8EmIcnj+5Ob5wceNdHattx58OXBKyxNBUpu8b59+/+nS8I+vV3Ry7tGtx6ftzk4MoACgZZGeNH756oOVZPp0Uzp40S5sOvvpi0F7Y+vyZP+TbHLyTUVa+aot0YKxMlvaJQDXKu2l0ts0EqJUVWbcLdnYNqBtlqo6/vGjeHqQB7aO4UtUCmFVzUz7pixHNf83mu7vDq6yOOi8s1dI12vnnnuTsgTLgtERbLVgAiIBlRBgu470/arT6LRUvTyrF2efTSDNz5l41LOkE0kAoSqvRzTZn7Fg1whVUTPLphfKYuOtO1TinDE4Ejz7XelU51qi5li+m7sPCQAkgKjevTcMnn9+zYys0Bat37tjN9ealP76A6loAHfJZq/CBC6BOU+f/VW7Wn393QIACwC5rN6dsrAuHCmiwZiV9D0eHewRqQGqLYLnFmBlNCNPYPFl6u8Ml1prGQAIAPrWxk6Yc+VSGwTG8TM5OYVrE3tVQIBZEzFLZIAZZrl37C/fH801sgBgAGRUeWVaaA7IQu7oK1WvM1gSA0IJwwmYRgT0ZrTaq7feDADoBQEDULXm3ThOOdp8bSXy/FRB6FyAYwIHYNEXhg+Z8G2MtaOl5lYy70Hgn+jl7ntJGInpelMFRNmIQRcw4ogFfwfiPdjuk37y8tNh9uh8jo+FBQDQpcbtqFQrn/l++EwnzpitPGRtH8/yRi/gV48m1/fP2usPxm9s3E7m+LwYYADacupha7X1J/K+ISJ9PNgehPbD56219y+7L7191QVmANT8ngEQACZmBgALQLV/+GEzmSo3cnbTza3dsH6jEQPI54/mOe2/ZQBYDBAANw4vyn69bQCkC03/Kf5v/ga1r5OLDiUtHgAAAABJRU5ErkJggg==";
 
		else if ($imageName == "extract")
			$data = "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";
 
		else if ($imageName == "file")
			$data = "iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAANkE3LLaAgAAARxJREFUeJydkTFqhEAUhv/RaQRJJRirnEJILhEJpLGyMpfxAFaWOUGaVHuAgHXA2iKCYCUS5vkmzWaZWV3X5IdhmOGb7/HmCRxzOBy+mVlhJY7juF3X1WmaPgP4WgC+7z8Rkb60pmnSTdPoqqo+ANxY8uOu1yqbGccRWZbFZVm+AwjOBZuRUoKI0Pc98jx/KIriDcDtboEQAkoptG2Luq6RJMl9FEWPACBNUOvLncRxDCICEcHzPAgh3IVgTytS2k+sEzPvErmu+z8BM4OZ4Xne3wTMDK01mBmO41isJVBKnQATMsXnRSwBEW22sBYJAPM8w9yvRWt9Yq/+gRBicWdyliAIgl19m2P81d+FYfjCBrkxEQEAwzC8Avj8ARK8rCIgUJQ+AAAAAElFTkSuQmCC";
 
		else if ($imageName == "settings")
			$data = "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";
 
		if (!empty($data)) {
			header("Content-Type: image/png");
			echo base64_decode($data);
		}
	}
 
	// Returns filename of current script
	function getScriptPath() {
		$currentFile = $_SERVER["SCRIPT_NAME"];
		$parts = Explode('/', $currentFile);
		$currentFile = $parts[count($parts) - 1]; 
 
		return $currentFile;
	}
 
	// Returns script path
	function getWorkingDir() {
		return array_pop(explode('/', str_replace('\\', '/', getcwd())));
	}
 
	// Returns CSS file
	function returnStyle() {
		header("Content-Type: text/css");
 
		echo '
		body {
			font-family: \'Trebuchet MS\', \'Verdana\';
			font-size: 16px;
		}
 
		a {
			text-decoration: none;
			border: 0;
			color: black;
		}
 
		a:hover {
			text-decoration: underline;
		}
 
		a.button1 {
			display: block;
			margin-left: 15px;
			margin-top: 40px;
		}
 
		a.button {
			display: block;
			margin-left: 15px;
			margin-top: 15px;
		}
 
		h1 {
			font-size: 20px;
		}
 
		img {
			border: 0;
		}
 
		img:hover {
			width: 80;
			height: 20;
			margin-top: 4px;
			margin-left: 4px;
		}
 
		div.list {
			margin-top: 30px;
			width: 400px;
			padding: 0;
			border-top: 1px solid black;
		}
 
		div.list ul {
			list-style: none;
			margin: 0;
			padding: 0;
		}
 
		div.list ul li {
			margin: 0;
			width: 100%;
			border-bottom: 1px solid black;
		}
 
		div.list ul li:hover {
			background: #CFEBFC;
		}
 
		div.list img {
			margin-right: 7px;
			margin-top: 3px;
			margin-left: 4px;
		}
 
		div.list li a {
			display: inline-block;
			padding: 5px 0px 5px 0px;
			text-decoration: none;
			width: 100%;
		}
 
		div.list li a:hover {
			text-decoration: none;
			color: white;
		}
 
		input[type="button"] {
			margin-top: 20px;
		}
 
		';
	}
 
	function BeginHtml() {
		echo '
		<html>
		<head>
			<link rel="stylesheet" type="text/css" href="'.getScriptPath().'?action='.GET_CSS.'"/>
		</head>
		<body>
		';
	}
 
	function EndHtml() {
		echo '
		</body>
		</html>';
	}
 
	function BeginList() {
		echo '<div class="list"><ul>';
	}
 
	function EndList() {
		echo '</ul></div>';
	}
 
	function Head($head, $stronged = '') {
		$str = '<h1>'.$head;
		if (!empty($stronged))
			$str .= ': <b>'.$stronged.'</b>';
		$str .= '</h1>';
 
		echo $str;
	}
 
	function Img($imageName) {
		return '<img class="button" src="'.getScriptPath().'?action='.GET_IMAGE.'&filename='.$imageName.'"/>';
	}
 
	// Reparses path to give the shortest string
	// For example: ./folder1/../folder1/./asdasd/./../../..  ->  ..
	function ParsePath($folder) {
		$folder = str_replace('\\', '/', $folder);
		$folder = rtrim($folder, '/');
		$folder = str_replace('../'.getWorkingDir(), './', $folder);
 
		$names = explode('/', $folder);
		$names2 = array();
		$new_folder = "";
 
		$remove = 0;
 
		while (!empty($names)) {
			$name = array_shift($names);
 
			// Just ignore '.'
			if ($name == '.') {
			}
 
			// Increment removing
			else if ($name == '..') {
				if (!(array_pop($names2))) {
					++$remove;
				}
			}
 
			// Add
			else if (!empty($name)) {
				array_push($names2, $name);
			}
		}
 
		// Now prepend '..' needed times
		for (; $remove > 0; --$remove) {
			array_unshift($names2, '..');
		}
 
		// Gather everything
		$str = implode($names2, '/');
		return $str;
	}
 
	function CreatePathLinks($fullpath) {
		$str = '';
		$names = explode('/', rtrim($fullpath, '/'));
 
		$tmp = '';
		while (!empty($names)) {
			global $Filename;
			global $Action;
 
			$action = $Action;
 
			$path = array_shift($names);
			$tmp .= $path.'/';
 
			$str .= '<a href="'.getScriptPath().'?action='.$action.'&folder='.$tmp;
			if (!empty($Filename)) {
				$str .= '&filename='.$Filename;
			}
			$str .= '">'.$path.'</a>';
 
			if (!empty($names)) {
				$str .= ' / ';
			}
		}
 
		return rtrim($str, '/');
	}
 
/****************************** End Functions **********************************/
 
?><?php
 
switch ($Action) {
 
	// List ZIP files and subfolders
	case LIST_FILES: {
		$files = listFiles($Folder);
 
		BeginHtml();
		Head('Folder', CreatePathLinks($Folder));
		BeginList();
 
		$isEmpty = (count($files) <= 1);
 
		while (!empty($files)) {
			$name = array_shift($files);
			$type = (is_dir($Folder.'/'.$name)) ? 'folder' : (stripos($name, ".zip") ? 'archive' : 'file');
 
			echo '<li><a href="';
 
			if ($type == "file") {
				echo $Folder.'/'.$name;
			}
			else {
				echo getScriptPath().'?action='.(($type=="archive") ? SELECT_UNPACK_FOLDER : LIST_FILES);
				echo (($type=="folder") ? ('&folder='.ParsePath($Folder.'/'.$name)) : '');
				echo (($type=="archive")? ('&folder='.ParsePath($Folder).'&filename='.$Folder.'/'.$name) : '');
			}
			echo '">';
			echo Img($type).$name.'</a></li>';
		}
 
		if ($isEmpty) {
			echo 'Folder is empty.';
		}
 
		EndList();
		echo '<a class="button1" href="'.getScriptPath().'?action='.SHOW_SETTINGS;
		echo '&folder='.$Folder.'">'.Img('settings').'</a>';
		EndHtml();
	}
	break;
 
	// Select folder to unzip file
	case SELECT_UNPACK_FOLDER: {
		$folders = listFiles($Folder, true, false, false);
		$fn = array_pop(explode('/', $Filename));
 
		BeginHtml();
		Head('Unpack', '<a href="'.getScriptPath().'?action='.$Action.'&folder='.dirname($Filename).'&filename='.$Filename.'">'.$fn.'</a>');
		Head('To', CreatePathLinks($Folder));
		BeginList();
 
		while (!empty($folders)) {
			$name = array_shift($folders);
 
			echo '<li><a href="'.getScriptPath().'?action='.SELECT_UNPACK_FOLDER;
			echo '&folder='.ParsePath($Folder.'/'.$name).'&filename='.$Filename.'">';
			echo Img('folder').$name.'</a></li>';
		}
 
		EndList();
 
		echo '<a class="button1" href="'.getScriptPath().'?action='.UNPACK_FILE;
		echo '&folder='.$Folder.'&filename='.$Filename.'">'.Img('extract').'</a>';
 
		echo '<a class="button" href="'.getScriptPath().'?action='.SHOW_SETTINGS;
		echo '&folder='.$Folder.'">'.Img('settings').'</a>';
		EndHtml();
	}
	break;
 
	// Unzip file
	case UNPACK_FILE: {
		BeginHtml();
 
		// Let's unpack file
		$zipFile = new ZipArchive();
		$fn = array_pop(explode('/', $Filename));
 
		if ($zipFile->open($Filename) === TRUE) {
			$zipFile->extractTo($Folder);
			$zipFile->close();
 
			Head('File '.$fn.' successfully unpacked.');
		}
		else {
			Head('Error', 'File '.$fn.' cannot be unpacked!');
		}
 
		echo '-> <a href="'.getScriptPath().'">Go to Start</a><br/>';
		echo '-> <a href="'.getScriptPath().'?action='.LIST_FILES.'&folder='.$Folder.'">Go to Unpack Destination Folder</a><br/>';
		echo '-> <a href="'.getScriptPath().'?action='.SELECT_UNPACK_FOLDER.'&folder='.$Folder.'&filename='.$Filename.'">Unpack again the same file somewhere else</a>';
 
		EndHtml();
	}
	break;
 
	// Get image
	case GET_IMAGE: {
		if (!empty($Filename)) {
			showImage($Filename);
		}
 
		exit;
	}
	break;
 
	// Get CSS
	case GET_CSS: {
		returnStyle();
	}
	break;
 
	// Show settings
	case SHOW_SETTINGS: {
		BeginHtml();
		Head("Settings");
 
		echo '<form action="'.getScriptPath().'" method="get">';
		echo '<input type="checkbox" name="other" id="other" value="true"'.(($_SESSION["SHOW_OTHER_FILES"]) ? ' checked' : '').' />';
		echo ' <label for="other">Show Other Files</label>';
		echo '<input type="hidden" name="action" value="'.CHANGE_SETTING.'" />';
		echo '<br/>';
		echo '<input type="submit" value="Save"/> ';
		echo '<input type="button" onclick="parent.location=\''.getScriptPath().'?folder='.$Folder.'\';" value="Cancel" />';
		echo '</form>';
 
		EndHtml();
	}
	break;
 
	// Change setting
	case CHANGE_SETTING: {
		$_SESSION["SHOW_OTHER_FILES"] = ((isset($_GET["other"]) && $_GET["other"]) ? true : false);
 
		header('Location: '.getScriptPath());
	}
	break;
 
 }
 
?>
<?php ob_end_flush(); ?>