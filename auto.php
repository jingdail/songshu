<?php
include './vendor/autoload.php';
include './config.php';
use EasyWeChat\Factory;
use Pdd\Api;
use Medoo\Medoo;
/*
 * 拼多多授权演示
 * @Author: lovefc 
 * @Date: 2019-07-30 09:05:21
 */

// 接口配置
$config = array(
    'client_id' => '239ae12adeab41b3becff5e89c661331', //client_id
    'client_secret' => '8d9de04e910c8009c5714a29801c0299dce05036', //client_secret
    'backurl' => 'http://hubangtuan.cn/pddapi/auto.php', //回调地址
    'data_type' => 'json', // 返回数据格式
    'pdd_token_file' => dirname(__FILE__) . '/cache/pdd_token.txt', // token存储文件地址
);

// 加载公共文件,内含文件加载，无需多行引入


// 实例化参数，有两个，都是数组，第二个参数可以传授权后的token的json字符串，不传会读取token文件，建议授权后使用
$obj = new Api($config);
$pxx = $obj;
//$href = $obj->getHref(); // 授权链接地址


// 这里使用的函数就是拼多多的api接口名
// 参考 https://open.pinduoduo.com/application/document/api?id=pdd.order.list.get
// 注意把api接口名中的点号换成下划线即可，传参请参考文档
//
// $json = $obj->pdd_ddk_goods_search();



//echo '<a href="' . $obj->getHref() . '">商家授权</a><br />';

//echo '<a href="' . $obj->getHref('ddk') . '">多多客授权</a>';

/** 检测有没有code的值，一般这个值是回调地址传过来的，我这里只是展示下使用代码 */
/*
$code = isset($_GET['code']) ? $_GET['code'] : '';

if (!empty($code)) {
    // 获取到access_token
    $token = $obj->getToken($code);
    echo $token;
    // 调用这个方法，将会保存token到你设置的文件。
    $obj->saveToken($token);
}
*/
// 拼多多的token一般都有24个小时的保质期，为了避免过期，可以每隔一段时间，刷新下
// 比如你可以判断文件时间是否过期而刷新
// 你如果有刷新token，就可以直接调用这个方法进行刷新新的token了
/*
$token = $obj->getNewToken();
// 调用这个方法，将会保存token到你设置的文件

$obj->saveToken($token);
*/

//pdd.ddk.theme.list.get 13375587_156487163
//pdd.ddk.goods.promotion.url.generate pdd.ddk.rp.prom.url.generate
// $json = $obj->pdd_ddk_theme_list_get();
/*
搜索id，建议填写，提高收益。来自
pdd.ddk.goods.recommend.get、
pdd.ddk.goods.search、
pdd.ddk.top.goods.list.query等接口

 */
/*根据商品ID查询*/
function goods_detail($obj,$goods_id){
    $data = array(
   'client_id' => '239ae12adeab41b3becff5e89c661331',
   'client_secret' => '8d9de04e910c8009c5714a29801c0299dce05036',
   'goods_id_list' => "[$goods_id]"
    );
    $josn = $obj->pdd_ddk_goods_detail($data);    
   echo $josn;
    $detail = json_decode($josn,true)['goods_detail_response']['goods_details'][0];
    $data['coupon_total_quantity'] = $detail['coupon_total_quantity'];
    $data['coupon_remain_quantity'] = $detail['coupon_remain_quantity'];
    //$coupon_end_time = $detail['coupon_end_time'];
    //$coupon_end_time = empty($coupon_end_time) ? '已失效' : date('y-m-d H:i:s',$coupon_end_time) ;
    $data['goods_name'] = $detail['goods_name'];
    $data['min_group_price'] = $detail['min_group_price']/100;
    $data['min_normal_price'] = $detail['min_normal_price']/100;
    $data['coupon_discount'] = $detail['coupon_discount']/100;
    $data['price'] = $data['min_group_price']-$data['coupon_discount'];
    $data['yongjinbl'] = ($detail['promotion_rate']/1000)*100;
    $data['yongjin'] = round(($detail['promotion_rate']/1000)*$data['price'],2);
    $data['ss'] = url_generate($obj,$goods_id); 
    // echo json_encode($data);
    // min_normal_price
    
    echo "\r\n";
    echo $data['goods_name']."\r\n";
    echo "优惠券总数量: ".$data['coupon_total_quantity']."\r\n";
    echo "优惠券剩余数量: ".$data['coupon_remain_quantity']."\r\n";
    echo "优惠券失效时间：".$data['coupon_end_time']."\r\n";
    echo "拼单价格:".$data['min_group_price']."\r\n";
    echo "单独购买价格:".$data['min_normal_price']."\r\n";
    echo "优惠券:".$data['coupon_discount']."\r\n";
    echo "到手价:".$data['price']."\r\n";    
    echo "佣金比例:".$data['yongjinbl']."%\r\n";
    echo "可得佣金:".$data['yongjin']."\r\n";
    echo "link：".$data['ss'];
    // url_generate($obj,$goods_id);
    
}
function mall_goods_list($obj){
    $param=array(
        'mall_id'=>'644462718',
        'page_number'=>1,
        'page_size'=>10
    );
    $josn = $obj->pdd_ddk_mall_goods_list_get($param);
    echo $josn;
    $list = json_decode($josn,true)['goods_info_list_response']['goods_list'];
    // var_dump($list);
    foreach ($list as $key => $v) {
        $coupon_end_time = empty($v['coupon_end_time']) ? '已失效' : date('y-m-d H:i:s',$v['coupon_end_time']) ;
        $min_group_price = $v['min_group_price']/100;
        echo "\r\n";
        echo $v['goods_name']."\r\n";
        echo "优惠券总数量: ".$v['coupon_total_quantity']."\r\n";
        echo "优惠券剩余数量: ".$v['coupon_remain_quantity']."\r\n";
        echo "优惠券失效时间：".$coupon_end_time."\r\n";
        echo "市场价格：".$min_group_price."\r\n";
        echo "已售卖件数: ".$v['sales_tip']."\r\n";
    }
}








function search($obj){
    $param = array(
        'page'=>1,
        'page_size'=>10,
        'opt_id'=>4,
        'with_coupon'=>true,
        'keyword'=>'',
    );
    $json = $obj->pdd_ddk_goods_search($param);
    $detail = json_decode($json,true)['goods_search_response']['goods_list'];
    foreach ($detail as $key => $value) {
        $coupon_total_quantity = $value['coupon_total_quantity'];
        $coupon_remain_quantity = $value['coupon_remain_quantity'];
        $coupon_end_time = $value['coupon_end_time'];
        $coupon_end_time = empty($coupon_end_time) ? '已失效' : date('Y-m-d H:i:s',$coupon_end_time) ;
        $goods_name = $value['goods_name'];
        $min_group_price = $value['min_group_price']/100;
        $min_normal_price = $value['min_normal_price']/100;
        $coupon_discount = $value['coupon_discount']/100;
        $price = $min_group_price-$coupon_discount;
        $yongjinbl = ($value['promotion_rate']/1000)*100;
        $yongjin = round(($value['promotion_rate']/1000)*$price,2);
        // min_normal_price
        echo "\r\n";
        echo $goods_name."\r\n";
        echo "优惠券总数量: ".$coupon_total_quantity."\r\n";
        echo "优惠券剩余数量: ".$coupon_remain_quantity."\r\n";
        echo "优惠券失效时间：".$coupon_end_time."\r\n";
        echo "拼单价格:".$min_group_price."\r\n";
        echo "单独购买价格:".$min_normal_price."\r\n";
        echo "优惠券:".$coupon_discount."\r\n";
        echo "到手价:".$price."\r\n";    
        echo "佣金比例:".$yongjinbl."%\r\n";
        echo "可得佣金:".$yongjin."\r\n";
        // sleep(3);
    }
}













function search_pdd($obj,$k){
   
    $json = $obj->pdd_ddk_goods_search($k);
    
    $arrData = json_decode($json,true);
    if(array_key_exists('error_response', $arrData)){
        echo " \r\n";
        die($json);
    }
    $detail = $arrData['goods_search_response']['goods_list'];
    foreach ($detail as $key => $value) {
        $coupon_total_quantity = $value['coupon_total_quantity'];
        $coupon_remain_quantity = $value['coupon_remain_quantity'];
        $coupon_end_time = $value['coupon_end_time'];
        $coupon_end_time = empty($coupon_end_time) ? '已失效' : date('Y-m-d H:i:s',$coupon_end_time) ;
        $goods_name = $value['goods_name'];
        $min_group_price = $value['min_group_price']/100;
        $min_normal_price = $value['min_normal_price']/100;
        $coupon_discount = $value['coupon_discount']/100;
        $price = $min_group_price-$coupon_discount;
        $yongjinbl = ($value['promotion_rate']/1000)*100;
        $yongjin = round(($value['promotion_rate']/1000)*$price,2);


        if(($price<100 and $price>50) && $coupon_remain_quantity){
            echo "<br/>";
            echo $goods_name."<br/>";
            echo "优惠券总数量: ".$coupon_total_quantity."<br/>";
            echo "优惠券剩余数量: ".$coupon_remain_quantity."<br/>";
            echo "优惠券失效时间：".$coupon_end_time."<br/>";
            echo "拼单价格:".$min_group_price."<br/>";
            echo "单独购买价格:".$min_normal_price."<br/>";
            echo "优惠券:".$coupon_discount."<br/>";
            echo "到手价:".$price."<br/>";    
            echo "佣金比例:".$yongjinbl."%<br/>";
            echo "可得佣金:".$yongjin."<br/>";
        }
        // sleep(3);
        url_generate($obj,$value['goods_id']);
        echo " <br/>";        
        echo str_repeat("*",20)."<br/>";
        echo " <br/>";
    }
}

function url_generate($obj,$goodid){
    $param = array(
    'p_id'                =>'13375587_156487163',
    'goods_id_list'       => "[$goodid]"
    );
    
    $p = $obj->pdd_ddk_goods_promotion_url_generate($param);
    $arr = json_decode($p,true)['goods_promotion_url_generate_response']['goods_promotion_url_list'];
    // var_dump($arr);
    return  $arr[0]['mobile_short_url'];
    
}
// search($obj);



/*
2.9 0.58 20%

11.9 10.9
https://mobile.yangkeduo.com/goods2.html?goods_id=
 */

/*

*/
// $param = [
//     'goods_id'=>'150168531867'
// ];
// $st = $obj->pdd_ddk_goods_unit_query($param);
// file_put_contents('./stt.txt', $st);

// goods_detail($obj);
// search($obj);
/*
$keyword = "https://mobile.yangkeduo.com/goods2.html?goods_id=103852873083&page_from=0&pxq_secret_key=MVSOWH5AXGWZ33RPMEXMUCUGW6TXRUPSSTXKEBNUDYYNUXC2UKEQ&share_uin=HSBMRFXFYTE725JZN4L7FRDSQE_GEXDA&refer_share_id=eedb55b57ad44989a5d532071a1379dd&refer_share_uid=9192981989&refer_share_channel=copy_link&refer_share_form=text";





if(filter_var($keyword, FILTER_VALIDATE_URL) !== false){
    $url = parse_url($keyword)['query'];
    parse_str($url,$arr);
    // echo $arr['goods_id'];
    goods_detail($obj,$arr['goods_id']);
}else{
    $q = array(
        'keyword'=>'',
        'page'=>1,
        "page_size"=>10,
        "with_coupon"=>true,
        "sort_type"=>2,
        "is_brand_goods"=>1,
        // "opt_id"=>'',
        "pid"  =>"13375587_156487163"
    );    
    search_pdd($obj,$q);
}*/
/*
$x = 1;
do {
    $q = array(
        'keyword'=>'秋冬被',
        'page'=>$x,
        "page_size"=>10,
        "with_coupon"=>true,
        "sort_type"=>2,
        "is_brand_goods"=>1,
        // "opt_id"=>'',
        "pid"  =>"13375587_156487163"
    );    
    search_pdd($obj,$q);
    $x++;
} while ($x<=1000);

*/
// $end_time = date('Y-m-d H:i:s');
// $p = array('start_update_time' => '1600566919', 'end_update_time' => '1600653319');
// $q = array('order_sn'=>'200918-445226517461509225');
// $i = array('start_time'=>'2020-09-10 11:30:00','end_time'=>$end_time);
// $json = $obj->pdd_ddk_order_list_increment_get($p);
// $j = $obj->pdd_ddk_order_detail_get($q);
// $o = $obj->pdd_ddk_order_list_range_get($i);
// echo $o;
// 
// 
// custom_parameters

// echo $obj->pdd_ddk_finance_cpa_query(array('date_query'=>'2020-09-18'));
// echo $pxx->pdd_ddk_goods_pid_generate(array('number'=>1,'p_id_name_list'=>"['HBT02']"));
// echo $pxx->pdd_ddk_goods_pid_query();
// echo $pxx->pdd_ddk_member_authority_query(array('pid'=>'13375587_157571589'));
// echo$pxx->pdd_ddk_resource_url_gen(array('pid'=>'13375587_157571589','resource_type'=>50002));
// 644462718,
// echo $pxx->pdd_ddk_mall_url_gen(array('pid'=>'13375587_157571589','mall_id'=>'644462718'));
// mall_goods_list($obj);
// live_type    INTEGER 必填  直播间类型；1-达人，2-店铺，3-预约直播间；默认1
// p_id    STRING  必填  推广位ID
// room_id STRING  必填  直播间id或者店铺id
 
$x= 1;
$num = 0;
do {
     $param = array(
        'page'=>$x,
        'page_size'=>10,        
        'with_coupon'=>true,
        'keyword'=>'',
);
    $json = $obj->pdd_ddk_goods_search($param);
   
    $detail = json_decode($json,true)['goods_search_response']['goods_list'];

    foreach ($detail as $key => $value) {
        $coupon_total_quantity = $value['coupon_total_quantity'];
        $coupon_remain_quantity = $value['coupon_remain_quantity'];
        $coupon_end_time = $value['coupon_end_time'];
        $coupon_end_time = empty($coupon_end_time) ? '已失效' : date('Y-m-d H:i:s',$coupon_end_time) ;
        $goods_name = $value['goods_name'];
        $min_group_price = $value['min_group_price']/100;
        $min_normal_price = $value['min_normal_price']/100;
        $coupon_discount = $value['coupon_discount']/100;
        $price = $min_group_price-$coupon_discount;
        $yongjinbl = ($value['promotion_rate']/1000)*100;
        $yongjin = round(($value['promotion_rate']/1000)*$price,2);


        if(($price<100 and $price>50) && $coupon_remain_quantity){
            echo "\r\n";
            echo $goods_name."\r\n";
            echo "优惠券总数量: ".$coupon_total_quantity."\r\n";
            echo "优惠券剩余数量: ".$coupon_remain_quantity."\r\n";
            echo "优惠券失效时间：".$coupon_end_time."\r\n";
            echo "拼单价格:".$min_group_price."\r\n";
            echo "单独购买价格:".$min_normal_price."\r\n";
            echo "优惠券:".$coupon_discount."\r\n";
            echo "到手价:".$price."\r\n";    
            echo "佣金比例:".$yongjinbl."%\r\n";
            echo "可得佣金:".$yongjin."\r\n";
        }
        // sleep(3);
        url_generate($obj,$value['goods_id']);
        echo " <br/>";        
        echo str_repeat("*",20)."<br/>";
        echo " <br/>";
    }

    
    $x++;
} while ($x<=600);
echo $num;


// echo $obj->pd_ddk_live_detail(array('room_id'=>'644462718'));
// echo $obj->pdd_ddk_live_url_gen(array('custom_parameters'=>'','live_type'=>1,'p_id'=>'13375587_157571589','room_id'=>'644462718'));
//
/*
function pdd_field($arr){
    foreach ($arr as $key => $value) {
       echo $value->mall_name.":".$value->goods_name."\r\n";
    }
}
$page = 0;
$num=1;
while($num){
    $list_id = '';
    $offset = $page*400;  
    $p_id = '13375587_157571589';
    $sort_type = 1;

    $data = $obj->pdd_ddk_top_goods_list_query(array("limit"=>400,"offset"=>$offset,"sort_type"=>1));
    if(strpos($data,'error_response')!==false){
        break;
    }
   
    pdd_field(json_decode($data)->top_goods_list_get_response->list);
    $page++;

}

*/

// $obj = new Pdd\Api($config);
// $obj->format = true;
// $helper = new Pdd\Helper();
// $helper->goodsDetail($obj,'150168531867');pdd.ddk.finance.cpa.query

// echo $obj->pdd_ddk_finance_cpa_query(array('date_query'=>'2020-09-18','p_id'=>'13375587_157571589'));
$database = new Medoo([
    'database_type' => 'sqlite',
    'database_file'=>'G://api/songshuxianbao.db'

]);
$start_update_time = microtime(true);
$end_update_time = $start_update_time+70000;
//10000000

$p = array(
    'start_update_time' => '1600566919',
    'end_update_time' => '1600653319',
    'page_size'=>50,
    "return_count"=>0,
    'page'=>1
);
//$p = array('start_update_time' => $start_update_time, 'end_update_time' => $end_update_time);
/*$a=1;
while($a<5){
    $json = $obj->pdd_ddk_order_list_increment_get($p);
    $data = json_decode($json,true)['order_list_get_response']['order_list'];
    var_dump($data);

    if(empty($data)){die('meiyoul');}
    foreach($data as $key=>$value){
        $database->insert("orders", $value);
    }
    echo $p['page']++;
    echo "<br/>";
    $p['start_update_time']= $p['end_update_time'];
    $p['end_update_time'] = microtime(true)-2000;
    echo $p['start_update_time']; echo "<br/>";
    echo $p['end_update_time'];
    echo "<hr/>";
    sleep(3);
    $a++;

}*/

//


?>

