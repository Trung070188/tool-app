<?php
return [
    [
        "name" => "Dashboard",
        "icon" => "fa fa-home",
        "url" => "/xadmin/dashboard/index",
        "group" => 1
    ],
    [
        "name" => "Campaign",
        "icon" => "fa fa-address-book",
        "group" => 3,
        'base' => '/xadmin/campaigns/index',
//        'permission' => 'EWALLET.RequestLogs.index',
        'subs' => [
            [
                "name" => "campaign index",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/campaigns/index',
            ],
            [
                "name" => "Tạo campaign",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/campaigns/create',
            ]
        ]

    ],
    [
        "name" => "User",
        "icon" => "fa fa-user",
        "group" => 3,
        'base' => '/xadmin/users/index',
        'subs' => [
            [
                "name" => "User Index",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/users/index',
            ],
            [
                "name" => "User Form",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/users/create',
            ]
        ]

    ],
    [
        "name" => "Customer",
        "icon" => "fa fa-user",
        "group" => 3,
        'base' => '/xadmin/customers/index',
        'subs' => [
            [
                "name" => "Customer Index",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/customers/index',
            ],
            [
                "name" => "Customer Form",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/customers/create',
            ]
        ]

    ],

    [
        "name" => "Permission",
        "icon" => "fa fa-lock",
        "url" => "/xadmin/permissions/index",
        "group" => 2
    ],

    [
        "name" => "Partner",
        "icon" => "fas fa-user-friends
",
        "group" => 3,
        'base' => '/xadmin/partners/index',
        'subs' => [
            [
                "name" => "Partners Index",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/partners/index',
            ],
            [
                "name" => "Partner Form",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/partners/create',
            ]
        ]
    ],
    [
        "name" => "Roles",
        "icon" => "fa fa-users",
        "group" => 3,
        'base' => '/xadmin/roles/index',
        'subs' => [
            [
                "name" => "Roles Index",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/roles/index',
            ],
            [
                "name" => "Roles Form",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/roles/create',
            ]
        ]
    ]


];
