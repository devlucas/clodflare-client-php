<?php

namespace cloudflaresdk\cloudflare;

class Operation{

	private static $domain = null;
	private static $user = null;
	public function __construct($domain, $user){
		self::$user = $user;
		self::$domain = ($domain == '*') ? self::listDomains() : $domain;

	}
	
	public function stats(){
		$args = array('a'=>'stats', 'z'=>null);
		if(is_array(self::$domain)){
			$stats = array();
			for ($i=0; $i < count(self::$domain); $i++) { 
				$args['z'] = self::$domain[$i];
				array_push($stats, self::post($args)->response->result);
			}
			return $stats;
		} else {
			$args['z'] = self::$domain;
			return self::post($args)->response->result;
		}
	}

	public function recLoad()
	{
		$args = array('a'=>'rec_load_all', 'z'=>null);
		if(!is_array(self::$domain)){
			$args['z']= self::$domain;
			return self::post($args)->response;
		}
		else{
			$result = array();
			for ($i=0; $i < count(self::$domain); $i++) { 
				$args['z']=self::$domain[$i];
				array_push($result, self::post($args)->response);
			}
			return $result;
		}
	}
	public function zoneCheck(){
		$domain = is_array(self::$domain)? implode(',', self::$domain) : self::$domain;
		$args = array('a'=>'zone_check', 'zones'=>$domain);
		return self::post($args)->response;
	}

	public function getZoneId(){
		
		$zones = self::showAll()->zones->objs;
		if(!is_array(self::$domain)){
			for ($i=0; $i < count($zones); $i++) { 
				if($zones[$i]->zone_name == self::$domain){
					return (object)array('zone_name' =>self::$domain,'zone_id' => $zones[$i]->zone_id);
				}
			}	
		}
		else {
			$result = array();
			for ($j=0; $j < count(self::$domain); $j++) { 
				for ($i=0; $i < count($zones); $i++) { 
					if($zones[$i]->zone_name == self::$domain[$j]){
						array_push($result, (object)array('zone_name' =>self::$domain[$j],'zone_id' => $zones[$i]->zone_id));
						
					}
				}		
			}

			return $result;
		}
		return null;

	}
	/* options class (r,s,t), geo (1 to enable)*/
	public function zoneIps($hours, $options = null){
		$args = array('a'=>'zone_ips', 'z'=>null, 'hours'=>$hours);
		$args = isset($options)? array_merge($args, $options):$args;
		if(!is_array(self::$domain)){
			$args['z']=self::$domain;
			return self::post($args)->response;
		}else{
			$result = array();
			for ($i=0; $i < count(self::$domain); $i++) { 
				$args['z']=self::$domain[$i];
				array_push($result, self::post($args)->response);
			}
			return $result;
		}

	}
	public function settings(){
		$args = array('a'=>'zone_settings', 'z'=>null);
		if(!is_array(self::$domain)){
			$args['z']=self::$domain;
			return self::post($args)->response;
		}else{
			$result = array();
			for ($i=0; $i < count(self::$domain); $i++) { 
				$args['z']=self::$domain[$i];
				array_push($result, self::post($args)->response);
			}
			return $result;
		}
	}
	public function ipLockup($ip){
		$args = array('a'=>'ip_lkup', 'z'=>null, 'ip'=>$ip);
		if(!is_array(self::$domain)){
			$args['z']=self::$domain;
			return self::post($args)->response;
		}else{
			$result = array();
			for ($i=0; $i < count(self::$domain); $i++) { 
				$args['z']=self::$domain[$i];
				array_push($result, self::post($args)->response);
			}
			return $result;
		}

	}
	private function listDomains(){	
		$domains = self::showAll();
		$domains = $domains->zones->objs;
		$list = array();
		for ($i=0; $i < count($domains); $i++) { 
			array_push($list, $domains[$i]->zone_name);
		}
		return $list;
	}


	private function showAll(){
		$args = array_merge(array('a'=>'zone_load_multi'), self::$user);
		return self::post($args)->response;
	}

	private function post($args){
		$args = array_merge(self::$user, $args);
		$cURL = curl_init("https://www.cloudflare.com/api_json.html");
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cURL, CURLOPT_HTTPGET, true);
		curl_setopt($cURL, CURLOPT_POSTFIELDS, $args); 
		$result = curl_exec($cURL);
		curl_close($cURL);
		$result = json_decode($result);
		return $result;
	}

}
