<?php
	
	require_once(TOOLKIT . '/class.administrationpage.php');
	
	Class ContentExtensionHealth_CheckDirectories extends AdministrationPage{
		
		public function __viewIndex() {		
			// fetch all entries with upload fields
			$extensionManager = new ExtensionManager($this->_Parent);
			if($extensionManager->fetchStatus('uniqueuploadfield') == EXTENSION_ENABLED) {
				$destinations = Symphony::Database()->fetch("SELECT destination COLLATE utf8_general_ci AS destination FROM tbl_fields_upload UNION ALL SELECT destination FROM tbl_fields_uniqueupload ORDER BY destination ASC");
			} else {
				$destinations = Symphony::Database()->fetch("SELECT destination FROM tbl_fields_upload ORDER BY destination ASC");
			}

			$this->setPageType('index');
			$this->setTitle(__('Directory Health Check'));


			$this->appendSubheading(__('Health Check'));

			if(is_dir(getcwd() . __('/manifest/cache')) == false || is_dir(getcwd() . __('/manifest/tmp')) == false) {
				$button = new XMLElement('input');
				$button->setAttribute('type','submit');
				$button->setAttribute('class','button');
				if(is_dir(getcwd() . __('/manifest/cache')) == false && is_dir(getcwd() . __('/manifest/tmp')) == false) {
					$button->setAttribute('name','action[create-tmp-cache]');
					$button->setAttribute('value',__('Create Cache/Tmp folders'));
				} elseif(is_dir(getcwd() . __('/manifest/cache')) == false && is_dir(getcwd() . __('/manifest/tmp')) != false) {
					$button->setAttribute('name','action[create-cache]');
					$button->setAttribute('value',__('Create Cache folder'));
				} elseif(is_dir(getcwd() . __('/manifest/cache')) != false && is_dir(getcwd() . __('/manifest/tmp')) == false) {
					$button->setAttribute('name','action[create-tmp]');
					$button->setAttribute('value',__('Create Tmp folder'));
				}
				$this->Form->appendChild($button);
			}

			$table = new XMLElement('table');

			$tableBody = array();
			$tableHead = array(
				array(__('Directory'), 'col'),
				array(__('Octal Permissions'), 'col'),
				array(__('Full Permissions'), 'col')
			);	
							
			function info($fileperms) {
				// Socket
				if (($fileperms & 0xC000) == 0xC000) $info = 's';
				// Symbolic Link
				elseif (($fileperms & 0xA000) == 0xA000) $info = 'l';
				// Regular
				elseif (($fileperms & 0x8000) == 0x8000) $info = '-';
				// Block special
				elseif (($fileperms & 0x6000) == 0x6000) $info = 'b';
				// Directory
				elseif (($fileperms & 0x4000) == 0x4000) $info = 'd';
				// Character special
				elseif (($fileperms & 0x2000) == 0x2000) $info = 'c';
				// FIFO pipe
				elseif (($fileperms & 0x1000) == 0x1000) $info = 'p';
				// Unknown
				else $info = 'u';
				
				// Owner
				$info .= (($fileperms & 0x0100) ? 'r' : '-');
				$info .= (($fileperms & 0x0080) ? 'w' : '-');
				$info .= (($fileperms & 0x0040) ?
				            (($fileperms & 0x0800) ? 's' : 'x' ) :
				            (($fileperms & 0x0800) ? 'S' : '-'));
				
				// Group
				$info .= (($fileperms & 0x0020) ? 'r' : '-');
				$info .= (($fileperms & 0x0010) ? 'w' : '-');
				$info .= (($fileperms & 0x0008) ?
				            (($fileperms & 0x0400) ? 's' : 'x' ) :
				            (($fileperms & 0x0400) ? 'S' : '-'));
				
				// World
				$info .= (($fileperms & 0x0004) ? 'r' : '-');
				$info .= (($fileperms & 0x0002) ? 'w' : '-');
				$info .= (($fileperms & 0x0001) ?
				            (($fileperms & 0x0200) ? 't' : 'x' ) :
				            (($fileperms & 0x0200) ? 'T' : '-'));
				
				return $info;
			}
			
			//manifest/cache

			$dir = getcwd() . __('/manifest/cache');
			if(is_dir($dir) == true) {
				$fileperms = fileperms($dir);
				$perms = substr(sprintf("%o", $fileperms), -4);
				$col_dir = Widget::TableData(General::sanitize(__('/manifest/cache')));
				$col_dir->appendChild(Widget::Input("item['/manifest/cache']",null, 'checkbox'));
				$col_perms = Widget::TableData(General::sanitize($perms));
				$col_info = Widget::TableData(General::sanitize(info($fileperms)));
				if($perms != '0777') {
					$tableBody[] = Widget::TableRow(
						array(
							$col_dir, 
							$col_perms,
							$col_info
						),
						'invalid'
					);
				} else {
					$tableBody[] = Widget::TableRow(
						array(
							$col_dir, 
							$col_perms,
							$col_info
						)
					);
				}
			} else {
				$col_dir = Widget::TableData(General::sanitize(__('/manifest/cache')));
				$col_dir->appendChild(Widget::Input("item['/manifest/cache']",null, 'checkbox'));
				$col_perms = Widget::TableData(General::sanitize(__('WARNING: This directory does not exist.')));
				$col_info = Widget::TableData(General::sanitize());
				$tableBody[] = Widget::TableRow(
					array(
						$col_dir, 
						$col_perms,
						$col_info
					),
					'invalid'
				);
			}
			
			//manifest/tmp
			$dir = getcwd() . __('/manifest/tmp');
			if(is_dir($dir) == true) {
				$fileperms = fileperms($dir);
				$perms = substr(sprintf("%o", $fileperms), -4);
				$col_dir = Widget::TableData(General::sanitize(__('/manifest/tmp')));
				$col_dir->appendChild(Widget::Input("item['/manifest/tmp']",null, 'checkbox'));
				$col_perms = Widget::TableData(General::sanitize($perms));
				$col_info = Widget::TableData(General::sanitize(info($fileperms)));
				if($perms != '0777') {
					$tableBody[] = Widget::TableRow(
						array(
							$col_dir, 
							$col_perms,
							$col_info
						),
						'invalid'
					);
				} else {
					$tableBody[] = Widget::TableRow(
						array(
							$col_dir, 
							$col_perms,
							$col_info
						)
					);
				}
			} else {
				$col_dir = Widget::TableData(General::sanitize(__('/manifest/tmp')));
				$col_dir->appendChild(Widget::Input("item['/manifest/tmp']",null, 'checkbox'));
				$col_perms = Widget::TableData(General::sanitize(__('WARNING: This directory does not exist.')));
				$col_info = Widget::TableData(General::sanitize());
				$tableBody[] = Widget::TableRow(
					array(
						$col_dir, 
						$col_perms,
						$col_info
					),
					'invalid'
				);
			}

			//array_unique didn't work, so run this function instead
			function remove_duplicates(array $array){
				$tmp_array = array();

				foreach($array as $key => $val) {
					if (!in_array($val, $tmp_array)) {
						$tmp_array[$key]  = $val;
					}
				}

				return $tmp_array;
			}

			$destinations = remove_duplicates($destinations);

			// Upload directories
			foreach($destinations as $destination) {
				$dir = getcwd() . $destination['destination'];
				$fileperms = fileperms($dir);
				$perms = substr(sprintf("%o", $fileperms), -4);
				$col_dir = Widget::TableData(General::sanitize($destination['destination']));
				$col_dir->appendChild(Widget::Input("item[{$destination['destination']}]",null, 'checkbox'));
				$col_perms = Widget::TableData(General::sanitize($perms));
				$col_info = Widget::TableData(General::sanitize(info($fileperms)));
				if($perms != '0777') {
					$tableBody[] = Widget::TableRow(
						array(
							$col_dir, 
							$col_perms,
							$col_info
						),
						'invalid'
					);
				} else {
					$tableBody[] = Widget::TableRow(
						array(
							$col_dir, 
							$col_perms,
							$col_info
						)
					);
				}
			}
			
			$table = Widget::Table(
				Widget::TableHead($tableHead), null, 
				Widget::TableBody($tableBody)
			);
			$table->setAttribute('class', 'selectable');
			
			$this->Form->appendChild($table);	
			
			$actions = new XMLElement('div');
			$actions->setAttribute('class', 'actions');
			
			$options = array(
				array(null, false, __('With Selected...')),
				array('0777', false, __('Update to 0777')),
				array('0755', false, __('Update to 0755')),
				array('0750', false, __('Update to 0750')),
				array('0644', false, __('Update to 0644')),
				array('0600', false, __('Update to 0600'))									
			);

			$actions->appendChild(Widget::Select('with-selected', $options));
			$actions->appendChild(Widget::Input('action[apply]', 'Apply', 'submit'));
			
			$this->Form->appendChild($actions);
		}
		
		public function __actionIndex() {
			$checked = ((isset($_POST['item']) && is_array($_POST['item'])) ? array_keys($_POST['item']) : null);
			
			if (is_array($checked) and !empty($checked)) {
				switch ($_POST['with-selected']) {
					case '0777':
						chmod(getcwd() . $checked[0], 0777);
						break;
					case '0755':
						chmod(getcwd() . $checked[0], 0755);
						break;
					case '0750':
						chmod(getcwd() . $checked[0], 0750);
						break;
					case '0644':
						chmod(getcwd() . $checked[0], 0644);
						break;
					case '0600':
						chmod(getcwd() . $checked[0], 0600);
						break;
				}
			}

			if(isset($_POST['action']) == 'create-tmp-cache') {
				mkdir(getcwd() . '/manifest/tmp', 0777);
				mkdir(getcwd() . '/manifest/cache', 0777);
			}elseif(isset($_POST['action']) == 'create-tmp') {
				mkdir(getcwd() . '/manifest/tmp', 0777);
			}elseif(isset($_POST['action']) == 'create-cache') {
				mkdir(getcwd() . '/manifest/cache', 0777);
			}
		}
	}