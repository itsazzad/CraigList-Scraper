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
		
		return redirect()->back()->with('message',"Link insert was successfull");
	}

	/**
	 * @return get all assosiative link form this url and insert all url
	 * to database
	 */

	public function getGeturl(Request $request){

		// Can't add proxy error handling 
		// I need to find a good way
		// So can't run it in while loop

		$requesturl = $request->get('url');
		$url = Url::where('name', $requesturl)->firstOrFail();
		if($url->links()->count() > 0){
			return redirect()->back()->with('message', 'You already do it !! . Try with new url :)');
		}
		$this->urlId = $url->id;

		//if proxy list table is empty
		if(!$this->proxylist()) return redirect()->back()->with('message', 'Please add porxy list or update proxy list');

		$crawler = $this->helper_crawler($url);
		$count = 0;
		$run = true;

//while($run){

		$isBlock = $crawler->filter('p')->text();

		if(strpos($isBlock,'blocked') != false ) {

			//$this->torNew();
			//return $this->getIndex();
			$crawler = $this->helper_crawler($url);
		} else {

			$crawler->filter('a.i')->each(function ($node) {
				    $url = $node->attr("href");
				    //$link = $node->filter('a')->first();
				    $text = $node->text();
				    Url::find($this->urlId)->links()->create(['name'=>$url]);
			});

			$run = false;
		}

		return redirect()->back()->with('message', "Link was scraped please view link");
		//}
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


		$crawler = $this->helper_crawler($url);

		$count = 0; 

	$run = true;

	//while($run){

		$isBlock = $crawler->filter('p')->text();

		if(strpos($isBlock,'blocked') != false ) {
			//next process and change ip
			$crawler = $this->helper_crawler($url);

		} else {

			$lnk = $crawler->selectLink('reply')->link();
			$crawler = $client->click($lnk);

			if ($crawler->filterXpath("//div[@class='captcha']")->count()) {

				//Next process and change ip
				$crawler = $this->helper_crawler($url);

			} else {

				//Need to apply try cache here
				//Can't do it with try cache so can't enable mobile and name
				//Many job post have't mobile and name
				echo "hello";
				$title = $name = $email = $mobile = "";

				if(!empty($title = $crawler->filter('title'))) {
					$title = $title->text();
				}
				
				if(!empty($name = $crawler->filterXPath('//div[@class="reply_options"]//ul/li'))){
					$name = $name->text();
				}
		    	
		    	if(!empty($email = $crawler->filterXPath('//ul/li/a[@class="mailapp"]'))) {
		    		$email = $email->text();
		    	}
		    	
		    	if($mobile = $crawler->filterXPath('//a[@class="mobile-only replytellink"]')){
		    		$mobile = $mobile->attr('href');
		    	}
		    	
	
				$link->lead()->create(['title'=>$title,'email'=>$email, 'name'=> $name, 'phone' => $mobile]);

				$run = false;
			}
			
		}

			return redirect()->back()->with('message', "Please check scrap data");
		
		//}
	}

	/**
	 * @return @data list
	 */

	public function getInfolist()
	{
		$leads = Lead::all();
		return view('app.data-list', compact('leads'));
	}

	public function proxylist()
	{
		// $file = file(url('proxylist.txt'));

		// return $file[array_rand($file)];
		$row = Proxy::orderByRaw("RAND()")->first();
		if(count($row) > 0 )		
			return $row->ip.':'.$row->port;
		else 
			return false;
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

	/**
	 * @return all data of that url
	 */

	public function helper_crawler($url)
	{
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

		return $client->request('GET', $url);		
	}	
}