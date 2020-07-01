# Passport 授权码模式

This is Client side. 

Client side is PassortWeixin.



#### 第三方应用程序（bilibili）**

###### **准备**

```php
composer create-project --prefer-dist laravel/laravel laravel6
composer require guzzlehttp/guzzle // 伪造 http 请求
```

###### **web.php**

```php
<?php

$clientId = 1;
$clientSecret = '8sGiTDgHb69Y6nTiFImTJO32jm3jB7x2BzMxrhDF';

// bili 登录页面
Route::view('/login', 'login');


// 第三方登陆，重定向
Route::get('/lishen/login',
    function (\Illuminate\Http\Request $request) use ($clientId) {
        $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => 'http://bili.com/auth/callback',
            'response_type' => 'code',
            'scope' => '*',
            'state' => $state,
        ]);

        return redirect('http://lishen.com/oauth/authorize?'.$query);
    });



// 回调地址，获取 code，并随后发出获取 token 请求
Route::view('/auth/callback', 'auth_callback');

Route::post('/get/token', function (\Illuminate\Http\Request $request) use (
    $clientId,
    $clientSecret
) {
    // csrf 攻击处理
    $state = $request->session()->pull('state');
    throw_unless(
        strlen($state) > 0 && $state === $request->params['state'],
        InvalidArgumentException::class
    );


    $response
        = (new \GuzzleHttp\Client())->post('http://lishen.com/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => 'http://bili.com/auth/callback',
            'code' => $request->params['code'],
        ],
    ]);

    return json_decode((string)$response->getBody(), true);
});


// 刷新 token
Route::view('/refresh/page', 'refresh_page');

Route::post('/refresh', function (\Illuminate\Http\Request $request) use (
    $clientId,
    $clientSecret
) {
    $http = new GuzzleHttp\Client;
    $response = $http->post('http://lishen.com/oauth/token', [
        'form_params' => [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->params['refresh_token'],
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ],
    ]);

    return json_decode((string)$response->getBody(), true);
});
```

###### **refresh_page**

###### **

```php
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
    axios.post('/refresh', {
        params: {
            refresh_token: "def502009e634dd59ac4dcd4843be50c3a7a6c76fe0c26a6a948d45b99e393cdf99d1a212a8752d0ce02f4cbc25008972b524336f23b60dfc4198e5413b7e43250126b0d1780afb85443edc1579870e823eedea4313448ffcbe8ca73dc2441e1b1f54d3c0ffc31888e0afeb3b1d4516f6986e540b6a56490dfbfabfe7a88e9fb8539a18cb08f8a2ce10962a3c79e7eed137f137f605cb1ab26254e642750f7f07ebdf17a9ce07a370fabc85e769326cb4fbc9aad402bb69615357766f56e9e26feafac306a7338781317e8baa88e9df9dc0096c92522c8d3cdc1b77cf5273bb0866608575eec5688815d294de22cf8bdf1689cb7e11d6caeb2f3bd80cc57d911b712f79609a45e6e1def42709776c75ca16b56ce6449c25c1660635dfc4a590560db5d2bb52ffcb9be601b8a1ea51c221246815a4f08ed262290cf4fdf0c9c9d357c189f5fa4b9d32c7b9c98a8832666e1ee2eba38b9dc642b02fcc05c38bbdecc"
        }
    })
        .then(function (response) {
            console.log(response.data);
        });
</script>
```

###### **auth_callback.blade.php

```PHP
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
    function GetRequest() {
        var url = location.search; //获取url中"?"符后的字串
        var theRequest = {};
        if (url.indexOf("?") !== -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for (var i = 0; i < strs.length; i++) {
                theRequest[strs[i].split("=")[0]] = decodeURI(strs[i].split("=")[1]);
            }
        }
        return theRequest;
    }

    //调用
    var Request = GetRequest();

    if (Request['error']) {
        // 用户未授权处理
        alert(Request['error']);
    }else
    {
        var code = Request['code'];
        var state = Request['state'];

        axios.post('/get/token', {
            params: {
                code,
                state
            }
        })
            .then(function (response) {
                console.log(response.data);
            });
    }
</script>
```

