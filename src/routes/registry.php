<?php
 return
 [
    [
        'path' => '/campaigns/{action}',
        'action' => 'CampaignsController',
        'name' => 'campaigns',
    ],
    [
        'path' => '/customers/{action}',
        'action' => 'CustomersController',
        'name' => 'customers',
    ],
    [
        'path' => '/roles/{action}',
        'action' => 'RolesController',
        'name' => 'roles',
    ],
    [
        'path' => '/partners/{action}',
        'action' => 'PartnersController',
        'name' => 'partners',
    ],
    [
        'path' => '/permissions/{action}',
        'action' => 'PermissionsController',
        'name' => 'permissions',
    ],
    [
        'path' => '/users/{action}',
        'action' => 'UsersController',
        'name' => 'user',
    ],
    [
        'path' => '/files/{action}',
        'action' => 'FilesController',
        'name' => 'files',
    ],
];