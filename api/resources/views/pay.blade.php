<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>支付中...</title>
</head>
<body>
<form name="autoForm" id="autoForm" action="{{$request_url}}" method="post">
    @foreach ( $data['data'] as $_k => $_v)
        <input type="hidden" name="{{$_k}}" value="{{$_v}}" />
    @endforeach
</form>
<script>document.autoForm.submit();</script>
</body>
</html>
