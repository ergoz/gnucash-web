<?php

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'=> 'index',
                    ],
                ],
            ],
            'accounts' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/accounts',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Accounts',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'view-account' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/view/:id[/:page]',
                            'constraints' => [
                                'id'     => '[a-zA-Z0-9]*',
                                'page'     => '[0-9]*',
                            ],
                            'defaults' => [
                                'action' => 'view',
                                'page' => 1
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
        'factories' => [
            'AccountRepository' => function (Zend\ServiceManager\ServiceManager $sm) {
                $em = $sm->get('EntityManager');
                $accountRepository = new GnuCash\Persistence\Repository\AccountRepository($em);
                return $accountRepository;
            },
            'EntityManager' => function (Zend\ServiceManager\ServiceManager $sm) {
                $config = $sm->get('Config');
                if (!isset($config['doctrine'])) {
                    throw new \Exception(
                        'Doctrine database connection information is not configured. See ' .
                        '[config/autoload/local.php.dist] for a sample configuration.'
                    );
                }

                $configManager = new GnuCash\Persistence\ConfigurationFactory();
                $configManager->loadConfiguration($config['doctrine']);

                $entityFactory = new GnuCash\Persistence\EntityManagerFactory($configManager);
                $entity = $entityFactory->getSingleton();

                return $entity;
            },
            'Navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ]
    ],
    'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            'Application\Controller\Index' => function (Zend\Mvc\Controller\ControllerManager $cm) {
                $repo = $cm->getServiceLocator()->get('AccountRepository');
                $controller = new Application\Controller\IndexController($repo);
                return $controller;
            },
            'Application\Controller\Accounts' => function (Zend\Mvc\Controller\ControllerManager $cm) {
                $repo = $cm->getServiceLocator()->get('AccountRepository');
                $controller = new Application\Controller\AccountsController($repo);
                return $controller;
            }
        ]
    ],
    'view_manager' => [
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'application/layout/menu' => __DIR__ . '/../view/layout/menu.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ]
    ],
    'view_helpers' => [
        'invokables' => [
            'formatCurrency' => 'Application\View\FormatCurrency'
        ]
    ],
];
