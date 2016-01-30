<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Goutte\Client;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    //

    public function getIndex()
    {
    	$client = new Client();
    	$crawler = $client->request('GET', url('test/data'));
    	$name = $crawler->filterXPath('//div[@class="reply_options"]//ul/li')->text();
    	$email = $crawler->filterXPath('//ul/li/a[@class="mailapp"]')->text();
    	$mobile = $crawler->filterXPath('//a[@class="mobile-only replytellink"]')->attr('href');
    	if($name){
    		echo $name;
    		echo $email;
    		echo $mobile;
    	} else {
    		echo "no";
    		die();
    	}
    	//dd($name->html());
    }


    public function getData()
    {
    	$data = '<div class="reply_options">
<b>contact name:</b><ul> <li>phil</li></ul><a class="mobile-only replytellink" href="tel:13347480484">call</a><b class="no-mobile">call</b><b>:</b><ul> <li>☎ (334) 748-0484</li></ul>
<b>reply by email:</b>
<ul class="pad">
<li><a href="mailto:8tgzp-5345124783%40hous.craigslist.org?subject=Garden%20District%20Auburn%2C%20Al&amp;body=%0A%0Ahttp://auburn.craigslist.org/apa/5345124783.html%0a" class="mailapp">8tgzp-5345124783@hous.craigslist.org</a></li>
</ul>
<div id="webmailinks">
<b>webmail links:</b>
<ul class="pad">
<li>
<a target="_blank" href="https://mail.google.com/mail/?view=cm&amp;fs=1&amp;to=8tgzp-5345124783%40hous.craigslist.org&amp;su=Garden%20District%20Auburn%2C%20Al&amp;body=%0A%0Ahttp://auburn.craigslist.org/apa/5345124783.html%0A" class="gmail">gmail</a>
</li>
<li>
<a target="_blank" href="http://compose.mail.yahoo.com/?to=8tgzp-5345124783%40hous.craigslist.org&amp;subj=Garden%20District%20Auburn%2C%20Al&amp;body=http://auburn.craigslist.org/apa/5345124783.html" class="yahoo">yahoo mail</a>
</li>
<li>
<a target="_blank" href="https://mail.live.com/default.aspx?rru=compose&amp;to=8tgzp-5345124783%40hous.craigslist.org&amp;subject=Garden%20District%20Auburn%2C%20Al&amp;body=%0A%0Ahttp://auburn.craigslist.org/apa/5345124783.html%0A" class="msmail">hotmail, outlook, live mail</a>
</li>
<li>
<a target="_blank" href="http://mail.aol.com/mail/compose-message.aspx?to=8tgzp-5345124783%40hous.craigslist.org&amp;subject=Garden%20District%20Auburn%2C%20Al&amp;body=http://auburn.craigslist.org/apa/5345124783.html" class="aol">aol mail</a>
</li>
</ul>
<b>copy and paste into your email:</b>
<ul>
<li><div class="anonemail">8tgzp-5345124783@hous.craigslist.org</div></li>
</ul>
<div>
</div>

</div></div>';

echo $data;
    }
}
