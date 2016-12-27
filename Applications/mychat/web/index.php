<html><head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>naiveman</title>
  <script type="text/javascript">
  //WebSocket = null;
  </script>
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/1.css" rel="stylesheet">
  <!-- Include these three JS files: -->
  <script type="text/javascript" src="/js/swfobject.js"></script>
  <script type="text/javascript" src="/js/web_socket.js"></script>
  <script type="text/javascript" src="/js/jquery.min.js"></script>

  <script type="text/javascript">
    if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
    WEB_SOCKET_SWF_LOCATION = "/swf/WebSocketMain.swf";
    //WEB_SOCKET_DEBUG = true;
    var ws, name, client_list={};

    // 连接服务端
    function connect() {
       // 创建websocket
       ws = new WebSocket("ws://"+document.domain+":7274");
       // 当socket连接打开时，输入用户名
      
       ws.onopen = onopen;
    
       // 当有消息时根据消息类型显示不同信息
       ws.onmessage = onmessage;
         
       ws.onclose = function() {
        console.log("连接关闭，定时重连");
          connect();
       };
       ws.onerror = function() {
        console.log("出现错误");
       };
    }
       
    

    // 连接建立时发送登录信息
    function onopen()
    {
        if(!name)
        {
            show_prompt();
        }
        // 登录
        var login_data = '{"type":"login","client_name":"'+name.replace(/"/g, '\\"')+'","room_id":"<?php echo isset($_GET['room_id']) ? $_GET['room_id'] : 1?>"}';
        console.log("websocket握手成功，发送登录数据:"+login_data);
        ws.send(login_data);

    }

    // 服务端发来消息时
    function onmessage(e)
    {    
      //console.log(e);
      console.log(e.data);
      //console.log(e.type);
     
        var data = eval("("+e.data+")");
       // console.log(data);
        switch(data['type']){
            // 服务端ping客户端
            case 'ping':
                
                ws.send('{"type":"pong"}');
                break;;
            // 登录 更新用户列表
            case 'login':
                
                ///{"type":"login","client_id":xxx,"client_name":"xxx","client_list":"[...]","time":"xxx"}
                say(data['client_name']+' 加入了聊天室', data['time'],data['content']='');
                if(data['client_list'])
                {
                    client_list = data['client_list'];
                }
                else
                {
                    client_list[data['client_id']] = data['client_name']; 
                }
              //  flush_client_list();
                console.log(data['client_name']+"登录成功");
                break;
            // 发言
            case 'say':
                //{"type":"say","from_client_id":xxx,"to_client_id":"all/client_id","content":"xxx","time":"xxx"}
                say(data['client_name'],data['time'] ,data['content'],data['from_client_id']);
                break;
            // 用户退出 更新用户列表
            case 'logout':
                //{"type":"logout","client_id":xxx,"time":"xxx"}
                say(data['from_client_id'], data['from_client_name'], data['from_client_name']+' 退出了', data['time']);
                delete client_list[data['from_client_id']];
               
        }
    }

    // 输入姓名
    function show_prompt(){  
        name = prompt('输入你的名字：', '');
        if(!name || name=='null'){  
            name = '游客';
        }
    }

    // 提交对话
    function onSubmit() {
      var input = document.getElementById("textarea");
      var to_client_id = $("#client_list option:selected").attr("value");
      var to_client_name = $("#client_list option:selected").text();
      ws.send('{"type":"say","to_client_id":"'+to_client_id+'","client_name":"'+name+'","content":"'+input.value.replace(/"/g, '\\"').replace(/\n/g,'\\n').replace(/\r/g, '\\r')+'"}');
    
      input.value = "";
      input.focus();
    }

   

    // 发言
    // function say(from_client_id, from_client_name, content, time){
    //   $("#dialog").append('<div class="speech_item"><img src="http://lorempixel.com/38/38/?'+from_client_id+'" class="user_icon" /> '+from_client_name+' <br> '+time+'<div style="clear:both;"></div><p class="triangle-isosceles top">'+content+'</p> </div>');
    // }

    function say( client_name,time,content){
      $("#dialog").append('<div>'+'&nbsp;&nbsp;&nbsp;&nbsp;'+client_name+'<br>'+'&nbsp;&nbsp;&nbsp;&nbsp;'+time+'<br>'+'<p style="color:red">'+content+'<p>'+ '</div>');
    }
   
  </script>
</head>
<body onload="connect();">
    

       <div class="main">
            <div id="dialog" class="main_dia">
              
            </div>

            <form onsubmit="onSubmit(); return false;">
                <textarea class="main_text" id="textarea"></textarea>
               <input type="submit" value="发送" class="main_sub"></input>
            </form>
       </div>
    
</body>
</html>
