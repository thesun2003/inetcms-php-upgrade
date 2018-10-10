<?php
if (!defined('IN_WPRO')) exit;

				
				// check vars
				if (!isset($params['folderID']) || !isset($params['folderPath']) || empty($params['file'])) {
					exit;
				}
				ini_set('display_errors', false);
				$params['folderPath'] = base64_decode($params['folderPath']);
				// display a dynamic thumbnail...
				$x = null;
				if ($arr = $this->getFolder($params['folderID'], $params['folderPath'], $x)) {
					$directory = $arr['directory'];
					$file = $params['file'];
					$fs = new wproFilesystem();
					if ($fs->fileNameOK($file)) {
						if (is_file($directory.$file)) {
							require_once(WPRO_DIR.'core/libs/wproImageEditor.class.php');
							$imageEditor = new wproImageEditor();
							if (!$imageEditor->proportionalResize($directory.$file, '', 94, 94)) {
								$extension = strrchr(strtolower($file),'.');
								$GDExtensions = array('.jpg','.jpeg','.gif','.png'); // filetypes that can be resized with GD
								if (in_array($extension, $GDExtensions)) {
									$icon = str_replace('.', '', $extension);
									$thumb_src = $EDITOR->themeFolderURL.$EDITOR->theme."/wysiwygpro/icons/{$icon}32.gif";
									header('Location: '.$thumb_src);
								}
							}
						}
					}
				}
				exit;
				
?>