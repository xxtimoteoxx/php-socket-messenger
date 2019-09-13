<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8' />
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<style>
    * {
        box-sizing: border-box;
    }

    body {
        background-color: #edeff2;
        font-family: "Calibri", "Roboto", sans-serif;
    }

    .chat_window {
        position: absolute;
        width: calc(100% - 20px);
        max-width: 800px;
        height: 500px;
        border-radius: 10px;
        background-color: #fff;
        left: 50%;
        top: 50%;
        transform: translateX(-50%) translateY(-50%);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        background-color: #f8f8f8;
        overflow: hidden;
    }

    .top_menu {
        background-color: #fff;
        width: 100%;
        padding: 20px 0 15px;
        box-shadow: 0 1px 30px rgba(0, 0, 0, 0.1);
    }
    .top_menu .buttons {
        margin: 3px 0 0 20px;
        position: absolute;
    }
    .top_menu .buttons .button {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 10px;
        position: relative;
    }
    .top_menu .buttons .button.close {
        background-color: #f5886e;
    }
    .top_menu .buttons .button.minimize {
        background-color: #fdbf68;
    }
    .top_menu .buttons .button.maximize {
        background-color: #a3d063;
    }
    .top_menu .title {
        text-align: center;
        color: #bcbdc0;
        font-size: 20px;
    }

    .messages {
        position: relative;
        list-style: none;
        padding: 20px 10px 0 10px;
        margin: 0;
        height: 347px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .messages .message {
        clear: both;
        overflow: hidden;
        margin-bottom: 20px;
        transition: all 0.5s linear;
        opacity: 0;
    }
    .messages .message.left .avatar {
        background-color: #f5886e;
        float: left;
    }
    .messages .message.left .text_wrapper {
        background-color: #ffe6cb;
        margin-left: 20px;
    }
    .messages .message.left .text_wrapper::after, .messages .message.left .text_wrapper::before {
        right: 100%;
        border-right-color: #ffe6cb;
    }
    .messages .message.left .text {
        color: #c48843;
    }
    .messages .message.right .avatar {
        background-color: #fdbf68;
        float: right;
    }
    .messages .message.right .text_wrapper {
        background-color: #c7eafc;
        margin-right: 20px;
        float: right;
    }
    .messages .message.right .text_wrapper::after, .messages .message.right .text_wrapper::before {
        left: 100%;
        border-left-color: #c7eafc;
    }
    .messages .message.right .text {
        color: #45829b;
    }
    .messages .message.appeared {
        opacity: 1;
    }
    .messages .message .avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: inline-block;
    }
    .messages .message .text_wrapper {
        display: inline-block;
        padding: 20px;
        border-radius: 6px;
        width: calc(100% - 85px);
        min-width: 100px;
        position: relative;
    }
    .messages .message .text_wrapper::after, .messages .message .text_wrapper:before {
        top: 18px;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
    }
    .messages .message .text_wrapper::after {
        border-width: 13px;
        margin-top: 0px;
    }
    .messages .message .text_wrapper::before {
        border-width: 15px;
        margin-top: -2px;
    }
    .messages .message .text_wrapper .text {
        font-size: 18px;
        font-weight: 300;
    }

    .bottom_wrapper {
        position: relative;
        width: 100%;
        background-color: #fff;
        padding: 20px 20px;
        position: absolute;
        bottom: 0;
    }
    .bottom_wrapper .message_input_wrapper {
        display: inline-block;
        height: 50px;
        border-radius: 25px;
        border: 1px solid #bcbdc0;
        width: calc(100% - 160px);
        position: relative;
        padding: 0 20px;
    }
    .bottom_wrapper .message_input_wrapper .message_input {
        border: none;
        height: 100%;
        box-sizing: border-box;
        width: calc(75% - 40px);
        position: absolute;
        outline-width: 0;
        color: gray;
        left: 25%;
        border-left: solid 1px #bcbdc0;
        padding-left: 10px;
    }
    .bottom_wrapper .message_input_wrapper .name_input {
        border: none;
        height: 100%;
        box-sizing: border-box;
        width: calc(25% - 40px);
        position: absolute;
        outline-width: 0;
        color: gray;
    }
    .bottom_wrapper .send_message {
        width: 140px;
        height: 50px;
        display: inline-block;
        border-radius: 50px;
        background-color: #a3d063;
        border: 2px solid #a3d063;
        color: #fff;
        cursor: pointer;
        transition: all 0.2s linear;
        text-align: center;
        float: right;
    }
    .bottom_wrapper .send_message:hover {
        color: #a3d063;
        background-color: #fff;
    }
    .bottom_wrapper .send_message .text {
        font-size: 18px;
        font-weight: 300;
        display: inline-block;
        line-height: 48px;
    }

    .message_template {
        display: none;
    }

    .system_error{
        color: #bcbdc0;
        font-weight: bold;
        font-style: italic;
    }

    .system_msg{
        color: #bcbdc0;
        font-style: italic;
    }


</style>
    <?
    $colours = array('007AFF','FF7000','FF7000','15E25F','CFC700','CFC700','CF1100','CF00BE','F00');
    $user_colour = $colours[array_rand($colours)];
    ?>
<script language="javascript" type="text/javascript">
    $(document).ready(function(){

            var Message;
            Message = function (arg) {
                this.text = arg.text, this.message_side = arg.message_side;
                this.draw = function (_this) {
                    return function () {
                        var $message;
                        $message = $($('.message_template').clone().html());
                        $message.addClass(_this.message_side).find('.text').html(_this.text);
                        $('.messages').append($message);
                        return setTimeout(function () {
                            return $message.addClass('appeared');
                        }, 0);
                    };
                }(this);
                return this;
            };

                var  sendMessage;

                sendMessage = function (text, message_side) {
                    var $messages, message;
                    if (text.trim() === '') {
                        return;
                    }
                    $('.message_input').val('');
                    $messages = $('#message_box');
                    message = new Message({
                        text: text,
                        message_side: message_side
                    });
                    message.draw();
                    return $messages.animate({ scrollTop: $messages.prop('scrollHeight') }, 300);
                };




        //create a new WebSocket object.
        //var wsUri = "ws://localhost:9000/demo/server.php";
        var wsUri = "ws://git.local:25003/server.php";
        websocket = new WebSocket(wsUri);
        websocket.onopen = function(ev) { // connection is open
            $('#message_box').append("<div class=\"system_msg\">Connected!</div>"); //notify user
        }
        $('#send-btn').click(function(){ //use clicks message send button
            var mymessage = $('#message').val(); //get message text
            var myname = $('#name').val();; //get user name
            if(myname == ""){ //empty name?
                alert("Enter your Name please!");
                return;
            }
            if(mymessage == ""){ //emtpy message?
                alert("Enter Some message Please!");
                return;
            }
            //prepare json data
            var msg = {
                message: mymessage,
                name: myname,
                color : '<?php echo $user_colour; ?>'
            };
            //convert and send data to server
            websocket.send(JSON.stringify(msg));
        });
        //#### Message received from server?
        websocket.onmessage = function(ev) {
            var msg = JSON.parse(ev.data); //PHP sends Json data
            var type = msg.type; //message type
            var umsg = msg.message; //message text
            var uname = msg.name; //user name
            var ucolor = msg.color; //color
            var ualign = msg.align; //color

            if(type == 'usermsg')
            {
                sendMessage(umsg, ualign);
            }
            if(type == 'system')
            {
                $('#message_box').append("<div class=\"system_msg\">"+umsg+"</div>");
            }
            $('#message').val(''); //reset text
        };

        websocket.onerror	= function(ev){
            var err = '';
            if(!ev.data) err ='Could not connect to server!';
            else err = ev.data;

            $('#message_box').append("<div class=\"system_error\">Error Occurred - "+err+"</div>");
        };

        websocket.onclose 	= function(ev){$('#message_box').append("<div class=\"system_msg\">Connection Closed</div>");};
    });
</script>
</head>
<body>
<div class="chat_window">
    <div class="top_menu">
        <div class="buttons">
            <div class="button close"></div>
            <div class="button minimize"></div>
            <div class="button maximize"></div>
        </div>
        <div class="title">Chat</div>
    </div>
    <ul class="messages" id="message_box"></ul>
    <div class="bottom_wrapper clearfix">
        <div class="message_input_wrapper">
            <input class="name_input"  id="name" placeholder="Type your name" style="color:#<?=$user_colour?>"/>
            <input class="message_input"  id="message" placeholder="Type your message"/>
        </div>
        <div class="send_message">
            <div class="icon"></div>
            <div class="text"  id="send-btn">Send</div>
        </div>
    </div>
</div>
<div class="message_template"><li class="message"><div class="avatar"></div><div class="text_wrapper"><div class="text"></div></div></li></div>
</body>
</html>