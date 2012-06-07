<?php
/**
 * Graph Facebook Class
 * Paulo McNally Zambrana
 * 2010/09/03 22:49:15 -0600
 * http://developers.facebook.com/docs/reference/api/post
 * http://developers.facebook.com/docs/api
 */
class GraphFacebook
	{
	private $uid	=	0;
	private $post	=	array();
	public $json_response;
	
	private $graph = "https://graph.facebook.com/";
	private $api = "https://api.facebook.com/method/";
	
	function __construct( $uid, $post )
		{
		$this->uid		=	$uid;
		$this->post		=	$post;
		}
	
	private function _curl( $url, $data )
		{
		$request_data = http_build_query($data);
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, $request_data);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($c);
		$this->json_response = $result;
		if( $result === FALSE )
			{
			$this->error = curl_error($c);
			}
		$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
		curl_close($c);
		return $result;
    	}
	
	function _curl_get($url)
		{
		$ch = curl_init();
		$timeout = 20;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
		}
	
	public function Publish( )
		{
		return $this->_curl( $this->graph . $this->uid . "/feed?", $this->post );
		}
	
	public function SetPoint( )
		{
		return $this->_curl_get( $this->api . "dashboard.incrementCount?format=json&" . http_build_query( $this->post ) );
		}
	
	public function RemovePoint( )
		{
		return $this->_curl_get( $this->api . "dashboard.decrementCount?format=json&uid=".$this->uid."&" . http_build_query( $this->post ) );
		}
	

	public function GetUser( )
		{
		return $this->_curl_get( $this->graph . $this->uid . "/?" . http_build_query( $this->post ) );
		}
	
	public function CheckAccess( )
		{
		if( @preg_match( '/"error":/', $this->GetUser() ) )
			{
			return false;
			}
			else
				{
				return true;
				}
		}
	
	public function GetFriendList()
		{
		return $this->_curl_get( $this->graph . $this->uid . "/friends?" . http_build_query( $this->post ) );
		}
	
	public function SetLike()
		{
		return $this->_curl( $this->graph . $this->uid . "/likes", $this->post );
		}
	}
?>