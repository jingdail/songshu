<?php
//多多客配置
$pdd_config = array(
    'client_id' => '239ae12adeab41b3becff5e89c661331', //client_id
    'client_secret' => '8d9de04e910c8009c5714a29801c0299dce05036', //client_secret
    'backurl' => 'http://hubangtuan.cn/pddapi/auto.php', //回调地址
    'data_type' => 'json', // 返回数据格式
    'pdd_token_file' => dirname(__FILE__) . '/cache/pdd_token.txt', // token存储文件地址
);

//松鼠线报公众号配置
$mp_config = [
    'app_id' => 'wxefca75d11f95bab4',
    'secret' => '01eeb6cc255ee06ff074d25d4089b028',
    'token'   => 'songshuxianbao',          // Token
    'aes_key' => 'PfZZ3rwAAlPp2wUW0DcRNfWrKZvcd9sZ4ftCYoPOvDs',
     //指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
    'response_type' => 'array',
];

$withdraw = "提现";
$rebate = "返利";
$no_goods_back_url = "该商品暂无更多优惠，可在原链接购买";
$no_promotion= "该商品暂无优惠";
$promotion = "券后价";
$line="\r\n-------点击下方链接购买-------\r\n";
$sales_tip = "已售";
$default = "先领券，再下单。省钱不止一点点！";
//关注回复
$subscribe_message = <<<data
你好，亲，有什么疑问需要解答
————————————
松鼠线报：先领券，再下单，省钱不止一点点！不定期发放福利
微信客服：xiaobin987
支持商品链接和商品标题搜索
data;

//没有搜索到商品回复
$no_goods_message = "该商品暂无更多优惠，可在原链接购买\r\n客服微信：xiaobin987";



$reply_message = array(
    "subscribe_message"   =>$subscribe_message,
    // "$no_merce_messageessage"    =>$no_merce_message,
    "withdraw"=>$withdraw,
    "rebate"=>$rebate,
    "no_goods_back_url"=>$no_goods_back_url,
    "no_promotion"=>$no_promotion,
    "promotion"=>$promotion,
    "line"=>$line,
    "sales_tip"=>$sales_tip,
    "default"=>$default
);
/*
 * //
// https://mobile.yangkeduo.com/goods2.html?goods_id=
// http://hubangtuan.cn/pddapi/server.php
// ERC6CJ1W85 zhang0000@
// Token songshuxianbao
// EncodingAESKey PfZZ3rwAAlPp2wUW0DcRNfWrKZvcd9sZ4ftCYoPOvDs
// 9T2lCO
 *
 * */
/*
 * 公用函数
 * */

//友好格式化
function num2tring($num) {
    if ($num >= 10000) {
        $num = round($num / 10000 * 100) / 100 .'万';
    }
    return $num;
}
//coupon_discount = 0

//错误处理
function ssd(){

    return "cp";


//    if($detail["coupon_discount"]==0){
////        return $reply_message["no_promotion"];
//        return "s";
//    }


}
//格式化
function formatString($detail,$reply_message){

    $price = $detail['min_group_price']-$detail['coupon_discount'];
    $real_price = round($price/100,2);
    $fanli = round(($detail['promotion_rate']/1000)*$real_price,2);
    $body = $detail['goods_name']."\r\n---------------------------------\r\n";
    $body .= $reply_message['promotion']."：￥".round($price/100,2)."\r\n";
    // $body .= "优惠券：￥".$detail["coupon_discount"]."\r\n";
    // $body .= "原价：￥".($detail['min_group_price']/100)."\r\n";
    $body .= "省：￥".round(($detail['min_group_price']-$price)/100,2)."\r\n";
    $body .= $reply_message['sales_tip']."：".num2tring($detail['sales_tip'])."件\r\n";
    $body .= "返利：￥".round(($fanli*0.3),2)."\r\n";
    $body .= "\r\n-------".$reply_message['line']."-------\r\n";

    return $body;
}
?>