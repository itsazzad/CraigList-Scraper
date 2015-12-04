<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Link;
use App\Scrap;
use Goutte\Client;
use Illuminate\Http\Request;

class ScraperController extends Controller
{

public $tc=false;

public function torNew(){
        // Connect to the TOR server using password authentication
        $this->tc = new \TorControl\TorControl(
            array(
                'server' => 'localhost',
                'port'   => 9051,
                //'password' => '16:0DB96B1B985D6BA160DEE39AB7FCCBDFB864CE6C89FF8DCB63982F1AC2',//0VTbMcmjTi
                'password' => '0VTbMcmjTi',//16:0DB96B1B985D6BA160DEE39AB7FCCBDFB864CE6C89FF8DCB63982F1AC2
                //'password' => 'sohelrana',
                'authmethod' => 1
            )
        );
        
        $this->tc->connect();
        
        $this->tc->authenticate();
        
        // Renew identity
        $res = $this->tc->executeCommand('SIGNAL NEWNYM');
        
        // Echo the server reply code and message
        \Log::info($res[0]['code'].': '.$res[0]['message']);
}   
 

	public function craiglist(){
		
		$ip=\Request::ip();

		$condition = array("open","blocked");
		$key=array_rand($condition, 1);
		$status = $condition[$key];

		\Log::info($ip.":".$status);
		if($status=='blocked'){
			\Log::info("Gaining new tor identity");
			echo "Blocked!!! Requesting New Ip";
		        // Quit
        if(isset($this->tc->connected)) $this->tc->quit();
	
         $this->torNew();

		        
			
			
		}else{
		
		//Make a new Request
    	$client = new Client();
		$guzzleClient = new \GuzzleHttp\Client([
		    'curl' => [
		        CURLOPT_PROXY => '127.0.0.1:9050',
		        CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5,
		    ],
		]);  

		$client->setClient($guzzleClient);	    	
    	$client->setHeader('User-Agent', "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36");
		$crawler = $client->request('GET', 'http://188.166.223.127/ip.php');
		var_dump($crawler->html());

		}
	} 

	public function getTest(){
		$ip=\Request::ip();
		\Log::info($ip);
		echo "<h2>Hello</h2>";
	}

	/**
	 * @return Category search page link and store it to database
	 */

    public function getIndex()
    {

		//$ua = 'Mozilla/5.0 (Windows NT 5.1; rv:16.0) Gecko/20100101 Firefox/16.0 (ROBOT)';
    	$client = new Client();
    	$client->setHeader('User-Agent', "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36");
    	//Set proxy using tor
		$guzzleClient = new \GuzzleHttp\Client([
		    'curl' => [
		        CURLOPT_PROXY => '127.0.0.1:9050',
		        CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5,
		    ],
		]);  

		$client->setClient($guzzleClient);	

		$crawler = $client->request('GET', 'http://auburn.craigslist.org/apa');

		$isBlock = $crawler->filter('p')->text();
		$isRun = true;
	while($isRun){
		if(strpos($isBlock,'blocked') != false ) {

			$this->torNew();
			//return $this->getIndex();
		} else {

			$crawler->filter('a.i')->each(function ($node) {
				    $url = $node->attr("href");
				    //$link = $node->filter('a')->first();
				    $text = $node->text();
				    $fullUrl = "http://auburn.craigslist.org".$url;
				    //$scrap::create(['url' => $url, 'title' => $text ]);

				   	Link::create(['url'=>$fullUrl, 'title'=> $text]);
			});			
			$isRun = true;
		}

	}
		


    }
    /**
     * @return category page single link url data eg.mobile, email etc
     */
    public function getData(){

    	$link = Link::first();

 		//$ua = 'Mozilla/5.0 (Windows NT 5.1; rv:16.0) Gecko/20100101 Firefox/16.0 (ROBOT)';
    	$client = new Client();
    	$client->setHeader('User-Agent', "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36");
    	//Set proxy using tor
		$guzzleClient = new \GuzzleHttp\Client([
		    'curl' => [
		        CURLOPT_PROXY => '127.0.0.1:9050',
		        CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5,
		    ],
		]);  

		$client->setClient($guzzleClient);	

		$crawler = $client->request('GET', $link->url);
		//$button = $crawler->filter('.reply_button');

		$isBlock = $crawler->filter('p')->text();
		$isRun = true;
		$i = 0;
		while($isRun){
		if(strpos($isBlock,'blocked') != false ) {

			$this->torNew();
			//return $this->getIndex();
			$crawler = $client->request('GET', $link->url);
			$isBlock = $crawler->filter('p')->text();	

		} else {

			$lnk = $crawler->selectLink('reply')->link();
			$crawler = $client->click($lnk);

			if ($crawler->filterXpath("//div[@class='captcha']")->count()) {

				$this->torNew();

			} else {

				var_dump($crawler->html());
				$title = $crawler->filter('title')->text();
				$mobile = $crawler->filter('.mobile-only')->first()->text();
				$email = $crawler->filter('.mailapp')->first()->text();
				echo $link->url .' '.$title .' '. $mobile.' '.$email;	
				Scrap::create(['url' => $link->url, 'title' => $title, 'email' => $email, 'phone' => $mobile ]);				
				$isRun = false;
			}
			
		}
	}//End While


		// $crawler->filter('a.i')->each(function ($node) {
		// 	    $url = $node->attr("href")."\n";
		// 	    //$link = $node->filter('a')->first();
		// 	    $text = $node->text();
		// 	    $fullUrl = "http://auburn.craigslist.org".$url;
		// 	    //$scrap::create(['url' => $url, 'title' => $text ]);
		// 	   	Link::create(['url'=>$fullUrl, 'title'=> $text]);
		// 	    var_dump($url);
		// 	    $this->tor_new_identity();
		// });   	
    }

    /**
     * @using gutte proxy 
     */

    public function getGutte()
    {

    	$client = new Client();
    	//Set proxy using tor
		$guzzleClient = new \GuzzleHttp\Client([
		    'curl' => [
		        CURLOPT_PROXY => '127.0.0.1:9050',
		        CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5,
		    ],
		]);  

		$client->setClient($guzzleClient);	

		$crawler = $client->request('GET', 'http://188.166.243.11');

		dd($crawler->html());

    }

	public function tor_new_identity($tor_ip='127.0.0.1', $control_port='9051', $auth_code='sohelrana'){
	    
	    $fp = fsockopen($tor_ip, $control_port, $errno, $errstr, 30);
	    if (!$fp) return false; //can't connect to the control port
	     
	    fputs($fp, "AUTHENTICATE \"$auth_code\"\r\n");
	    $response = fread($fp, 1024);
	    var_dump($response);
	    list($code, $text) = explode(' ', $response, 2);
	    if ($code != '250') return false; //authentication failed
	     
	    //send the request to for new identity
	    fputs($fp, "signal NEWNYM\r\n");
	    $response = fread($fp, 1024);
	    var_dump($response);
	    list($code, $text) = explode(' ', $response, 2);
	    if ($code != '250') return false; //signal failed
	     
	    fclose($fp);
	    return true;

	}
}
