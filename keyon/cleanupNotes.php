#!/usr/bin/php
<?php
require("consts.php");

require_once("processClass.php");
$dbName = $DB;
$processData = new ProcessData();
$db = $processData->dbConnect2();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



$db->commit();
