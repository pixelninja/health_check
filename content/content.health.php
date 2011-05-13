<?php
	
	require_once(TOOLKIT . '/class.administrationpage.php');
	
	Class ContentExtensionHealth_CheckHealth extends AdministrationPage{
		
		public function view() {		
			// fetch all entries with upload fields
			$destinations = Symphony::Database()->fetch("SELECT destination COLLATE utf8_general_ci AS destination FROM tbl_fields_uniqueupload UNION ALL SELECT destination FROM tbl_fields_upload ORDER BY destination ASC");
			
			$this->setPageType('table');
			$this->setTitle(__('Directory Health Check'));

			$this->appendSubheading('Health Check');

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
			$dir = getcwd() . '/manifest/cache';
			$fileperms = fileperms($dir);
			$perms = substr(sprintf("%o", $fileperms), -4);
			$col_dir = Widget::TableData(General::sanitize('/manifest/cache'));
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
						col_info
					)
				);
			}
			
			//manifest/tmp
			$dir = getcwd() . '/manifest/tmp';
			$fileperms = fileperms($dir);
			$perms = substr(sprintf("%o", $fileperms), -4);
			$col_dir = Widget::TableData(General::sanitize('/manifest/tmp'));
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

			// Upload directories
			foreach($destinations as $destination) {
				$dir = getcwd() . $destination['destination'];
				$fileperms = fileperms($dir);
				$perms = substr(sprintf("%o", $fileperms), -4);
				$col_dir = Widget::TableData(General::sanitize($destination['destination']));
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
				array(null, false, 'With Selected...'),
				array('change', false, 'Update permissions')									
			);

			$actions->appendChild(Widget::Select('with-selected', $options));
			$actions->appendChild(Widget::Input('action[apply]', 'Apply', 'submit'));
			
			$this->Form->appendChild($actions);
		}
	}