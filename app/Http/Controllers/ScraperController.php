<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Scrap;
use Goutte\Client;
use Illuminate\Http\Request;

class ScraperController extends Controller
{
    

    public function getIndex()
    {

    	$scrap = new Scrap;
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
		
		if(strpos($isBlock,'blocked') != false ) {

			$this->tor_new_identity();	
			return $this->getIndex();
			
		} 

			$crawler->filter('p.row')->each(function ($node) {
				    $url = $node->attr("href")."\n";
				    $text = $node->filter('.hdrlnk')->text();
				    $scrap::create(['url' => $url, 'title' => $text ]);
			});
		


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

	public function tor_new_identity($tor_ip='127.0.0.1', $control_port='9051', $auth_code=''){
	    $fp = fsockopen($tor_ip, $control_port, $errno, $errstr, 30);
	    if (!$fp) return false; //can't connect to the control port
	     
	    fputs($fp, "AUTHENTICATE $auth_code\r\n");
	    $response = fread($fp, 1024);
	    list($code, $text) = explode(' ', $response, 2);
	    if ($code != '250') return false; //authentication failed
	     
	    //send the request to for new identity
	    fputs($fp, "signal NEWNYM\r\n");
	    $response = fread($fp, 1024);
	    list($code, $text) = explode(' ', $response, 2);
	    if ($code != '250') return false; //signal failed
	     
	    fclose($fp);
	    return true;
	}
}