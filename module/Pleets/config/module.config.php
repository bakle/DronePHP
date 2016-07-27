<?php

return array(
    'router' => array(
        'routes' => array(
        	'Pleets' => array(
        		'module' => 'Pleets',
        		'controller' => 'Pleets_Controller_App',
        		'view' => 'index'
        	)
        ),
    ),
   'view_manager' => array(
    	'template_map' => array(
        	'default'	=> dirname(__FILE__) . '/../view/layout/layout.phtml',
        	'error'	=> dirname(__FILE__) . '/../view/layout/error.phtml',
     	),
   ),
);