<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Goutte\Client;

class ScraperController extends Controller
{
    

    public function getIndex()
    {
    	//Using Curl 
		$url = 'http://188.166.243.11';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_PROXY, "http://127.0.0.1:9050/");
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		$output = curl_exec($ch);
		$curl_error = curl_error($ch);
		curl_close($ch);
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

    public function getGit()
    {	$client = new Client();
		$crawler = $client->request('GET', 'http://github.com/');
		$crawler = $client->click($crawler->selectLink('Sign in')->link());
		$form = $crawler->selectButton('Sign in')->form();
		$crawler = $client->submit($form, array('login' => 'sohel4r@gmail.com', 'password' => '$Carbon123'));

		$crawler->filter('.header-logo')->each(function ($node) {
		    print $node->text()."\n";
		});    	
    }
}
