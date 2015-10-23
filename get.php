<?php
header('Content-Type: text/plain');

require_once('Pinterest.php');

if (isset($_GET['url']) && filter_var($_GET['url'], FILTER_VALIDATE_URL)) {
    print new Pinterest($_GET['url']);
} else {
    print "Your url is not valid.";
}