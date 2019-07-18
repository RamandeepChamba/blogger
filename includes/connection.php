<?php

// Establish connection to db
$pdo = new PDO('mysql:host=localhost;
  dbname=blogdb;charset=utf8',
  'blogdb',
  'blogdb');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
