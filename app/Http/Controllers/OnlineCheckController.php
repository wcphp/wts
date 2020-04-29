<?php
/**
 *
 * User: weika <iweika@wcphp.com>
 * Date: 2019-08-07
 * Time: 11:07
 */

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;

class OnlineCheckController extends Controller
{
    /**
     * 查询线上重复支付数据
     * @param mixed
     * @param boolean
     * @param string
     * @param array
     * @return void
     */
    public function check()
    {

//订单号   付款时间   第几期  金额  流水
        $res = DB::table('lea_order_trans_flow')->where('pay_status','=',2)->select('order_no','extra','out_trade_no','pay_time')->get();
        $res = $res->toArray();

        $arr=[];
        foreach($res as $value){

            if(!empty($value->extra)){
                $extra = json_decode($value->extra,true);

                if(!empty($extra) && is_array($extra)){

                    foreach ($extra as $v){
                        if(isset($v['order_lease_id'])){
                            $v['out_trade_no'] = $value->out_trade_no;
                            $v['pay_time'] = $value->pay_time;
                            $v['order_no'] = $value->order_no;
                            if(isset($arr[$v['order_lease_id']])){
                                $arr[$v['order_lease_id']]['num']  =  $arr[$v['order_lease_id']]['num'] + 1;
                                $arr[$v['order_lease_id']]['data'][] = $v;
                            }else{
                                $arr[$v['order_lease_id']] = ['data'=>[$v],'num'=>1];
                            }
                        }
                    }

                }

            }

        }
        $manyArray = [];
        foreach ($arr as $key=>$val){
            if($val['num']>1){
                foreach($val['data'] as $v){
                    echo  '订单号：'. (empty($v['order_no']) ? '':$v['order_no'] ) .'  第'.(empty($v['order_lease']) ? 0 : $v['order_lease']) .'期  '.' 支付流水号：'.(empty($v['out_trade_no']) ? '':$v['out_trade_no'] ) .'  支付金额：'.(empty($v['overdue_amount']) ? '':$v['overdue_amount'] ).' 付款完成时间： '.(empty($v['pay_time']) ? '':$v['pay_time'] );
                    echo '<br>';
                }
            }
        }



    }

}