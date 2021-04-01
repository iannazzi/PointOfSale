
################## SETUP ##################################
# MAKE THIS FILE EXECUTABLE : chmod +x pos_printer.command
#your LOCAL_PRINTER_NAME is in settings->printers: change a 'space' to '_'
#
#
#########################################################################
################## GENERAL CODING RULES ##################################
# DONT LEAVE SPACES BETWEEN EQUAL SIGNS
# reference : http://www.tldp.org/LDP/abs/html/
#
#
#
#########################################################################
################## SETTING UP THE PRINTER ################################
# The printer name is the queue name!!! 
# To get the printer name and the queue name matching, when adding the printer be sure to get the right printer name
# Otherwise, delete the printer, re-add it.
# SET THE DEFAULT PAPER SIZE
#Using the CUPS web interface (http://localhost:631/printers), click on the queue name, select "Set Default Options" from the 
# Administration drop-down list, select desired page size and click on "Set Default Options".
# If a password is prompted then username lowercase, no spaces, no hyphens. Rightclick on user and select advanced options 
# in system preferences - accounts (after unlocking) and look at the user name there
#http://www.it.uu.se/datordrift/maskinpark/skrivare/cups/#OPTIONS
# lpr -P T8XR6 -o page-ranges=1 -o media=Custom.5.5x8.5in -o orientation-requested=4 1.pdf
##########################################################################
#Numbers used for testing purposes.
#ACCOUNT_KEY='1234'
#PRINTER_QUEUE='CEF5W'
#LIST_URL='embrasse-moi.com/POS_TEST/Engine/system/printers/printer_queue.php?type=LIST&print_queue='$PRINTER_QUEUE'&ACCOUNT_KEY='$ACCOUNT_KEY
#DOWNLOAD_URL='http://www.embrasse-moi.com/POS_TEST/PrintQueue/invoices/'$PRINTER_QUEUE
#DELETE_URL='embrasse-moi.com/POS_TEST/Engine/system/printers/printer_queue.php?type=DELETE&print_queue='$PRINTER_QUEUE'&ACCOUNT_KEY='$ACCOUNT_KEY
#This is the local printer name. 
#Set the name to match a printer queue on the mac running the script.
#go to system preferences-printer & fax and enter a printer name below with the identical name on the mac
clear				# clear terminal window
echo "######################################################"
echo "Craig Iannazzi's Remote Print to Local  Queue Script"
echo "For this to work you need a print queue named $PRINTER_QUEUE"
echo "Files will be saved to the folder with the script"
echo "######################################################"
#echo $ACCOUNT_KEY
#echo $PRINTER_QUEUE
#echo $LIST_URL
#echo $DOWNLOAD_URL
#echo $DELETE_URL

# Check the printer
PRINTER_CHECK=$(lpq -P $LOCAL_PRINTER_NAME)
# This gets the error code of the last thingy, does not work well with ftp... echo $?
if [[ $? -ne 0 ]]; then
	echo "$LOCAL_PRINTER_NAME was not found. Remove any printers with the name $LOCAL_PRINTER_NAME."
	#echo Add a printer with the name '$PRINTER_QUEUE'
	#echo Do not use spaces in the name
	#echo "Exiting"
	exit
fi
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
#echo The current working directory "$DIR".
cd "$DIR"

if [ ! -d "$PRINTER_QUEUE" ]; then
  #echo "Directory $PRINTER_QUEUE doesnot exist, creating it now."
  mkdir "$PRINTER_QUEUE"
fi
cd "$PRINTER_QUEUE"
while true;  do
	#echo 'LIST URL'
	#echo $LIST_URL
	FILES=$(curl -L $LIST_URL)
	# break the string into an array
	IFS=, read -r -a FILE_ARRAY <<< "$FILES"
	printf '%s\n' "${FILE_ARRAY[@]}"
	for f in "${FILE_ARRAY[@]}"
		do
			#echo 'DOWNLOADING FILE NAMED '$f
			curl -L -O $DOWNLOAD_URL'/'$f
			#print
			#echo '### PRINTING ### PRINTING ### PRINTING ###'
			lpr -P $LOCAL_PRINTER_NAME -o media=Custom.5.5x8.5in -o orientation-requested=4 $f
			#echo "Deleting LocalFile: $f"
			rm "$f"
			#echo "Deleting Remote File: $f"
			DELETE_RESPONSE=$(curl -L $DELETE_URL'&FILE_NAME='$f)
			# echo "Delete Respose:"$DELETE_RESPONSE
		done
	echo "Script Complete... now repeat until killed..."
  	sleep 5
done
exit 0