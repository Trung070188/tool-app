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
                "name" => "Danh sách",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/campaigns/index',
            ],
            [
                "name" => "Thêm mới",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/campaigns/create',
            ] ,

        ]

    ],
    [
        "name" => "Campaign Partner",
        "icon" => "fa fa-home",
        "url" => "/xadmin/campaign_partners/index",
        "group" => 3,
        "subs" => [
            [
                "name" => "Danh sách",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/campaign_partners/index',
            ] ,
            [
                "name" => "Thêm mới",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/campaign_partners/create',
            ] ,
            [
                "name" => " Thống kê",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/campaign_installs/index',
            ]

        ]
    ],
//    [
//        "name" => "User",
//        "icon" => "fa fa-user",
//        "group" => 3,
//        'base' => '/xadmin/users/index',
//        'subs' => [
//            [
//                "name" => "User Index",
//                "icon" => "fas fa-list ",
//                "group" => 3,
//                'url' => '/xadmin/users/index',
//            ],
//            [
//                "name" => "User Form",
//                "icon" => "fas fa-list ",
//                "group" => 3,
//                'url' => '/xadmin/users/create',
//            ]
//        ]
//
//    ],
    [
        "name" => "Khách hàng",
        "icon" => "fa fa-user",
        "group" => 3,
        'base' => '/xadmin/customers/index',
        'subs' => [
            [
                "name" => "Danh sách",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/customers/index',
            ],
            [
                "name" => "Thêm mới",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/customers/create',
            ]
        ]

    ],
    [
        "name"=>"Thông kê công nợ" ,
        "icon"=>"fas fa-lis",
        "group"=>3,
        "url"=>"/xadmin/debt_settle/index"
    ],

//    [
//        "name" => "Permission",
//        "icon" => "fa fa-lock",
//        "url" => "/xadmin/permissions/index",
//        "group" => 2
//    ],

    [
        "name" => "Đối tác",
        "icon" => "fas fa-user-friends
",
        "group" => 3,
        'base' => '/xadmin/partners/index',
        'subs' => [
            [
                "name" => "Danh sách",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/partners/index',
            ],
            [
                "name" => "Thêm mới",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/partners/create',
            ]
        ]
    ],
//    [
//        "name" => "Roles",
//        "icon" => "fa fa-users",
//        "group" => 3,
//        'base' => '/xadmin/roles/index',
//        'subs' => [
//            [
//                "name" => "Roles Index",
//                "icon" => "fas fa-list ",
//                "group" => 3,
//                'url' => '/xadmin/roles/index',
//            ],
//            [
//                "name" => "Roles Form",
//                "icon" => "fas fa-list ",
//                "group" => 3,
//                'url' => '/xadmin/roles/create',
//            ]
//        ]
//    ]


];
