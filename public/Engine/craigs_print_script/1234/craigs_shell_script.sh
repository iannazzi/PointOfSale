ACCOUNT_KEY='1234'PRINTER_QUEUE='cefr'#This is the local printer name. 
#Set the name to match a printer queue on the mac running the script.
#go to system preferences-printer & fax and enter a printer name below with the identical name on the mac
LOCAL_PRINTER='CEF5W'
PRINTER_QUEUE='CEF5W'



#Go to a webpage to find the queue information

#load a webpage to delete the job

clear				# clear terminal window

echo "##########################################"
echo "TESTINGG"
echo "##########################################"

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cd "$DIR"
echo "The current working directory $DIR."
if [ ! -d "$PRINTER_QUEUE" ]; then
  # Control will enter here if $DIRECTORY doesn't exist.
  mkdir "$PRINTER_QUEUE"
fi
cd "$PRINTER_QUEUE"

TEST_URL='embrasse-moi.com/POS_TEST/Engine/system/printers/printer_queue.php?type=LIST&print_queue='$PRINTER_QUEUE
echo 'TEST_URL'
echo $TEST_URL
FILES=$(curl -L $TEST_URL)
echo 'Files:'
echo $FILES



# break the string into an array
IFS=, read -r -a FILE_ARRAY <<< "$FILES"
printf '%s\n' "${FILE_ARRAY[@]}"
for f in "${FILE_ARRAY[@]}"
	do
		TEST_URL='http://www.embrasse-moi.com/POS_TEST/PrintQueue/invoices/'$PRINTER_QUEUE'/'$f
	  	echo 'Downloading From '$TEST_URL
	  	curl -L -O $TEST_URL
	  	
	done
exit 0


#!/bin/bash
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

clear				# clear terminal window
#script output.txt

echo "##########################################"
echo "Craig Iannazzi's Remote Print Queue Script"
echo "##########################################"

#Local Print Queue Directory - will make if it does not exist...
COPY_TO_DIRECTORY=/CIPrintQueue

#Priter - I do not think you can use any spaces....copy the printer name from the remote system
#Create the POS_PATH HERE
REMOTE_PRINTER_QUEUE='T8XR6'
COPY_REMOTE_DIRECTORY_FILES=invoices/$REMOTE_PRINTER_QUEUE

# Check the printer
PRINTER_CHECK=$(lpq -P $LOCAL_PRINTER)

# This gets the error code of the last thingy, does not work well with ftp... echo $?
if [[ $? -ne 0 ]]; then
	echo 'Printer Not Found'
	echo "Exiting"
	exit
else
	echo 'Printer OK, Good To Continue'
	
fi




#FTP Info
USER="printer2"
PASS="iluv2tow"
HOST="ftp.embrasse-moi.com"


echo "Remote Print Queue Directory: $HOST$COPY_REMOTE_DIRECTORY_FILES"
echo "Local Directory: $COPY_TO_DIRECTORY"
echo "Local Printer Name: $LOCAL_PRINTER"
echo "Remote Printer Queue Name: $REMOTE_PRINTER_QUEUE"

#Check The ftp  login
echo 'Checking LOGIN'
CHECK_LOGIN=$(ftp -n $HOST <<END_SCRIPT
quote USER $USER
quote PASS $PASS
END_SCRIPT
)


LOGIN_LENGTH=$(echo ${#CHECK_LOGIN})	

if [ "0" = $LOGIN_LENGTH ]; then
	echo 'Login OK, Good To Continue'
	
else
	echo 'Login Failure'
	echo $CHECK_LOGIN
	echo "Exiting"
	exit
fi

#Check the FTP folder exists
check_remote_folder=$(ftp -n $HOST <<END_SCRIPT
quote USER $USER
quote PASS $PASS
cd $COPY_REMOTE_DIRECTORY_FILES
END_SCRIPT
)
echo ${#check_remote_folder}
CHECK_FOLDER_LENGTH=$(echo ${#check_remote_folder})	
echo $CHECK_FOLDER_LENGTH

if [ "0" = $CHECK_FOLDER_LENGTH ]; then
	echo 'Directory OK, Good To Continue'
else
	echo 'Directory does not exist failure.'
	echo $check_remote_folder
	echo "Exiting"
	exit
fi


echo 'Entering Repeating Loop'
while true;  do

	#make the copy to directory if not exist
	echo "Making Directory If it Does Not Exist"
	mkdir -p $COPY_TO_DIRECTORY
	cd $COPY_TO_DIRECTORY
	
	######################### WGET ATTEMPTS ############################################
	#NOTE wget will need to be installed
	#echo "NOT Using wget to retrieve remote files..."
	
	# wget --tries=45 -o log --ftp-user=$USER --ftp-password=$PASS ftp://ftp.embrasse-moi.com$COPY_REMOTE_DIRECTORY_FILES/*.pdf
	
	######################### FTP ATTEMPTS ############################################
	echo "Using ftp to retrieve and delete remote files..."
	#check the folder, otherwise it will delete everything off the main server..
	
ftp -n $HOST <<END_SCRIPT
	quote USER $USER
	quote PASS $PASS
	cd $COPY_REMOTE_DIRECTORY_FILES
	ls
	prompt
	binary
	mget *.pdf
	mdel *.pdf
END_SCRIPT

	echo "Examining Files in $COPY_TO_DIRECTORY"
	for f in $COPY_TO_DIRECTORY/*.pdf
	do
	  # take action on each file. $f store current file name	  
	  #print
	  echo "Printing File: $f To: $LOCAL_PRINTER"
	  
	  #lpr -P $LOCAL_PRINTER -o page-ranges=1 -o media=Custom.5.5x8.5in -o orientation-requested=4 $f
	  #Duplex no worky on small size paper...
	  # lpr -P $LOCAL_PRINTER -o media=Custom.5.5x8.5in -o orientation-requested=4 -o Duplex=DuplexTumble $f
	  lpr -P $LOCAL_PRINTER -o media=Custom.5.5x8.5in -o orientation-requested=4 $f
	  # This will remove the file.....
	  echo "Deleting File: $f"
	  rm "$f"
	done
	echo "Script Complete... now repeat until killed..."
  	sleep 5
done
