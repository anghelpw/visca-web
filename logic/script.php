<?php
// The purpose of this cron is to update remotely a PHP file that is present on 4 websites.
// This file is large and heavy, as it contains a list of articles and their content.
// The content of this file is not provided, as it is not necessary to do this test.

$ftp_servers = array(
	array('ftp.site1.com', 'sEt7UMac', '&Re2adav9n'),
	array('85.98.102.10', 'h5THaW2U', 'p@U5Eb5phu"'),
	array('site3.com', 'M6wareb7', '2joQDLe_-PQO'),
	array('redirect.site4.com', '6utrAfru', 'jepoe123___'),
);

print '<pre>';
foreach($ftp_servers as $server_data) {
	list($host, $user, $pass) = $server_data;
	$upload_status = upload_latest_file($host, $user, $pass);
	print str_pad("Server is $host ($user / $pass)", 0, 50);
	print $upload_status ? 'success' : 'error';
	print "\n";
}
print '</pre>';

// This function requires a lot of time (between 60 and 120 seconds) every time that it is called.
function upload_latest_file($host, $user, $pass) {
	$conn = @ftp_connect($host);
	if (!$conn) return false;

	$conn_login = @ftp_login($conn, $user, $pass);
	if (!$conn_login) return false;

	$upload_from = 'latest-news.php';
	$upload_to = '/news.php';
	return (@ftp_fput($conn, $upload_from, $upload_to, FTP_ASCII));
}
