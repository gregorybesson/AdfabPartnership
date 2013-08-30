<?php

return array(
    'doctrine' => array(
        'driver' => array(
            'adfabpartnership_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/AdfabPartnership/Entity'
            ),

            'orm_default' => array(
                'drivers' => array(
                    'AdfabPartnership\Entity'  => 'adfabpartnership_entity'
                )
            )
        )
    ),

    'translator' => array(
        'locale' => 'fr_FR',
        'translation_file_patterns' => array(
            array(
                'type'         => 'phpArray',
                'base_dir'     => __DIR__ . '/../language',
                'pattern'      => '%s.php',
                'text_domain'  => 'adfabpartnership'
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view/admin',
        	__DIR__ . '/../view/frontend',
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'adfabpartnership_admin' => 'AdfabPartnership\Controller\AdminController',
            'adfabpartnership'      => 'AdfabPartnership\Controller\IndexController',
        ),
    ),

    'router' => array(
        'routes' => array(
        	'frontend' => array(
       			'child_routes' => array(        		
		            'partnership' => array(
		                'type' => 'Zend\Mvc\Router\Http\Segment',
		                'options' => array(
		                    'route'    => 'partnership[/:id]',
		                    'defaults' => array(
		                        'controller' => 'adfabpartnership',
		                        'action'     => 'index',
		                    ),
		                ),
		                'may_terminate' => true,
		                'child_routes' =>array(
		                    'share' => array(
		                        'type' => 'Literal',
		                        'options' => array(
		                            'route' => '/share',
		                            'defaults' => array(
		                                'controller' => 'adfabpartnership',
		                                'action'     => 'share'
		                            ),
		                        ),
		                    ),
		                    'ajax_newsletter' => array(
		                        'type' => 'Literal',
		                        'options' => array(
		                            'route' => '/ajax-newsletter',
		                            'defaults' => array(
		                                'controller' => 'adfabpartnership',
		                                'action'     => 'ajaxNewsletter',
		                            ),
		                        ),
		                    ),
		                ),
		            ),
       			),
        	),
            'zfcadmin' => array(
                'child_routes' => array(
                    'adfabpartnership_admin' => array(
                        'type' => 'Literal',
                        'priority' => 1000,
                        'options' => array(
                            'route' => '/partnership',
                            'defaults' => array(
                                'controller' => 'adfabpartnership_admin',
                                'action'     => 'index',
                            ),
                        ),
                        'child_routes' =>array(
                            'list' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/list[/:p]',
                                    'defaults' => array(
                                        'controller' => 'adfabpartnership_admin',
                                        'action'     => 'list',
                                    ),
                                ),
                            ),
                            'create' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/create',
                                    'defaults' => array(
                                        'controller' => 'adfabpartnership_admin',
                                        'action'     => 'create'
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/edit/:partnerId',
                                    'defaults' => array(
                                        'controller' => 'adfabpartnership_admin',
                                        'action'     => 'edit',
                                        'partnerId'     => 0
                                    ),
                                ),
                            ),
                            'remove' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/remove/:partnerId',
                                    'defaults' => array(
                                        'controller' => 'adfabpartnership_admin',
                                        'action'     => 'remove',
                                        'partnerId'     => 0
                                    ),
                                ),
                            ),
                            'newsletter' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/newsletter/:partnerId[/:p]',
                                    'defaults' => array(
                                        'controller' => 'adfabpartnership_admin',
                                        'action'     => 'newsletter',
                                    ),
                                ),
                            ),
                            'download' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/download/:partnerId',
                                    'defaults' => array(
                                        'controller' => 'adfabpartnership_admin',
                                        'action'     => 'download',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'navigation' => array(
        'admin' => array(
            'adfabpartnershipadmin' => array(
                'order' => 80,
                'label' => 'Partenaires',
                'route' => 'zfcadmin/adfabpartnership_admin/list',
                'resource' => 'partner',
                'privilege' => 'list',
                'pages' => array(
                    'list' => array(
                        'label' => 'Liste des partenaires',
                        'route' => 'zfcadmin/adfabpartnership_admin/list',
                        'resource' => 'partner',
                        'privilege' => 'list',
                    ),
                    'create' => array(
                        'label' => 'Créer un nouveau partenaire',
                        'route' => 'zfcadmin/adfabpartnership_admin/create',
                        'resource' => 'partner',
                        'privilege' => 'list',
                    ),
                ),
            ),
        ),
    )
);
