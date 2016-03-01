<?php
function IRIXAutoload($classname) {
  list($namespace, $class) = explode('\\', $classname);
  $filename = __DIR__.'/class.'.strtolower($class).'.php';
  if (is_readable($filename)) { require $filename; }
}

spl_autoload_register('IRIXAutoload', TRUE, TRUE);