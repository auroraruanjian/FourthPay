<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link id="linkWebCss" href="/res/css/Web.min.css" rel="stylesheet"/>
    <link id="linkWeixinCss" href="/res/css/Weixin.css" rel="stylesheet"/>
    <title>支付中...</title>
</head>
<body>
<div id="divTitle" class="Header">
    <div class="Wrap1000">
        <h1>{{$pay_cn_name}}收银台</h1>
    </div>
</div>
<div id="divLine" style="border-bottom: 2px solid #ea5a42;"></div>
<div id="div1000" class="Wrap1000">
    <div>
        <div id="IsShowBillInfo" class="divShow">
            <div>
                <span class="Contant">应付金额：<span><span id="LabelAmt">{{$amount}}</span></span>元</span>
                <br />
                <span class="Sum">
                        请您在提交订单后<span style="color: #f60; font-weight: bold;">10分钟</span>完成支付。
                    </span>
            </div>
        </div>
    </div>
    <div style="width: 100%;">
        <div id="divQRCode" class="divQRCode">
            <p align="center" style="margin:0px;padding:10px;">
                @if($type=='images')
                    <img src='{{$qrcode}}' width="280" height="280">
                @else
                    {!! QrCode::size(280)->margin(0)->generate($qrcode); !!}
                @endif
            </p>
        </div>
        <br />
        <div id="divHasPay" class="divHasPay">
            <input id="btnHasPay" class="paySuccessBtn" style="display: none;" type="button" value="查询支付状态" onclick="QueryStatus()" />
        </div>
        <div id="line" class="divLine"></div>
        <img width="430" height="430" src="/res/images/{{$bank_code}}.jpg" id="imgExample" alt="{{$pay_cn_name}}扫码示意图" />
    </div>
</div>
<div id="divFooter" class="Footer" style="margin-top: 100px;">
    <div class="ClearFloat"></div>
    <div class="Footer_part2">
        <div style="border: 1px solid #CFCFCF;"></div>
        <div class="footerD"></div>
    </div>
</div>
</body>
<script src="/js/jquery.min.js"></script>
<script>
    var time = 0;
    var inter = setInterval(function(){
        $.ajax({
            'url' :'/payment/orderStatus',
            'data':{'order_no':'{{$order_no}}'},
            'method':'post',
            'success':function(data){
                if( data.status ){
                    $('#divQRCode').append('<div class="qrcode_mask_qrcode"></div><span class="qrcode_mask_text">您已支付成功，请返回平台查看是否到账，如三分钟未到账，请联系客服！</span>');
                    clearInterval(inter);
                }
            }
        });
        if( time > 200){
            clearInterval(inter);
        }
        time++;
    },3000);
</script>
</html>
