Jaime
===

Jaime is just a handy PHP5.3 utility class to automate repetitive tasks from the command line. 

The way it works is simple. You instantiate the <code>Jaime</code> class. You pass a list of tasks (closures) inside an array with the <code>please()</code> method. Then yo ask Jaime to <code>work()</code>.


For example, this is a simplified version of a Jaime Script that I use to deploy a WordPress plugin:

Example
---
	<?php
	$jaime = require('jaime.php');
	
	$jaime->please(array(
		
		// it's nicer when you name your tasks descriptively
		'duplicate the theme directory'=>function($jaime){

			$folder = dirname(dirname(__FILE__));			
			$destination = dirname($folder).DIRECTORY_SEPARATOR."export";

			if (!is_dir($destination)) {
				mkdir($destination,0777,true);
			}
			
			// this is a shell command
			exec("cp -R -f $folder $destination");

			// remember is a handy function to pass variables between 
			// tasks without resorting to the globl scope
			$jaime->remember('build',$destination."/padpressed");			
		},
		
		'remove development stuff'=>function($jaime){
			
			// we retrieve values from Jaime's memory.
			$buildDir = $jaime->remember('build');
			chdir($buildDir);
			exec('rm -rf .git*');
			exec('rm -rf .sass-cache');		
			
		}, 
		
		'minify javascript'=>function($jaime){
			
			$buildDir = $jaime->remember('build');
			$appJs = $buildDir.'/themes/today/assets/js/app.js';
			$miniAppJs = $buildDir.'/themes/today/assets/js/app.min.js';

			// obviously you need to download YUICompressor first,
			$compressor  = dirname(__FILE__)."/yui/yuicompressor-2.4.2.jar";

			echo exec("java -jar $compressor -o $miniAppJs $appJs");

		},		
		
	));
	?>
	

I'll be adding more documentation and examples later.	
	