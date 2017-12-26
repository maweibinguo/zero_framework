<?php
/**
 * 请求类（模拟用户请求）
 * User: MOON
 * Date: 16/3/14
 */
namespace core\curl;

class CurlRequest {

	const TIMEOUT = 20;

	public static  $userAgents = array(
		'IOS4' => 'User-Agent:Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_0_1 like Mac OS X; ja-jp) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8A306 Safari/6531.22.7',
		'IOS5'=>'User-Agent:Mozilla/5.0 (iPhone; U; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9A334 Safari/7534.48.3',
		'IOS5_1'=>'User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 5_0_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9A405 Safari/7534.48.3',
		'WX_IPHONE' => 'User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B176 MicroMessenger/4.3.2',
		'WX_ANDROID' => 'User-Agent:Mozilla/5.0 (Linux; U; Android 2.3.6; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1 MicroMessenger/4.5.255',
		'FIREFOX' => 'User-Agent:Mozilla/5.0 (Windows; U; Windows NT 6.1; uk; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13 WebMoney Advisor',
		'WINDOWS7' => 'User-Agent:Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)',
	);

	public static $contentType = array(
		'Content-Type:application/x-www-form-urlencoded; charset=UTF-8',
		'Content-Type:application/x-www-form-urlencoded',
	);

	public static $cacheControl = array(
		'Cache-Control:no-cache',
		'Cache-Control:max-age=0',
	);

	public static $connection = array(
		'Connection:keep-alive',
	);

	public static $proxyConnection = array(
		'Proxy-Connection:keep-alive',
	);

	public static $acceptEncoding = array(
		'Accept-Encoding:gzip, deflate',
	);

	public static $accept = array(
		'Accept:*/*',
		'Accept:application/json, text/javascript, */*; q=0.01',
		'Accept:application/json, text/javascript, */*',
		'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',

	);

	public static $uir = array(
		'Upgrade-Insecure-Requests:1',
	);

	public static $xrw = array(
		'X-Requested-With:XMLHttpRequest',
	);

	public static $acceptLanguage = array(
		'Accept-Language:zh-CN,zh;q=0.8',
	);

	public static $xmad = array(
		'X-MicrosoftAjax:Delta=true',
	);

	public static $pragma = array(
		'Pragma:no-cache',
	);
	/**
	 * CURL请求入口
	 * @param $request array 请求参数
	 * @return array
	 */
	public function getData($request) {
		$result = array();
        $request['explodelimit'] = isset($request ['explodelimit']) ? $request ['explodelimit'] : 2;//截取总几条，从1开始
        $request['headerlimit'] = isset($request ['headerlimit']) ? $request ['headerlimit'] : 0;//获取第几个header，从0开始
		$request['header'] = true;
		$request ['dest'] = isset($request ['dest']) ? $request ['dest'] : '';
		$request['headers']['User-Agent'] = isset($request['headers']['User-Agent']) ? $request['headers']['User-Agent'] : self::$userAgents['IOS4'];
		$request['timeouts'] = isset ($request ['timeouts']) ? $request ['timeouts'] : self::TIMEOUT;
		$request['referer'] = isset($request['referer']) ? $request ['referer'] : $request ['dest'];
		$request['follwlocation'] = isset($request['follwlocation']) ? $request ['follwlocation'] : 1;
		$request['returntransfer'] = isset($request['returntransfer']) ? $request ['returntransfer'] : 1;
		$request ['charset'] = isset($request ['charset']) ? $request ['charset'] : 'utf-8';
		if (isset ( $request ['postdata'] )) {
			$request ['postdata'] = $this->setPostdata($request['charset'], $request ['postdata']);
		}
		$curl = $this->getCurl($request);
		$result ['data'] = '';
		if ($curl['httpcode'] == 200 || $curl['httpcode'] == 302){
            if($curl ['data']){
                //list ($result ['header'], $result ['data']) = explode("\r\n\r\n", $curl ['data'], $request['explodelimit']);
                $explode_arr = explode("\r\n\r\n", $curl ['data'], ($request['explodelimit']));
                $result ['header'] = isset($explode_arr[$request['headerlimit']]) ? $explode_arr[$request['headerlimit']] : '';
                $result ['data'] = isset($explode_arr[($request['explodelimit']-1)]) ? $explode_arr[($request['explodelimit']-1)] : '';
            }
		}
		$result ['httpcode'] = $curl['httpcode'];
		$result ['totaltime'] = $curl['totaltime'];
		if ($result ['data']) {
			if (isset ($request ['image_file'] )) {
				$result ['image_file'] = $request ['image_file'];
				$this->setImage ( $request ['image_file'], $result ['data'] );
			} elseif (isset ($request ['json_decode'])) {
				$result ['data'] = $this->setCharset ( $request ['charset'], $result ['data'] );
				$result ['json'] = json_decode ( $result ['data'], true );
			} elseif (isset ($result ['data']) && $result ['data']) {
				if( isset($request ['charset']) ){
					$result ['data'] = $this->setCharset ( $request ['charset'], $result ['data'] );
				}
				$result ['data'] = $this->tagsRemove ( $result ['data'] );
				$result ['data'] = $this->setMeta ( $result ['data'] );
			}
			if (isset ($request ['get_cookie'])) {
				$result ['cookie'] = $this->getCookie ( $result ['header'] );
			}
			if (isset ($request ['get_pagestate'])) {
				$result ['pagestate'] = $this->pagestate ( $result ['data'] );
			}
			if (isset ($request ['get_pages'])) {
				$result ['pages'] = $this->regex ( $result ['data'], $request ['get_pages'] );
			}
		}
		return $result;
	}

	/**
	 * CURL请求
	 * @param  $request
	 * @return array
	 */
	private function getCurl($request){
		$ch = curl_init ( $request ['dest'] );
		curl_setopt ( $ch, CURLOPT_REFERER, $request ['referer'] );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $request ['headers'] );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, $request['follwlocation']);
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, $request['returntransfer']);
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $request ['timeouts'] );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, $request ['timeouts'] );
		curl_setopt ( $ch, CURLOPT_HEADER, $request ['header'] );
		if (isset($request ['proxy'])) {
			curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
			curl_setopt($ch, CURLOPT_PROXY, $request ['proxy']['ip']); //代理服务器地址
			curl_setopt($ch, CURLOPT_PROXYPORT, $request ['proxy']['port']); //代理服务器端口
			//curl_setopt($ch, CURLOPT_PROXYUSERPWD, ":"); //http代理认证帐号，username:password的格式
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
		}

		if( isset($request ['cookie']) ) {
			if (strpos ( $request ['cookie'], '=' )) {
				curl_setopt ( $ch, CURLOPT_COOKIE, $request ['cookie'] );
			} elseif (isset($request ['cookie_file'])) {
				curl_setopt ( $ch, CURLOPT_COOKIEFILE, $request ['cookie_file'] );
			}
		}
		if (isset($request ['cookie_jar'])) {
			curl_setopt ( $ch, CURLOPT_COOKIEJAR, $request ['cookie_jar'] );
		}
		if (isset($request ['gzip'])) {
			curl_setopt ( $ch, CURLOPT_ENCODING, 'gzip' );
		}
		if (isset($request ['postdata'])) {
			curl_setopt ( $ch, CURLOPT_POST, 1 );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $request ['postdata'] );
		}
		if (isset($request ['username']) && isset($request ['password'])) {
			curl_setopt ( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
			curl_setopt ( $ch, CURLOPT_USERPWD, $request ['username'] . ':' . $request ['password'] );
		}
		$data = curl_exec ( $ch );
		$info = curl_getinfo ( $ch );
		curl_close ( $ch );

		return [
            'httpcode' => $info ['http_code'],
            'totaltime' => isset($info ['total_time'])? $info ['total_time']:0,
            'data' => $data
        ];
	}


	/**
	 * 设置图片
	 * @param  $image_file
	 * @param  $body
	 * @return bool
	 */
	private function setImage($image_file, $body){
		if ($image_file){
			file_put_contents($image_file, $body);
		}
		return true;
	}

	/**
	 * 获取cookie
	 * @param  $header	string
	 * @return array
	 */
	private function getCookie($header){
		$header .= "\r\n";
		preg_match_all("/Set-Cookie: (.*?)\r\n/", $header, $matches);
		return implode(';', $matches[1]);
	}

	/**
	 * 设置post参数方法
	 * @param  $charset
	 * @param  $postdata
	 * @return string
	 */
	private function setPostdata($charset, $postdata){
		if ($charset == 'gb2312' && $postdata){
			$postdata = iconv('utf-8', 'gb2312', $postdata);
		}
		return $postdata;
	}

	/**
	 * 设置字符类型
	 * @param  $charset
	 * @param  $body
	 * @return string
	 */
	private function setCharset($charset, $body){
		if ($charset == 'gb2312' && $body){
			$body = iconv('gb2312', 'utf-8//IGNORE', $body);
		}
		return $body;
	}

	/**
	 * 去除标签
	 * @param  $data
	 * @return string
	 */
	private function tagsRemove($data){
		if (strpos($data, '</body>')){
			$data = str_replace('</body>', '', $data);
			$data = str_replace('</html>', '', $data);
		}
		return $data;
	}
	/**
	 * 设置meta
	 * @param  $body
	 * @return string
	 */
	private function setMeta($body){
		if ($body){
			return '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $body;
		}
	}


	/**
	 * 匹配页码正则
	 * @param  $data
	 * @param  $pattern
	 * @return array
	 */
	private function regex($data, $pattern){
		if ($data && $pattern){
			if (preg_match($pattern, $data, $matches)) {
				return $matches[1];
			}
		}
	}

    /**
     * 获取真实IP
     * @return string
     */
    public static function getRealIp(){
        static $realip;
        if(isset($_SERVER)){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $realip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }else if(isset($_SERVER['HTTP_CLIENT_IP'])){
                $realip=$_SERVER['HTTP_CLIENT_IP'];
            }else{
                $realip=$_SERVER['REMOTE_ADDR'];
            }
        }else{
            if(getenv('HTTP_X_FORWARDED_FOR')){
                $realip=getenv('HTTP_X_FORWARDED_FOR');
            }else if(getenv('HTTP_CLIENT_IP')){
                $realip=getenv('HTTP_CLIENT_IP');
            }else{
                $realip=getenv('REMOTE_ADDR');
            }
        }
        return $realip;
    }
}
