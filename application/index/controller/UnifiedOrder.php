<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/18
 * Time: 15:11
 */

namespace app\index\controller;

/**
 * Class UnifiedOrder
 * @package app\index\controller
 * 统一订单控制器
 */
class UnifiedOrder {

    /**
     * @return mixed
     * @throws \WxPayException
     * http://localhost/ckb/public/index/unified_order
     * 线上测试
     * https://www.easykj.cn/ckb/public/index.php/index/unified_order/index
     *
     */
    public function index(){

        ini_set('date.timezone','Asia/Shanghai');

        require_once APP_ROOT."/wxpay/WxPay.Api.php";
        require_once APP_ROOT."/wxpay/WxPay.JsApiPay.php";
        require_once APP_ROOT."/wxpay/log.php";

        //初始化日志
        //$logHandler= new \CLogFileHandler(APP_ROOT."/logs/".date('Y-m-d').'.log');
        //$log = \Log::Init($logHandler, 15);

        //①、获取用户openid
        $tools = new \JsApiPay();

        // 这里是获取openid的方法，因为授权登录后把用户的openid存起来了，所以这里用不到了
        $openId = $tools->GetOpenid();

        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("test");	//商品描述
        $input->SetAttach("test");	//附加数据
        $input->SetOut_trade_no("sdkphp".date("YmdHis"));	//商户订单号
        $input->SetTotal_fee("1");	//标价金额
        $input->SetTime_start(date("YmdHis"));	//交易起始时间
        $input->SetTime_expire(date("YmdHis", time() + 600));	//交易结束时间
        $input->SetGoods_tag("test");	//订单优惠标记
        $input->SetNotify_url("http://paysdk.weixin.qq.com/notify.php");	//异步回调通知地址
        $input->SetTrade_type("JSAPI");	//交易类型

        // 下面是获取授权登录存入数据库里的openid
        // ......

        $input->SetOpenid($openId);

        $config = new \WxPayConfig();
        $order = \WxPayApi::unifiedOrder($config, $input);
        $jsApiParameters = $tools->GetJsApiParameters($order);

        //获取共享收货地址js函数参数
        $editAddress = $tools->GetEditAddressParameters();

        // 把获得的json数据解析成数组
        $result = json_decode(['jsApiParameters' => $jsApiParameters, 'editAddress' => $editAddress]);

        return $result;

    }

}