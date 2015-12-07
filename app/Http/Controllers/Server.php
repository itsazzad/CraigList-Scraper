class Server extends BaseController {
    
        public function deploy() {
          SSH::into('production')->run(array(
        	    'cd /var/www/html',
        	    'git pull origin master'
        	), function($line){
        	
        	    echo $line.PHP_EOL; // outputs server feedback
        	});

        }
    }
