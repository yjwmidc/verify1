<?php
namespace app\controller;

use app\BaseController;
use app\model\GeetestModel;
use think\Response;

class VerifyController extends BaseController
{
    /**
     * @title 生成验证链接
     * @desc 生成验证链接
     * @author VanillaNahida
     * @url /verify/create
     * @method post
     * @return json
     */
    public function create()
    {   
        //实例化模型类
        $GeetestModel = new GeetestModel();

        //赋值参数
        $groupId = $this->request->post('group_id', '');
        $userId = $this->request->post('user_id', '');

        //不存在返回400
        if (empty($groupId) || empty($userId)) {
            $Errorresult = ['code' => 400, 'msg' => '参数错误'];
            return json($Errorresult);
        }

        //生成唯一Token
        if (!ctype_digit($groupId) || !ctype_digit($userId)) {
            $Errorresult = ['code' => 400, 'msg' => '参数错误：group_id 和 user_id 必须为数字'];
            return json($Errorresult);
        }

        $token = $GeetestModel->generateToken($groupId, $userId);
        
        //保存验证数据
        $GeetestModel->saveVerifyData($token, ['group_id' => $groupId,'user_id' => $userId,'verified' => false,'code' => null]);
        
        // 生成验证链接
        $validate = $this->request->domain() . '/v/' . $token;
        //构建json并返回
        $result = ['code' => 0,'msg' => 'success','data' => ['ticket' => $token,'url' => $validate,'expire' => 300,]];
        return json($result);
    }

    /**
     * 时间 2026-01-03
     * @title 生成用户访问的验证页
     * @desc 生成用户访问的验证页
     * @author VanillaNahida
     * @url /verify/page?=
     * @method get
     * @return html
     */
    public function page()
    {
        //实例化模型类
        $GeetestModel = new GeetestModel();
        
        //从路由参数里获取ticket
        $ticket = $this->request->route('ticket', '');
        
        //如果ticket不存在 就返回无效并且400
        if (empty($ticket)) {
            return response('无效的验证链接', 400);
        }
        
        //验证用户ID是否匹配
        $data = $GeetestModel->getVerifyData($ticket);
        
        if (!$data) {
            return response('验证链接已过期或不存在', 400);
        }
        
        if ($data['verified']) {
            return response('您已完成验证，验证码: ' . $data['code'] . '，请在群内发送此验证码', 200);
        }
        
        $captchaId = $GeetestModel->getCaptchaId();
        
        //返回极验验证页面
        $html = $this->renderVerifyPage($ticket, $captchaId);
        
        return response($html)->contentType('text/html');
    }

    /**
     * 时间 2026-01-03
     * @title 处理极验验证结果
     * @desc 处理极验验证结果
     * @author VanillaNahida
     * @url /verify/callback
     * @method post
     * @return json
     */
    public function callback()
    {
        //实例化模型类
        $GeetestModel = new GeetestModel();

        //从post请求里获取信息
        $ticket = $this->request->post('ticket', '');
        $lotNumber = $this->request->post('lot_number', '');
        $captchaOutput = $this->request->post('captcha_output', '');
        $passToken = $this->request->post('pass_token', '');
        $genTime = $this->request->post('gen_time', '');
        
        //ticket不存在的处理
        if (empty($ticket)) {
            return json(['code' => 400, 'msg' => '参数错误']);
        }
        
        $data = $GeetestModel->getVerifyData($ticket);
        
        if (!$data) {
            return json(['code' => 400, 'msg' => '验证链接已过期']);
        }
        
        if ($data['verified']) {
            return json(['code' => 0, 'msg' => '已验证', 'data' => ['code' => $data['code']]]);
        }
        
        //验证
        $param = ['lot_number' => $lotNumber,'captcha_output' => $captchaOutput,'pass_token' => $passToken,'gen_time' => $genTime,];
        $geetestResult = $GeetestModel->verifyGeetest($param);
        
        if (!$geetestResult) {
            $result=['code' => 400, 'msg' => '验证失败，请重试'];
            return json($result);
        }
        
        //生成6位验证码
        $code = $GeetestModel->generateCode();
        
        // 更新验证数据
        $result = ['verified' => true, 'code' => $code, 'verified_at' => time()];
        $GeetestModel->updateVerifyData($ticket, $result);

        $jsonResult = ['code' => 0, 'msg' => '验证成功', 'data' => ['code' => $code]];
        return json($jsonResult);
    }

    /**
     * 时间 2026-01-03
     * @title 验证验证码
     * @desc 验证验证码
     * @author VanillaNahida
     * @url /verify/check
     * @method post
     * @return json
     */
    public function check()
    {
        //实例化模型类
        $GeetestModel = new GeetestModel();

        $groupId = $this->request->post('group_id', '');
        $userId = $this->request->post('user_id', '');
        $code = $this->request->post('code', '');
        
        if (empty($groupId) || empty($code)) {
            return json(['code' => 400, 'msg' => '参数错误：缺少必填参数 group_id 或 code', 'passed' => false]);
        }
        
        // 查找匹配的验证码
        if (!ctype_digit($groupId)) {
            return json(['code' => 400, 'msg' => '参数错误：group_id 必须为数字', 'passed' => false]);
        }

        if (!empty($userId) && !ctype_digit($userId)) {
            return json(['code' => 400, 'msg' => '参数错误：user_id 必须为数字', 'passed' => false]);
        }
        
        $data = $GeetestModel->findByCode($code, $groupId);
        
        if (!$data) {
            $allStatusData = $GeetestModel->findCodeByAllStatus($code, $groupId);
            
            if ($allStatusData) {
                if ($allStatusData['used'] == 1) {
                    return json(['code' => 400, 'msg' => '验证失败：验证码已使用', 'passed' => false]);
                } elseif ($allStatusData['expire_at'] < time()) {
                    return json(['code' => 400, 'msg' => '验证失败：验证码已过期', 'passed' => false]);
                } elseif ($allStatusData['verified'] != 1) {
                    return json(['code' => 400, 'msg' => '验证失败：验证码未完成验证', 'passed' => false]);
                } else {
                    return json(['code' => 400, 'msg' => '验证失败：验证码不存在或已失效', 'passed' => false]);
                }
            }
            
            return json(['code' => 400, 'msg' => '验证失败：验证码不存在或已失效', 'passed' => false]);
        }
        
        if (!empty($userId) && $data['user_id'] !== $userId) {
            return json(['code' => 400, 'msg' => '验证失败：用户ID不匹配', 'passed' => false]);
        }

        // 标记验证码为已使用
        $GeetestModel->markCodeAsUsed($code, $groupId);
        
        //构建返回数据
        $result = ['code' => 0,'msg' => '验证通过','passed' => true,'data' => ['user_id' => $data['user_id'],'group_id' => $data['group_id'],]];
        return json($result);
    }

    /**
     * 时间 2026-01-03
     * @title 删除验证码
     * @desc 删除验证码
     * @author VanillaNahida
     * @url /verify/clean
     * @method post
     * @return json
     */
    public function clean()
    {
        //实例化模型类
        $GeetestModel = new GeetestModel();
        $cleaned = $GeetestModel->cleanExpiredCodes();
        $result = ['code' => 0, 'msg' => "清理了 {$cleaned} 个过期验证码"];
        return json($result);
    }

    /**
     * 渲染验证页面HTML
     */
    private function renderVerifyPage(string $token, string $captchaId): string
    {
        return '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>入群验证</title>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        /* 简化字体定义，避免加载失败 */
        @font-face {
            font-family: "zh-cn-ys";
            src: url("https://webstatic.mihoyo.com/common/clgm-static/ys/fonts/zh-cn.ttf") format("truetype");
            font-display: swap;
        }
        
        body {
            font-family: "zh-cn-ys", -apple-system, BlinkMacSystemFont, "Segoe UI", "Microsoft YaHei", sans-serif;
            background-image: url("https://img.dkdun.cn/v1/2026/17/09f88b4b97cff02d.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px 30px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        h1 { 
            color: #333; 
            margin-bottom: 10px; 
            font-size: 28px;
            font-weight: bold;
        }
        
        .subtitle { 
            color: #666; 
            margin-bottom: 30px; 
            font-size: 16px;
        }
        
        .captcha-wrapper { 
            margin: 30px 0; 
            min-height: 50px;
            display: flex;
            justify-content: center;
        }
        
        .btn-verify {
            background: linear-gradient(90deg, #00b09b 0%, #96c93d 100%);
            color: white;
            border: none;
            padding: 16px 50px;
            font-size: 18px;
            font-weight: bold;
            font-family: "zh-cn-ys", "Courier New", monospace;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            min-width: 200px;
            box-shadow: 0 8px 20px rgba(0, 176, 155, 0.3);
            letter-spacing: 1px;
        }
        
        .btn-verify:hover { 
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(0, 176, 155, 0.4);
        }
        
        .btn-verify:active { 
            transform: translateY(0);
        }
        
        .btn-verify:disabled { 
            opacity: 0.6; 
            cursor: not-allowed; 
            transform: none !important;
        }
        
        .result { 
            margin-top: 25px; 
            padding: 20px; 
            border-radius: 12px; 
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .result.success { 
            background: linear-gradient(135deg, rgba(212, 237, 218, 0.9) 0%, rgba(200, 230, 210, 0.9) 100%); 
            color: #155724; 
            display: block; 
            border: 2px solid #c3e6cb;
        }
        
        .result.error { 
            background: linear-gradient(135deg, rgba(248, 215, 218, 0.9) 0%, rgba(240, 200, 210, 0.9) 100%); 
            color: #721c24; 
            display: block; 
            border: 2px solid #f5c6cb;
        }
        
        .code-display {
            font-size: 40px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #00b09b;
            margin: 20px 0;
            padding: 15px;
            background: white;
            border-radius: 10px;
            border: 2px dashed #00b09b;
            font-family: "zh-cn-ys", "Courier New", monospace;
        }
        
        .btn-copy {
            background: linear-gradient(90deg, #00b09b 0%, #00a8a8 100%);
            color: white;
            border: none;
            padding: 12px 35px;
            font-family: "zh-cn-ys", "Courier New", monospace;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
            display: none;
            box-shadow: 0 4px 12px rgba(0, 176, 155, 0.3);
        }
        
        .btn-copy:hover { 
            background: linear-gradient(90deg, #00a8a8 0%, #009999 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 176, 155, 0.4);
        }
        
        .btn-copy:active { 
            transform: translateY(0);
        }
        
        .toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.85);
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 1000;
            display: none;
            animation: slideIn 0.3s ease, fadeOut 0.3s ease 1.7s;
        }
        
        @keyframes slideIn {
            from { 
                opacity: 0; 
                transform: translateX(-50%) translateY(-20px); 
            }
            to { 
                opacity: 1; 
                transform: translateX(-50%) translateY(0); 
            }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        
        .tip { 
            font-size: 13px; 
            color: #666; 
            margin-top: 15px; 
            line-height: 1.5;
        }
        
        .status { 
            color: #666; 
            font-size: 14px; 
            margin: 15px 0;
            min-height: 20px;
        }
        
        .loader {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #00b09b;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .instruction {
            background: rgba(0, 176, 155, 0.1);
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
            font-size: 13px;
        }
        
        .instruction ol {
            margin-left: 20px;
            margin-top: 5px;
        }
        
        .instruction li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 入群验证</h1>
        <p class="subtitle">请点击下方按钮完成人机验证</p>
        
        <div class="instruction">
            <strong>验证步骤：</strong>
            <ol>
                <li>点击下方"开始验证"按钮</li>
                <li>完成极验行为验证</li>
                <li>获取6位数字验证码</li>
                <li>在群内发送6位数字验证码完成验证</li>
            </ol>
        </div>
        
        <div class="captcha-wrapper">
            <button type="button" class="btn-verify" id="btn-verify" onclick="startVerification()">
                开始验证
            </button>
            <div class="loader" id="loader"></div>
        </div>
        
        <p class="status" id="status">点击上方按钮开始验证</p>
        
        <div class="result" id="result">
            <p id="result-text"></p>
            <div class="code-display" id="code-display"></div>
            <button type="button" class="btn-copy" id="btn-copy">📋 一键复制验证码</button>
            <p class="tip" id="tip"></p>
        </div>
    </div>
    
    <div class="toast" id="toast"></div>

    <!-- 使用CDN加载极验库，备用方案 -->
    <script src="https://static.geetest.com/v4/gt4.js" onerror="geetestLoadError()"></script>
    
    <script>
        var TOKEN = "' . htmlspecialchars($token) . '";
        var CAPTCHA_ID = "' . htmlspecialchars($captchaId) . '";
        var captchaObj = null;
        var isGeetestLoaded = false;
        var btn = document.getElementById("btn-verify");
        var loader = document.getElementById("loader");
        var status = document.getElementById("status");
        
        // 页面加载时检查资源
        window.addEventListener("load", function() {
            console.log("页面加载完成");
            console.log("Token:", TOKEN);
            console.log("CaptchaID:", CAPTCHA_ID);
            
            // 检查极验脚本是否加载成功
            setTimeout(function() {
                if (typeof initGeetest4 === "undefined") {
                    console.warn("极验脚本加载失败，正在重试...");
                    loadGeetestFallback();
                } else {
                    console.log("极验脚本加载成功");
                    initGeetest();
                }
            }, 1000);
        });
        
        // 极验脚本加载失败处理
        function geetestLoadError() {
            console.error("极验脚本加载出错，使用备用方案");
            loadGeetestFallback();
        }
        
        // 加载极验备用方案
        function loadGeetestFallback() {
            var script = document.createElement("script");
            script.src = "https://gcaptcha4.geetest.com/load?captcha_id=" + CAPTCHA_ID;
            script.onerror = function() {
                status.innerHTML = "❌ 验证码加载失败，请刷新页面重试<br><small>如果问题持续，请联系管理员</small>";
                btn.disabled = true;
                btn.innerHTML = "验证码加载失败";
                btn.style.background = "#dc3545";
            };
            document.head.appendChild(script);
            
            // 设置超时检查
            setTimeout(function() {
                if (typeof initGeetest4 === "undefined") {
                    status.innerHTML = "⚠️ 验证码加载较慢，请稍候或刷新页面";
                }
            }, 5000);
        }
        
        // 初始化极验
        function initGeetest() {
            if (typeof initGeetest4 === "undefined") {
                status.innerHTML = "❌ 验证码库未加载，请刷新页面";
                return;
            }
            
            try {
                initGeetest4({
                    captchaId: CAPTCHA_ID,
                    product: "bind",
                    language: "zh-cn",
                    timeout: 10000
                }, function(obj) {
                    captchaObj = obj;
                    isGeetestLoaded = true;
                    
                    captchaObj.onReady(function() {
                        console.log("极验验证码已就绪");
                        status.innerHTML = "✅ 验证码已准备就绪";
                        btn.disabled = false;
                        btn.innerHTML = "开始验证";
                    });
                    
                    captchaObj.onSuccess(function() {
                        var result = captchaObj.getValidate();
                        if (!result) {
                            showError("验证失败，请重试");
                            return;
                        }
                        
                        // 显示加载状态
                        btn.disabled = true;
                        btn.style.display = "none";
                        loader.style.display = "block";
                        status.innerHTML = "⏳ 正在验证，请稍候...";
                        
                        // 发送验证请求
                        submitVerification(result);
                    });
                    
                    captchaObj.onError(function(error) {
                        console.error("极验错误:", error);
                        status.innerHTML = "❌ 验证码出错，请刷新页面重试";
                        btn.disabled = true;
                        btn.innerHTML = "验证码出错";
                    });
                    
                    captchaObj.onClose(function() {
                        status.innerHTML = "验证已取消，如需验证请重新点击";
                        btn.disabled = false;
                        btn.innerHTML = "重新验证";
                    });
                });
            } catch (error) {
                console.error("初始化极验时出错:", error);
                status.innerHTML = "❌ 验证码初始化失败";
            }
        }
        
        // 开始验证
        function startVerification() {
            if (!isGeetestLoaded || !captchaObj) {
                status.innerHTML = "⏳ 验证码正在加载，请稍候...";
                setTimeout(function() {
                    if (captchaObj) {
                        captchaObj.showCaptcha();
                    } else {
                        status.innerHTML = "❌ 验证码加载失败，请刷新页面";
                    }
                }, 1000);
                return;
            }
            
            try {
                captchaObj.showCaptcha();
            } catch (error) {
                console.error("显示验证码时出错:", error);
                status.innerHTML = "❌ 验证码出错，请刷新页面";
            }
        }
        
        // 提交验证
        function submitVerification(result) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "/verify/callback", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.timeout = 10000;
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    loader.style.display = "none";
                    btn.style.display = "inline-block";
                    
                    if (xhr.status === 200) {
                        try {
                            var data = JSON.parse(xhr.responseText);
                            if (data.code === 0) {
                                showSuccess(data.data.code);
                            } else {
                                showError(data.msg || "验证失败，请重试");
                                resetVerification();
                            }
                        } catch(e) {
                            console.error("解析响应失败:", e);
                            showError("服务器错误，请稍后重试");
                            resetVerification();
                        }
                    } else {
                        showError("网络连接失败，请检查网络后重试");
                        resetVerification();
                    }
                }
            };
            
            xhr.ontimeout = function() {
                loader.style.display = "none";
                btn.style.display = "inline-block";
                showError("请求超时，请重试");
                resetVerification();
            };
            
            var params = "ticket=" + encodeURIComponent(TOKEN) +
                "&lot_number=" + encodeURIComponent(result.lot_number) +
                "&captcha_output=" + encodeURIComponent(result.captcha_output) +
                "&pass_token=" + encodeURIComponent(result.pass_token) +
                "&gen_time=" + encodeURIComponent(result.gen_time);
            
            xhr.send(params);
        }
        
        // 重置验证
        function resetVerification() {
            btn.disabled = false;
            btn.innerHTML = "重新验证";
            if (captchaObj) {
                captchaObj.reset();
            }
        }
        
        // 显示成功
        function showSuccess(code) {
            var resultDiv = document.getElementById("result");
            var codeDisplay = document.getElementById("code-display");
            var copyBtn = document.getElementById("btn-copy");
            var tip = document.getElementById("tip");
            
            resultDiv.className = "result success";
            document.getElementById("result-text").innerHTML = "✅ 验证成功！您的验证码是：";
            codeDisplay.textContent = code;
            tip.innerHTML = "请复制此验证码，在群内发送以完成入群验证<br>验证码5分钟内有效。";
            
            copyBtn.style.display = "inline-block";
            status.style.display = "none";
            
            // 自动复制到剪贴板
            setTimeout(function() {
                copyToClipboard(code);
            }, 500);
        }
        
        // 显示错误
        function showError(msg) {
            status.innerHTML = "❌ " + msg;
            status.style.color = "#dc3545";
        }
        
        // 复制到剪贴板
        function copyToClipboard(text) {
            var copyBtn = document.getElementById("btn-copy");
            
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    showToast("✅ 验证码已自动复制，请在群聊中粘贴发送");
                }).catch(function() {
                    copyBtn.onclick = function() { fallbackCopy(text); };
                });
            } else {
                copyBtn.onclick = function() { fallbackCopy(text); };
                showToast("⚠️ 点击复制按钮手动复制验证码");
            }
        }
        
        // 备用复制方法
        function fallbackCopy(text) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            document.body.appendChild(textArea);
            textArea.select();
            
            try {
                var successful = document.execCommand("copy");
                if (successful) {
                    showToast("✅ 验证码已复制，请在群聊中粘贴发送");
                } else {
                    showToast("❌ 复制失败，请手动复制验证码");
                }
            } catch (err) {
                showToast("❌ 复制失败，请手动复制: " + text);
            }
            
            document.body.removeChild(textArea);
        }
        
        // 显示提示
        function showToast(message) {
            var toast = document.getElementById("toast");
            toast.textContent = message;
            toast.style.display = "block";
            
            setTimeout(function() {
                toast.style.display = "none";
            }, 3000);
        }
        
        // 绑定复制按钮事件
        document.getElementById("btn-copy").onclick = function() {
            var code = document.getElementById("code-display").textContent;
            if (code) {
                copyToClipboard(code);
            }
        };
    </script>
</body>
</html>';
    }
}