<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Proxy;
use App\Url;
use Goutte\Client;
use Illuminate\Http\Request;

class DataController extends Controller
{
    
	public function getIndex()
	{
		$urls = Url::all();

		return view("app/index", compact('urls'));
	}

	/**
	 * @return insert data to url table
	 */

	public function postUrl(Request $request){

	    $this->validate($request, [
	        'name' => 'required|unique:urls|max:255',
	    ]);		
		$url = Url::create($request->all());
		dd($url);
	}

	/**
	 * @return get all assosiative link form this url and insert all url
	 * to database
	 */

	public function getGeturl(Request $request){

		echo $this->proxylist();

		$url = $request->get('url');
    	$client = new Client();
    	$userAgent = "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36";
    	//Set proxy using tor
		$guzzleClient = new \GuzzleHttp\Client([
            "headers"         => [
                "User-Agent"  => $userAgent,
            ],			
		    'curl' => [
		        CURLOPT_PROXY => $this->proxylist(),//'127.0.0.1:9050',
		        CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_TIMEOUT_MS => 0,
                CURLOPT_CONNECTTIMEOUT => 0,		        
		    ],
		]); 

		if(!$guzzleClient){
			dd($guzzleClient);
			die();
		} 

		$client->setClient($guzzleClient);	

		$crawler = $client->request('GET', $url);
		dd($crawler->html());
	}

	public function proxylist()
	{
		// $file = file(url('proxylist.txt'));

		// return $file[array_rand($file)];
		$row = Proxy::orderByRaw("RAND()")->first();
		
		return $row->ip.':'.$row->port;
	}

	/**
	 * @return Collect latest 80 proxy list and insert it to database
	 */
	public function getProxylist()
	{

		$client = new Client();	
		$crawler = $client->request('GET', 'https://www.socks-proxy.net');

		$data = $crawler->filter('tbody tr')->each(function ($node) {
				$ip = $node->filter('td')->eq(0)->text();
				$port = $node->filter('td')->eq(1)->text();

				if(! Proxy::where('ip', $ip)->first() )
				Proxy::create(['ip' => $ip, 'port' => $port ]);
			});

		echo "success";
	}
}
