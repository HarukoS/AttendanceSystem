# "Atte" Attendance System
    勤怠管理システム「Atte」

## 作成した目的
    企業の人事評価のため

## アプリケーションURL
    - 開発環境：http://localhost/
    - phpMyAdmin:http://localhost:8080/

## 他のリポジトリ

## 機能一覧
    - ユーザー登録（メール認証付き）、ログイン、ログアウト機能
    - 勤怠、休憩打刻
    - 日付別勤怠情報取得
    - ユーザー別、月別勤怠情報取得

## 使用技術（実行環境）
    - PHP 8.1
    - Laravel 10
    - MySQL 8.0

## テーブル設計

## ER図

## 環境構築

### Dockerビルド
    1. [git clone リンク](git@github.com:HarukoS/AttendanceSystem.git)
    2. docker-compose up -d --build

*MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせてdocker-compose.ymlファイルを編集してください。

### Laravel環境構築
    1. docker-compose exec php bash
    2. composer install
    3. .env.exampleファイルから.envを作成し、環境変数を変更
    4. php artisan key:generate
    5. php artisan migrate
    6. php artisan db:seed
