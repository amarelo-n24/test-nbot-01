<?php
  //composerでインストールしたライブラリを一括読み込み
  require_once __DIR__ . '/vendor/autoload.php';

  //アクセストークンを用い、CurlHTTPClientをインスタンス化
  $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
  //CurlHTTPClientとシークレットを使いLINEBotをインスタンス化
  $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);
  //LINE Messaging APIがリクエストに付与した署名を取得
  $signature = $_SERVER['HTTP_' . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
  //署名が正当かチェック。正当であればリクエストをパースし配列へ
  $events = $bot->parseEventRequest(file_get_contents('php://input'),$signature);
  //配列に格納された各イベントをループ処理
  foreach ($events as $event){
    //テキストを返信
    //$bot->replyText($event->getReplyToken(),'TextMessage');
    //replyTextMessage($bot,$event->getReplyToken(),'textmessage');
    //ButtonsTemplateを返信
    /*
    replyButtonsTemplate($bot,
      $event->getReplyToken(),
      'お天気お知らせ - 今日の天気予報は晴れです',
      'https://' . $_SERVER['HTTP_HOST'] . '/imgs/template.jpg',
      'お天気お知らせ',
      '今日の天気予報は晴れです',
      new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(
        '明日の天気','tomorrow'),
      new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder(
        '週末の天気','weekend'),
      new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder(
        'webで見る','http://google.jp')
    );

    //イベントがPostbackEventクラスのインスタンスであれば、
    if ($event instanceof \LINE\LINEBot\Event\PostbackEvent){
      //テキストを返信し次のイベントの処理
      replyTextMessage($bot,$event->getReplyToken(),'Postback受信「' . $event->getPostbackData() . '」');
      continue;
    }
    //confirmテンプレートメッセージを返信
    replyConfirmTemplate($bot,
      $event->getReplyToken(),
      'webで詳しく見ますか？',
      'webで詳しく見ますか？',
      new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder(
        '見る','http://google.jp'),
      new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(
        '見ない','ignore')
    );
    */
    //Carouselテンプレートメッセージを返信
    //ダイアログの配列
    $columnArray = array();
    for($i = 0; $i < 5; $i++){
      //アクションの配列
      $actionArray = array();
      array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(
        'ボタン' . $i . '-' . 1, 'c-' . $i . '-' . 1));
      array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(
        'ボタン' . $i . '-' . 2, 'c-' . $i . '-' . 2));
      array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder(
        'ボタン' . $i . '-' . 3, 'c-' . $i . '-' . 3));
      $column = new LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder(
        ($i + 1) . '日後の天気',
        '晴れ',
        'https://' . $_SERVER['HTTP_HOST'] . '/imgs/template.jpg',
        $actionArray
      );
      //配列に追加
      array_push($columnArray,$column);
    }
    replyCarouselTemplate($bot,$event->getReplyToken(),'今後の天気予報',$columnArray);
  }

  //テキストを返信。引数はLINEBOT、返信先、テキスト
  function replyTextMessage($bot,$replyToken,$text){
    //返信を行い、レスポンスを取得
    //TextMessageBuilderの引数はテキスト
    $response = $bot->replyMessage($replyToken,new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));
    //レスポンスが異常な場合
    if (!$response->isSucceeded()){
      //エラー内容を出力
      error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
    }
  }

  //ButtonsTemplateを返信。引数は、LINEBot、返信先、代替テキスト、画像URL、タイトル、本文、アクション（可変長引数）
  function replyButtonsTemplate($bot,$replyToken,$altenativeText,$imageUrl,$title,$text, ...$actions){
    //actionを格納する配列
    $actionArray = array();
    //アクションすべてを追加
    foreach($actions as $value){
      array_push($actionArray,$value);
    }
    //TemplateMessageBuilderの引数は代替テキスト、ButttonTemplateBuilder
    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
      $altenativeText,
      //ButttonTemplateBuilderの引数はタイトル、本文
      //画像URL、アクションの配列
      new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder($title,$text,$imageUrl,$actionArray)
    );
    $response = $bot->replyMessage($replyToken,$builder);
    if(!$response->isSucceeded()){
      error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
    }
  }

  //Confirmテンプレートを返信。引数はLINEBot、返信先、代替テキスト
  //本文、アクション（可変長引数）
  function replyConfirmTemplate($bot,$replyToken,$altenativeText,$text,...$actions){
    $actionArray = array();
    foreach($actions as $value){
      array_push($actionArray,$value);
    }
    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
      $altenativeText,

      new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder($text,$actionArray)
    );
    $response = $bot->replyMessage($replyToken,$builder);
    if(!$response->isSucceeded()){
      error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
    }
  }

  //Carouselテンプレートを返信。引数はLINEBot、返信先、代替テキスト
  //ダイアログの配列
  function replyCarouselTemplate($bot,$replyToken,$altenativeText,$columnArray){
    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
      $altenativeText,
      //Carouselテンプレートの引数はダイアログの配列
      new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder($columnArray)
    );
    $response = $bot->replyMessage($replyToken,$builder);
    if(!$response->isSucceeded()){
      error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
    }
  }
?>
