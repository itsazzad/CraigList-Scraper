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

		$requesturl = $request->get('url');
		$url = Url::where('name', $requesturl)->firstOrFail();
		if($url->links()->count() > 0){
			return redirect()->back()->with('message', 'You already do it !! . Try with new url :)');
		}
		$this->urlId = $url->id;

		$crawler = $this->helper_crawler($url->name);


		$isBlock = $crawler->filter('p')->text();

		if(strpos($isBlock,'blocked') != false ) {
			echo "Your ip is blocked. Please try again later";
			die();

		} else {

				$data = $crawler->filterXpath("//span[@class='rows']/p/a[@class='i']");
				$data->each(function ($node){
					$url = $node->attr('href');
					if( ! preg_match("/\/\/.+/", $url)) {

						Url::find($this->urlId)->links()->create(['name'=>$url]);
					}
				});	
		}

		return redirect()->back()->with('message', "Link was scraped please view link");

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


		$isBlock = $crawler->filter('p')->text();

		if(strpos($isBlock,'blocked') != false ) {
			//next process and change ip
			echo "Ip Address is blocked";
			die();

		} else {

			$lnk = $crawler->selectLink('reply')->link();

			//Ading user-agent
			$agent= 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.82 Safari/537.36';
			
			$client = new Client(['HTTP_USER_AGENT' => $agent]);

			$crawler = $client->click($lnk);

			if ($crawler->filterXpath("//div[@class='captcha']")->count()) {

				//Next process and change ip
				echo "Captcha given wait few hours";

			} else {


				$title = $name = $email = $mobile = "";

				if($crawler->filter('title')->count()) {
					$title = $crawler->filter('title')->text();
				}
				
				if($crawler->filterXPath('//div[@class="reply_options"]//ul/li')->count()){
					$name = $crawler->filterXPath('//div[@class="reply_options"]//ul/li')->text();
				}
		    	
		    	if($crawler->filterXPath('//ul/li/a[@class="mailapp"]')->count()) {
		    		$email = $crawler->filterXPath('//ul/li/a[@class="mailapp"]')->text();
		    	}
		    	
		    	if($crawler->filterXPath('//a[@class="mobile-only replytellink"]')->count()){
		    		$mb = $crawler->filterXPath('//a[@class="mobile-only replytellink"]')->attr('href');
		    		$mobile = str_replace("tel:", '', $mb);
		    	}
		    	
	
				$link->lead()->create(['title'=>$title,'email'=>$email, 'name'=> $name, 'phone' => $mobile]);

			}
			
		}

			return redirect()->back()->with('message', "Please check scrap data");
		

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

		$agent= 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.82 Safari/537.36';
		$Accept = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
		
		$client = new Client(['HTTP_USER_AGENT' => $agent]);		
		return  $client->request('GET', $url );			
	}	


	public function getTest()
	{
		$agent= 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.82 Safari/537.36';
		$Accept = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
		
		$client = new Client(['HTTP_USER_AGENT' => $agent]);		
		$data = $client->request('GET', 'http://localhost/test.html' );	
		$data = $data->filterXpath("//span[@class='rows']/p/a[@class='i']");
		$data->each(function ($node){

			$data = $node->attr('href');
			if( ! preg_match("/\/\/.+/", $data)) {
				echo $data . '<br>';
			}

		});	
	}
}