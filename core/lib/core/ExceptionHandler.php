<?php

namespace Core;

class ExceptionHandler {
	public static function process ( \Exception $e ) {
            ob_start();
            echo "<h1>{$e->getMessage()}</h1>";
            echo "<h3>".$e->getFile().':'.$e->getLine().'</h3>';
            echo "<hr/>";
            foreach ($e->getTrace() as $t) {
                echo "<strong>";
                if(isset($t["class"])) echo $t["class"].$t["type"];
                echo $t["function"]."()";
                echo "</strong>";
                if(isset($t["file"])) echo " @ ".$t["file"].":<strong>".$t["line"]."</strong>";
                if(count($t["args"])) { echo "<pre>"; var_dump($t["args"]); echo "</pre>"; }
                echo "<hr/>";
            }
            echo ob_get_clean();
            exit;
	}
	
}
