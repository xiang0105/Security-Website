# Supabase 自動切換設定

本專案現在保留同一組前端欄位與 PHP API：

- 前端仍送出 `account`、`password`、`name`
- 登入仍呼叫 `login.php`
- 註冊仍呼叫 `register.php`
- 後端會自動判斷使用 XAMPP MySQL 或 Supabase Auth

判斷規則很單純：只要偵測到 `SUPABASE_URL`，且同時偵測到 Supabase 金鑰，就使用 Supabase；沒有 Supabase 金鑰就使用本機 XAMPP。

## 環境變數

### XAMPP 模式

不設定 Supabase 變數時，系統會使用 XAMPP MySQL。以下是預設值：

```text
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=test
DB_USER=root
DB_PASS=
```

### Supabase 模式

設定以下變數後會自動切換：

```text
SUPABASE_URL=https://YOUR_PROJECT_ID.supabase.co
SUPABASE_KEY=YOUR_SUPABASE_PUBLISHABLE_OR_ANON_KEY
```

金鑰變數可使用其中一個：

```text
SUPABASE_KEY
SUPABASE_ANON_KEY
SUPABASE_PUBLISHABLE_KEY
```

優先順序為 `SUPABASE_KEY`、`SUPABASE_ANON_KEY`、`SUPABASE_PUBLISHABLE_KEY`。

## 欄位一致性

前端不用分 XAMPP 或 Supabase，送出的欄位都一樣：

```json
{
  "account": "user@example.com",
  "password": "password123",
  "name": "王小明"
}
```

差異只在帳號格式：

- XAMPP 模式：`account` 可用 4-32 個英數字、底線、連字號或小數點。
- Supabase 模式：`account` 會當作 Email，所以必須是 Email 格式。

## Supabase 後台設定

1. 建立 Supabase 專案。
2. 到 Project Settings > API 取得 Project URL 與 publishable/anon key。
3. 到 Authentication > Providers 確認 Email provider 已啟用。
4. 若希望註冊後可直接登入，請依需求調整 Email confirmation 設定。

## PHP 端如何切換

切換邏輯集中在 `auth.php`：

```php
function use_supabase(): bool
{
    return supabase_url() !== '' && supabase_key() !== '';
}
```

`login.php` 與 `register.php` 只會呼叫：

```php
login_user($account, $password);
register_user($account, $password, $name);
```

實際走 XAMPP 或 Supabase 由 `auth.php` 自動決定。

## Supabase 資料儲存方式

目前 Supabase 模式使用 Supabase Auth：

- Email 來自 `account`
- 密碼交給 Supabase Auth 管理
- 顯示名稱存在使用者 metadata 的 `name`

因此不需要另外建立 `user` 資料表，也不會在自己的資料庫儲存明文密碼或密碼雜湊。

## 安全注意事項

- 前端不要放 service role 或 secret key。
- PHP 後端可使用 publishable/anon key 呼叫 Supabase Auth。
- 生產環境請使用 HTTPS。
- 若未設定 Supabase 變數，系統會回到 XAMPP，不會報錯。
- 如果要擴充會員資料，建議另外建立 `profiles` 表並啟用 Row Level Security。
