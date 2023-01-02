<?php

namespace App\Http\Controllers\Admin;

use App\Components\ReportExcel\ExcelModel\BlockAcceptedPaymentUnit;
use App\Components\ReportExcel\ExcelModel\BlockIntegratedBank;
use App\Components\ReportExcel\ExcelModel\BlockIntegratedBankCpob;
use App\Components\ReportExcel\ExcelModel\BlockIntegratedBankTransferEMoney;
use App\Components\ReportExcel\ExcelModel\BlockIntegratedBankWallet;
use App\Components\ReportExcel\ExcelModel\BlockLiquidityRiskWallet;
use App\Components\ReportExcel\ExcelModel\BlockOtherPartnerWallet;
use App\Components\ReportExcel\ExcelModel\BlockOtherTargetsWallet;
use App\Components\ReportExcel\ExcelModel\BlockTopAmountTransactionAcceptedUnit;
use App\Components\ReportExcel\ExcelModel\BlockTopCountTransactionAcceptedUnit;
use App\Components\ReportExcel\ExcelModel\BlockTopCustomerCountWithoutAcceptedUnit;
use App\Components\ReportExcel\ExcelModel\BlockTopEnterpriseWithoutAcceptedUnit;
use App\Components\ReportExcel\ExcelModel\BlockTopTransactionAmountWithoutAcceptedUnit;
use App\Components\ReportExcel\ExcelModel\BlockTopTransactionCountWithoutAcceptedUnit;
use App\Components\ReportExcel\ExcelModel\BlockTopTransactionPersonAmountWithoutAcceptedUnit;
use App\Components\ReportExcel\ExcelModel\BlockTransactionDetail;
use App\Components\ReportExcel\ExcelModel\BlockTransactionEnterpriseWithoutAcceptedUnit;
use App\Components\ReportExcel\ExcelModel\BlockTransactionPayforCollect;
use App\Components\ReportExcel\ExcelModel\BlockTransactionPersonWithoutAcceptedUnit;
use App\Components\ReportExcel\ExcelModel\BlockTransactionSystem;
use App\Components\ReportExcel\ExcelModel\BlockTransactionTransferEMoney;
use App\Components\ReportExcel\ExcelReportBuilder;
use App\Http\Controllers\AppController;
use App\Http\Controllers\Controller;
use App\Models\ConnectionUnit;
use App\Models\ReportCpobTransactionStatus;
use App\Models\ReportMbTransactionStatus;
use App\Models\ReportPGWTransactionStatus;
use App\Models\ReportTopCustomer;
use App\Models\ReportTransactionStatus;
use App\Models\ReportTransactionType;
use App\Models\ReportWalletStatus;
use App\Models\SecuredAccountBalance;
use App\Models\SecuredAccountModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends AppController
{
    public function index()
    {
        $title = 'Thống kê';
        $component = 'DashboardIndex';

        return vue(compact('component', 'title'));
    }

    public function export(Request $req)
    {
        $filePath = 'report/';
        $fileName = '';
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $filePath .= $req->year;
                    $fileName = 'VNPD_BC_NHNN_' . $req->year;
                }
            } else {
                if ($req->year) {
                    $filePath .= $req->year;
                }
                if (isset($req->quarter)) {
                    $filePath .= '/Quarter';
                    $fileName = 'VNPD_BC_NHNN_Quy-' . $req->quarter . '-'.$req->year;
                }
                if ($req->months) {
                    $req->months = explode(',', $req->months);
                }
            }
        }

//        $oldFile = storage_path($filePath.'/'.$fileName . '.xlsx');
//        if (file_exists($oldFile)) {
//            $builder = new ExcelReportBuilder($oldFile);
//        } else {
            $sample = storage_path('sample/VNPD_BC_NHNN.xlsx');
            $block1 = $this->getBlockIntegratedBank($req);
            $block2 = $this->getDetailTransactionTypeBGWByMonth($req);
            $block3 = $this->getDetailTransactionTypeEWalletByMonth($req);
            $block4 = $this->getBlockIntegratedBankWallet($req);
            $block5 = $this->getBlockIntegratedBankCpob($req);
            $block6 = $this->getBlockIntegratedBankTransferEMoney($req);
            $block7 = $this->getPersonTransactionWithoutAcceptedUnit($req);
            $block8 = $this->getEnterpriseTransactionWithoutAcceptedUnit($req);
            $block9 = $this->getTransactionAcceptedPaymentUnit($req);
            $block10 = $this->getTopCustomerEnterpriseCountWithoutAcceptedUnit($req);
            $block11 = $this->getTopCustomerEnterpriseAmountWithoutAcceptedUnit($req);
            $block12 = $this->getTopCustomerPersonCountWithoutAcceptedUnit($req);
            $block13 = $this->getTopCustomerPersonAmountWithoutAcceptedUnit($req);
            $block14 = $this->getTopCustomerPaymentAcceptedUnitCount($req);
            $block15 = $this->getTopCustomerPaymentAcceptedUnitCount($req);
            $block16 = $this->getDetailTransactionTypeCpobByMonth($req);
            $block17 = $this->getDetailTransactionTypeMbByMonth($req);
            $block18 = $this->getTopCustomerPaymentAcceptedUnitAmount($req);
            $block19 = $this->getCustomerByEWL($req);
            $block20 = $this->getBlockLiquidityRiskWallet($req);
            $block21 = $this->getBlockOtherTargetsWallet($req);

            $builder = new ExcelReportBuilder($sample);
            $builder->add($block1);
            $builder->add($block2);
            $builder->add($block3);
            $builder->add($block4);
            $builder->add($block5);
            $builder->add($block6);
            $builder->add($block7);
            $builder->add($block8);
            $builder->add($block9);
            $builder->add($block10);
            $builder->add($block11);
            $builder->add($block12);
            $builder->add($block13);
            $builder->add($block14);
            $builder->add($block15);
            $builder->add($block16);
            $builder->add($block17);
            $builder->add($block18);
            $builder->add($block19);
            $builder->add($block20);
            $builder->add($block21);
//        }
        $builder->save($req, $filePath, $fileName);

    }

    public function getBlockIntegratedBank(Request $req) {
        $block = new BlockIntegratedBank();
        $data = ConnectionUnit::query()->where('unit_type', ConnectionUnit::UNIT_TYPE_GATEWAY)
            ->where('status', ConnectionUnit::STATUS_ACTIVE);

        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $results = $data->limit(6)->get();

        $index = 0;
        foreach ($results as $d) {
            $index++;
            if ($d) {
                $block->push([$index, $d->partner_name, $d->create_date]);
            }

        }

        return $block;

    }

    public function getBlockIntegratedBankWallet(Request $req) {
        $block = new BlockIntegratedBankWallet();
        $data = ConnectionUnit::query()->where('unit_type', ConnectionUnit::UNIT_TYPE_EWALLET)
            ->where('status', ConnectionUnit::STATUS_ACTIVE);

        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $results = $data->orderBy('partner_code')->limit(6)->get();

        $index = 0;
        foreach ($results as $d) {
            $index++;
            if ($d) {
                $block->push([$index, $d->partner_name, $d->create_date]);
            }

        }

        return $block;

    }

    public function getBlockIntegratedBankCpob(Request $req) {
        $block = new BlockIntegratedBankCpob();
        $data = ConnectionUnit::query()->where('unit_type', ConnectionUnit::UNIT_TYPE_PAYFOR_COLLECT)
            ->where('status', ConnectionUnit::STATUS_ACTIVE);

        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $results = $data->orderBy('partner_code')->limit(6)->get();

        $index = 0;
        foreach ($results as $d) {
            $index++;
            if ($d) {
                $block->push([$index, $d->partner_name, $d->create_date]);
            }

        }

        return $block;

    }

    public function getBlockIntegratedBankTransferEMoney(Request $req) {
        $block = new BlockIntegratedBankTransferEMoney();
        $data = ConnectionUnit::query()->where('unit_type', ConnectionUnit::UNIT_TYPE_TRANSFER)
            ->where('status', ConnectionUnit::STATUS_ACTIVE);

        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $results = $data->orderBy('partner_code')->limit(6)->get();

        $index = 0;
        foreach ($results as $d) {
            $index++;
            if ($d) {
                $block->push([$index, $d->partner_name, $d->create_date]);
            }

        }

        return $block;

    }

    public function getDetailTransactionTypeEWalletByMonth(Request $req) {
        $block = new BlockTransactionSystem();
        $dataByStatus = ReportTransactionStatus::query()->selectRaw("transaction_status, month,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount");
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
                if ($req->months) {
                    $dataByStatus->whereIn('month', $req->months);
                }
            }
        }

        $dataSuccessClone = clone ($dataByStatus);
        $dataFailedClone = clone ($dataByStatus);
        $dataSuccess = $dataSuccessClone->where('transaction_status', ReportTransactionStatus::TRANS_STATUS_SUCCESS)
            ->groupBy('month')->orderBy('month')->get();

        $dataFailed = $dataFailedClone->where('transaction_status', ReportTransactionStatus::TRANS_STATUS_FAILED)
            ->groupBy('month')->orderBy('month')->get();
        $resultCountSuccess = $dataSuccess->pluck('sumTotalCount', 'month');
        $resultAmountSuccess = $dataSuccess->pluck('sumTotalAmount', 'month');

        $arrCountSuccess = [];
        $arrAmountSuccess = [];
        $totalCountSum = 0;
        $totalAmountSum = 0;
        $resultCountFailed = $dataFailed->pluck('sumTotalCount', 'month');
        $resultAmountFailed = $dataFailed->pluck('sumTotalAmount', 'month');
        $arrCountFailed = [];
        $arrAmountFailed = [];
        $totalCountSumFailed = 0;
        $totalAmountSumFailed = 0;
        for ($i = 1; $i <= 12; $i++) {
            $arrCountSuccess[] = $resultCountSuccess[$i] ?? 0;
            $arrAmountSuccess[] = $resultAmountSuccess[$i] ?? 0;
            $totalCountSum += $resultCountSuccess[$i] ?? 0;
            $totalAmountSum += $resultAmountSuccess[$i] ?? 0;
            $arrCountFailed[] = $resultCountFailed[$i] ?? 0;
            $arrAmountFailed[] = $resultAmountFailed[$i] ?? 0;
            $totalCountSumFailed += $resultCountFailed[$i] ?? 0;
            $totalAmountSumFailed += $resultAmountFailed[$i] ?? 0;

        }

        $arrAmountSuccess[] = $totalAmountSum;
        $arrCountSuccess[] = $totalCountSum;

        $arrCountFailed[] = $totalCountSumFailed;
        $arrAmountFailed[] = $totalAmountSumFailed;

        $block->push($arrCountSuccess);
        $block->push($arrAmountSuccess);
        $block->push($arrCountFailed);
        $block->push($arrAmountFailed);

        $dataByStatus = ReportTransactionType::query()->selectRaw("
                transaction_type_id, month,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount");
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
                if ($req->months) {
                    $dataByStatus->whereIn('month', $req->months);
                }
            }
        }

        $dataDepositClone = clone ($dataByStatus);
        $dataWithdrawClone = clone ($dataByStatus);
        $dataPaymentTransferClone = clone ($dataByStatus);
        $dataDeposit = $dataDepositClone->where('transaction_type_id', ReportTransactionType::TRANS_TYPE_DEPOSIT)
            ->groupBy('month')->orderBy('month')->get();

        $dataWithdraw = $dataWithdrawClone->where('transaction_type_id', ReportTransactionType::TRANS_TYPE_WITHDRAW)
            ->groupBy('month')->orderBy('month')->get();
        $dataPaymentTransfer = $dataPaymentTransferClone
            ->whereIn('transaction_type_id', [ReportTransactionType::TRANS_TYPE_PAYMENT, ReportTransactionType::TRANS_TYPE_TRANSFER])
            ->groupBy('month')->orderBy('month')->get();
        $dataDepositCount = $dataDeposit->pluck('sumTotalCount', 'month');
        $dataDepositAmount = $dataDeposit->pluck('sumTotalAmount', 'month');

        $arrDataDepositCount = [];
        $arrDataDepositAmount = [];
        $totalDepositCount = 0;
        $totalDepositAmount = 0;

        $dataWithdrawCount = $dataWithdraw->pluck('sumTotalCount', 'month');
        $dataWithdrawAmount = $dataWithdraw->pluck('sumTotalAmount', 'month');

        $arrDataWithdrawCount = [];
        $arrDataWithdrawAmount = [];
        $totalWithdrawCount = 0;
        $totalWithdrawAmount = 0;

        $dataPaymentTransferCount = $dataPaymentTransfer->pluck('sumTotalCount', 'month');
        $dataPaymentTransferAmount = $dataPaymentTransfer->pluck('sumTotalAmount', 'month');

        $arrDataPaymentTransferCount = [];
        $arrDataPaymentTransferAmount = [];
        $totalPaymentTransferCount = 0;
        $totalPaymentTransferAmount = 0;

        for ($i = 1; $i <= 12; $i++) {
            $arrDataDepositCount[] = $dataDepositCount[$i] ?? 0;
            $arrDataDepositAmount[] = $dataDepositAmount[$i] ?? 0;
            $totalDepositCount += $dataDepositCount[$i] ?? 0;
            $totalDepositAmount += $dataDepositAmount[$i] ?? 0;
            $arrDataWithdrawCount[] = $dataWithdrawCount[$i] ?? 0;
            $arrDataWithdrawAmount[] = $dataWithdrawAmount[$i] ?? 0;
            $totalWithdrawCount += $dataWithdrawCount[$i] ?? 0;
            $totalWithdrawAmount += $dataWithdrawAmount[$i] ?? 0;
            $arrDataPaymentTransferCount[] = $dataPaymentTransferCount[$i] ?? 0;
            $arrDataPaymentTransferAmount[] = $dataPaymentTransferAmount[$i] ?? 0;
            $totalPaymentTransferCount += $dataPaymentTransferCount[$i] ?? 0;
            $totalPaymentTransferAmount += $dataPaymentTransferAmount[$i] ?? 0;

        }

        $arrDataDepositCount[] = $totalDepositCount;
        $arrDataDepositAmount[] = $totalDepositAmount;

        $arrDataWithdrawCount[] = $totalWithdrawCount;
        $arrDataWithdrawAmount[] = $totalWithdrawAmount;
        $arrDataPaymentTransferCount[] = $totalPaymentTransferCount;
        $arrDataPaymentTransferAmount[] = $totalPaymentTransferAmount;

        $block->push($arrDataDepositCount);
        $block->push($arrDataDepositAmount);
        $block->push($arrDataWithdrawCount);
        $block->push($arrDataWithdrawAmount);
        $block->push($arrDataPaymentTransferCount);
        $block->push($arrDataPaymentTransferAmount);

        return $block;
    }

    public function getDetailTransactionTypeBGWByMonth(Request $req) {
        $block = new BlockTransactionDetail();
        $data = ReportPGWTransactionStatus::query()->selectRaw("transaction_status, month,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount");
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $dataSuccessClone = clone ($data);
        $dataFailedClone = clone ($data);
        $dataSuccess = $dataSuccessClone->where('transaction_status', ReportPGWTransactionStatus::TRANS_STATUS_SUCCESS)
            ->groupBy('month')->orderBy('month')->get();

        $dataFailed = $dataFailedClone->where('transaction_status', ReportPGWTransactionStatus::TRANS_STATUS_FAILED)
            ->groupBy('month')->orderBy('month')->get();
        $resultCountSuccess = $dataSuccess->pluck('sumTotalCount', 'month');
        $resultAmountSuccess = $dataSuccess->pluck('sumTotalAmount', 'month');

        $arrCountSuccess = [];
        $arrAmountSuccess = [];
        $totalCountSum = 0;
        $totalAmountSum = 0;
        $resultCountFailed = $dataFailed->pluck('sumTotalCount', 'month');
        $resultAmountFailed = $dataFailed->pluck('sumTotalAmount', 'month');
        $arrCountFailed = [];
        $arrAmountFailed = [];
        $totalCountSumFailed = 0;
        $totalAmountSumFailed = 0;
        for ($i = 1; $i <= 12; $i++) {
            $arrCountSuccess[] = $resultCountSuccess[$i] ?? 0;
            $arrAmountSuccess[] = $resultAmountSuccess[$i] ?? 0;
            $totalCountSum += $resultCountSuccess[$i] ?? 0;
            $totalAmountSum += $resultAmountSuccess[$i] ?? 0;
            $arrCountFailed[] = $resultCountFailed[$i] ?? 0;
            $arrAmountFailed[] = $resultAmountFailed[$i] ?? 0;
            $totalCountSumFailed += $resultCountFailed[$i] ?? 0;
            $totalAmountSumFailed += $resultAmountFailed[$i] ?? 0;

        }

        $arrAmountSuccess[] = $totalAmountSum;
        $arrCountSuccess[] = $totalCountSum;

        $arrCountFailed[] = $totalCountSumFailed;
        $arrAmountFailed[] = $totalAmountSumFailed;

        $block->push($arrCountSuccess);
        $block->push($arrAmountSuccess);
        $block->push($arrCountFailed);
        $block->push($arrAmountFailed);

        return $block;
    }

    public function getDetailTransactionTypeCpobByMonth(Request $req) {
        $block = new BlockTransactionPayforCollect();
        $data = ReportCpobTransactionStatus::query()->selectRaw("transaction_status, month,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount");
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $dataSuccessClone = clone ($data);
        $dataFailedClone = clone ($data);
        $dataSuccess = $dataSuccessClone->where('transaction_status', ReportCpobTransactionStatus::TRANS_STATUS_SUCCESS)
            ->groupBy('month')->orderBy('month')->get();

        $dataFailed = $dataFailedClone->where('transaction_status', ReportCpobTransactionStatus::TRANS_STATUS_FAILED)
            ->groupBy('month')->orderBy('month')->get();
        $resultCountSuccess = $dataSuccess->pluck('sumTotalCount', 'month');
        $resultAmountSuccess = $dataSuccess->pluck('sumTotalAmount', 'month');

        $arrCountSuccess = [];
        $arrAmountSuccess = [];
        $totalCountSum = 0;
        $totalAmountSum = 0;
        $resultCountFailed = $dataFailed->pluck('sumTotalCount', 'month');
        $resultAmountFailed = $dataFailed->pluck('sumTotalAmount', 'month');
        $arrCountFailed = [];
        $arrAmountFailed = [];
        $totalCountSumFailed = 0;
        $totalAmountSumFailed = 0;
        for ($i = 1; $i <= 12; $i++) {
            $arrCountSuccess[] = $resultCountSuccess[$i] ?? 0;
            $arrAmountSuccess[] = $resultAmountSuccess[$i] ?? 0;
            $totalCountSum += $resultCountSuccess[$i] ?? 0;
            $totalAmountSum += $resultAmountSuccess[$i] ?? 0;
            $arrCountFailed[] = $resultCountFailed[$i] ?? 0;
            $arrAmountFailed[] = $resultAmountFailed[$i] ?? 0;
            $totalCountSumFailed += $resultCountFailed[$i] ?? 0;
            $totalAmountSumFailed += $resultAmountFailed[$i] ?? 0;

        }

        $arrAmountSuccess[] = $totalAmountSum;
        $arrCountSuccess[] = $totalCountSum;

        $arrCountFailed[] = $totalCountSumFailed;
        $arrAmountFailed[] = $totalAmountSumFailed;

        $block->push($arrCountSuccess);
        $block->push($arrAmountSuccess);
        $block->push($arrCountFailed);
        $block->push($arrAmountFailed);

        return $block;
    }

    public function getDetailTransactionTypeMbByMonth(Request $req) {
        $block = new BlockTransactionTransferEMoney();
        $data = ReportMbTransactionStatus::query()->selectRaw("transaction_status, month,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount");
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $dataSuccessClone = clone ($data);
        $dataFailedClone = clone ($data);
        $dataSuccess = $dataSuccessClone->where('transaction_status', ReportMbTransactionStatus::TRANS_STATUS_SUCCESS)
            ->groupBy('month')->orderBy('month')->get();

        $dataFailed = $dataFailedClone->where('transaction_status', ReportMbTransactionStatus::TRANS_STATUS_FAILED)
            ->groupBy('month')->orderBy('month')->get();
        $resultCountSuccess = $dataSuccess->pluck('sumTotalCount', 'month');
        $resultAmountSuccess = $dataSuccess->pluck('sumTotalAmount', 'month');

        $arrCountSuccess = [];
        $arrAmountSuccess = [];
        $totalCountSum = 0;
        $totalAmountSum = 0;
        $resultCountFailed = $dataFailed->pluck('sumTotalCount', 'month');
        $resultAmountFailed = $dataFailed->pluck('sumTotalAmount', 'month');
        $arrCountFailed = [];
        $arrAmountFailed = [];
        $totalCountSumFailed = 0;
        $totalAmountSumFailed = 0;
        for ($i = 1; $i <= 12; $i++) {
            $arrCountSuccess[] = $resultCountSuccess[$i] ?? 0;
            $arrAmountSuccess[] = $resultAmountSuccess[$i] ?? 0;
            $totalCountSum += $resultCountSuccess[$i] ?? 0;
            $totalAmountSum += $resultAmountSuccess[$i] ?? 0;
            $arrCountFailed[] = $resultCountFailed[$i] ?? 0;
            $arrAmountFailed[] = $resultAmountFailed[$i] ?? 0;
            $totalCountSumFailed += $resultCountFailed[$i] ?? 0;
            $totalAmountSumFailed += $resultAmountFailed[$i] ?? 0;

        }

        $arrAmountSuccess[] = $totalAmountSum;
        $arrCountSuccess[] = $totalCountSum;

        $arrCountFailed[] = $totalCountSumFailed;
        $arrAmountFailed[] = $totalAmountSumFailed;

        $block->push($arrCountSuccess);
        $block->push($arrAmountSuccess);
        $block->push($arrCountFailed);
        $block->push($arrAmountFailed);

        return $block;
    }

    public function getPersonTransactionWithoutAcceptedUnit(Request $req) {
        $block = new BlockTransactionPersonWithoutAcceptedUnit();
        $dataByStatus = ReportTopCustomer::query()->selectRaw("customer_category_id, month,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount");
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
                if ($req->months) {
                    $dataByStatus->whereIn('month', $req->months);
                }
            }
        }

        $dataSuccessClone = clone ($dataByStatus);
        $dataFailedClone = clone ($dataByStatus);
        $dataSuccess = $dataSuccessClone->where('customer_category_id', ReportTopCustomer::CUS_TYPE_KHCN)
            ->groupBy('month')->orderBy('month')->get();

//        $dataFailed = $dataFailedClone->where('transaction_status', ReportTransactionStatus::TRANS_STATUS_FAILED)
//            ->groupBy('month')->orderBy('month')->get();
        $resultCountSuccess = $dataSuccess->pluck('sumTotalCount', 'month');
        $resultAmountSuccess = $dataSuccess->pluck('sumTotalAmount', 'month');

        $arrCountSuccess = [];
        $arrAmountSuccess = [];
        $totalCountSum = 0;
        $totalAmountSum = 0;
//        $resultCountFailed = $dataFailed->pluck('sumTotalCount', 'month');
//        $resultAmountFailed = $dataFailed->pluck('sumTotalAmount', 'month');
        $arrCountFailed = [];
        $arrAmountFailed = [];
        $totalCountSumFailed = 0;
        $totalAmountSumFailed = 0;
        for ($i = 1; $i <= 12; $i++) {
            $arrCountSuccess[] = $resultCountSuccess[$i] ?? 0;
            $arrAmountSuccess[] = $resultAmountSuccess[$i] ?? 0;
            $totalCountSum += $resultCountSuccess[$i] ?? 0;
            $totalAmountSum += $resultAmountSuccess[$i] ?? 0;
            $arrCountFailed[] = 0;
            $arrAmountFailed[] = 0;
            $totalCountSumFailed += 0;
            $totalAmountSumFailed +=  0;

        }

        $arrAmountSuccess[] = $totalAmountSum;
        $arrCountSuccess[] = $totalCountSum;

        $arrCountFailed[] = $totalCountSumFailed;
        $arrAmountFailed[] = $totalAmountSumFailed;

        $block->push($arrCountSuccess);
        $block->push($arrAmountSuccess);
//        $block->push($arrCountFailed);
//        $block->push($arrAmountFailed);

        $dataByStatus = ReportTopCustomer::query()->selectRaw("
                transaction_type_id, month,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount");
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
                if ($req->months) {
                    $dataByStatus->whereIn('month', $req->months);
                }
            }
        }

        $dataDepositClone = clone ($dataByStatus);
        $dataWithdrawClone = clone ($dataByStatus);
        $dataPaymentTransferClone = clone ($dataByStatus);
        $dataDeposit = $dataDepositClone->where('transaction_type_id', ReportTransactionType::TRANS_TYPE_DEPOSIT)
            ->groupBy('month')->orderBy('month')->get();

        $dataWithdraw = $dataWithdrawClone->where('transaction_type_id', ReportTransactionType::TRANS_TYPE_WITHDRAW)
            ->groupBy('month')->orderBy('month')->get();
        $dataPaymentTransfer = $dataPaymentTransferClone
            ->whereIn('transaction_type_id', [ReportTransactionType::TRANS_TYPE_PAYMENT, ReportTransactionType::TRANS_TYPE_TRANSFER])
            ->groupBy('month')->orderBy('month')->get();
        $dataDepositCount = $dataDeposit->pluck('sumTotalCount', 'month');
        $dataDepositAmount = $dataDeposit->pluck('sumTotalAmount', 'month');

        $arrDataDepositCount = [];
        $arrDataDepositAmount = [];
        $totalDepositCount = 0;
        $totalDepositAmount = 0;

        $dataWithdrawCount = $dataWithdraw->pluck('sumTotalCount', 'month');
        $dataWithdrawAmount = $dataWithdraw->pluck('sumTotalAmount', 'month');

        $arrDataWithdrawCount = [];
        $arrDataWithdrawAmount = [];
        $totalWithdrawCount = 0;
        $totalWithdrawAmount = 0;

        $dataPaymentTransferCount = $dataPaymentTransfer->pluck('sumTotalCount', 'month');
        $dataPaymentTransferAmount = $dataPaymentTransfer->pluck('sumTotalAmount', 'month');

        $arrDataPaymentTransferCount = [];
        $arrDataPaymentTransferAmount = [];
        $totalPaymentTransferCount = 0;
        $totalPaymentTransferAmount = 0;

        for ($i = 1; $i <= 12; $i++) {
            $arrDataDepositCount[] = $dataDepositCount[$i] ?? 0;
            $arrDataDepositAmount[] = $dataDepositAmount[$i] ?? 0;
            $totalDepositCount += $dataDepositCount[$i] ?? 0;
            $totalDepositAmount += $dataDepositAmount[$i] ?? 0;
            $arrDataWithdrawCount[] = $dataWithdrawCount[$i] ?? 0;
            $arrDataWithdrawAmount[] = $dataWithdrawAmount[$i] ?? 0;
            $totalWithdrawCount += $dataWithdrawCount[$i] ?? 0;
            $totalWithdrawAmount += $dataWithdrawAmount[$i] ?? 0;
            $arrDataPaymentTransferCount[] = $dataPaymentTransferCount[$i] ?? 0;
            $arrDataPaymentTransferAmount[] = $dataPaymentTransferAmount[$i] ?? 0;
            $totalPaymentTransferCount += $dataPaymentTransferCount[$i] ?? 0;
            $totalPaymentTransferAmount += $dataPaymentTransferAmount[$i] ?? 0;

        }

        $arrDataDepositCount[] = $totalDepositCount;
        $arrDataDepositAmount[] = $totalDepositAmount;

        $arrDataWithdrawCount[] = $totalWithdrawCount;
        $arrDataWithdrawAmount[] = $totalWithdrawAmount;
        $arrDataPaymentTransferCount[] = $totalPaymentTransferCount;
        $arrDataPaymentTransferAmount[] = $totalPaymentTransferAmount;

        $block->push($arrDataDepositCount);
        $block->push($arrDataDepositAmount);
        $block->push($arrDataWithdrawCount);
        $block->push($arrDataWithdrawAmount);
        $block->push($arrDataPaymentTransferCount);
        $block->push($arrDataPaymentTransferAmount);

        return $block;
    }

    public function getEnterpriseTransactionWithoutAcceptedUnit(Request $req) {
        $block = new BlockTransactionEnterpriseWithoutAcceptedUnit();
        $dataByStatus = ReportTopCustomer::query()->selectRaw("customer_category_id, month,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount")->where('customer_category_id', ReportTopCustomer::CUS_TYPE_KHDN);
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
                if ($req->months) {
                    $dataByStatus->whereIn('month', $req->months);
                }
            }
        }

        $dataSuccessClone = clone ($dataByStatus);
        $dataFailedClone = clone ($dataByStatus);
        $dataSuccess = $dataSuccessClone->where('customer_category_id', ReportTopCustomer::CUS_TYPE_KHDN)
            ->groupBy('month')->orderBy('month')->get();

//        $dataFailed = $dataFailedClone->where('transaction_status', ReportTransactionStatus::TRANS_STATUS_FAILED)
//            ->groupBy('month')->orderBy('month')->get();
        $resultCountSuccess = $dataSuccess->pluck('sumTotalCount', 'month');
        $resultAmountSuccess = $dataSuccess->pluck('sumTotalAmount', 'month');

        $arrCountSuccess = [];
        $arrAmountSuccess = [];
        $totalCountSum = 0;
        $totalAmountSum = 0;
//        $resultCountFailed = $dataFailed->pluck('sumTotalCount', 'month');
//        $resultAmountFailed = $dataFailed->pluck('sumTotalAmount', 'month');
        $arrCountFailed = [];
        $arrAmountFailed = [];
        $totalCountSumFailed = 0;
        $totalAmountSumFailed = 0;
        for ($i = 1; $i <= 12; $i++) {
            $arrCountSuccess[] = $resultCountSuccess[$i] ?? 0;
            $arrAmountSuccess[] = $resultAmountSuccess[$i] ?? 0;
            $totalCountSum += $resultCountSuccess[$i] ?? 0;
            $totalAmountSum += $resultAmountSuccess[$i] ?? 0;
            $arrCountFailed[] = 0;
            $arrAmountFailed[] = 0;
            $totalCountSumFailed += 0;
            $totalAmountSumFailed +=  0;

        }

        $arrAmountSuccess[] = $totalAmountSum;
        $arrCountSuccess[] = $totalCountSum;

        $arrCountFailed[] = $totalCountSumFailed;
        $arrAmountFailed[] = $totalAmountSumFailed;

        $block->push($arrCountSuccess);
        $block->push($arrAmountSuccess);
//        $block->push($arrCountFailed);
//        $block->push($arrAmountFailed);

        $dataByStatus = ReportTopCustomer::query()->selectRaw("
                transaction_type_id, month,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount")->where('customer_category_id', ReportTopCustomer::CUS_TYPE_KHDN);
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
                if ($req->months) {
                    $dataByStatus->whereIn('month', $req->months);
                }
            }
        }

        $dataDepositClone = clone ($dataByStatus);
        $dataWithdrawClone = clone ($dataByStatus);
        $dataPaymentTransferClone = clone ($dataByStatus);
        $dataDeposit = $dataDepositClone->where('transaction_type_id', ReportTransactionType::TRANS_TYPE_DEPOSIT)
            ->groupBy('month')->orderBy('month')->get();

        $dataWithdraw = $dataWithdrawClone->where('transaction_type_id', ReportTransactionType::TRANS_TYPE_WITHDRAW)
            ->groupBy('month')->orderBy('month')->get();
        $dataPaymentTransfer = $dataPaymentTransferClone
            ->whereIn('transaction_type_id', [ReportTransactionType::TRANS_TYPE_PAYMENT, ReportTransactionType::TRANS_TYPE_TRANSFER])
            ->groupBy('month')->orderBy('month')->get();
        $dataDepositCount = $dataDeposit->pluck('sumTotalCount', 'month');
        $dataDepositAmount = $dataDeposit->pluck('sumTotalAmount', 'month');

        $arrDataDepositCount = [];
        $arrDataDepositAmount = [];
        $totalDepositCount = 0;
        $totalDepositAmount = 0;

        $dataWithdrawCount = $dataWithdraw->pluck('sumTotalCount', 'month');
        $dataWithdrawAmount = $dataWithdraw->pluck('sumTotalAmount', 'month');

        $arrDataWithdrawCount = [];
        $arrDataWithdrawAmount = [];
        $totalWithdrawCount = 0;
        $totalWithdrawAmount = 0;

        $dataPaymentTransferCount = $dataPaymentTransfer->pluck('sumTotalCount', 'month');
        $dataPaymentTransferAmount = $dataPaymentTransfer->pluck('sumTotalAmount', 'month');

        $arrDataPaymentTransferCount = [];
        $arrDataPaymentTransferAmount = [];
        $totalPaymentTransferCount = 0;
        $totalPaymentTransferAmount = 0;

        for ($i = 1; $i <= 12; $i++) {
            $arrDataDepositCount[] = $dataDepositCount[$i] ?? 0;
            $arrDataDepositAmount[] = $dataDepositAmount[$i] ?? 0;
            $totalDepositCount += $dataDepositCount[$i] ?? 0;
            $totalDepositAmount += $dataDepositAmount[$i] ?? 0;
            $arrDataWithdrawCount[] = $dataWithdrawCount[$i] ?? 0;
            $arrDataWithdrawAmount[] = $dataWithdrawAmount[$i] ?? 0;
            $totalWithdrawCount += $dataWithdrawCount[$i] ?? 0;
            $totalWithdrawAmount += $dataWithdrawAmount[$i] ?? 0;
            $arrDataPaymentTransferCount[] = $dataPaymentTransferCount[$i] ?? 0;
            $arrDataPaymentTransferAmount[] = $dataPaymentTransferAmount[$i] ?? 0;
            $totalPaymentTransferCount += $dataPaymentTransferCount[$i] ?? 0;
            $totalPaymentTransferAmount += $dataPaymentTransferAmount[$i] ?? 0;

        }

        $arrDataDepositCount[] = $totalDepositCount;
        $arrDataDepositAmount[] = $totalDepositAmount;

        $arrDataWithdrawCount[] = $totalWithdrawCount;
        $arrDataWithdrawAmount[] = $totalWithdrawAmount;
        $arrDataPaymentTransferCount[] = $totalPaymentTransferCount;
        $arrDataPaymentTransferAmount[] = $totalPaymentTransferAmount;

        $block->push($arrDataDepositCount);
        $block->push($arrDataDepositAmount);
        $block->push($arrDataWithdrawCount);
        $block->push($arrDataWithdrawAmount);
        $block->push($arrDataPaymentTransferCount);
        $block->push($arrDataPaymentTransferAmount);

        return $block;
    }

    public function getTransactionAcceptedPaymentUnit(Request $req) {
        $block = new BlockAcceptedPaymentUnit();
        $dataByStatus = ReportTopCustomer::query()->selectRaw("customer_category_id, month,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount")->where('customer_category_id', ReportTopCustomer::CUS_TYPE_DVCNTT);
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
                if ($req->months) {
                    $dataByStatus->whereIn('month', $req->months);
                }
            }
        }

        $dataSuccessClone = clone ($dataByStatus);
        $dataFailedClone = clone ($dataByStatus);
        $dataSuccess = $dataSuccessClone->groupBy('month')->orderBy('month')->get();

//        $dataFailed = $dataFailedClone->where('transaction_status', ReportTransactionStatus::TRANS_STATUS_FAILED)
//            ->groupBy('month')->orderBy('month')->get();
        $resultCountSuccess = $dataSuccess->pluck('sumTotalCount', 'month');
        $resultAmountSuccess = $dataSuccess->pluck('sumTotalAmount', 'month');

        $arrCountSuccess = [];
        $arrAmountSuccess = [];
        $totalCountSum = 0;
        $totalAmountSum = 0;
//        $resultCountFailed = $dataFailed->pluck('sumTotalCount', 'month');
//        $resultAmountFailed = $dataFailed->pluck('sumTotalAmount', 'month');
        $arrCountFailed = [];
        $arrAmountFailed = [];
        $totalCountSumFailed = 0;
        $totalAmountSumFailed = 0;
        for ($i = 1; $i <= 12; $i++) {
            $arrCountSuccess[] = $resultCountSuccess[$i] ?? 0;
            $arrAmountSuccess[] = $resultAmountSuccess[$i] ?? 0;
            $totalCountSum += $resultCountSuccess[$i] ?? 0;
            $totalAmountSum += $resultAmountSuccess[$i] ?? 0;
            $arrCountFailed[] = 0;
            $arrAmountFailed[] = 0;
            $totalCountSumFailed += 0;
            $totalAmountSumFailed +=  0;

        }

        $arrAmountSuccess[] = $totalAmountSum;
        $arrCountSuccess[] = $totalCountSum;

        $arrCountFailed[] = $totalCountSumFailed;
        $arrAmountFailed[] = $totalAmountSumFailed;

        $block->push($arrCountSuccess);
        $block->push($arrAmountSuccess);
//        $block->push($arrCountFailed);
//        $block->push($arrAmountFailed);

        $dataByStatus = ReportTopCustomer::query()->selectRaw("
                transaction_type_id, month,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount")->where('customer_category_id', ReportTopCustomer::CUS_TYPE_DVCNTT);
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $dataByStatus->where('year', $req->year);
                }
                if ($req->months) {
                    $dataByStatus->whereIn('month', $req->months);
                }
            }
        }

        $dataDepositClone = clone ($dataByStatus);
        $dataWithdrawClone = clone ($dataByStatus);
        $dataPaymentTransferClone = clone ($dataByStatus);
        $dataDeposit = $dataDepositClone->where('transaction_type_id', ReportTransactionType::TRANS_TYPE_DEPOSIT)
            ->groupBy('month')->orderBy('month')->get();

        $dataWithdraw = $dataWithdrawClone->where('transaction_type_id', ReportTransactionType::TRANS_TYPE_WITHDRAW)
            ->groupBy('month')->orderBy('month')->get();
        $dataPaymentTransfer = $dataPaymentTransferClone
            ->whereIn('transaction_type_id', [ReportTransactionType::TRANS_TYPE_PAYMENT, ReportTransactionType::TRANS_TYPE_TRANSFER])
            ->groupBy('month')->orderBy('month')->get();
        $dataDepositCount = $dataDeposit->pluck('sumTotalCount', 'month');
        $dataDepositAmount = $dataDeposit->pluck('sumTotalAmount', 'month');

        $arrDataDepositCount = [];
        $arrDataDepositAmount = [];
        $totalDepositCount = 0;
        $totalDepositAmount = 0;

        $dataWithdrawCount = $dataWithdraw->pluck('sumTotalCount', 'month');
        $dataWithdrawAmount = $dataWithdraw->pluck('sumTotalAmount', 'month');

        $arrDataWithdrawCount = [];
        $arrDataWithdrawAmount = [];
        $totalWithdrawCount = 0;
        $totalWithdrawAmount = 0;

        $dataPaymentTransferCount = $dataPaymentTransfer->pluck('sumTotalCount', 'month');
        $dataPaymentTransferAmount = $dataPaymentTransfer->pluck('sumTotalAmount', 'month');

        $arrDataPaymentTransferCount = [];
        $arrDataPaymentTransferAmount = [];
        $totalPaymentTransferCount = 0;
        $totalPaymentTransferAmount = 0;

        for ($i = 1; $i <= 12; $i++) {
            $arrDataDepositCount[] = $dataDepositCount[$i] ?? 0;
            $arrDataDepositAmount[] = $dataDepositAmount[$i] ?? 0;
            $totalDepositCount += $dataDepositCount[$i] ?? 0;
            $totalDepositAmount += $dataDepositAmount[$i] ?? 0;
            $arrDataWithdrawCount[] = $dataWithdrawCount[$i] ?? 0;
            $arrDataWithdrawAmount[] = $dataWithdrawAmount[$i] ?? 0;
            $totalWithdrawCount += $dataWithdrawCount[$i] ?? 0;
            $totalWithdrawAmount += $dataWithdrawAmount[$i] ?? 0;
            $arrDataPaymentTransferCount[] = $dataPaymentTransferCount[$i] ?? 0;
            $arrDataPaymentTransferAmount[] = $dataPaymentTransferAmount[$i] ?? 0;
            $totalPaymentTransferCount += $dataPaymentTransferCount[$i] ?? 0;
            $totalPaymentTransferAmount += $dataPaymentTransferAmount[$i] ?? 0;

        }

        $arrDataDepositCount[] = $totalDepositCount;
        $arrDataDepositAmount[] = $totalDepositAmount;

        $arrDataWithdrawCount[] = $totalWithdrawCount;
        $arrDataWithdrawAmount[] = $totalWithdrawAmount;
        $arrDataPaymentTransferCount[] = $totalPaymentTransferCount;
        $arrDataPaymentTransferAmount[] = $totalPaymentTransferAmount;

        $block->push($arrDataDepositCount);
        $block->push($arrDataDepositAmount);
        $block->push($arrDataWithdrawCount);
        $block->push($arrDataWithdrawAmount);
        $block->push($arrDataPaymentTransferCount);
        $block->push($arrDataPaymentTransferAmount);

        return $block;
    }

    public function getTopCustomerEnterpriseCountWithoutAcceptedUnit(Request $req) {
        $block = new BlockTopTransactionCountWithoutAcceptedUnit();
        $data = ReportTopCustomer::query()->selectRaw("
        customer_id, transaction_type_id, customer_name, identity_number,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount")
            ->where('customer_category_id', ReportTopCustomer::CUS_TYPE_KHDN);
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $result = $data->groupBy('customer_id')->groupBy('transaction_type_id')
            ->orderBy('sumTotalCount', 'desc')->limit(1000)->get();

        $transGroups = [];
        $totalCountMap = [];
        $totalAmountMap = [];
        $customerNameMap = [];
        $customerIdentityMap = [];
        $customerIdArr = [];

        $resultAll = [];
        $groupCus = $this->groupBy($result, 'customer_id');
        foreach ($groupCus as $customerId=>$entry) {
            $customerIdArr[] = $customerId;
            $customerNameMap[$customerId] = $entry[0]['customer_name'];
            $customerIdentityMap[$customerId] = $entry[0]['identity_number'];
            $totalCountMap[$customerId] = $this->arraySum($entry, 'sumTotalCount');
            $totalAmountMap[$customerId] = $this->arraySum($entry, 'sumTotalAmount');
            $transGroups[$customerId] = $this->groupByLast($entry, 'transaction_type_id');

        }

        foreach ($customerIdArr as $id) {
            $trans = new \stdClass();
            $trans->customerName = $customerNameMap[$id] ?? '';
            $trans->customerIdentity = $customerIdentityMap[$id] ?? '';
            $trans->value = $transGroups[$id] ?? [];
            $trans->totalCount = $totalCountMap[$id] ?? 0;
            $trans->totalAmount = $totalAmountMap[$id] ?? 0;
            $resultAll[] = $trans;
        }

        usort($resultAll, function ($a, $b) {
            return $b->totalCount - $a->totalCount;
        });
        $arrayVal = array_slice($resultAll, 0, 10);
        foreach ($arrayVal as $result) {
            $block->push([$result->customerName,$result->customerName, $result->customerIdentity,
                $result->value[1]['sumTotalCount'] ?? 0, $result->value[1]['sumTotalAmount'] ?? 0,
                $result->value[2]['sumTotalCount'] ?? 0, $result->value[2]['sumTotalAmount'] ?? 0,
                ($result->value[3]['sumTotalCount'] ?? 0) + ($result->value[4]['sumTotalCount'] ?? 0),
                ($result->value[3]['sumTotalAmount'] ?? 0) + ($result->value[4]['sumTotalAmount'] ?? 0),
                $result->value[5]['sumTotalCount'] ?? 0, $result->value[5]['sumTotalAmount'] ?? 0,
                $result->totalCount, $result->totalAmount]);

        }

        return $block;
    }

    public function getTopCustomerEnterpriseAmountWithoutAcceptedUnit(Request $req) {
        $block = new BlockTopTransactionAmountWithoutAcceptedUnit();
        $data = ReportTopCustomer::query()->selectRaw("
        customer_id, transaction_type_id, customer_name, identity_number,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount")
            ->where('customer_category_id', ReportTopCustomer::CUS_TYPE_KHDN);
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $result = $data->groupBy('customer_id')->groupBy('transaction_type_id')
            ->orderBy('sumTotalAmount', 'desc')->limit(1000)->get();

        $transGroups = [];
        $totalCountMap = [];
        $totalAmountMap = [];
        $customerNameMap = [];
        $customerIdentityMap = [];
        $customerIdArr = [];

        $resultAll = [];
        $groupCus = $this->groupBy($result, 'customer_id');
        foreach ($groupCus as $customerId=>$entry) {
            $customerIdArr[] = $customerId;
            $customerNameMap[$customerId] = $entry[0]['customer_name'];
            $customerIdentityMap[$customerId] = $entry[0]['identity_number'];
            $totalCountMap[$customerId] = $this->arraySum($entry, 'sumTotalCount');
            $totalAmountMap[$customerId] = $this->arraySum($entry, 'sumTotalAmount');
            $transGroups[$customerId] = $this->groupByLast($entry, 'transaction_type_id');

        }

        foreach ($customerIdArr as $id) {
            $trans = new \stdClass();
            $trans->customerName = $customerNameMap[$id] ?? '';
            $trans->customerIdentity = $customerIdentityMap[$id] ?? '';
            $trans->value = $transGroups[$id] ?? [];
            $trans->totalCount = $totalCountMap[$id] ?? 0;
            $trans->totalAmount = $totalAmountMap[$id] ?? 0;
            $resultAll[] = $trans;
        }

        usort($resultAll, function ($a, $b) {
            return $b->totalAmount - $a->totalAmount;
        });
        $arrayVal = array_slice($resultAll, 0, 10);
        foreach ($arrayVal as $result) {
            $block->push([$result->customerName,$result->customerName, $result->customerIdentity,
                $result->value[1]['sumTotalCount'] ?? 0, $result->value[1]['sumTotalAmount'] ?? 0,
                $result->value[2]['sumTotalCount'] ?? 0, $result->value[2]['sumTotalAmount'] ?? 0,
                ($result->value[3]['sumTotalCount'] ?? 0) + ($result->value[4]['sumTotalCount'] ?? 0),
                ($result->value[3]['sumTotalAmount'] ?? 0) + ($result->value[4]['sumTotalAmount'] ?? 0),
                $result->value[5]['sumTotalCount'] ?? 0, $result->value[5]['sumTotalAmount'] ?? 0,
                $result->totalCount, $result->totalAmount]);

        }

        return $block;
    }

    public function getTopCustomerPersonCountWithoutAcceptedUnit(Request $req) {
        $block = new BlockTopCustomerCountWithoutAcceptedUnit();
        $data = ReportTopCustomer::query()->selectRaw("
        customer_id, transaction_type_id, customer_name, identity_number,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount")
            ->where('customer_category_id', ReportTopCustomer::CUS_TYPE_KHCN);
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $result = $data->groupBy('customer_id')->groupBy('transaction_type_id')
            ->orderBy('sumTotalCount', 'desc')->limit(1000)->get();

        $transGroups = [];
        $totalCountMap = [];
        $totalAmountMap = [];
        $customerNameMap = [];
        $customerIdentityMap = [];
        $customerIdArr = [];

        $resultAll = [];
        $groupCus = $this->groupBy($result, 'customer_id');
        foreach ($groupCus as $customerId=>$entry) {
            $customerIdArr[] = $customerId;
            $customerNameMap[$customerId] = $entry[0]['customer_name'];
            $customerIdentityMap[$customerId] = $entry[0]['identity_number'];
            $totalCountMap[$customerId] = $this->arraySum($entry, 'sumTotalCount');
            $totalAmountMap[$customerId] = $this->arraySum($entry, 'sumTotalAmount');
            $transGroups[$customerId] = $this->groupByLast($entry, 'transaction_type_id');

        }

        foreach ($customerIdArr as $id) {
            $trans = new \stdClass();
            $trans->customerName = $customerNameMap[$id] ?? '';
            $trans->customerIdentity = $customerIdentityMap[$id] ?? '';
            $trans->value = $transGroups[$id] ?? [];
            $trans->totalCount = $totalCountMap[$id] ?? 0;
            $trans->totalAmount = $totalAmountMap[$id] ?? 0;
            $resultAll[] = $trans;
        }

        usort($resultAll, function ($a, $b) {
            return $b->totalCount - $a->totalCount;
        });
        $arrayVal = array_slice($resultAll, 0, 10);
        foreach ($arrayVal as $result) {
            $block->push([$result->customerName,$result->customerName, $result->customerIdentity,
                $result->value[1]['sumTotalCount'] ?? 0, $result->value[1]['sumTotalAmount'] ?? 0,
                $result->value[2]['sumTotalCount'] ?? 0, $result->value[2]['sumTotalAmount'] ?? 0,
                ($result->value[3]['sumTotalCount'] ?? 0) + ($result->value[4]['sumTotalCount'] ?? 0),
                ($result->value[3]['sumTotalAmount'] ?? 0) + ($result->value[4]['sumTotalAmount'] ?? 0),
                $result->value[5]['sumTotalCount'] ?? 0, $result->value[5]['sumTotalAmount'] ?? 0,
                $result->totalCount, $result->totalAmount]);

        }

        return $block;
    }

    public function getTopCustomerPersonAmountWithoutAcceptedUnit(Request $req) {
        $block = new BlockTopTransactionPersonAmountWithoutAcceptedUnit();
        $data = ReportTopCustomer::query()->selectRaw("
        customer_id, transaction_type_id, customer_name, identity_number,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount")
            ->where('customer_category_id', ReportTopCustomer::CUS_TYPE_KHCN);
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $result = $data->groupBy('customer_id')->groupBy('transaction_type_id')
            ->orderBy('sumTotalAmount', 'desc')->limit(1000)->get();

        $transGroups = [];
        $totalCountMap = [];
        $totalAmountMap = [];
        $customerNameMap = [];
        $customerIdentityMap = [];
        $customerIdArr = [];

        $resultAll = [];
        $groupCus = $this->groupBy($result, 'customer_id');
        foreach ($groupCus as $customerId=>$entry) {
            $customerIdArr[] = $customerId;
            $customerNameMap[$customerId] = $entry[0]['customer_name'];
            $customerIdentityMap[$customerId] = $entry[0]['identity_number'];
            $totalCountMap[$customerId] = $this->arraySum($entry, 'sumTotalCount');
            $totalAmountMap[$customerId] = $this->arraySum($entry, 'sumTotalAmount');
            $transGroups[$customerId] = $this->groupByLast($entry, 'transaction_type_id');

        }

        foreach ($customerIdArr as $id) {
            $trans = new \stdClass();
            $trans->customerName = $customerNameMap[$id] ?? '';
            $trans->customerIdentity = $customerIdentityMap[$id] ?? '';
            $trans->value = $transGroups[$id] ?? [];
            $trans->totalCount = $totalCountMap[$id] ?? 0;
            $trans->totalAmount = $totalAmountMap[$id] ?? 0;
            $resultAll[] = $trans;
        }

        usort($resultAll, function ($a, $b) {
            return $b->totalAmount - $a->totalAmount;
        });
        $arrayVal = array_slice($resultAll, 0, 10);
        foreach ($arrayVal as $result) {
            $block->push([$result->customerName,$result->customerName, $result->customerIdentity,
                $result->value[1]['sumTotalCount'] ?? 0, $result->value[1]['sumTotalAmount'] ?? 0,
                $result->value[2]['sumTotalCount'] ?? 0, $result->value[2]['sumTotalAmount'] ?? 0,
                ($result->value[3]['sumTotalCount'] ?? 0) + ($result->value[4]['sumTotalCount'] ?? 0),
                ($result->value[3]['sumTotalAmount'] ?? 0) + ($result->value[4]['sumTotalAmount'] ?? 0),
                $result->value[5]['sumTotalCount'] ?? 0, $result->value[5]['sumTotalAmount'] ?? 0,
                $result->totalCount, $result->totalAmount]);

        }

        return $block;
    }

    public function getTopCustomerPaymentAcceptedUnitCount(Request $req) {
        $block = new BlockTopCountTransactionAcceptedUnit();
        $data = ReportTopCustomer::query()->selectRaw("
        customer_id, transaction_type_id, customer_name, identity_number,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount")
            ->where('customer_category_id', ReportTopCustomer::CUS_TYPE_DVCNTT);
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $result = $data->groupBy('customer_id')->groupBy('transaction_type_id')
            ->orderBy('sumTotalCount', 'desc')->limit(1000)->get();

        $transGroups = [];
        $totalCountMap = [];
        $totalAmountMap = [];
        $customerNameMap = [];
        $customerIdentityMap = [];
        $customerIdArr = [];

        $resultAll = [];
        $groupCus = $this->groupBy($result, 'customer_id');
        foreach ($groupCus as $customerId=>$entry) {
            $customerIdArr[] = $customerId;
            $customerNameMap[$customerId] = $entry[0]['customer_name'];
            $customerIdentityMap[$customerId] = $entry[0]['identity_number'];
            $totalCountMap[$customerId] = $this->arraySum($entry, 'sumTotalCount');
            $totalAmountMap[$customerId] = $this->arraySum($entry, 'sumTotalAmount');
            $transGroups[$customerId] = $this->groupByLast($entry, 'transaction_type_id');

        }

        foreach ($customerIdArr as $id) {
            $trans = new \stdClass();
            $trans->customerName = $customerNameMap[$id] ?? '';
            $trans->customerIdentity = $customerIdentityMap[$id] ?? '';
            $trans->value = $transGroups[$id] ?? [];
            $trans->totalCount = $totalCountMap[$id] ?? 0;
            $trans->totalAmount = $totalAmountMap[$id] ?? 0;
            $resultAll[] = $trans;
        }

        usort($resultAll, function ($a, $b) {
            return $b->totalCount - $a->totalCount;
        });
        $arrayVal = array_slice($resultAll, 0, 10);
        foreach ($arrayVal as $result) {
            $block->push([$result->customerName,$result->customerName, $result->customerIdentity,
                $result->value[1]['sumTotalCount'] ?? 0, $result->value[1]['sumTotalAmount'] ?? 0,
                $result->value[2]['sumTotalCount'] ?? 0, $result->value[2]['sumTotalAmount'] ?? 0,
                ($result->value[3]['sumTotalCount'] ?? 0) + ($result->value[4]['sumTotalCount'] ?? 0),
                ($result->value[3]['sumTotalAmount'] ?? 0) + ($result->value[4]['sumTotalAmount'] ?? 0),
                $result->value[5]['sumTotalCount'] ?? 0, $result->value[5]['sumTotalAmount'] ?? 0,
                $result->totalCount, $result->totalAmount]);

        }

        return $block;
    }

    public function getTopCustomerPaymentAcceptedUnitAmount(Request $req) {
        $block = new BlockTopAmountTransactionAcceptedUnit();
        $data = ReportTopCustomer::query()->selectRaw("
        customer_id, transaction_type_id, customer_name, identity_number,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount")
            ->where('customer_category_id', ReportTopCustomer::CUS_TYPE_DVCNTT);
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $result = $data->groupBy('customer_id')->groupBy('transaction_type_id')
            ->orderBy('sumTotalAmount', 'desc')->limit(1000)->get();

        $transGroups = [];
        $totalCountMap = [];
        $totalAmountMap = [];
        $customerNameMap = [];
        $customerIdentityMap = [];
        $customerIdArr = [];

        $resultAll = [];
        $groupCus = $this->groupBy($result, 'customer_id');
        foreach ($groupCus as $customerId=>$entry) {
            $customerIdArr[] = $customerId;
            $customerNameMap[$customerId] = $entry[0]['customer_name'];
            $customerIdentityMap[$customerId] = $entry[0]['identity_number'];
            $totalCountMap[$customerId] = $this->arraySum($entry, 'sumTotalCount');
            $totalAmountMap[$customerId] = $this->arraySum($entry, 'sumTotalAmount');
            $transGroups[$customerId] = $this->groupByLast($entry, 'transaction_type_id');

        }

        foreach ($customerIdArr as $id) {
            $trans = new \stdClass();
            $trans->customerName = $customerNameMap[$id] ?? '';
            $trans->customerIdentity = $customerIdentityMap[$id] ?? '';
            $trans->value = $transGroups[$id] ?? [];
            $trans->totalCount = $totalCountMap[$id] ?? 0;
            $trans->totalAmount = $totalAmountMap[$id] ?? 0;
            $resultAll[] = $trans;
        }

        usort($resultAll, function ($a, $b) {
            return $b->totalAmount - $a->totalAmount;
        });
        $arrayVal = array_slice($resultAll, 0, 10);
        foreach ($arrayVal as $result) {
            $block->push([$result->customerName,$result->customerName, $result->customerIdentity,
                $result->value[1]['sumTotalCount'] ?? 0, $result->value[1]['sumTotalAmount'] ?? 0,
                $result->value[2]['sumTotalCount'] ?? 0, $result->value[2]['sumTotalAmount'] ?? 0,
                ($result->value[3]['sumTotalCount'] ?? 0) + ($result->value[4]['sumTotalCount'] ?? 0),
                ($result->value[3]['sumTotalAmount'] ?? 0) + ($result->value[4]['sumTotalAmount'] ?? 0),
                $result->value[5]['sumTotalCount'] ?? 0, $result->value[5]['sumTotalAmount'] ?? 0,
                $result->totalCount, $result->totalAmount]);

        }

        return $block;
    }

    public function getCustomerByEWL(Request $req) {
        $block = new BlockOtherPartnerWallet();
        $data = ReportWalletStatus::query()->selectRaw("
        wallet_type_id, wallet_status,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount");
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $result = $data->groupBy('wallet_type_id')->limit(1000)->get();

        $totalCountMap = [];
        $totalAmountMap = [];

        $groupCus = $this->groupBy($result, 'wallet_type_id');
        foreach ($groupCus as $tranTypeId=>$entry) {
            $totalCountMap[$tranTypeId] = $this->arraySum($entry, 'sumTotalCount');
            $totalAmountMap[$tranTypeId] = $this->arraySum($entry, 'sumTotalAmount');

        }

        $block->push([$totalCountMap[3] ?? '-', $totalCountMap[3] ?? '-', $totalAmountMap[3] ?? '-']);
        $block->push([$totalCountMap[1] ?? '-', $totalCountMap[1] ?? '-', $totalAmountMap[1] ?? '-']);
        $block->push([$totalCountMap[2] ?? '-', $totalCountMap[2] ?? '-', $totalAmountMap[2] ?? '-']);

        return $block;
    }


    public function groupByLast($entries, $key) {
        $groups = [];
        foreach ($entries as $entry) {
            $keyVal = $entry[$key];
            if (isset($keyVal)) {
                $groups[$keyVal] = $entry;
            }
        }

        return $groups;
    }
    public function arraySum($entries, $key) {

        return array_reduce( $entries, function ($sum, $entry) use ($key) {
            $sum += $entry[$key];
            return $sum;
        }, 0);
    }

    public function groupBy($entries, $key) {
        $groups = [];
        foreach ($entries as $entry) {
            $keyVal = $entry[$key];
            if (isset($keyVal)) {
                $groups[$keyVal][] = $entry;
            }
        }

        return $groups;
    }

    public function getBlockLiquidityRiskWallet(Request $req) {
        $block = new BlockLiquidityRiskWallet();
        $data = SecuredAccountBalance::query()->selectRaw('account_number, GROUP_CONCAT(balance ORDER BY date desc) balances')
            ->groupBy('account_number');

        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $results = $data->get();
        $sum = 0;
        foreach ($results as $d) {
            $arr = explode(',', $d->balances);
            $sum += $arr[0] ?? 0;
        }

        $block->push(['']);
        $block->push([$sum]);


        return $block;

    }

    public function getBlockOtherTargetsWallet(Request $req) {
        $block = new BlockOtherTargetsWallet();
        $data = ReportWalletStatus::query()->selectRaw("
        wallet_type_id, wallet_status,
                SUM(total_count) as sumTotalCount,
                        SUM(total_amount) as sumTotalAmount");
        if (isset($req->type)) {
            if ($req->type == 2) {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
            } else {
                if ($req->year) {
                    $data->where('year', $req->year);
                }
                if ($req->months) {
                    $data->whereIn('month', $req->months);
                }
            }
        }

        $result = $data->groupBy('wallet_type_id')->groupBy('wallet_status')->limit(1000)->get();

        $transGroups = [];
        $totalCountMap = [];
        $totalAmountMap = [];


        $groupCus = $this->groupBy($result, 'wallet_type_id');
        foreach ($groupCus as $tranTypeId=>$entry) {
            $totalCountMap[$tranTypeId] = $this->arraySum($entry, 'sumTotalCount');
            $totalAmountMap[$tranTypeId] = $this->arraySum($entry, 'sumTotalAmount');
            $transGroups[$tranTypeId] = $this->groupByLast($entry, 'wallet_status');

        }

        $totalEWBalances = ($totalAmountMap[1] ?? 0) + ($totalAmountMap[2] ?? 0) + ($totalAmountMap[3] ?? 0);
        $block->push([$totalEWBalances]);

        $grEWStatusPerson = $transGroups[1] ?? [];
        $grEWStatusCompany = $transGroups[2] ?? [];
        $grEWStatusCPOB = $transGroups[3] ?? [];


        $countEWStatusPublish = ($grEWStatusPerson[1]['sumTotalCount'] ?? 0) + ($grEWStatusCompany[1]['sumTotalCount'] ?? 0) + ($grEWStatusCPOB[1]['sumTotalCount'] ?? 0);
        $countEWStatusActivated = ($grEWStatusPerson[2]['sumTotalCount'] ?? 0) + ($grEWStatusCompany[2]['sumTotalCount'] ?? 0) + ($grEWStatusCPOB[2]['sumTotalCount'] ?? 0);
        $countEWStatusActive = ($grEWStatusPerson[3]['sumTotalCount'] ?? 0) + ($grEWStatusCompany[3]['sumTotalCount'] ?? 0) + ($grEWStatusCPOB[3]['sumTotalCount'] ?? 0);

        $block->push(['']);
        $block->push([$countEWStatusPublish]);
        $block->push([$countEWStatusActivated]);
        $block->push([$countEWStatusActive]);

        return $block;

    }

}
