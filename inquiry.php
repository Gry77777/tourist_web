<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./js/jquery3.6.3.js"></script>
    <title>在线咨询</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .chat-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 85%;
            overflow: hidden;
        }

        .chat-header {
            background-color: #3498db;
            color: #fff;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
        }

        .chat-body {
            height: 300px;
            overflow-y: scroll;
            padding: 10px;
        }

        .message {
            margin-bottom: 10px;
            max-width: 80%;
            word-wrap: break-word;
        }

        .user-message {
            width: 20%;
            background-color: #3498db;
            color: #fff;
            border-radius: 5px;
            padding: 8px;
            margin-left: auto;
        }

        .bot-message {
            width: 20%;
            background-color: #ecf0f1;
            border-radius: 5px;
            padding: 8px;
            margin-right: auto;
        }

        .chat-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        .input-box {
            flex-grow: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }

        .send-button {
            background-color: #2ecc71;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .send-button:hover {
            background-color: #27ae60;
        }
    </style>
</head>

<body>
    <div class="chat-container">
        <div class="chat-header">在线咨询</div>
        <div class="chat-body" id="chatBody">
            <!-- 聊天记录将显示在这里 -->
        </div>
        <div class="chat-input">
            <input type="text" class="input-box" id="userInput" placeholder="输入你的问题...">
            <button class="send-button" onclick="sendMessage()">发送</button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var initialMessage = "欢迎使用金华旅游足迹网聊天机器人！您可以询问关于预订、景点信息、交通方式等问题，我会尽力提供帮助。";

            // Display initial message
            var initialBotMessage = $("<div>").addClass("message bot-message").text(initialMessage);
            $("#chatBody").append(initialBotMessage);
            // 定义发送消息的函数
            function sendMessage() {
                var userInput = $("#userInput").val();
                var chatBody = $("#chatBody");

                if (userInput.trim() !== "") {
                    // 用户消息
                    var userMessage = $("<div>").addClass("message user-message").text(userInput);
                    chatBody.append(userMessage);

                    // 根据关键词匹配选择机器人的回复
                    var botReply = getBotReply(userInput);
                    var botMessage = $("<div>").addClass("message bot-message").text(botReply);
                    chatBody.append(botMessage);

                    // 滚动到底部显示最新消息
                    chatBody.scrollTop(chatBody[0].scrollHeight);
                }

                // 清空输入框
                $("#userInput").val("");
            }

            // 将发送消息的函数绑定到按钮的点击事件
            $(".send-button").click(sendMessage);

            // 根据关键词返回机器人的回复
            function getBotReply(userInput) {
                userInput = userInput.toLowerCase();

                // 定义关键词数组
                var keywords = [{
                        term: "预订",
                        reply: "感谢您的预订咨询，我们会尽快联系您！"
                    },
                    {
                        term: "景点信息",
                        reply: "以下是金华内的一些热门景点：东阳木雕博物馆、婺源篁岭风景区、洋埠老街等。您有任何关于这些景点的特定问题吗？"
                    },
                    {
                        term: "交通方式",
                        reply: "前往金华的常见交通方式包括高铁、汽车等。具体可以根据您的出发地和喜好选择合适的交通工具。"
                    },
                    {
                        term: "酒店",
                        reply: "金华有许多优质酒店可供选择，如锦江之星、如家酒店等。您可以根据自己的需求和预算进行选择。"
                    },
                    {
                        term: "美食",
                        reply: "金华的美食非常丰富，有很多当地特色小吃和美味佳肴。例如，您可以尝试金华火腿、义乌小吃等。"
                    },
                    {
                        term: "活动",
                        reply: "金华定期举办各种文化活动、节庆活动和体育赛事。您可以查看当地的活动日历，了解最新的活动信息。"
                    },
                    {
                        term: "天气",
                        reply: "金华的天气四季分明，夏季温暖，冬季较冷。建议根据具体的出行时间来了解当时的天气状况，以便合理安排行程。"
                    },
                    {
                        term: "开放时间",
                        reply: "景点的开放时间因景点而异，您可以在前往之前查看各个景点的官方网站或咨询相关部门，以获取准确的开放时间信息。"
                    },
                    {
                        term: "特色",
                        reply: "金华有丰富的文化和历史特色，您可以在游览景点的同时品味到当地的独特魅力。"
                    },
                    {
                        term: "退款",
                        reply: "关于退款和取消的政策可能因不同服务提供商而异，建议您在预订前详细阅读相关政策或咨询客服以获取准确信息。"
                    },
                    {
                        term: "你好",
                        reply: "您好，欢迎咨询金华旅游相关信息。如果有其他问题，随时告诉我。"
                    },
                    {
                        term: "哈哈哈哈",
                        reply: "哈哈哈哈，你脑子呢？"
                    },
                    {
                        term: "你是？",
                        reply: "我是机器人古古"
                    },
                    {
                        term: "在吗？",
                        reply: "在，请问有什么事吗"
                    },
                    {
                        term: "哪里好玩点？",
                        reply: "东阳吧"
                    }
                ];

                // 遍历关键词数组，检查是否有任何关键词包含在用户输入中
                var matchedKeyword = keywords.find(keyword => userInput.includes(keyword.term));

                // 如果匹配到关键词，则返回对应的回复，否则返回默认回复
                return matchedKeyword ? matchedKeyword.reply : "抱歉，暂时无法回答这个问题。";
            }



            // 动态调整对话框宽度
            function adjustChatWidth() {
                var chatContainer = $(".chat-container");
                var chatBody = $(".chat-body");

                // 获取最大宽度
                var maxWidth = Math.max(chatContainer.width(), chatBody.width());

                // 设置对话框宽度
                chatContainer.width(maxWidth);
            }

            // 窗口大小变化时调整宽度
            $(window).resize(adjustChatWidth);

            // 初始调整宽度
            adjustChatWidth();
        });

        // async function sendMessage() {
        //     var userInput = $("#userInput").val();
        //     var chatBody = $("#chatBody");

        //     if (userInput.trim() !== "") {
        //         // 用户消息
        //         var userMessage = $("<div>").addClass("message user-message").text(userInput);
        //         chatBody.append(userMessage);

        //         // 调用后端服务获取 GPT 回复
        //         try {
        //             const response = await fetch('http://localhost:3000/get-gpt-reply', {
        //                 method: 'POST',
        //                 headers: {
        //                     'Content-Type': 'application/json',
        //                 },
        //                 body: JSON.stringify({
        //                     userInput
        //                 }),
        //             });

        //             const data = await response.json();

        //             // 在这里处理 GPT 的回复，可以根据需要进行处理
        //             var botMessage = $("<div>").addClass("message bot-message").text(data.reply);
        //             chatBody.append(botMessage);

        //             // 滚动到底部显示最新消息
        //             chatBody.scrollTop(chatBody[0].scrollHeight);
        //         } catch (error) {
        //             console.error(error);
        //             // 处理错误
        //         }

        //         // 清空输入框
        //         $("#userInput").val("");
        //     }
        // }
    </script>
</body>

</html>