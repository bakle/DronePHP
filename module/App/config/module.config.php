<?php

return array(
    'router' => array(
        'routes' => array(
        	'App' => array(
        		'module' => 'App',
        		'controller' => 'App_Controller_Index',
        		'view' => 'index'
        	)
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'default'	=> __DIR__ . '/../view/layout/layout.phtml',
        ),
    ),
);