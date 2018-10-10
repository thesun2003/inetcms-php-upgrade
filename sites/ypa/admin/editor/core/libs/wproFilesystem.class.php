<?php
if (!defined('IN_WPRO')) exit;
require_once(dirname(__FILE__).'/wproCore.class.php');
class wproFilesystem extends wproCore {
	
	// folders and files containing these strings are considered invalid.
	var $badFolderChars = array('./','.\\','?','&','%','#','~',':','^','<','>','[',']','(',')','*','+','@','"',"'",'`',',','|',"\r","\n","\t");
	var $badFileChars =     array('/','\\','?','&','%','#','~',':','^','<','>','[',']','(',')','*','+','@','"',"'",'`',',','|',"\r","\n","\t");
	
	// if the below are set then files and folders containing anything other than these characters are considered invalid.
	var $acceptedFolderChars = "A-Za-z0-9_\-.\/\\ ";
	var $acceptedFileChars = "A-Za-z0-9_\-. ";
		
	function wproFilesystem () {

	}
	
	//function zafFilesystem () {
		//$this->fileDefinitionsInclude = dirname(__FILE__).'/zafFilesystem/file_definitions.inc.php';
	//}
	// includes a PHP file..
	
	function includeFileOnce($file, $base='', $extra='') {
		if ($this->fileNameOk($file)) {
			$file = $this->fileName($base.$file.$extra);
			if (is_file($file)) {
				if ( include_once($file) ) { 
					return true;
				}
			}
		}
		return false;
	}
	
	function includeFile($file, $base='', $extra='') {
		if ($this->fileNameOk($file)) {
			$file = $this->fileName($base.$file.$extra);
			if (is_file($file)) {
				if ( include($file) ) { 
					return true;
				}
			}
		}
		return false;
	}
	
	function makeFileNameOK($file) {
		// names cannot have illegal characters
		$file = trim(str_replace($this->badFileChars, '', basename($file)));
		$file = preg_replace("/[^".$this->acceptedFileChars."]/i", '', $file);
		// names cannot have multiple dots
		$file = preg_replace("/(\.)\.+/smi", "$1", $file);
		// names cannot have multiple spaces
		$file = preg_replace("/(\s)\s+/smi", "$1", $file);
		// names cannot start with a . or -
		$file = preg_replace("/^(\.|-)+/smi", '', $file);
		
		if (empty($file)) {
			return false;
		}
		
		return $file;
	}
	
	/*function makeFolderNameOK($file) {
		// names cannot start with a .
		$file = preg_replace("/^\.+/smi", '', $file);
		// names cannot have multiple spaces
		$file = preg_replace("/(\s)\s+/smi", "$1", $file);
		
		return str_replace($this->badFolderChars, '', $file);
	}*/
	
	function folderNameOK($file) {
		return $this->dirNameOK($file);
	}
	
	/* returns true if file name is OK */
	function fileNameOK($name = '') {
		$values = $this->badFileChars;
		$num = sizeof($values);
		$match = false;
		if (!is_string($name)) {
			return false;
		}
		// names cannot start with a .
		if (substr($name, 0, 1) == '.') {
			$match = true;
		}
		// names cannot start with a -
		if (substr($name, 0, 1) == '-') {
			$match = true;
		}
		// names cannot have multiple spaces
		if (preg_match("/\s\s/smi", $name)) {
			$match = true;
		}
		// names cannot have multiple dots
		if (preg_match("/\.\./smi", $name)) {
			$match = true;
		}
		// name cannot be empty
		if (empty($name)) {
			$match = true;
		}
		// name cannot have illegal characters
		for ($i=0; $i<$num; $i++) { 
			if (stristr($name,$values[$i])) {
				$match = true;
				break;
			}
		}
		if (preg_match("/[^".$this->acceptedFileChars."]/i", $name)) $match = true;
		if ($match) {
			return false;
		} else {
			return true;
		}
	}

	/* returns true if dir name is OK */
	function dirNameOK($name = '') {
		$values = $this->badFolderChars;
		$num = sizeof($values);
		$match = false;
		if (!is_string($name)) {
			return false;
		}
		// names cannot start with a .
		if (substr($name, 0, 1) == '.') {
			$match = true;
		}
		// names cannot start with a -
		if (substr($name, 0, 1) == '-') {
			$match = true;
		}
		// names cannot have multiple spaces
		if (preg_match("/\s\s/smi", $name)) {
			$match = true;
		}
		// names cannot have multiple dots
		if (preg_match("/\.\./smi", $name)) {
			$match = true;
		}
		// name cannot be empty
		if (empty($name)) {
			$match = true;
		}
		// name cannot have illegal characters
		for ($i=0; $i<$num; $i++) { 
			if (stristr($name,$values[$i])) {
				$match = true;
				break;
			}
		}
		if (preg_match("/[^".$this->acceptedFolderChars."]/i", $name)) $match = true;
		if ($match) {
			return false;
		} else {
			return true;
		}
	}

	/* checks if an extension is OK */
	function extensionOK($extension, $accept_array) {
		
		if (!is_array($accept_array)) {
			$accept_array = explode(',', str_replace(' ', '', strtolower($accept_array)));
		} else {
			for ($i=0;$i<count($accept_array);$i++) {
				$accept_array[$i] = trim(strtolower($accept_array[$i]));
			}
		}
		if (empty($accept_array)) {
			return false;
		}
		if (in_array(strtolower($extension), $accept_array)) {
			return true;
		} else {
			return false;
		}
	}
	
	
	// fixes slash inconsistencies
	function fileName($file) {
		return str_replace(array('/', '\\', '//', '\\\\'), '/', $file);
	}
	
	// checks if a file exists and if it does it returns a filename with a numeric suffix that doesn't exist
	function resolveDuplicate($file,$dir,$prefix='_copy_',$suffix='') {
		$file = basename($file);
		$i=1;$probeer=$file;
		while(file_exists($dir.$probeer)) {
			$punt=strrpos($file,".");
			if ($punt==false) {
				$test = $file;
			} else {
				$test = substr($file, 0, $punt);
			}
			if (!preg_match("/".quotemeta($prefix)."[0-9]+".quotemeta($suffix)."$/i",$test)) {
				$probeer=$test.$prefix.$i.$suffix;
 			} else {
				$probeer=preg_replace("/".quotemeta($prefix)."[0-9]+".quotemeta($suffix)."$/i",$prefix.$i.$suffix,$test);
			}
			if ($punt!=false) $probeer.=substr($file,($punt),strlen($file)-$punt);
			$i++;
		}
		return $probeer;
	}
	
	// chmods a file
	function chmod ($filename, $mode) {
		$mode = intval($mode, 8);
		if ($mode) {
			if ($this->dirNameOK($filename)) {
				//die("return @chmod(stripslashes('".addslashes($filename)."'), 0".decoct($mode).");");
				return eval ("return @chmod(stripslashes('".addslashes($filename)."'), 0".decoct($mode).");");
			}
		}
		//return @chmod($filename, octdec($mode));
		return false;
	}
	
	// deletes a file or directory
	function delete($file) {
		if ((file_exists ($file)) && (!is_file($file))) { 
			if ($this->emptyDir($file)) {
				return true;
			} else {
				return false;
			}
		} elseif ((file_exists ($file)) && (is_file($file))) {
			if (@unlink($file)) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	
	// deletes all files in a directory
	function emptyDir ($dir) {
		if(@ ! $opendir = @opendir($dir)) {
			return false;
		}
		while(false !== ($readdir = @readdir($opendir))) {
			if($readdir !== '..' && $readdir !== '.') {
				$readdir = trim($readdir);
				if(is_file($dir.'/'.$readdir)) {
					if(@ ! unlink($dir.'/'.$readdir)) {
						return false;
					}
				} elseif(is_dir($dir.'/'.$readdir)) {
					// Calls itself to clear subdirectories
					if(! $this->emptyDir($dir.'/'.$readdir)) {
						return false;
					}
				}
			}
		}
		@closedir($opendir);
		if(@ ! rmdir($dir)) {
			return false;
		}
		return true;
	
	}
	
	// creates a directory
	function makeDir($dir, $chmod=0) {
		if (!file_exists ($dir)) {
			if (@mkdir ($dir)) {
				if (!empty($chmod)) {
					//@chmod($dir, octdec($chmod));
					$this->chmod($dir, $chmod);
					// make group the same as folder if possible
					$parent = $dir;
					if (substr($parent, strlen($parent)-1)=='/' || substr($parent, strlen($parent)-1)=='\\') {
						$parent = substr($parent, 0, strlen($parent)-1);
					}
					$parent = dirname($parent);
					//echo $parent;
					if (@filegroup($parent) != @filegroup($dir)) {
						if (@filegroup($parent)) {
							@chgrp ( $dir, @filegroup($parent) );
						}
					}
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	// copy a file or directory
	// just use rename to move a file/folder
	function copy ($oldname, $newname) {
		if (is_file($oldname)){
			return @copy($oldname, $newname);
		} else if (is_dir($oldname)){
			return $this->dirCopy($oldname, $newname);
		} else {
			return false;
		}
	}
	function dirCopy($oldname, $newname) {
		if (!is_dir($newname)) {
			@mkdir($newname);
			//if (!empty($chmod)) {
				@chmod($newname, fileperms($oldname));
				// make group the same as folder if possible
				if (@filegroup($oldname) != @filegroup($newname)) {
					if (@filegroup($oldname)) {
						@chgrp ( $newname, @filegroup($oldname) );
					}
				}
			//}
		}
		$dir = @opendir($oldname);
		//while($file = readdir($dir)){
		while (false !== ($file = @readdir($dir))) {
			if ($file == "." || $file == "..") continue;
			$this->copy($oldname.'/'.$file, $newname.'/'.$file);
		}
		@closedir($dir);
		return true;
	}

	function rename ($oldname, $newname) {
		return @rename($oldname, $newname);
	}
	
	// writes data to a file
	function writeFile($file, $data) {
		if (empty ($file)) { return false; }
		$retVal = false;
		$fp = @fopen ($file, 'w');
		@flock ($fp, 2);
		$retVal = @fwrite ($fp, $data);
		@flock ($fp, 3);
		@fclose ($fp);
		return $retVal;
	}
	
	// appends data to a file
	function appendContents($filename, $data) {
		if (($existing = implode("", @file($filename))) === false) {
			return false;
		}
		if (($h = @fopen($filename, 'w')) === false) {
			return false;
		}
		if (($bytes = @fwrite($h, $existing.$data)) === false) {
			return false;
		}
		@fclose($h);
		return $bytes;
	}
	
	function fileModTime($file) {
		if (! is_file ($file)) {
			return false;
		}
		return @filemtime ($file);
	}
	
	function fileExists($file) {
		if (is_file ($file)) {
			return true;
		} else {
			return false;
		}
	}
	
	function isFile ($file) {
		if (is_file($file)) {
			return true;
		} else {
			return false;
		}
	}
	
	function getContents($file) {
		//return implode("", @file($file)) === false;
		$code = @implode('', @file ($file));
		if (get_magic_quotes_runtime ()) {
			$code = stripslashes($code);
		}
		return $code;
	}
	
	function fileSize($file) {
		// First check if the file exists.
		if(!is_file($file)) return '';
		
		// Get the file size in bytes.
		$size = @filesize($file);
		
		return $this->convertByteSize($size);
	}
	
	function dirSize($dir) {
		$dirSize=0;
		if ($handle=@opendir($dir)) {
			while (false !== ($file = @readdir($handle))) {
				if (($file!=".")&&($file!="..")) {
					if (is_dir($dir."/".$file)) {
						$tmp=$this->dirSize($dir."/".$file);
						if ($tmp!==false) $dirSize+=$tmp;
					} else {
						$dirSize+=filesize($dir."/".$file);
					}
				}
			}
			@closedir($handle);
		} else {
			return false;
		}
		
		return $dirSize;
	}

	// converts bytes 
	function convertByteSize($size) {
		// Setup some common file size measurements.
		$kb = 1024;         // Kilobyte
		$mb = 1024 * $kb;   // Megabyte
		$gb = 1024 * $mb;   // Gigabyte
		$tb = 1024 * $gb;   // Terabyte
		/* If it's less than a kb we just return the size, otherwise we keep going until
		the size is in the appropriate measurement range. */
		if($size < $kb) {
			return $size." B";
		} else if($size < $mb) {
			return round($size/$kb,2)." KB";
		} else if($size < $gb) {
			return round($size/$mb,2)." MB";
		} else if($size < $tb) {
			return round($size/$gb,2)." GB";
		} else {
			return round($size/$tb,2)." TB";
		}
	}
	
	// when passed a string in this format [0-9]+ [A-Za-z]+ it returns it as bytes
	function returnBytes($val) {
	   if (!empty($val)) {
		   $val = trim($val);
		   $last = strtolower(preg_replace("/^[0-9]+\s*([A-Za-z]+)$/smi", "$1", $val));//strtolower($val{strlen($val)-1});
		   $val = preg_replace("/[^0-9]/smi", "", $val);
		   switch($last) {
			   // The 'G' modifier is available since PHP 5.1.0
			   case 't':
			   case 'tb':
					$val *= 1024;
			   case 'g':
			   case 'gb':
				   $val *= 1024;
			   case 'm':
			   case 'mb':
				   $val *= 1024;
			   case 'k':
			   case 'kb':
				   $val *= 1024;
		   }
		}
	   return $val;
	}

	function getFileInfo($extension) {
		// to add more filetypes save an icon image to the images folder and add a description to your language file, then describe how the function should handle the file below:
		require(WPRO_DIR.'conf/fileDefinitions.inc.php');
		return $info;
	}
	
	// function for checking filter matches (filters are used to filter out directories and files that developers don't want displayed)
	function filterMatch($filename, $filters) {
		foreach($filters as $filter) {
			if (@preg_match($filter, $filename)) {
				return true;
			}
		}
		return false;
	}
	
	function getFoldersInDir ($directory, $sortby='name', $sortdir='asc', $filters=array() ) {
		$folderlist = array();
		$bhandle = @opendir($directory);
		if (substr($directory, strlen($directory)-1) != '/') {
			$directory.='/';
		}
		$i = 0;
		while (false !== ($folder = @readdir($bhandle))) {
			if (file_exists($directory.$folder) 
			&& (!is_file($directory.$folder)) 
			&& ($folder != ".") 
			&& ($folder != "..") 
			&& ($folder != "_vti_cnf") 
			&& ($folder != "aspnet_client") 
			&& ($folder != "_notes") 
			&& (!strstr($folder, '_WPROTEMP_'))) {
				if (!$this->folderNameOK($folder)) continue;
				if (!empty($filters)) {
					if ($this->filterMatch($directory.$folder, $filters)) continue;
				}
				$folderlist[$i]['name'] = $folder;
				$folderlist[$i]['modified'] = $this->fileModTime($directory.$folder);
				$i ++;
			}
		}
		@closedir($bhandle);
		
		// do sorting...
		// (other types of sorting may be available in future versions if your wondering why the sortby variable is here.)
		if ($sortby != 'name'&&$sortby!='modified') {
			$sortby='name';
		}
		if (strtolower($sortdir) == 'asc') {
			$sortdir = SORT_ASC;
		} else {
			$sortdir = SORT_DESC;
		}
		$folderlist = $this->arrayCSort($folderlist, $sortby, $sortdir);
		
		return $folderlist;
	}
	
	// returns files in a directory
	function getFilesInDir ($directory, $sortby='name', $sortdir='asc', $file_types=array(), $filters=array(), $getDimensions=false) {
		$filelist = array();
		$handle = @opendir($directory);
		if (substr($directory, strlen($directory)-1) != '/') {
			$directory.='/';
		}
		$i=0;
		while (false !== ($file = @readdir($handle))) {
			$extension = strrchr(strtolower($file), '.');
			if (is_file($directory.$file) && ($file != ".") && ($file != "..")) {
				if (strstr($file, '_WPROTEMP_')) {
					// cleanup temp files over 48 hours old.
					if (filemtime($directory.$file) < time()-172800) {
						unlink($directory.$file);
					}
					continue;
				}				
				if (!empty($file_types)) {
					if (!$this->extensionOK($extension, $file_types)) {
						continue;
					}
				}
				if (!empty($filters)) {
					if ($this->filterMatch($file, $filters)) continue;
				}
				if (!$this->fileNameOK($file)) continue;
				$file_info = $this->getFileInfo($extension);
				$filelist[$i]['name'] = $file;
				$filelist[$i]['type'] = $file_info['description'];
				$filelist[$i]['modified'] = $this->fileModTime($directory.$file);
				$filelist[$i]['size'] = $this->fileSize($directory.$file);
				$filelist[$i]['info'] = $file_info;
				if ($getDimensions) {
					if (@list ($width, $height) = @getimagesize($directory.$file)) {
						$filelist[$i]['dimensions']['width'] = $width;
						$filelist[$i]['dimensions']['height'] = $height;
						$filelist[$i]['dimensions']['text'] = $width.' x '.$height;
					}
				}
				
				$i ++;
			}
		}
		@closedir($handle);
		// do sorting...
		if ($sortby != 'name' && $sortby != 'type' && $sortby != 'modified' && $sortby != 'size') {
			$sortby='name';
		}
		if (strtolower($sortdir) == 'asc') {
			$sortflag = SORT_ASC;
		} else {
			$sortflag = SORT_DESC;
		}
		
		$filelist = $this->arrayCSort($filelist, $sortby, $sortflag);
		
		return($filelist);
	}
	
	
	/* uploads multiple files (if there are files uploaded)
	if stopOnError returns the error and stops, else returns an array of errors	
	*/
	function uploadFiles($field, $folder, $extensions, $filters=array(), $sizeLimit=1024, $overwrite=false, $chkimgwidth=true, $maxwidth=500, $maxheight=500, $chmod=0, $changeGroup=true, $stopOnError=false) {
		
		$errors = array();
		$errors['fatal'] = array(); // an array of files that failed to upload
		$errors['resized'] = array(); // images resized to maximum allowed size
		$errors['renamed'] = array(); // an array of files which had to be slightly re-named
		$errors['overwrite'] = array(); // an array of files where a file with that name already exists
		$errors['succeeded'] = array(); // array of files successfully uploaded, if files were renamed this has the renamed name not the original.
		
		
		$GDExtensions = array('.jpg','.jpeg','.gif','.png'); // filetypes that can be resized with GD
		
		if (substr($folder, strlen($folder)-1) != '/') {
			$folder.='/';
		}
		
		require_once(WPRO_DIR.'core/libs/wproImageEditor.class.php');
		$imageEditor = new wproImageEditor();
		
		if (isset($_FILES[$field])) {
			$num = count($_FILES[$field]['tmp_name']);
			for ($i=0; $i<=$num - 1; $i++) {
				if (is_uploaded_file($_FILES[$field]['tmp_name'][$i])) {
				
					if (empty($_FILES[$field]['name'][$i])) continue;
					
					$extension = strrchr(strtolower($_FILES[$field]['name'][$i]),'.');
					
					// check filetype against accepted files
					if (!$this->extensionOK($extension, $extensions)) {
						
						// bad extension...
						$errors['fatal'][$_FILES[$field]['name'][$i]] = 'badExtension';
						@unlink($_FILES[$field]['tmp_name'][$i]);
						if ($stopOnError) return $errors;
						continue;
						
					} else if ($_FILES[$field]['size'][$i] >= $sizeLimit) {
						
						// bad size
						$errors['fatal'][$_FILES[$field]['name'][$i]] = 'badSize';
						@unlink($_FILES[$field]['tmp_name'][$i]);
						if ($stopOnError) return $errors;
						continue;
					
					} else if ($_FILES[$field]['size'][$i] == 0) {
						
						// bad size
						$errors['fatal'][$_FILES[$field]['name'][$i]] = 'badSize';
						@unlink($_FILES[$field]['tmp_name'][$i]);
						if ($stopOnError) return $errors;
						continue;						
						
					} else {
						
						// fix bad file names
						$name = $this->makeFileNameOK($_FILES[$field]['name'][$i]);
						$goodName = $name;
						
						if (!$name) {
							continue;
						}
						
						// check filters
						if ($this->filterMatch($name, $filters)){
							$errors['fatal'][$_FILES[$field]['name'][$i]] = 'reserved';
							@unlink($_FILES[$field]['tmp_name'][$i]);
							if ($stopOnError) return $errors;
							continue;
						}
						
						// was file renamed?
						if ($name != $_FILES[$field]['name'][$i]) $errors['rename'][$_FILES[$field]['name'][$i]] = $name;
						
						// does this file already exist?
						if (file_exists($folder.$name) && !$overwrite) {
							// file with this name already exists
							$tempName = $this->resolveDuplicate(uniqid('_WPROTEMP_').$extension, $folder);
							$errors['overwrite'][$_FILES[$field]['name'][$i]] = $tempName;
							$name = $tempName;
							//@move_uploaded_file($_FILES[$field]['tmp_name'][$i], $folder.$name.'.WPTEMP');
							//@unlink($_FILES[$field]['tmp_name'][$i]);
							if ($stopOnError) return $errors;
						}
						
						// try to move file to final destination...
						if (!@move_uploaded_file($_FILES[$field]['tmp_name'][$i], $folder.$name)) {
							// failed to move file
							$errors['fatal'][$_FILES[$field]['name'][$i]] = 'unknown';
							@unlink($_FILES[$field]['tmp_name'][$i]);
							if ($stopOnError) return $errors;
							
						} else {
							// if image check size...
							if ($chkimgwidth) {
								if (in_array($extension, $GDExtensions)) {
									list ($width, $height) = @getimagesize($folder.$name);
									if ($width > $maxwidth || $height > $maxheight) {
										// image too large
										// if GD library is installed re-size image to maximum acceptable size...
										if ($resizedTo = @$imageEditor->proportionalResize ($folder.$name, $folder.$name, $maxwidth, $maxheight)) {
											$errors['resized'][$_FILES[$field]['name'][$i]] = $resizedTo;
											$name = basename($resizedTo[2]);
											if (isset($errors['overwrite'][$_FILES[$field]['name'][$i]])) {
												if ($errors['overwrite'][$_FILES[$field]['name'][$i]] != $name) {
													unset($errors['overwrite'][$_FILES[$field]['name'][$i]]);
													if (file_exists($folder.$name) && !$overwrite) {
														$errors['overwrite'][$goodName.'.png'] = basename($resizedTo[2]);
													} else {
														$this->rename($folder.basename($resizedTo[2]), $folder.$goodName.'.png');
													}
												}
											}
										} else {
											$errors['fatal'][$_FILES[$field]['name'][$i]] = 'badDimensions';
											@unlink($folder.$name);
											if ($stopOnError) return $errors;
											continue;
										}
									}
								}
							}
							
							array_push($errors['succeeded'], $name);
							if (!empty($chmod)) {
								$this->chmod($folder.$name, $chmod);
							}
							if ($changeGroup) {
								// make group the same as folder if possible
								if (@filegroup($folder) != @filegroup($folder.$name)) {
									if (@filegroup($folder)) {
										@chgrp ( $folder.$name, @filegroup($folder) );
									}
								}
							}
						}					
						
					}
					@unlink($_FILES[$field]['tmp_name'][$i]);
				} else {
					if (!empty($_FILES[$field]['name'][$i])) {
						$errors['fatal'][$_FILES[$field]['name'][$i]] = $_FILES[$field]['error'][$i];
					}
				}
			}		
		}
		return $errors;		
	}
}

?>