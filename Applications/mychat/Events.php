<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose 
 */
use \GatewayWorker\Lib\Gateway;

class Events
{
   
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param string $message 具体消息
    */
   public static function onMessage($client_id, $message)
   {      
        // $message1=json_decode($message);
        //$message['time']=date('Y-m-d H:i:s');
      
             $message_data=json_decode($message,true);
       $message_data['time']=date('Y-m-d H:i:s');
       //var_dump($message_data);
         //echo $message_data;
        // 向所有人发送
       // Gateway::sendToAll("$client_id said $message");
       //$message_data = json_decode($message, true);
       //$message2=json_encode($message1);
       $message1=json_encode($message_data);
       echo $message1;
       echo "\n"."      ----------------*************************************---------------"."\n";
        Gateway::sendToAll($message1);

   }

  
}