# 全台資安防護網

全台資安防護網是一個以資安宣導、服務介紹與會員互動為主題的網站作品。頁面使用 Bootstrap、Vue.js 與 jQuery 製作響應式介面，後端以 PHP 提供會員註冊與登入 API，資料庫預設連接本機 XAMPP 的 MySQL。

## 網站功能

- 首頁輪播與資安數據展示
- 資安議題、服務方案與聯絡資訊區塊
- 站內搜尋視窗與簡易互動客服
- 會員註冊與登入
- PHP + MySQL 後端 API

## 技術架構

- 前端：HTML5、CSS3、Bootstrap 4、Vue.js、jQuery
- 後端：PHP、PDO
- 資料庫：XAMPP MySQL / MariaDB
- 圖示與素材：Font Awesome、本機圖片資源

## 專案結構

```text
.
├── css/                 # 樣式與 Bootstrap
├── images/              # 圖片資源
├── img/                 # Logo、icon、頁面圖片
├── js/                  # Vue、jQuery 與網站互動程式
├── index.html           # 網站主頁
├── api.php              # API 共用工具與安全回應
├── auth.php             # 自動判斷 XAMPP / Supabase 的會員資料來源
├── database.php         # XAMPP MySQL PDO 連線設定
├── login.php            # 會員登入 API
├── register.php         # 會員註冊 API
├── .htaccess            # Apache 基礎安全設定
└── SUPABASE_MIGRATION.md
```

## 本機執行

1. 啟動 XAMPP 的 Apache 與 MySQL。
2. 將專案放在 XAMPP 的 `htdocs` 目錄，或設定 Apache VirtualHost 指向本專案。
3. 建立資料庫與資料表：

```sql
CREATE DATABASE IF NOT EXISTS test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE test;

CREATE TABLE IF NOT EXISTS `user` (
  account VARCHAR(32) NOT NULL PRIMARY KEY,
  password VARCHAR(255) NOT NULL,
  name VARCHAR(40) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. 開啟 `http://localhost/專案資料夾名稱/index.html`。

## 資安優化

- 使用 PDO prepared statement，避免 SQL injection。
- API 僅允許 POST，避免帳號密碼出現在網址與伺服器紀錄。
- 註冊密碼使用 `password_hash()` 雜湊儲存。
- 舊明文密碼在成功登入後會自動升級為雜湊。
- 統一 JSON 回應，避免將例外細節直接顯示給使用者。
- 加入輸入長度、帳號格式與密碼長度檢查。
- Apache 停用目錄索引，並阻擋共用 PHP、log、Dreamweaver sync 檔直接下載。

## 資料庫設定與自動切換

網站前端固定呼叫 `login.php` 與 `register.php`，後端會依環境變數自動選擇資料來源。

沒有設定 Supabase 金鑰時，使用本機 XAMPP MySQL：

```text
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=test
DB_USER=root
DB_PASS=
```

有設定 `SUPABASE_URL` 且有任一 Supabase 金鑰時，改用 Supabase Auth：

```text
SUPABASE_URL=https://YOUR_PROJECT_ID.supabase.co
SUPABASE_KEY=YOUR_SUPABASE_PUBLISHABLE_OR_ANON_KEY
```

也可以使用 `SUPABASE_ANON_KEY` 或 `SUPABASE_PUBLISHABLE_KEY`。變數存在時會優先使用 Supabase，不需要修改前端程式。

Supabase 模式會把原本的 `account` 當作 Email 使用；XAMPP 模式則維持原本的帳號格式。
