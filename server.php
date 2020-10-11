<?php
include './vendor/autoload.php';
include './config.php';
use EasyWeChat\Factory;
use Pdd\Api;
use Medoo\Medoo;


//初始化pdd、mp
$pddx = new Api($pdd_config);
$app = Factory::officialAccount($mp_config);
$database = new Medoo([
   'database_type' => 'sqlite',
   'database_file'=>'./songshuxianbao.db'
]);


$app->server->push(function($message) use($pddx,$reply_message,$database){
    //openid

    /*关注*/
    if ($message['MsgType']=='event'){
        if ($message['Event']=='subscribe'){
            $rep_msg = $reply_message['subscribe_message'];
            return $rep_msg;
        }
    }

    //关键词
    $word = $message['Content'];
   
    if($word==$reply_message["withdraw"]){
        return "请添加客服微信:\r\n\r\nxiaobin987";
        
    }
     if($word==$reply_message['rebate']){
        $qian = $database->select("orders", "promotion_amount", [
            "openid" => $message['FromUserName'],
            "order_status"=>5
        ]);
        if($qian == NULL){
            return "暂无返利";
        }else{
            $total_qian = round((array_sum($qian)/100)*0.3,2);
            return "您的返利额度:¥".$total_qian."\r\n回复[提现]\r\n将获得返利。";
        }
        
    }

    /*链接搜索*/
    if(filter_var($word, FILTER_VALIDATE_URL) !== false){

        $url = parse_url($word)['query'];
        parse_str($url,$arrurl);
        $gid = $arrurl['goods_id'];
        $josn = $pddx->pdd_ddk_goods_detail(array('goods_id_list'=>"[$gid]"));

        if(strpos($josn,'error_response')!==false){return $reply_message["no_goods_back_url"];}

        $detail = json_decode($josn,true)['goods_detail_response']['goods_details'][0];
        $body = formatString($detail,$reply_message);

        $openid = $message['FromUserName'];
        $param = array(
            'p_id'                => '13375587_156487163',
            'goods_id_list'       => "[$gid]",
            'custom_parameters'   => '{"uid":"'.$openid.'"}'
        );
        $p = $pddx->pdd_ddk_goods_promotion_url_generate($param);
        $arr = json_decode($p,true)['goods_promotion_url_generate_response']['goods_promotion_url_list'];

        $body .= $arr[0]['mobile_short_url'];
        return  $body;

    }

    //标题搜索
    /*
    if(filter_var($word, FILTER_VALIDATE_URL) === false){

        $k = array(
            'page'=>1,
            'page_size'=>10,
            'with_coupon'=>true,
            'keyword'=>$word
        );
        $json = $pddx->pdd_ddk_goods_search($k);
        if(strpos($josn,'error_response')!==false){return $reply_message["no_goods_back_url"];}

        $detail = json_decode($json,true)['goods_search_response']['goods_list'];
        $body = '为您推荐以下商品'."\r\n";
        $num = 1;
        foreach ($detail as $key => $value) {
            $body .= "----$num----\r\n";
            $price = $value['min_group_price']-$value['coupon_discount'];
            // $fanli = round(($value['promotion_rate']/1000)*$price,2));
            //."\r\n---------------------------------\r\n"
            $body .= $value['goods_name']."\r\n";

            $body .= "券后价：￥".round($price/100,2)."\r\n";
            $body .= "省：￥".round(($value['min_group_price']-$price)/100,2)."\r\n";
            $body .= "已售：".num2tring($value['sales_tip'])."件\r\n";
            // $body .= "可得返利：$".$fanli;
            $gid = $value['goods_id'];
            $openid = $message['FromUserName'];
            $param = array(
            'p_id'                =>'13375587_156487163',
            'goods_id_list'       => "[$gid]",
            'custom_parameters'   => '{"uid":"'.$openid.'"}'
            );

            $p = $pddx->pdd_ddk_goods_promotion_url_generate($param);
            $arr = json_decode($p,true)['goods_promotion_url_generate_response']['goods_promotion_url_list'];
            $body .= $arr[0]['mobile_short_url'];
            $body .= "\r\n\r\n";
            $num++;

        }
        $body .= '为您推荐以上商品';
        return $body;
    }*/
    return "\r\n".$reply_message["default"];
});


$response = $app->server->serve();
// 将响应输出
$response->send();exit;
?>