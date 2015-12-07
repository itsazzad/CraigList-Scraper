<?php
namespace App\Http\Controllers;

class Server extends Controller {
    
        public function deploy() {
          \SSH::into('local')->run(array(
        	    'cd /var/www/html',
        	    'git pull origin master'
        	), function($line){
        	
        	    echo $line.PHP_EOL; // outputs server feedback
        	});

        }
    }
?>
