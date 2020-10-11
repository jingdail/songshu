<?php
include './vendor/autoload.php';
include './config.php';
use EasyWeChat\Factory;
use Pdd\Api;
use Medoo\Medoo;


//初始化pdd、mp
$pddx = new Api($pdd_config);
$action = $_GET["act"];
if($action == "list"){
    $page = $_GET['page'];
    $k = array(
            'page'=>$page,
            'page_size'=>10,
            'with_coupon'=>true,
            'keyword'=>''
        );
$json = $pddx->pdd_ddk_goods_search($k);
echo $json;
}
if($action == "genurl"){
    $gid = $_GET['gid'];
    $param = array(
            'p_id'                => '13375587_156487163',
            'goods_id_list'       => "[$gid]"
        );
    $p = $pddx->pdd_ddk_goods_promotion_url_generate($param);
    echo $p;
}


?>