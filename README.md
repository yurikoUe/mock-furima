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
    APIキーは、以下のStripeのダッシュボードからテスト環境のAPIキーを取得してください.
    https://dashboard.stripe.com/test/apikeys

    未登録の人は先に登録が必要です→[Stripe公式サイト](https://stripe.com/jp?utm_campaign=APAC_JP_JA_Search_Brand_Payments-Pure_EXA-21278920274&utm_medium=cpc&utm_source=google&ad_content=714155577511&utm_term=stripe&utm_matchtype=e&utm_adposition=&utm_device=c&gad_source=1&gclid=Cj0KCQjwhMq-BhCFARIsAGvo0Kde-7Fg6U7v2NTt4uBilQa9vI2G0Sk_U19TmXSWLmxrDDyY7Fbv_ncaAnIJEALw_wcB)


2. ローカル環境でのテスト

    開発環境では、Stripeの テスト用カード(以下参照）を使用して決済を試すことができます。

     ```
    カード番号: 4242 4242 4242 4242
    有効期限: 任意の未来の日付（例: 12/34）
    CVC: 任意の3桁の番号（例: 123）
     ```

    **注意:**
    * テスト環境では 実際の決済は行われません。
    * 本番環境へ移行する際は、 本番用のAPIキーを設定 してください。


3. 決済の流れ

    本アプリでは、クレジットカード決済とコンビニ決済を搭載しており、それぞれで以下のように動作します。

    - クレジットカード決済の場合
        1. 商品をカートに追加し、支払い方法を選択後、購入ボタンをクリック
        2. クレジットカード情報を入力
        3. Stripe API を通じて決済を実行
        4. 決済成功後、注文が確定し商品が購入済みとなる(Ordersテーブルのstatusが「決済完了」となる)

    - コンビニ決済の場合
        1. 商品をカートに追加し、支払い方法としてコンビニを選択し、購入ボタンをクリック
        2. 電話番号を入力
        3. Stripe API を通じて決済を実行
        4. Ordersテーブルのstatusが「決済待機中」となる
        5. メールで支払い方法の詳細が届く（テスト環境では省略）
        6. Stripeで決済が確認され次第、「決済完了」になる


## 初期ユーザーデータ（UserSeeder）

| 名前       | メールアドレス          | パスワード           | 備考                         |
|------------|--------------------------|------------------------------|------------------------------|
| ユーザーA  | usera@example.com        | password            | 出品中の商品id:1~5 |
| ユーザーB  | userb@example.com        | password            | 出品中の商品id:6~10 |
| ユーザーC  | userc@example.com        | password            | 未出品         |


## PHPunitテストの実行について

1. テスト準備

    1. MySQLコンテナからMySQLに、rootユーザでログインして、demo_testというデータベースを作成します。

        新規でデータベースを作成する際は、権限の問題でrootユーザ（管理者)でログインする必要があります。

        1. `docker compose exec mysql bash`
        2. `mysql -u root -p`

        3.パスワードを求められるので、docker-compose.ymlファイルのMYSQL_ROOT_PASSWORD:に設定されているrootを入力します。

        4. MySQLログイン後、以下のコードでdemo_testというデータベースを作成します。
        ```
        CREATE DATABASE demo_test;
        ```

        5. 以下のコードで、demo_testが作成されていれば成功です。
        ```
        SHOW DATABASES;
        ```
    
    2. configファイルの変更

        configディレクトリの中のdatabase.phpを開き、mysql_testがあることを確認してください。（ない場合は、mysqlの配列部分をコピーして、その下に新たにmysql_testを作成します。）

        配列の中のdatabase、username、passwordは以下であることを確認してください。

        **'database' => 'demo_test',**  
        **'username' => 'root',**  
        **'password' => 'root',**  

        ＜以下、参照＞

        ```database.php
        'mysql_test' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => 'demo_test',
            'username' => 'root',
            'password' => 'root',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        ```

    3. テスト用の.envファイル作成

        1. PHPコンテナにログインし、.envをコピーして.env.testingというファイルを作成します。

        ```PHPコンテナ上
        cp .env .env.testing
        ```

        2. ファイルの作成ができたたら、.env.testingファイルの文頭部分にあるAPP_ENVとAPP_KEYを以下のように編集します。

        ```
        APP_ENV=test
        APP_KEY=
        ```

        3. .env.testingにデータベースの接続情報を以下のように編集してください。

        ```
        DB_DATABASE=demo_test
        DB_USERNAME=root
        DB_PASSWORD=root
        ```

        4. 先ほど「空」にしたAPP_KEYに新たなテスト用のアプリケーションキーを加えるために以下のコマンドを実行します。

        ```
        php artisan key:generate --env=testing
        ```

        5. キャッシュをクリアします。
        ```
        php artisan config:clear
        ```

        6. マイグレーションコマンドを実行して、テスト用のテーブルを作成します。
        ```
        php artisan migrate --env=testing
        ```


2. 外部決済システムStripeを使用しているため、PHPunitテストをするためにMockery が必要です。以下のコマンドでインストールします。

    ```
    composer require mockery/mockery --dev
    ```


3. テスト実行

    テストは以下のコマンドで実行できます。

    ```
    vendor/bin/phpunit tests/Feature/LoginTest.php  #テストファイル名を変えて実行してください
    ```

    テストファイル名一覧:  
        - 1. 会員登録機能テスト         RegisterTest.php  
        - 2. ログイン機能テスト         LoginTest.php  
        - 3. ログアウト機能テスト       LogoutTest.php  
        - 4. 商品一覧取得テスト         ProductListTest.php  
        - 5. マイリスト取得テスト       MyListFeatureTest.php  
        - 6. 商品検索機能テスト         ProductSearchTest.php  
        - 7. 商品詳細情報取得テスト     ProductDetailTest.php  
        - 8. いいね機能テスト          LikeProductTest.php  
        - 9. コメント送信機能テスト     CommentSubmissionTest.php  
        - 10. 商品購入機能テスト        PurchaseTest.php  
        - 11. 支払い方法選択機能テスト  未実装  
        - 12. 配送先変更機能            DeliveryAddressChangeTest.php  
        - 13. ユーザー情報取得         UserProfileTest.php  
        - 14. ユーザー情報変更         UserProfileEditTest.php  
        - 15. 出品商品情報登録         ExhibitionTest.php  




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