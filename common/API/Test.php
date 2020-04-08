<?php

namespace Common\API;

use Illuminate\Support\Str;

class Test
{
    /**
     * 番茄表单自动提交工具
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function run()
    {
        // 收费版
        $base_url = 'https://ding.fanqier.cn/api/f/';
        // 免费版
        $base_url = 'https://uw12nw.fanqier.cn/api/f/';

        $client = new \GuzzleHttp\Client([
            'connect_timeout'   => 5,
            'timeout'           => 30,
            'debug'             => false,
            'http_errors'       => false,
            'base_uri'          => $base_url,
        ]);

        $_api = 'ynt5cxux'; // 正式
        $_api = 'uyw5rljx'; // 测试

        $response = $client->request(
            'GET',
            $_api
        );


        if( $response->getStatusCode() == 200 ){
            $body = json_decode($response->getBody(),true);

            if( !empty($body['status']) && !empty($body['status']['code']) && $body['status']['code'] == 200 ){
                $_api = $body['data']['_id'];

                $_request_data = [
                    "formId"    => $_api,
                    "wechat"    => null,
                    "duration"  => 115094,
                    "requestId" => "835e4e6f-d42e-4bad-94c9-".Str::random(12),
                    "formV"     => 0,
                    "salt"      => Str::random(32),
                    "dingtalk"  => '{}',
                    "isEncrypt" => false,
                    "values"    => []
                ];

                // 获取区域和药店规则
                $logicRules = [];
                if( !empty($body['data']['logicRules']) ){
                    foreach($body['data']['logicRules'] as $_rule){
                        $logicRules[$_rule['item']] = $_rule['rules'];
                    }
                }

                foreach( $body['data']['items'] as $item ){
                    if( $item['type'] == 'title' ) continue;

                    $_from_item = [
                        "definition"    => $item['_id'],
                        "type"          => $item['type'],
                    ];

                    switch ($item['type']){
                        case 'name':
                            $_from_item['value'] = '张三';
                            break;
                        case 'mobile':
                            $_from_item['value'] = '13139520613';
                            break;
                        case 'idcard':
                            $_from_item['value'] = '642224199305221212';
                            break;
                        case 'radio':
                            // 如果是区域
                            if( $item['label'] == '所在区域' ){
                                foreach( $item['items'] as $area_item){
                                    if( $area_item['text'] == '石嘴山市' ){
                                        $_from_item['selectedId']   = $area_item['_id'];
                                        $_from_item['selectedItem'] = $area_item['text'];
                                        break;
                                    }
                                }

                                $_from_item['isOther']      = false;
                                $_from_item['remark']       = '';
                            }else if( $item['label'] == '石嘴山市门店' ){
                                // 如果是大武口药店，并且 库存总量 大于 已预约数量
                                foreach( $item['items'] as $area_item){
                                    $tip_text = "药店名称：{$area_item['text']} 总库存：{$area_item['voteCount']} 已预约数量：{$area_item['selectedNumber']}";

                                    if( strpos($area_item['text'],'大武口') === 0 ){
                                        if( $area_item['voteCount'] > $area_item['selectedNumber'] || $area_item['selectedNumber'] == 0 ){
                                            $_from_item['selectedId']   = $area_item['_id'];
                                            $_from_item['selectedItem'] = $area_item['text'];
                                            $_from_item['isOther']      = false;
                                            $_from_item['remark']       = '';

                                            echo '√ '.$tip_text.' 符合条件'.PHP_EOL;
                                            break;
                                        }else{
                                            echo '× '.$tip_text.' 不符合条件'.PHP_EOL;
                                        }
                                    }else{
                                        echo '× '.$tip_text.' 非所属区域'.PHP_EOL;
                                    }
                                }
                            }else{
                                $_from_item['selectedId']   = null;
                                $_from_item['selectedItem'] = '';
                            }
                            break;
                        case 'text':
                            $_from_item['value']    =  '';
                            $_from_item['isNumber'] = $item['isNumber'];
                            break;
                    }

                    $_request_data['values'][] = $_from_item;
                }

                dump($_request_data);
                $response = $client->request(
                    'POST',
                    $_api,
                    ['json' => $_request_data]
                );

                if( $response->getStatusCode() == 200 ) {
                    $body = json_decode($response->getBody(), true);

                    echo "预约状态：{$body['status']['code']}  消息：{$body['status']['message']}".PHP_EOL;
                }else{
                    echo '预约失败，HTTP状态码：'.$response->getStatusCode() .PHP_EOL;
                }
            }else{
                echo "返回数据不正确：".PHP_EOL;
                dump($body);
            }
        }else{
            echo '获取表单请求失败，HTTP状态码：'.$response->getStatusCode() .PHP_EOL;
        }
    }
}
