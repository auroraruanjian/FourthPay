<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>授权结果</title>
    <link rel="stylesheet" href="/css/weui.css">
</head>
<body ontouchstart>
<div class="container" id="container">
    <div class="page msg_success js_show">
        <div class="weui-msg">
            <div class="weui-msg__icon-area">
                @if( $code == 0 )
                <i class="weui-icon-warn weui-icon_msg"></i>
                @else
                <i class="weui-icon-success weui-icon_msg"></i>
                @endif
            </div>
            <div class="weui-msg__text-area">
                <h2 class="weui-msg__title">{{ $title }}</h2>
                <p class="weui-msg__desc">{{$desc}}</p>
            </div>
            <div class="weui-msg__extra-area">
                <div class="weui-footer">
                    <p class="weui-footer__text">Copyright © 2015-2019</p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>