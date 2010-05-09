<?php

function __autoload($class)
{
    require_once __DIR__ . "/../../lib/" . str_replace("_", "/", $class) . ".php";
}