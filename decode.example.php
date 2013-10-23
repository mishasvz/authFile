<?php
require_once 'authFile.php';

$authFile = new AuthFile;

echo "hash ".$authFile->readFile("filename","hash")."<br>";
echo "login ".$authFile->readFile("filename","login");