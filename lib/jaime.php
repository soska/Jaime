<?php
/**
 * Jaime is an utility class that makes easy to write shell scripts for repetitive tasks. 
 *
 * @package default
 * @author Armando Sosa
 */

class Jaime{
	
	var $tasks = array();
	var $arguments = array();
	var $memory = array();
	var $active;
	
	function __construct(){
		$this->getargs();
		$this->tasks = $this->setup($this);
	}
	
	function setup($jaime){
	}
	
	function getargs(){
		global $argv, $argc;

		$i = 1; $done = false;
		$currentKey = 0;
		unset($argv[0]);
		foreach ($argv as $part) {
			echo "\n";
			if (substr($part, 0, 1) === "-") {
				$currentKey = $part;
			}else{
				$this->arguments[$currentKey][] = $part;
			}
		}

		if (!isset($this->arguments[$currentKey])) {
			$this->arguments[$currentKey][] = true;
		}
		
	}
	
	function please($task){
		if (is_array($task)) {
			$this->tasks = set_merge($this->tasks,$task);
		}else{
			$this->tasks[] = $task;			
		}
		return $this;
	}

	function remember($key,$val = null){
		if ($val) {
			$this->memory[$key] = $val;
			return $this;
		}else{
			return $this->memory[$key];
		}
	}
	
	function work(){
		$this->active = true;
		foreach ($this->tasks as $name=>$task) {
			if ($this->active) {
				echo "\n( Jaime is doing this:\t";
				echo (is_string($name))?$name:"Task #".($name+1);
				echo " )";
				$task($this);
			}else{
				echo "\n(Jaime is aborting)";				
				break;
			}
		}

		echo "\n( Jaime is done.) \n\n";
		return $this;
	}
	
	function say($what){
		echo "\n  -- Jaime says: \t$what\n";
		return $this;
	}
	
	function abort(){
		$this->active = false;
		return $this;
	}
	
	function shave($template, $vars = array(), $methods = array()){
		if(preg_match_all("/\{\{[^\s]+\}\}/",$template,$matches)){
			foreach ($matches[0] as $token) {
				$token = trim($token);
				$key = str_replace('{{','',str_replace('}}','',$token));
				if (isset($vars[$key])) {
					$var = $vars[$key];				
					$template = str_replace($token,$var,$template);
				}else{

					if(strpos($key,'|') !== false){
						$parts = array_reverse(explode('|',$key));

						$seed = array_shift($parts);
						if (isset($vars[$seed])) {
							$seed = $vars[$seed];
						}

						do {
							$method = array_shift($parts);
							if (isset($methods[$method]) && is_callable($methods[$method])) {
								$seed = $methods[$method]($seed);
							}else{
								$this->say(" I don't know what you to do with $method ");
								$this->abort();
							}
						} while (!empty($parts));

						$template = str_replace($token,$seed,$template);					

					}
				}
			}
		}	
		return $template;
	}

	
}


if (!function_exists('set_merge')) {
	/**
	 * set_merge, borrowed from he cakephp.org project. Is like array_merge, except it merges even deep arrays
	 *
	 * @param string $arr1 
	 * @param string $arr2 
	 * @return void
	 * @author Armando Sosa
	 */
	function set_merge($arr1, $arr2 = null) {
		$args = func_get_args();

		$r = (array)current($args);
		while (($arg = next($args)) !== false) {
			foreach ((array)$arg as $key => $val) {
				if (is_array($val) && isset($r[$key]) && is_array($r[$key])) {
					$r[$key] = set_merge($r[$key], $val);
				} elseif (is_int($key)) {
					$r[] = $val;
				} else {
					$r[$key] = $val;
				}
			}
		}
		return $r;
	}
}

return new Jaime;
?>