<?php

return array(
    'router' => array(
        'routes' => array(
        	'App' => array(
        		'module' => 'App',
        		'controller' => 'Index',
        		'view' => 'index'
        	)
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'default'	=> dirname(__FILE__) . '/../view/layout/layout.phtml',
        ),
    ),
);