<?php

	Class extension_health_check extends Extension{
	
		public function about(){
			return array(
				'name' => 'Health Check',
				'version' => '1.2',
				'release-date' => '2011-05-31',
				'author' => array(
				 		'name' => 'Phill Gray',
						'email' => 'phill@randb.com.au'
					),
				'description' => 'Checks if your writable directories are in fact writable.'
		 		);
		}
		
		public function fetchNavigation() {
			return array(
				array(
					'location' => 'Blueprints',
					'name'	=> 'Health Check',
					'link'	=> '/directories/',
				),
			);
		}
		public function getSubscribedDelegates() {
			return array(
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'initaliseAdminPageHead'
				),
		        array(
		            'page'      => '/backend/',
		            'delegate'  => 'DashboardPanelRender',
		            'callback'  => 'render_panel'
		        ),
		        array(
		            'page'      => '/backend/',
		            'delegate'  => 'DashboardPanelOptions',
		            'callback'  => 'dashboard_panel_options'
		        ),
		        array(
		            'page'      => '/backend/',
		            'delegate'  => 'DashboardPanelTypes',
		            'callback'  => 'dashboard_panel_types'
		        )
			);
		}
		
		public function initaliseAdminPageHead($context) {
			$callback = Symphony::Engine()->getPageCallback();

			// Append assets
			if($callback['driver'] == 'directories' || $callback['classname'] == 'contentExtensionDashboardIndex') {
				Symphony::Engine()->Page->addStylesheetToHead(URL . '/extensions/health_check/assets/healthcheck.publish.css', 'screen');
			}
		}
		
		public function dashboard_panel_types($context) {
		    $context['types']['health_check_panel'] = __('Health Check Panel');
		}
		
		public function dashboard_panel_options($context) {
		    // make sure it's your own panel type, as this delegate fires for all panel types!
		    if ($context['type'] != 'health_check_panel') return;

		    $config = $context['existing_config'];
		}
		
		public function render_panel($context) {
		    if ($context['type'] != 'health_check_panel') return;

			$extensionManager = new ExtensionManager($this->_Parent);
			if($extensionManager->fetchStatus('uniqueuploadfield') == EXTENSION_ENABLED) {
				$destinations = Symphony::Database()->fetch("SELECT destination COLLATE utf8_general_ci AS destination FROM tbl_fields_upload UNION ALL SELECT destination FROM tbl_fields_uniqueupload ORDER BY destination ASC");
			} else {
				$destinations = Symphony::Database()->fetch("SELECT destination FROM tbl_fields_upload ORDER BY destination ASC");
			}

			function remove_duplicates(array $array){
				$tmp_array = array();
				foreach($array as $key => $val) {
					if (!in_array($val, $tmp_array)) $tmp_array[$key]  = $val;
				}

				return $tmp_array;
			}

			$div = new XMLElement('div');
			$table = new XMLElement('table');

			$directory = array('/manifest/cache','/manifest/tmp','/manifest/config.php','/workspace/data-sources/','/workspace/events/');
			if($extensionManager->fetchStatus('xmlimporter') == EXTENSION_ENABLED) $directory[] =  '/workspace/xml-importers';
			foreach(remove_duplicates($destinations) as $destination) $directory[] = $destination['destination'];

		   	foreach($directory as $dir) {
				$d = getcwd() . __($dir);
				
				if(is_dir($d) == true || $dir == '/manifest/config.php') {
					$permissions = substr(sprintf("%o", fileperms($d)), -4);
					$td_directory = Widget::TableData(General::sanitize(__($dir)));
					$td_permissions = Widget::TableData(General::sanitize($permissions));
					if($permissions != '0777') {
						$table->appendChild(Widget::TableRow(array($td_directory, $td_permissions),'invalid'));
					} else {
						$table->appendChild(Widget::TableRow(array($td_directory, $td_permissions)));
					}
				} else {
					$td_directory = Widget::TableData(General::sanitize(__($dir)));
					$td_permissions = Widget::TableData(General::sanitize(__('WARNING: This directory does not exist.')));
					$table->appendChild(Widget::TableRow(array($td_directory, $td_permissions),'invalid'));
				}
			}
		   
			$div->appendChild($table);

			$context['panel']->appendChild($div);
		}
	}
?>