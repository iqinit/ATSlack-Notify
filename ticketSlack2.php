<?php
/* 	
	ATSlack
    Copyright (C) 2016  domkirby
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>. 
	
	ticketReply.php - Alerts the ticket owner via Slack when a customer replies to the ticket.
	You should not need to edit this file, just edit config.php with the appropriate variables.
*/
#
#
#FILL OUT config.php
#
#EDIT AT YOUR OWN RISK
#
//Get required files and stuff
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/autoload.php';
$ticketNumber = $_POST['number'];
$ticketId = $_POST['id'];
require_once __DIR__ . '/functions.php';
#I WANT YOU TO USE SSL ~~ Comment this part out at your own risk
if (empty($_SERVER['HTTPS'])) {
    die("SSL WAS NOT USED <br />We want you to use SSL for your own good. Please go back and use SSL");
}
#end ssl check
##########################################################
####THIS FUNCTION IS IMPORTANT TO PREVENT DATA LEAKAGE####
##########################################################
if(!($_GET['s'] == $extensiontoken)) {
	die("Invalid Token or No Token Received");
}
# Now that we've checked security, we'll do some real work
//Fire GetTicketInfo to get our array of data
$ticketData = GetTicketInfo($ticketNumber,$wsdl,$username,$password);
//Unwrap the array
$ticketTitle = $ticketData["TicketTitle"];
$ContactName = $ticketData["ContactName"];
$ContactPhone = $ticketData["ContactPhone"];
$ContactEmail = $ticketData["ContactEmail"];
$companyName = $ticketData["CompanyName"];
//Fire MakeSlackNewTicketMessage to get an encoded message for Slack
$message = MakeSlackNewTicketMessage($ticketNumber,$ticketId,$ticketTitle,$ContactName,$ContactPhone,$ContactEmail,$companyName,$atzone);
##TESTMODE is created from the checkbox in form.html. It stops the message from being dispatched to Slack but displayes it in the browser.
if($testmode){
	echo urldecode($message)."<br />";
	echo $room."<br />";
	echo $slacknotificationsendpoint;
	echo "<br /><br /><br /><br /><br />";
	print_r($ticketData);
}
else {
	slack($message,'#'.$ticketnotificationroom,$slacknotificationsendpoint);
}
?>