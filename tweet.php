<?php
$api_key = 'C2YLvwvhzWm2RFtJ3uVGkyLEz' ;	// APIキー
$api_secret = 'im5xaViA508y3GvnbkoB2bD8l6QKnPV5xKIaEozUFmfgLvWjKa' ;	// APIシークレット
$access_token = '207173453-5l2hafmrRbfkqhSJtWBt7qTSY0we1slp5vnI98pS' ;	// アクセストークン
$access_token_secret = '8ucpfLBprjc1vSZ2OzRD5aZmMchWx57Q4qlG0M6Ju6Oav' ;	// アクセストークンシークレット

$request_url = 'https://api.twitter.com/1.1/statuses/update.json' ;	// エンドポイント
$request_method = 'POST' ;

// パラメータA (リクエストのオプション)
$params_a = array(
    'status' => 'APIを通して投稿してみました。' . "\n\n" . '投稿時間: ' . date( 'Y/m/d H:i' ) . "\n" . '投稿元: https://syncer.jp/Web/API/Twitter/Rest_API/' ,
//	'media_ids' => "" ,	// 添付する画像のメディアID
) ;

// キーを作成する (URLエンコードする)
$signature_key = rawurlencode( $api_secret ) . '&' . rawurlencode( $access_token_secret ) ;

// パラメータB (署名の材料用)
$params_b = array(
	'oauth_token' => $access_token ,
	'oauth_consumer_key' => $api_key ,
	'oauth_signature_method' => 'HMAC-SHA1' ,
	'oauth_timestamp' => time() ,
	'oauth_nonce' => microtime() ,
	'oauth_version' => '1.0' ,
) ;

// パラメータAとパラメータBを合成してパラメータCを作る
$params_c = array_merge( $params_a , $params_b ) ;

// 連想配列をアルファベット順に並び替える
ksort( $params_c ) ;

// パラメータの連想配列を[キー=値&キー=値...]の文字列に変換する
$request_params = http_build_query( $params_c , '' , '&' ) ;

// 一部の文字列をフォロー
$request_params = str_replace( array( '+' , '%7E' ) , array( '%20' , '~' ) , $request_params ) ;

// 変換した文字列をURLエンコードする
$request_params = rawurlencode( $request_params ) ;

// リクエストメソッドをURLエンコードする
// ここでは、URL末尾の[?]以下は付けないこと
$encoded_request_method = rawurlencode( $request_method ) ;
 
// リクエストURLをURLエンコードする
$encoded_request_url = rawurlencode( $request_url ) ;
 
// リクエストメソッド、リクエストURL、パラメータを[&]で繋ぐ
$signature_data = $encoded_request_method . '&' . $encoded_request_url . '&' . $request_params ;

// キー[$signature_key]とデータ[$signature_data]を利用して、HMAC-SHA1方式のハッシュ値に変換する
$hash = hash_hmac( 'sha1' , $signature_data , $signature_key , TRUE ) ;

// base64エンコードして、署名[$signature]が完成する
$signature = base64_encode( $hash ) ;

// パラメータの連想配列、[$params]に、作成した署名を加える
$params_c['oauth_signature'] = $signature ;

// パラメータの連想配列を[キー=値,キー=値,...]の文字列に変換する
$header_params = http_build_query( $params_c , '' , ',' ) ;

// リクエスト用のコンテキスト
$context = array(
	'http' => array(
		'method' => $request_method , // リクエストメソッド
		'header' => array(			  // ヘッダー
			'Authorization: OAuth ' . $header_params ,
		) ,
	) ,
) ;


// オプションがある場合、コンテキストにPOSTフィールドを作成する
if ( $params_a ) {
	$context['http']['content'] = http_build_query( $params_a ) ;
}

// cURLを使ってリクエスト
$curl = curl_init() ;
curl_setopt( $curl, CURLOPT_URL , $request_url ) ;	// リクエストURL
curl_setopt( $curl, CURLOPT_HEADER, true ) ;	// ヘッダーを取得
curl_setopt( $curl, CURLINFO_HEADER_OUT, true ) ;
curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $context['http']['method'] ) ;	// メソッド
curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false ) ;	// 証明書の検証を行わない
curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true ) ;	// curl_execの結果を文字列で返す
curl_setopt( $curl, CURLOPT_HTTPHEADER, $context['http']['header'] ) ;	// ヘッダー
if( isset( $context['http']['content'] ) && !empty( $context['http']['content'] ) ) {
	curl_setopt( $curl, CURLOPT_POSTFIELDS, $context['http']['content'] ) ;	// リクエストボディ
}
curl_setopt( $curl, CURLOPT_TIMEOUT, 5 ) ;	// タイムアウトの秒数
$res1 = curl_exec( $curl ) ;
$res2 = curl_getinfo( $curl, CURLINFO_HEADER_OUT ) ;
curl_close( $curl ) ;

echo $res2;

/*echo 'requesturl:' . $request_url . '<br>';
echo 'method:' . $context['http']['method'] . '<br>';
echo 'header:' . $context['http']['header'][0] . '<br>';
echo 'header-size:' . count($context['http']['header']) . '<br>';
echo 'content:' . $context['http']['content'] . '<br>';*/


?>
