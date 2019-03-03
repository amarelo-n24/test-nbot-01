<?php
  //composerでインストールしたライブラリを一括読み込み
  require_once __DIR__ . '/vendor/autoload.php';

  //POSTメゾットで渡される値を取得、表示
  $inputString = file_get_contents('php://input');
  error_log($inputString);
?>
