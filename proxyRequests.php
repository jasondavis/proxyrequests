<?php

/**
 * @author Luka Pušić <luka@pusic.si>
 * proxyRequests can send loads of get requests through different proxys and user agents with ease
 */
class requests {

    function __construct() {
	$this->proxylist = 'proxy.txt';
	$this->multi = true;
	$this->postdata = false;
	$this->multi_limit = 1020; // more paralell requests are not recommended
    }

    /**
     * Function get(url, proxy) will send
     * @param type $url
     * @param type $proxy
     * @return type string
     */
    public function get($url, $proxy = false) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	if ($proxy) {
	    curl_setopt($ch, CURLOPT_PROXY, $proxy);
	}
	if ($this->postdata) {
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postdata);
	}
	if (curl_error($ch)) {
	    return ": " . curl_error($ch) . "\n";
	} else {
	    return ": OK\n";
	}
    }

    /**
     * Function getMulti uses paralell requests curl_multi
     * @param type $url
     * @param type $count 
     */
    public function getMulti($url, $count) {
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
		echo 'remainder: ' . $limit;
	    }

	    for ($i = 0; $i < $limit; $i++) {
		$ch[$i] = curl_init();
		curl_setopt($ch[$i], CURLOPT_URL, $url);
		curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch[$i], CURLOPT_USERAGENT, rand(1, 10000));
		curl_setopt($ch[$i], CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch[$i], CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch[$i], CURLOPT_TIMEOUT, 30);
		if ($this->proxies) {
		    curl_setopt($ch[$i], CURLOPT_PROXY, $proxies[$i]);
		}
		if ($this->postdata) {
		    curl_setopt($ch[$i], CURLOPT_POST, true);
		    curl_setopt($ch[$i], CURLOPT_POSTFIELDS, $this->postdata);
		}
		curl_multi_add_handle($master, $ch[$i]); //add the current curl handle to the master
	    }

	    $running = null;
	    do {
		curl_multi_exec($master, $running); //while there are running connections just keep looping
	    } while ($running > 0);

	    for ($i = 0; $i < $limit; $i++) {
		if (curl_error($ch[$i])) {
		    echo ($i + $offset) . ': ' . curl_error($ch[$i]) . "\n";
		} else {
		    echo ($i + $offset) . ": OK\n";
		}
	    }
	    curl_multi_close($master); //destory the multi curl resource
	}
    }

    /**
     * Function that loads proxy file to an array
     * @return type array of strings
     */
    public function loadProxies() {
	return file($this->proxylist);
    }

    public function start() {
	$this->proxies = $this->loadProxies();
	$count = count($this->proxies);
	if ($this->multi) {
	    $this->getMulti($this->url, $count);
	} else {
	    for ($i = 0; $i < sizeof($this->proxies); $i++) {
		echo $i . $this->get($this->url, $proxies[$i]);
	    }
	}
    }

}

$request = new requests();
$request->proxylist = 'proxy.txt';
$request->url = 'http://something.com/';
$request->multi = true;
$request->start();
?>
