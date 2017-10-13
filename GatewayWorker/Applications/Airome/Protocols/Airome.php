<?php
namespace Protocols;
class Airome
{
    /**
     * 检查包的完整性
     * 如果能够得到包长，则返回包的在buffer中的长度，否则返回0继续等待数据
     * 如果协议有问题，则可以返回false，当前客户端连接会因此断开
     * @param string $buffer
     * @return int
     */
    public static function input($buffer)
    {
        // 获得换行字符"\n"位置
        $pos = strpos($buffer, "\n");
        // 没有换行符，无法得知包长，返回0继续等待数据
        if($pos === false)
        {
            return 0;
        }
        // 有换行符，返回当前包长（包含换行符）
        return $pos+1;
    }

    /**
     * 打包，当向客户端发送数据的时候会自动调用
     * @param string $buffer
     * @return string
     */
    public static function encode($buffer)
    {
        // json序列化，并加上换行符作为请求结束的标记
        $arr = str_split($buffer, 10);
        $to_send = "";
        foreach ($arr as $key => $value) {
            $to_send .= base64_encode($value)."\0";
        }
        return $to_send."\n";
    }

    /**
     * 解包，当接收到的数据字节数等于input返回的值（大于0的值）自动调用
     * 并传递给onMessage回调函数的$data参数
     * @param string $buffer
     * @return string
     */
    public static function decode($buffer)
    {
        var_dump($buffer);
        $base64 = explode("\0", trim($buffer));
        $data = "";
        foreach ($base64 as $key => $value) {
            $data .= base64_decode($value);
            // echo base64_decode($value);
        }
        return $data;
        // var_dump($base64);
        // 去掉换行，还原成数组
        //return base64_decode(trim($buffer), true);
    }
}