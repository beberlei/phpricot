<?php

function phpricot_autoload($class)
{
    if (strpos($class, "PHPricot") === 0) {
        require_once __DIR__ . "/../../lib/" . str_replace("_", "/", $class) . ".php";
    }
}

spl_autoload_register('phpricot_autoload');