<?php
require_once 'authFile.php';

$authFile = new AuthFile;

file_put_contents("filename",$authFile->createFile("login","pass","email")->generateKeyFile());