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
}
