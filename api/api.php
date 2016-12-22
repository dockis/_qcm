<?php

require_once("../lib/DB.php");
require_once("../lib/RestApi.php");

// připojení na databázi
// DB::connect(SERVER, DATABAZE, USER, HESLO );
DB::connect('localhost', 'qcm', 'root', '');

// inicializace API
$api = new RestApi();