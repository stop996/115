<?php
error_reporting(0);
//需要提供apikey 和修改localhost为本地IP
$localhost = '172.17.0.1';
$api_key = '' ;

ob_start('ob_gzhandler');
header("Access-Control-Allow-Headers:Accept, Accept-Language, Authorization, Cache-Control, Content-Disposition, Content-Encoding, Content-Language, Content-Length, Content-MD5, Content-Range, Content-Type, Date, Host, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, Origin, OriginToken, Pragma, Range, Slug, Transfer-Encoding, Want-Digest, X-MediaBrowser-Token, X-Emby-Authorization");
header("Access-Control-Allow-Methods:GET, POST, PUT, DELETE, PATCH, OPTIONS");
header("Access-Control-Allow-Origin:*");
header("Content-Type:application/json");
header("Proxy-Connection:keep-alive");
header("Content-Encoding:gzip");
header("Expires:0");
function triggerRequest($url){
        $method = "GET";  //可以通过POST或者GET传递一些参数给要触发的脚本
        $url_array = parse_url($url); //获取URL信息，以便平凑HTTP HEADER
        $port = isset($url_array['port'])? $url_array['port'] : 80; 
      
        $fp = fsockopen($url_array['host'], $port, $errno, $errstr, 30); 
        if (!$fp) {
                return FALSE;
        }
        $getPath = $url_array['path'] ."?". $url_array['query'];
        $header = $method . " " . $getPath;
        $header .= " HTTP/1.1\r\n";
        $header .= "Host: ". $url_array['host'] . "\r\n "; //HTTP 1.1 Host域不能省略
        $header .= "Connection:Close\r\n";
        fwrite($fp, $header);
        //echo fread($fp, 1024); //我们不关心服务器返回
        fclose($fp);

}
$cookie1 = file("cookie.txt");
$cookie =  $cookie1[count($cookie1)-1];//显示
if ($cookie==''){
	$cookie =  $cookie1[count($cookie1)-2];//显示
}


$patch = str_replace('/playback2.php','',$_SERVER['PHP_SELF']);
$patch = str_replace('PlaybackInfo','',$patch);
$url = 'http://'.$localhost.':8096/Users/aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'.$patch.'?'.'&api_key='.$api_key;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
curl_setopt($ch, CURLOPT_USERAGENT,$user_agent);
$response = curl_exec($ch);
curl_close($ch);
$response = json_decode($response);
$response = json_decode(json_encode($response), true);
$file_Path = $response["MediaSources"][0]['Path'] ;
//print_r($response);

$file_name = pathinfo($file_Path)['basename'] ;

class MyDB extends SQLite3
{
	function __construct()
	{
	 $this->open('115.db');
	}
}
$db = new MyDB();
if(!$db){

} else {

}

$sql ="SELECT * from list where  n='".$file_name."'";
$ret = $db->query($sql);
while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
	$s = $row['s'];
	$pc = $row['pc'];
}

$db->close();


$header = array();

if (empty($pc)){
	echo $file_name;

	$url ='';
} else {
	$url='https://webapi.115.com/files/download?pickcode='.$pc;
}
//获取115下载链接
$user_agent = " Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36 115Browser/9.2.1";
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_USERAGENT,$user_agent);

curl_setopt($ch,CURLOPT_COOKIE,$cookie);
$response = curl_exec($ch);


curl_close($ch);
$response = json_decode($response);
$response = json_decode(json_encode($response), true);
$file_url = $response["file_url"];

//echo $url;
//echo $file_url;
//提交下载任务
$file_Path1 = urlencode($file_Path);
$file_url1 = urlencode($file_url);

$url = 'http://127.0.0.1:7600/info?url='.$file_url1.'&name='.$file_Path1.'&size='.$s  ;

if (file_exists($file_Path)) {
	if(filesize($file_Path)<500000){
//		echo '小文件';

		triggerRequest($url);
		/*忽略执行结果
		while (!feof($fp)) {
		    echo fgets($fp, 128);
		}*/
		fclose($fp);
		sleep(7);
//		shell_exec($down);	
	}

	 else {
		}
} else {

//echo '文件不存在';
	
}

$url = str_replace('/new2.php','',$_SERVER['REQUEST_URI']);
$url = 'http://'.$localhost.':8096'.$url.'&api_key='.$api_key;
$data = file_get_contents("php://input");
//echo $data;
$ch = curl_init($url); //请求的URL地址

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//$data JSON类型字符串
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));

$data = curl_exec($ch);





echo json_encode(json_decode($data));




?>
