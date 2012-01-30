<?php

/**
 * @author Luka Pušić <luka@pusic.si>
 * proxyRequests can send loads of get requests through different proxys and user agents with ease
 */
class requests {

    function __construct() {
	/**
	 * default options
	 */
	$this->proxylist = 'proxy.txt';
	$this->postdata = false;
	$this->useragent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)'; //default uagent
	$this->uagents_list = false; //user agents file
	$this->gen_uagents = false; ; //choose to generate uagents on the fly
	$this->cookiefile = 'cookies.txt';
	$this->strict_uniq = false; //if true and count > proxies, throw error. if set to false, use one proxy as many times as needed
	$this->extra_headers = array(); //extra http headers to send (those not in curl options)
	$this->multi_limit = 1020; // more paralell requests are not recommended
    }

    /**
     * Function getMulti uses paralell requests curl_multi
     * @param type $url
     * @param type $count 
     */
    public function get($url, $count) {
	$limit = $this->multi_limit;
	if ($count < $limit) {
	    $limit = $count;
	}
	$passes = ceil($count / $limit);
	$offset = 0;

	for ($pass = 0; $pass < $passes; $pass++) {

	    $ch = array();
	    $master = curl_multi_init(); //create multi curl resource
	    $proxies = array_slice($this->proxies, $limit * $pass, $limit);
	    $offset = $pass * $limit;
	    if (($pass != 0) && ($pass == $passes - 1)) {
		$limit = $count % ($pass * $limit);
	    }

	    for ($i = 0; $i < $limit; $i++) {
		$ch[$i] = curl_init();
		curl_setopt($ch[$i], CURLOPT_URL, $url);
		curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch[$i], CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch[$i], CURLOPT_HTTPHEADER, $this->extra_headers);
		curl_setopt($ch[$i], CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch[$i], CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch[$i], CURLOPT_TIMEOUT, 20);
		if ($this->proxies) {
		    curl_setopt($ch[$i], CURLOPT_PROXY, $proxies[$i]);
		}
		if ($this->postdata) {
		    curl_setopt($ch[$i], CURLOPT_POST, true);
		    curl_setopt($ch[$i], CURLOPT_POSTFIELDS, $this->postdata);
		}
		curl_multi_add_handle($master, $ch[$i]); //add the current curl handle to the master
	    }
            $this->bad_proxies = array();
	    $running = null;
	    do {
		curl_multi_exec($master, $running); //while there are running connections just keep looping
	    } while ($running > 0);

	    for ($i = 0; $i < $limit; $i++) {
		if (curl_error($ch[$i])) {
		    echo ($i + $offset) . ': ' . curl_error($ch[$i]) . "\n";
                    array_push($this->bad_proxies,$i + $offset);
		} else {
		    echo ($i + $offset) . ": " . curl_multi_getcontent($ch[$i]) . ": OK\n";
		}
	    }
	    curl_multi_close($master); //destory the multi curl resource
	}
    }


    public function start() {
	if ($this->proxylist) {
	    $this->proxies = file($this->proxylist);
	    echo '* Loaded '.count($this->proxies) . " proxies\n";
	}
	if ($this->uagents_list) {
	    $this->uagents = file($this->uagents_list);
	    echo '* Loaded '.count($this->proxies) . " useragents\n";
	}
	$count = isset($this->count) ? $this->count : count($this->proxies);
	$this->get($this->url, $count);
    }

}

?>