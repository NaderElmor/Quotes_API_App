<?php

    require_once("../../../config/Database.php");

    $db = new DataBase();
    var_dump($db->fetchAll("select * from users"));
