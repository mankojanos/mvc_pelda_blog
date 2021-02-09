<?php
echo $_SERVER['REQUEST_URI'];
echo '<br>';
echo $_SERVER['PHP_SELF'];
echo '<br>';
echo basename($_SERVER['PHP_SELF']);

$path = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['PHP_SELF']) - strlen(basename($_SERVER['PHP_SELF'])) - 1);
