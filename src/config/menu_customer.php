<?php
return [
    [
    "name" => "Campaign",
    "icon" => "fa fa-user",
    "group" => 3,
    'base' => '/customer',
    'subs' => [
        [
            "name" => "Campaign",
            "icon" => "fas fa-list ",
            "group" => 3,
            'url' => '/customer/dashboard/index',
        ],
        [
            "name" => "Thống kê",
            "icon" => "fas fa-list ",
            "group" => 3,
            'url' => '/customer/campaigns/statistical',
        ]
    ]

],
];
