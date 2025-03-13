# mock-furima

## 環境構築

### Dockerビルド
1. プロジェクトのルートディレクトリに移動し、以下のコマンドでクローンします</br>
HTTP形式：
`git clone https://github.com/yurikoUe/mock-furima.git`  
SSH形式：
`git clone git@github.com:yurikoUe/mock-furima.git`

2. `docker-compose up -d --build`
>MacのM1・M2チップのPCの場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示され、ビルドができないことがあります。エラーが発生した場合は、docker-compose.ymlファイルの「mysql」の記述箇所に、platformの項目を追加で記載し、もう一度ビルドしてください。
```
mysql:
    platform: linux/x86_64（この文を追加）
    image: mysql:8.0.26
    environment:
```
3. DockerDesktopアプリでコンテナが起動していることを確認してください

### Laravel環境構築
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを「.env」ファイルに命名変更するか、以下のコマンドで「.env.example」をコピーして「.env」を作成。

`cp .env.example .env`

4. .envファイル内のDBの箇所を以下のように変更
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

5. アプリケーションキーの作成をします

`php artisan key:generate`

6. シンボリックリンクの作成をします

`php artisan storage:link`

7. マイグレーションの実行をします

`php artisan migrate`

8. シーディングの実行

`php artisan db:seed`

## メール認証の設定

このアプリケーションでは、メール認証が必要です。ユーザーが登録後、「メール認証」を完了するまで、ログインできないように設定されています。以下の手順でメール認証を使用するための設定を行ってください。

1. `.env` ファイルで、以下のメール関連の設定を行います。

    ```env
    MAIL_MAILER=smtp
    MAIL_HOST=mock-furima-mailhog-1  #Dockerコンテナ名（環境によって異なる場合があります）
    MAIL_PORT=1025
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS=no-reply@furimapp.local  # 必要に応じて変更
    MAIL_FROM_NAME="${APP_NAME}"
    ```

    **注意:** `MAIL_HOST` は、Mailhogのコンテナ名で設定してください。`docker ps` コマンドでMailhogのコンテナ名（例: `mock-furima-mailhog-1`）を確認できます。

2. 認証方法
- メール認証機能を動作させるためには、ユーザーが登録後に受信したメール内のリンクをクリックして、認証を完了する必要があります。Mailhogのウェブインターフェース (`http://localhost:8025`) でメールを確認できます。

3. ログインの制御確認
- usersテーブルのemail_verified_atカラムが null の場合、ログインできません。登録後、認証をすることにより、email_verified_atカラムがに認証時の時間が入ります。

## Stripe決済の導入
本アプリでは Stripe を使用して決済を行います。

1. 環境変数の設定
    以下のStripe APIキーを`.env` ファイルに追加してください。

    ```env
    STRIPE_KEY=your_stripe_public_key
    STRIPE_SECRET=your_stripe_secret_key
    ```
    テスト環境では、 Stripeのダッシュボード から取得した テスト用のAPIキー を設定してください。

2. 決済の流れ

    - クレジットカード決済の場合
        1. 商品をカートに追加し、支払い方法を選択後、購入ボタンをクリック
        2. クレジットカード情報を入力
        3. Stripe API を通じて決済を実行
        4. 決済成功後、注文が確定し、商品が購入済みとなる

    - コンビニ決済の場合
        1. 商品をカートに追加し、支払い方法としてコンビニを選択し、購入
        2. 電話番号を入力（テスト環境では省略可能）
        3. Stripe API を通じて決済を実行
        4. メールで支払い方法の詳細が届く（テスト環境では省略）

3. ローカル環境でのテスト
    開発環境では、Stripeの テスト用カード(以下参照）) を使用して決済を試すことができます。

     ```
    カード番号: 4242 4242 4242 4242
    有効期限: 任意の未来の日付（例: 12/34）
    CVC: 任意の3桁の番号（例: 123）
     ```

    **注意:**
    * テスト環境では 実際の決済は行われません。
    * 本番環境へ移行する際は、 本番用のAPIキーを設定 してください。

## PHPunitテストの実行について

プロジェクトのテストを実行するには、まず以下の依存関係をインストールしてください：

外部決済システムStripeを使用しているため、PHPunitテストではMockery が必要です。以下のコマンドでインストールします。
```
composer require mockery/mockery --dev
```

テストを実行するには、以下のコマンドを使用します：
```
php artisan test
```

## 使用技術
- **Laravel**: 8.75
- **Docker**: 最新版
- **MySQL**: 8.0.26
- **PHP**: 7.4.9
- **Nginx**: 1.21.1
- **phpMyAdmin**

## ER図

以下は、プロジェクトのER図です。

![ER図](src/public/er-diagram.png)


## URL
+ 開発環境: http://localhost/
+ phpMyAdmin: http://localhost:8080/
+ Mailhog: http://localhost:8025