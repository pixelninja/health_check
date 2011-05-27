<?php

	Class extension_health_check extends Extension{
	
		public function about(){
			return array(
				'name' => 'Health Check',
				'version' => '1.0',
				'release-date' => '2011-05-11',
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
			if($callback['driver'] == 'directories') {
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
		
			require_once(EXTENSIONS . '/health_check/content/content.directories.php');
			
			$div = new XMLElement('div');
			$table = new XMLElement('table');
			
		   	//manifest/cache
		   	$dir = array('/manifest/cache','/manifest/tmp');
		   	
		   	foreach($dir as $d) {
				$directory = getcwd() . __($d);
				if(is_dir($directory) == true) {
					$fileperms = fileperms($directory);
					$perms = substr(sprintf("%o", $fileperms), -4);
					$td_directory = Widget::TableData(General::sanitize(__($d)));
					$td_perms = Widget::TableData(General::sanitize($perms));
					if($perms != '0777') {
						$table->appendChild(Widget::TableRow(array($td_directory, $td_perms),'invalid'));
					} else {
						$table->appendChild(Widget::TableRow(array($td_directory, $td_perms)));
					}
				} else {
					$directory = Widget::TableData(General::sanitize(__('/manifest/cache')));
					$perms = Widget::TableData(General::sanitize(__('WARNING: This directory does not exist.')));
					$table->appendChild(Widget::TableRow(array($directory, $perms),'invalid'));
				}
			}
		   
			
			$div->appendChild($table);
		    
			$context['panel']->appendChild($div);
		}
	}
?>