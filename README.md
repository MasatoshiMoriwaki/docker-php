# はじめに
PHP, Docker, MVCフレームワークを学習するために制作したアプリケーションです

### アプリケーション概要
dockerで構築したコンテナ上に、nginx + PHP + MysqlのWebアプリケーションを開発しました。
既存のフレームワークは使用せず、MVCモデルに沿った自前のフレームワークを用意した上でアプリケーションを作り込みました。
フレームワークに実装した機能は以下の通りです。

| クラス | 機能 |
|----- | --- |
|Router|HTTPリクエストをルーティングし呼び出すコントローラを決定する|
|Request|リクエストデータを取得する|
|Response|ヘッダ情報のセット、ビューファイルの出力、リダイレクト|
|Controller|モデル呼び出し、入力チェック、ビューテンプレートの準備|
|Model| DBへのCRUD操作|
|Entity| DBの各テーブルカラムをマッピング|
|View| テンプレートファイルを読み込みブラウザに送信するコンテンツを生成する|

### 環境概要
 - docker
    - nginx:1.17.7-alpine
    - php:7.4.1-fpm-alpine3.11
    - mysql:8.0

### 機能概要
純喫茶の店舗情報を検索、登録、編集することができるサービスを構想して以下の機能を実装しました。
 - 純喫茶の検索(都道府県、店舗の特徴、フリーワード)
 - 純喫茶の新規登録、編集(ログインが必要)
 - ユーザの新規登録、編集
 - ログイン
 - 純喫茶、ユーザの画像登録・編集

### 今後について
現在はローカルでの開発にとどまっているため以下を目標に開発を進めて行きたいと思います。
 1. 既存のフレームワーク(Laravelなど)を使用して上記のアプリケーションを構築する
 2. フロントエンドの実装が皆無なのでUI/UXを充実させる
 3. レストラン情報検索のAPIなど(ぐるなびAPIなど)を利用して純喫茶情報を収集してDBに登録する
 4. 3までが実装できればサービスとしてリリースする
 5. ユーザが行った純喫茶の記録を投稿したり、他ユーザの投稿にいいねやコメントを残せる機能を実装する