<?php
return [
    [
        "name" => "TRANG CHỦ",
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

];
