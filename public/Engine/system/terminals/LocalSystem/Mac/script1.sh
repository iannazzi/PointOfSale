#!/bin/bash
################## GENERAL CODING RULES ##################################
# DONT LEAVE SPACES BETWEEN EQUAL SIGNS
#
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
PRINTER='T8XR6'
COPY_REMOTE_DIRECTORY_FILES=invoices/$PRINTER

# Check the printer
PRINTER_CHECK=$(lpq -P $PRINTER)

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
echo "Printer Name: $PRINTER"

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
	mget *.pdf
	mdel *.pdf
END_SCRIPT

	echo "Examining Files in $COPY_TO_DIRECTORY"
	for f in $COPY_TO_DIRECTORY/*.pdf
	do
	  # take action on each file. $f store current file name	  
	  #print
	  echo "Printing File: $f To: $PRINTER"
	  
	  #lpr -P $PRINTER -o page-ranges=1 -o media=Custom.5.5x8.5in -o orientation-requested=4 $f
	  #Duplex no worky on small size paper...
	  # lpr -P $PRINTER -o media=Custom.5.5x8.5in -o orientation-requested=4 -o Duplex=DuplexTumble $f
	  
	  lpr -P $PRINTER -o media=Custom.5.5x8.5in -o orientation-requested=4 $f
	  # This will remove the file.....
	  echo "Deleting File: $f"
	  rm "$f"
	done
	echo "Script Complete... now repeat until killed..."
  	sleep 5
done
