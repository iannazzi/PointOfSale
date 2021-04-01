<?php
	/* store credit form handler
	craig iannazzi 2-11-2013
	
	creating id's => 
		1) create a 'full' batch of card numbers, like a million +. 
		2) create a small batch of ids => here is the issue
			- oh i am checking you out and you want a gift card sure but ohhh there are no labeled cards and then peter 
				wants to be bra fit so how do ok here just take this card.
				sounds flakey
		so we are going to create the ids here and now.
	
		printing
		the next issue is each gift card will need an id. We would want to pre-number these cards with a barcode
		now if it goes to the printer I would consider it 'used' to avoid duplicte print id's 
		
	so we create a ton of ids - set a create date. we then print a batch of ids - set a print date. If the print fails all these ids are bunk. fine. if the number to print is > the remaining ids we generate more ids. So basically a physical card gets made.
	The physical card then is assigned to a customer, and the card number is guranteed to be unique.
	
	in case of a needed card we can use 'get a card number' and it will pull one out of the database.
		
	labelsthen show a 'print' page with a button to go to the credits
	assign id will update existing id with new information
	
	*/
$binder_name = 'Store Credits';
$access_type = 'WRITE';
require_once ('../sales_functions.php');
$type = getPostOrGetValue('type');


	if (strtoupper($type) == 'PRINT')
	{
		$filename = getDatetime()  . '_cc_labels.pdf';
		$quantity = scrubInput($_POST['qty']);
		$row_offset = scrubInput($_POST['row_offset']);
		$column_offset = scrubInput($_POST['column_offset']);
		$card_numbers = array();
		//make the ids and get the card numbers...
		for($cn=0;$cn<$quantity;$cn++)
		{
			$card_numbers[] = getCardNumber_v2();
		}
		printCardNumbersAvery5167($card_numbers, $row_offset, $column_offset,  $filename);
		exit();
		
	}
	elseif (strtoupper($type) == 'ASSIGN')
	{
		//ok put the card number into the system...
		//check that the card number is in the system....
		$card_number = str_replace(' ','',scrubInput($_POST['card_number']));
		
		$card_check = getSQL("SELECT pos_store_credit_card_number_id FROM pos_store_credit_card_numbers WHERE card_number='$card_number'");
		if (sizeof($card_check)==0)
		{
			INCLUDE(HEADER_FILE);
			echo 'Error - the card number entered was: ' . $card_number . '. This card is not in the system. You must first print or assign a card number, but preferably print a card number.';
			INCLUDE (FOOTER_FILE);
			exit();
		}		
		$insert['card_number'] = $card_number;
		$insert['date_issued'] = scrubInput($_POST['date_issued']);
		$insert['comments'] = scrubInput($_POST['comments']);
		$insert['original_amount'] = scrubInput($_POST['original_amount']);
		if(isset($_POST['locked']) && $_POST['locked'] =='on')
		{
			$insert['locked'] = 1;
		}
		else
		{
			$insert['locked'] = 0;
		}
		//$dbc = startTransaction();
		$pos_store_credit_id = simpleInsertSQLReturnID('pos_store_credit', $insert);
		
		
		//now we need to add a general journal entry.....??? noper
		
		//simpleCommitTransaction($dbc);
		$message = urlencode('Store Credit Id ' . $pos_store_credit_id . " has been added");
		header('Location: '.$_POST['complete_location'] .'?message=' . $message);
		exit();
	}
  	elseif (strtoupper($type) == 'EDIT')
  	{
  		$pos_store_credit_id = getPostOrGetID('pos_store_credit_id');
  		$insert['card_number'] = str_replace(' ','',scrubInput($_POST['card_number']));
		$insert['date_issued'] = scrubInput($_POST['date_issued']);
		$insert['comments'] = scrubInput($_POST['comments']);
		$insert['original_amount'] = scrubInput($_POST['original_amount']);
		if(isset($_POST['locked']) && $_POST['locked'] =='on')
		{
			$insert['locked'] = 1;
		}
		else
		{
			$insert['locked'] = 0;
		}
		//$dbc = startTransaction();
		$key_val_id['pos_store_credit_id'] = $pos_store_credit_id;
		$result = simpleUpdateSQL('pos_store_credit', $key_val_id, $insert);
		//simpleCommitTransaction($dbc);
		$message = urlencode('Store Credit Id ' . $pos_store_credit_id . " has been updated");
		header('Location: '.$_POST['complete_location'] .'?message=' . $message);
		exit();
  	}


	
?>
