<?php
return [
    [
        "name" => "TRANG CHỦ",
        "icon" => "fa fa-home",
        "url" => "/xadmin/dashboard/index",
        "group" => 1
    ],
    [
        "name" => "QUẢN LÝ ĐƠN VỊ KẾT NỐI",
        "icon" => "fa fa-address-book",
        "group" => 3,
        'base' => '/xadmin/connection_units',
//        'permission' => 'EWALLET.RequestLogs.index',
        'subs' => [
            [
                "name" => "VÍ ĐIỆN TỬ",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/connection_units/ewallet',
            ],
            [
                "name" => "CỔNG THANH TOÁN",
                "icon" => "fas fa-money-check-alt",
                "group" => 3,
                'url' => '/xadmin/connection_units/paymentGateway',
            ],

            [
                "name" => "HỖ TRỢ CHUYỂN TIỀN ĐIỆN TỬ",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/connection_units/transfer',
            ],
            [
                "name" => "HỖ TRỢ THU HỘ, CHI HỘ",
                "icon" => "fas fa-money-check-alt",
                "group" => 3,
                'url' => '/xadmin/connection_units/payforCollect',
            ],
        ]

    ],
    [
        "name" => "THỐNG KÊ VÍ",
        "icon" => "fa fa-window-maximize",
        "group" => 3,
        'base' => '/xadmin/wallets',
        'subs' => [
            [
                "name" => "VÍ ĐIỆN TỬ",
                "icon" => "fas fa-list ",
                "group" => 3,
//                'url' => '/xadmin/wallets',
                'subs' => [
                    [
                        "name" => "THỐNG KÊ THEO LOẠI VÍ",
                        "icon" => "fas fa-list ",
                        "group" => 3,
                        'url' => '/xadmin/wallets/reportWalletType',
                    ],
                    [
                        "name" => "THEO SỐ LƯỢNG",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/wallet_transactions/reportCountVDT',
                    ],
                    [
                        "name" => "THEO GIÁ TRỊ GIAO DỊCH",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/wallet_transactions/walletReportTransactionVDT',
                    ],
                ]
            ],
            [
                "name" => "CỔNG THANH TOÁN",
                "icon" => "fas fa-money-check-alt",
                "group" => 3,
//                'url' => '/xadmin/wallet_transactions',
                'subs' => [
                    [
                        "name" => "THỐNG KÊ CÁC ĐVCNTT CÓ NHIỀU GIAO DỊCH",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/wallet_transactions/reportCountCTT',
                    ],
                    [
                        "name" => "THỐNG KÊ CÁC ĐVCNTT CÓ GIÁ TRỊ GIAO DỊCH CAO",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/wallet_transactions/walletReportTransactionCTT',
                    ],
                ]
            ],

        ]
    ],
    [
        "name" => "BÁO CÁO TỔNG HỢP",
        "icon" => "fas fa-chart-line",
        "group" => 3,
        'base' => '/xadmin/reports',
        'subs' => [
            [
                "name" => "VÍ ĐIỆN TỬ",
                "icon" => "fas fa-list ",
                "group" => 3,
//                'url' => '/xadmin/reports',
                'subs' => [
                    [
                        "name" => "BÁO CÁO TỔNG HỢP GIAO DỊCH",
                        "icon" => "fas fa-list ",
                        "group" => 3,
                        'url' => '/xadmin/reports/reportWallet',
                    ],
                    [
                        "name" => "TỔNG HỢP GIAO DỊCH XỬ LÝ",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/reports/reportTransactionVDT',
                    ],
                ]
            ],

            [
                "name" => "CỔNG THANH TOÁN",
                "icon" => "fas fa-list ",
                "group" => 3,
//                'url' => '/xadmin/reports',
                'subs' => [
                    [
                        "name" => "TỔNG HỢP GIAO DỊCH XỬ LÝ",
                        "icon" => "fas fa-list ",
                        "group" => 3,
                        'url' => '/xadmin/reports/reportTransactionCTT',
                    ],
                ]
            ],
            [
                "name" => "HỖ TRỢ THU HỘ, CHI HỘ",
                "icon" => "fas fa-money-check-alt",
                "group" => 3,
//                'url' => '/xadmin/reports',
                'subs' => [
                    [
                        "name" => "TỔNG HỢP GIAO DỊCH XỬ LÝ",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/reports/reportTransactionCPOB',
                    ],
                ]
            ],
            [
                "name" => "HỖ TRỢ CHUYỂN TIỀN ĐIỆN TỬ",
                "icon" => "fas fa-money-check-alt",
                "group" => 3,
//                'url' => '/xadmin/reports',
                'subs' => [
                    [
                        "name" => "TỔNG HỢP GIAO DỊCH XỬ LÝ",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/reports/electronicMoneyTransfer',
                    ],
                ]
            ],

        ]
    ],
    [
        "name" => "TÌNH HÌNH RỦI RO",
        "icon" => "fa fa-user-secret",
        "group" => 3,
        'base' => '/xadmin/risk_reports',
        'subs' => [
            [
                "name" => "VÍ ĐIỆN TỬ",
                "icon" => "fas fa-list ",
                "group" => 3,
//                'url' => '/xadmin/risk_reports',
                'subs' => [
                    [
                        "name" => "RỦI RO VẬN HÀNH",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/risk_reports/riskReportOperateVDT'
                    ],
                    [
                        "name" => "RỦI RO GIAN LẬN GIẢ MẠO",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/risk_reports/riskReportCheatVDT'
                    ],
                ]
            ],
            [
                "name" => "CỔNG THANH TOÁN",
                "icon" => "fas fa-list ",
                "group" => 3,
//                'url' => '/xadmin/suspect_reports',
                'subs' => [
                    [
                        "name" => "RỦI RO VẬN HÀNH",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/risk_reports/riskReportOperateCTT'
                    ],
                    [
                        "name" => "RỦI RO GIAN LẬN GIẢ MẠO",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/risk_reports/riskReportCheatCTT'
                    ],
                ]
            ],
            [
                "name" => "HỖ TRỢ THU HỘ, CHI HỘ",
                "icon" => "fas fa-list ",
                "group" => 3,
//                'url' => '/xadmin/suspect_reports',
                'subs' => [
                    [
                        "name" => "RỦI RO VẬN HÀNH",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/risk_reports/riskReportOperateTHCH'
                    ],
                    [
                        "name" => "RỦI RO GIAN LẬN GIẢ MẠO",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/risk_reports/riskReportCheatTHCH'
                    ],
                ]
            ],
            [
                "name" => "HỖ TRỢ CHUYỂN TIỀN ĐIỆN TỬ",
                "icon" => "fas fa-list ",
                "group" => 3,
//                'url' => '/xadmin/suspect_reports',
                'subs' => [
                    [
                        "name" => "RỦI RO VẬN HÀNH",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/risk_reports/riskReportOperateCTDT'
                    ],
                    [
                        "name" => "RỦI RO GIAN LẬN GIẢ MẠO",
                        "icon" => "fas fa-money-check-alt",
                        "group" => 3,
                        'url' => '/xadmin/risk_reports/riskReportCheatCTDT'
                    ],
                ]
            ],

        ]
    ],
    [
        "name" => "TÀI KHOẢN ĐẢM BẢO TT",
        "icon" => "fa fa-user",
        "group" => 3,
        'base' => '/xadmin/secured_accounts',
        'subs' => [
            [
                "name" => "DANH SÁCH TÀI KHOẢN",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/secured_accounts/accounts'
            ],
            [
                "name" => "THỐNG KÊ GIAO DỊCH",
                "icon" => "fas fa-list ",
                "group" => 3,
                'url' => '/xadmin/secured_accounts/transactions',
            ],

        ]
    ]
];
