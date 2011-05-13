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
		
	}

?>