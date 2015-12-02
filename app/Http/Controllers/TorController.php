<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Link;
use App\Scrap;
use Goutte\Client;
use Illuminate\Http\Request;

class TorController extends Controller
{
    

    public function getIndex()
    {
        // Connect to the TOR server using password authentication
        $tc = new \TorControl\TorControl(
            array(
                'server' => 'localhost',
                'port'   => 9051,
                //'password' => '16:0DB96B1B985D6BA160DEE39AB7FCCBDFB864CE6C89FF8DCB63982F1AC2',//0VTbMcmjTi
                'password' => '0VTbMcmjTi',//16:0DB96B1B985D6BA160DEE39AB7FCCBDFB864CE6C89FF8DCB63982F1AC2
                'authmethod' => 1
            )
        );
        
        $tc->connect();
        
        $tc->authenticate();
        
        // Renew identity
        $res = $tc->executeCommand('SIGNAL NEWNYM');
        
        // Echo the server reply code and message
        echo $res[0]['code'].': '.$res[0]['message'];
        
        // Quit
        $tc->quit();


    }

    public function getData(){

    }

}
