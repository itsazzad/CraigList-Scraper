<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Lead;
use App\Link;
use App\Proxy;
use App\Url;
use Goutte\Client;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public $urlId;

	public function getIndex()
	{
		return view("app/index");
	}


	/**
	 * @return view data and add url
	 */

	public function getUrls()
	{
		$urls = Url::all();

		return view("app/urls", compact('urls'));		
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

		$requesturl = $request->get('url');
		$url = Url::where('name', $requesturl)->firstOrFail();
		$this->urlId = $url->id;

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

		$client->setClient($guzzleClient);	

		$crawler = $client->request('GET', $requesturl);

		$isBlock = $crawler->filter('p')->text();

		if(strpos($isBlock,'blocked') != false ) {

			//$this->torNew();
			//return $this->getIndex();
			echo $idBlock;
		} else {

			$crawler->filter('a.i')->each(function ($node) {
				    $url = $node->attr("href");
				    //$link = $node->filter('a')->first();
				    $text = $node->text();
				    Url::find($this->urlId)->links()->create(['name'=>$url]);
			});			

		}

	}

	/**
	 * Showing all urls 
	 */

	public function getUrllist()
	{
		$urls = Url::all();

		return view('app.url-list', compact('urls'));

	}

	public function getLinks($url)
	{

		$url = Url::findOrfail($url);
		
		return view('app.links', compact('url'));
	}

	/**
	 * @return Get user data from craglist
	 */

	public function getInfo($url)
	{
		$link = Link::findOrfail($url);
		if($link) {
			$ul = parse_url($link->url->name);
			$url = 'http://'.$ul['host'].$link->name;
		}


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

		$client->setClient($guzzleClient);	

		$crawler = $client->request('GET', $url);

		$isBlock = $crawler->filter('p')->text();

		if(strpos($isBlock,'blocked') != false ) {

			echo $isBlock;

		} else {

			$lnk = $crawler->selectLink('reply')->link();
			$crawler = $client->click($lnk);

			if ($crawler->filterXpath("//div[@class='captcha']")->count()) {

				dd($crawler->html());

			} else {

				var_dump($crawler->html());
				$title = $crawler->filter('title')->text();
				$mobile = $crawler->filter('.mobile-only')->first()->text();
				$email = $crawler->filter('.mailapp')->first()->text();
				//echo $link->url .' '.$title .' '. $mobile.' '.$email;	
				$link->lead()->create(['title'=>$title, 'phone'=>$mobile, 'email'=>$email]);
			}
			
		}


		
	}

	/**
	 * @return @data list
	 */

	public function getInfolist()
	{
		$datas = Lead::all();

		return view('app.data-list', compact('datas'));
	}

	public function proxylist()
	{
		// $file = file(url('proxylist.txt'));

		// return $file[array_rand($file)];
		$row = Proxy::orderByRaw("RAND()")->first();
		
		return $row->ip.':'.$row->port;
	}

	public function getProxy()
	{
		$proxys = Proxy::all();
		return view('app.proxy', compact('proxys'));
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

		return redirect()->back()->with('message', 'Proxy List was updated');
	}
}
