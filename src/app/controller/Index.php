<?php

namespace app\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        $version = \think\facade\App::version();
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Group Verify Service</title>
    <style>
        @font-face {
            font-family: "zh-cn-ys";
            src: url("https://webstatic.mihoyo.com/common/clgm-static/ys/fonts/zh-cn.ttf") format("truetype");
            font-display: swap;
        }

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: "zh-cn-ys", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        body {
            font-family: "zh-cn-ys", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: url("https://img.dkdun.cn/v1/2026/17/4c6eba4c08df1fec.jpg") no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #fa709a;
        }
        .container {
            text-align: center;
            padding: 40px;
        }
        .content-box {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 60px 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .emoji {
            font-size: 80px;
            margin-bottom: 30px;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .version {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
            background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: none;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }
        .slogan {
            font-size: 24px;
            margin-bottom: 40px;
            opacity: 0.9;
            background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }
        .icp {
            font-size: 17px;
            margin-top: 60px;
            opacity: 0.8;
        }
        .icp a {
            background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            border-bottom: 1px dashed rgba(255,255,255,0.6);
            transition: all 0.3s;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }
        .icp a:hover {
            border-bottom-style: solid;
            opacity: 1;
        }
        .hitokoto {
            font-size: 18px;
            margin-top: 40px;
            opacity: 0.9;
            max-width: 600px;
            line-height: 1.8;
        }
        .hitokoto-text {
            font-style: italic;
            margin-bottom: 10px;
            background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }
        .hitokoto-from {
            font-size: 14px;
            opacity: 0.7;
            text-align: right;
            background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content-box">
            <div class="emoji">:)</div>
            <div class="version">ThinkPHP V' . $version . '</div>
            <div class="slogan">18载初心不改 - 你值得信赖的PHP框架</div>
            <div class="hitokoto" id="hitokoto">
                <div class="hitokoto-text" id="hitokoto-text">加载中...</div>
                <div class="hitokoto-from" id="hitokoto-from"></div>
            </div>
            <div class="icp">
                <a href="https://beian.miit.gov.cn/" target="_blank">鄂ICP备2025161794号-1</a>
            </div>
        </div>
    </div>
    <script>
        fetch("https://v1.hitokoto.cn/")
            .then(response => response.json())
            .then(data => {
                document.getElementById("hitokoto-text").textContent = data.hitokoto;
                document.getElementById("hitokoto-from").textContent = "——" + data.from;
            })
            .catch(error => {
                document.getElementById("hitokoto-text").textContent = "一言加载失败";
            });
    </script>
</body>
</html>';
        return $html;
    }

    public function hello($name = 'ThinkPHP8')
    {
        return 'hello,' . $name;
    }

    public function setup()
    {
        $rootPath = root_path();
        $envPath = $rootPath . '.env';

        if (is_file($envPath)) {
            return $this->renderSetupDone();
        }

        $defaults = [
            'app_debug'            => 'false',
            'geetest_captcha_id'   => '',
            'geetest_captcha_key'  => '',
            'geetest_api_server'   => 'https://gcaptcha4.geetest.com',
            'geetest_code_expire'  => '300',
            'api_key'              => '',
            'salt'                 => '',
            'db_sqlite_path'       => './database/geetest.db',
        ];

        if (!$this->request->isPost()) {
            return $this->renderSetupForm([], $defaults);
        }

        $values = [
            'app_debug'            => (string)$this->request->post('app_debug', $defaults['app_debug']),
            'geetest_captcha_id'   => (string)$this->request->post('geetest_captcha_id', $defaults['geetest_captcha_id']),
            'geetest_captcha_key'  => (string)$this->request->post('geetest_captcha_key', $defaults['geetest_captcha_key']),
            'geetest_api_server'   => (string)$this->request->post('geetest_api_server', $defaults['geetest_api_server']),
            'geetest_code_expire'  => (string)$this->request->post('geetest_code_expire', $defaults['geetest_code_expire']),
            'api_key'              => (string)$this->request->post('api_key', $defaults['api_key']),
            'salt'                 => (string)$this->request->post('salt', $defaults['salt']),
            'db_sqlite_path'       => (string)$this->request->post('db_sqlite_path', $defaults['db_sqlite_path']),
        ];

        foreach ($values as $k => $v) {
            $values[$k] = $this->sanitizeEnvValue($v);
        }

        $errors = $this->validateSetupValues($values);
        if ($errors) {
            return $this->renderSetupForm($errors, $values);
        }

        $databaseDir = $rootPath . 'database';
        $runtimeDir = $rootPath . 'runtime';

        if (!is_dir($databaseDir) && !@mkdir($databaseDir, 0775, true) && !is_dir($databaseDir)) {
            return $this->renderSetupForm(['无法创建 database 目录，请检查权限'], $values);
        }

        if (!is_dir($runtimeDir) && !@mkdir($runtimeDir, 0775, true) && !is_dir($runtimeDir)) {
            return $this->renderSetupForm(['无法创建 runtime 目录，请检查权限'], $values);
        }

        if (!is_writable($databaseDir)) {
            return $this->renderSetupForm(['database 目录不可写，请检查权限'], $values);
        }

        if (!is_writable($runtimeDir)) {
            return $this->renderSetupForm(['runtime 目录不可写，请检查权限'], $values);
        }

        if (!is_writable($rootPath)) {
            return $this->renderSetupForm(['项目根目录不可写，无法生成 .env，请检查权限'], $values);
        }

        $envContent = $this->buildEnvContent($values);
        if (@file_put_contents($envPath, $envContent, LOCK_EX) === false) {
            return $this->renderSetupForm(['写入 .env 失败，请检查权限'], $values);
        }

        $dbPath = $this->resolveSqlitePath($values['db_sqlite_path'], $rootPath);
        $initResult = $this->initSqliteDatabase($dbPath, $rootPath);
        if ($initResult !== true) {
            @unlink($envPath);
            return $this->renderSetupForm([$initResult], $values);
        }

        return $this->renderSetupSuccess();
    }

    private function validateSetupValues(array $values): array
    {
        $errors = [];

        if ($values['geetest_captcha_id'] === '') {
            $errors[] = 'GEETEST_CAPTCHA_ID 不能为空';
        }
        if ($values['geetest_captcha_key'] === '') {
            $errors[] = 'GEETEST_CAPTCHA_KEY 不能为空';
        }
        if ($values['api_key'] === '') {
            $errors[] = 'API_KEY 不能为空';
        }
        if (mb_strlen($values['api_key']) < 16) {
            $errors[] = 'API_KEY 建议至少 16 位';
        }
        if ($values['salt'] === '') {
            $errors[] = 'SALT 不能为空';
        }
        if (mb_strlen($values['salt']) < 32) {
            $errors[] = 'SALT 建议至少 32 位';
        }
        if ($values['geetest_api_server'] === '') {
            $errors[] = 'GEETEST_API_SERVER 不能为空';
        }
        if (!filter_var($values['geetest_api_server'], FILTER_VALIDATE_URL)) {
            $errors[] = 'GEETEST_API_SERVER 不是合法 URL';
        }
        if ($values['geetest_code_expire'] === '' || !ctype_digit($values['geetest_code_expire'])) {
            $errors[] = 'GEETEST_CODE_EXPIRE 必须是整数';
        } else {
            $expire = (int)$values['geetest_code_expire'];
            if ($expire < 30 || $expire > 3600) {
                $errors[] = 'GEETEST_CODE_EXPIRE 建议在 30~3600 之间';
            }
        }
        $appDebug = strtolower($values['app_debug']);
        if (!in_array($appDebug, ['true', 'false', '1', '0'], true)) {
            $errors[] = 'APP_DEBUG 只能是 true/false';
        }
        if ($values['db_sqlite_path'] === '') {
            $errors[] = 'DB_SQLITE_PATH 不能为空';
        }

        return $errors;
    }

    private function sanitizeEnvValue(string $value): string
    {
        $value = trim($value);
        $value = str_replace(["\r", "\n"], '', $value);
        return $value;
    }

    private function buildEnvContent(array $values): string
    {
        $appDebug = strtolower($values['app_debug']);
        if ($appDebug === '1') {
            $appDebug = 'true';
        }
        if ($appDebug === '0') {
            $appDebug = 'false';
        }

        $lines = [];
        $lines[] = 'APP_DEBUG = ' . $appDebug;
        $lines[] = 'WITH_ROUTE = true';
        $lines[] = 'DEFAULT_APP = index';
        $lines[] = 'DEFAULT_TIMEZONE = Asia/Shanghai';
        $lines[] = 'SHOW_ERROR_MSG = false';
        $lines[] = 'GEETEST_CAPTCHA_ID = ' . $this->envEncode($values['geetest_captcha_id']);
        $lines[] = 'GEETEST_CAPTCHA_KEY = ' . $this->envEncode($values['geetest_captcha_key']);
        $lines[] = 'GEETEST_API_SERVER = ' . $this->envEncode($values['geetest_api_server']);
        $lines[] = 'GEETEST_CODE_EXPIRE = ' . $values['geetest_code_expire'];
        $lines[] = 'API_KEY = ' . $this->envEncode($values['api_key']);
        $lines[] = 'SALT = ' . $this->envEncode($values['salt']);
        $lines[] = 'DB_DRIVER = sqlite';
        $lines[] = 'DB_SQLITE_PATH = ' . $this->envEncode($values['db_sqlite_path']);

        return implode("\n", $lines) . "\n";
    }

    private function envEncode(string $value): string
    {
        $value = $this->sanitizeEnvValue($value);
        if ($value === '') {
            return '""';
        }
        if (preg_match('/[\s#="\'\\\\]/', $value)) {
            $escaped = addcslashes($value, "\\\"");
            return '"' . $escaped . '"';
        }
        return $value;
    }

    private function resolveSqlitePath(string $sqlitePath, string $rootPath): string
    {
        $sqlitePath = trim($sqlitePath);
        if ($sqlitePath === '') {
            return $rootPath . 'database' . DIRECTORY_SEPARATOR . 'geetest.db';
        }

        if (strpos($sqlitePath, '/') === 0 || strpos($sqlitePath, '\\') === 0 || preg_match('/^[a-zA-Z]:/', $sqlitePath)) {
            return $sqlitePath;
        }

        $sqlitePath = ltrim($sqlitePath, '/\\');
        return $rootPath . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $sqlitePath);
    }

    private function initSqliteDatabase(string $absoluteDbPath, string $rootPath)
    {
        $dir = dirname($absoluteDbPath);
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            return '无法创建 SQLite 目录：' . $dir;
        }
        if (!is_writable($dir)) {
            return 'SQLite 目录不可写：' . $dir;
        }

        try {
            $pdo = new \PDO('sqlite:' . $absoluteDbPath);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $sqlFile = $rootPath . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . 'Geetest_Table.sql';
            if (!is_file($sqlFile)) {
                return '找不到迁移文件：' . $sqlFile;
            }

            $sql = (string)file_get_contents($sqlFile);
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($statements as $statement) {
                if ($statement !== '') {
                    $pdo->exec($statement);
                }
            }
        } catch (\Throwable $e) {
            return '初始化数据库失败：' . $e->getMessage();
        }

        return true;
    }

    private function renderSetupDone(): string
    {
        return $this->renderHtml('初始化已完成', '<p>.env 已存在，已无需再次初始化。</p><p><a href="/">返回首页</a></p>');
    }

    private function renderSetupSuccess(): string
    {
        return $this->renderHtml('初始化成功', '<p>已生成 .env 并初始化 SQLite 数据库。</p><p><a href="/">返回首页</a></p>');
    }

    private function renderSetupForm(array $errors, array $values): string
    {
        $errorHtml = '';
        if ($errors) {
            $items = '';
            foreach ($errors as $error) {
                $items .= '<li>' . htmlspecialchars((string)$error, ENT_QUOTES) . '</li>';
            }
            $errorHtml = '<div class="errors"><ul>' . $items . '</ul></div>';
        }

        $appDebug = htmlspecialchars((string)$values['app_debug'], ENT_QUOTES);
        $captchaId = htmlspecialchars((string)$values['geetest_captcha_id'], ENT_QUOTES);
        $captchaKey = htmlspecialchars((string)$values['geetest_captcha_key'], ENT_QUOTES);
        $apiServer = htmlspecialchars((string)$values['geetest_api_server'], ENT_QUOTES);
        $codeExpire = htmlspecialchars((string)$values['geetest_code_expire'], ENT_QUOTES);
        $apiKey = htmlspecialchars((string)$values['api_key'], ENT_QUOTES);
        $salt = htmlspecialchars((string)$values['salt'], ENT_QUOTES);
        $dbSqlitePath = htmlspecialchars((string)$values['db_sqlite_path'], ENT_QUOTES);

        $body = $errorHtml . '
<form method="post" class="form">
  <label>APP_DEBUG (true/false)</label>
  <input name="app_debug" value="' . $appDebug . '" placeholder="false" />

  <label>GEETEST_CAPTCHA_ID</label>
  <input name="geetest_captcha_id" value="' . $captchaId . '" />

  <label>GEETEST_CAPTCHA_KEY</label>
  <input name="geetest_captcha_key" value="' . $captchaKey . '" />

  <label>GEETEST_API_SERVER</label>
  <input name="geetest_api_server" value="' . $apiServer . '" />

  <label>GEETEST_CODE_EXPIRE</label>
  <input name="geetest_code_expire" value="' . $codeExpire . '" />

  <label>API_KEY</label>
  <input name="api_key" value="' . $apiKey . '" />

  <label>SALT</label>
  <input name="salt" value="' . $salt . '" />

  <label>DB_SQLITE_PATH</label>
  <input name="db_sqlite_path" value="' . $dbSqlitePath . '" />

  <button type="submit">生成 .env 并初始化</button>
</form>
<div class="tips">
  <p>该页面仅在 .env 不存在时可用。</p>
  <p>请确保项目目录的 database/ 与 runtime/ 可写。</p>
</div>';

        return $this->renderHtml('首次初始化', $body);
    }

    private function renderHtml(string $title, string $body): string
    {
        $titleEscaped = htmlspecialchars($title, ENT_QUOTES);
        return '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . $titleEscaped . '</title><style>
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial;max-width:820px;margin:40px auto;padding:0 16px;color:#222;}
h1{font-size:22px;margin:0 0 16px;}
.errors{background:#fff3f3;border:1px solid #ffd6d6;padding:12px;border-radius:8px;margin:12px 0;}
.form{display:flex;flex-direction:column;gap:10px;margin-top:12px;}
label{font-size:13px;color:#555;}
input{padding:10px 12px;border:1px solid #ddd;border-radius:8px;font-size:14px;}
button{padding:10px 12px;border:0;border-radius:8px;background:#1677ff;color:#fff;font-size:14px;cursor:pointer;}
button:hover{background:#0958d9;}
.tips{margin-top:16px;color:#666;font-size:13px;line-height:1.8;}
a{color:#1677ff;text-decoration:none;}
a:hover{text-decoration:underline;}
</style></head><body><h1>' . $titleEscaped . '</h1>' . $body . '</body></html>';
    }
}
