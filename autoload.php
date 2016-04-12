<?php
function IRIXAutoload($classname) {
  list($namespace, $class) = explode('\\', $classname);
  $filename = __DIR__.'/class.'.strtolower($class).'.php';
  if (file_exists($filename)) {
    if (is_readable($filename)) { require $filename; }
  } else {
    require 'class.misc.php';
  }
}

spl_autoload_register('IRIXAutoload', TRUE, TRUE);