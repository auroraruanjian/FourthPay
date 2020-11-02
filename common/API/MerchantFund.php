<?php
namespace Common\API;

use Common\Models\Orders;
use Common\Models\OrderType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MerchantFund
{
    public static $error_msg = "数据更新失败";
    public static $order_type = [];


    /**
     * 商户账号资金变动以及生成账变记录
     * @param Orders $order
     * @param string $order_type
     * @param boolean $allow_minus 是否允许负数
     * @throws \Exception
     * @return boolean 是否成功
     */
    public static function modifyFund(Orders $order, $order_type_ident, $allow_minus = false)
    {
        if ($order->amount <= 0.0) {
            self::$error_msg = "账变为负数或者 0 !";
            return false;
        }

        if (App()->runningInConsole()) {
            if (isset(self::$order_type[$order_type_ident])) {
                $order_type = self::$order_type[$order_type_ident];
            } else {
                $order_type = self::$order_type[$order_type_ident] = OrderType::where(
                    'ident',
                    $order_type_ident
                )->first();
            }
        } else {
            $order_type = Cache::store('apc')->remember(
                'OrderType::' . $order_type_ident,
                1,
                function () use ($order_type_ident) {
                    return OrderType::where('ident', $order_type_ident)->first();
                }
            );
        }

        if (empty($order_type)) {
            self::$error_msg = "账变类型错误!";
            return false;
        }

        DB::beginTransaction();

        try {
            $merchant_fund = \Common\Models\MerchantFund::where('merchant_id', $order->from_merchant_id)
                ->lockForUpdate()
                ->first(['balance', 'hold_balance']);
        } catch (\Exception $e) {
            DB::rollBack();
            self::$error_msg = $e->getMessage();
            return false;
        }

        $order->pre_balance = $merchant_fund->balance;
        $order->pre_hold_balance = $merchant_fund->hold_balance;
        $order->order_type_id = $order_type->id;
        $order->balance = $merchant_fund->balance;
        $order->hold_balance = $merchant_fund->hold_balance;
        if ($order_type->operation == 1) {
            $affected = DB::update(
                "update merchant_fund set balance = balance + :amount where merchant_id = :merchant_id",
                [
                    'amount'    => $order->amount,
                    'merchant_id'   => $order->from_merchant_id
                ]
            );

            if (empty($affected)) {
                DB::rollBack();
                self::$error_msg = "加款操作失败!";
                return false;
            }
            $order->balance = $merchant_fund->balance + $order->amount;
        } elseif ($order_type->operation == 2) {
            $affected = DB::select(
                "update merchant_fund set balance = balance - :amount where merchant_id = :merchant_id RETURNING balance",
                [
                    'amount'        => $order->amount,
                    'merchant_id'   => $order->from_merchant_id
                ]
            );

            if (!count($affected) || (!$allow_minus && $affected[0]->balance < 0)) {
                self::$error_msg = "数据异常，账户余额不足!";
                DB::rollBack();
                return false;
            }
            $order->balance = $merchant_fund->balance - $order->amount;
        }

        if ($order_type->hold_operation == 1) {
            $affected = DB::update(
                "update merchant_fund set hold_balance = hold_balance + :amount where merchant_id = :merchant_id",
                [
                    'amount'        => $order->amount,
                    'merchant_id'   => $order->from_merchant_id
                ]
            );

            if (empty($affected)) {
                self::$error_msg = "冻结加款失败!";
                DB::rollBack();
                return false;
            }
            $order->hold_balance = $merchant_fund->hold_balance + $order->amount;
        } elseif ($order_type->hold_operation == 2) {
            $affected = DB::select(
                "update merchant_fund set hold_balance = hold_balance - :amount where merchant_id = :merchant_id RETURNING hold_balance",
                [
                    'amount'        => $order->amount,
                    'merchant_id'   => $order->from_merchant_id
                ]
            );

            if (!count($affected) || (!$allow_minus && $affected[0]->hold_balance < 0)) {
                self::$error_msg = "数据异常，冻结金额余额不足!";
                DB::rollBack();
                return false;
            }
            $order->hold_balance = $merchant_fund->hold_balance - $order->amount;
        }

        try {
            if (!$order->save()) {
                self::$error_msg = "保存账变记录失败!";
                DB::rollBack();
                return false;
            }
        } catch (\Exception $e) {
            self::$error_msg = "发生异常!";
            \Log::error('账变异常', ['Exception'=>$e]);
            DB::rollBack();
            return false;
        }
        DB::commit();

        return true;
    }
}
