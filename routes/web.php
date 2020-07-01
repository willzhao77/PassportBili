<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
$clientId = 1;
$clientSecret = 'YuX9yuCn38Dl0sJWSFw7VVacInYz8KjRuW5iug8L';

Route::get('/', function () {
    return view('welcome');
});


Route::view('/login', 'login');



Route::get('/passportweixin/login',
    function (\Illuminate\Http\Request $request) use ($clientId) {
        $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => 'http://bili.com/auth/callback',
            'response_type' => 'code',
            'scope' => '*',
            'state' => $state,
        ]);

        return redirect('http://passportweixin.com/oauth/authorize?'.$query);
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
        = (new \GuzzleHttp\Client())->post('http://passportweixin.com/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => 'http://bili.com/auth/callback',
            'code' => $request->params['code'],
        ],
    ]);

    return '$response->getBody()->getContents()';
});



Route::view('/refresh/page', 'refresh_page');

Route::post('/refresh', function (\Illuminate\Http\Request $request) use (
    $clientId,
    $clientSecret
) {
    $http = new GuzzleHttp\Client;
    $response = $http->post('http://passportweixin.com/oauth/token', [
        'form_params' => [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->params['refresh_token'],
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ],
    ]);

    return json_decode((string)$response->getBody(), true);
});
