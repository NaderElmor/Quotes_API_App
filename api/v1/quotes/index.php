<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With");

require_once "../../../config/Database.php";
require_once "../../../models/Quote.php";
require_once "../../../models/HttpResponse.php";

$db = new Database();
$quote = new Quote($db);
$http = new HttpResponse();
