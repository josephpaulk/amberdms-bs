<?php
/*
	customers/services-invoicegen-process.php

	access: customers_write

	Calls various functions to automatically generate any invoices owing for this customer. These functions
	are performed daily by the execute cronjob, but sometimes users may wish to run them manually, eg: after
	just adding a new service to a customer account.
*/

// includes
include_once("../include/config.php");
include_once("../include/amberphplib/main.php");

// custom includes
include_once("../include/services/inc_services_invoicegen.php");


if (user_permissions_get('customers_write'))
{
	/////////////////////////

	$id = security_script_input('/^[0-9]*$/', $_GET["customerid"]);
	
	
	// make sure the customer actually exists
	$mysql_string		= "SELECT id FROM `customers` WHERE id='$id'";
	$mysql_result		= mysql_query($mysql_string);
	$mysql_num_rows		= mysql_num_rows($mysql_result);

	if (!$mysql_num_rows)
	{
		$_SESSION["error"]["message"][] = "The customer you have attempted to edit - $id - does not exist in this system.";
	}


		
	//// ERROR CHECKING ///////////////////////



	/// if there was an error, go back to the entry page
	if ($_SESSION["error"]["message"])
	{	
		header("Location: ../index.php?page=customers/services.php&id=$id");
		exit(0);
	}
	else
	{
		// execute functions
		service_periods_generate($id);

	
		// display updated details
		header("Location: ../index.php?page=customers/invoices.php&id=$id");
		exit(0);
	}

	/////////////////////////
	
}
else
{
	// user does not have perms to view this page/isn't logged on
	error_render_noperms();
	header("Location: ../index.php?page=message.php");
	exit(0);
}


?>