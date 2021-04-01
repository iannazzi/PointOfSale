#!/bin/bash

# Hoping that this script will 
# Get a remote directory Folder
# List the contents every second
# Copy the files to a local folder
# delete the file from server
# send the file to a printer
# delete the file


clear				# clear terminal window
#script output.txt
sleep 5

echo "##########################################"
echo "Craig Iannazzi's Remote Print Queue Script"
echo "##########################################"

#Local Print Queue Directory - will make if it does not exist...
COPY_TO_DIRECTORY=/CIPrintQueue

#Priter - I do not think you can use any spaces....copy the printer name from the remote system
PRINTER='xx234'



#FTP Info
USER="printer"
PASS="X4ghy!23aab"
HOST="ftp.embrasse-moi.com"
COPY_REMOTE_DIRECTORY_FILES=/public_html/craigiannazzi.com/POS_TEST/PrintQueue/invoices/$PRINTER


#Check The ftp  login

ftp -n $HOST <<END_SCRIPT
quote USER $USER
quote PASS $PASS
END_SCRIPT

echo $?
exit
echo $CHECK_LOGIN
SUBSCRIPT = $(${CHECK_LOGIN:-6})

echo $SUBSCRIPT
if [ "failed" = $SUBSCRIPT ]; then
	echo $CHECK_LOGIN
	exit
else
	echo 'Login OK, Good To Continue'
fi

#Check the FTP folder exists
check_remote_folder=$(ftp -n $HOST <<END_SCRIPT
quote USER $USER
quote PASS $PASS
cd $COPY_REMOTE_DIRECTORY_FILES
END_SCRIPT
)
if [ "Can't" = ${check_remote_folder:0:5} ]; then
	echo $check_remote_folder
	exit
else
	echo 'Directory OK, Good To Continue'
fi


echo "Remote Print Queue Directory: $HOST$COPY_REMOTE_DIRECTORY_FILES"
echo "Local Directory: $COPY_TO_DIRECTORY"
echo "Printer Name: $PRINTER"
echo 'Entering Repeating Loop'
while true;  do

	#make the copy to directory if not exist
	echo "Making Directory If it Does Not Exist"
	mkdir -p $COPY_TO_DIRECTORY
	cd $COPY_TO_DIRECTORY
	
	######################### WGET ATTEMPTS ############################################
	#NOTE wget will need to be installed
	echo "NOT Using wget to retrieve remote files..."
	
	# wget --tries=45 -o log --ftp-user=$USER --ftp-password=$PASS ftp://ftp.embrasse-moi.com$COPY_REMOTE_DIRECTORY_FILES/*.pdf
	
	######################### FTP ATTEMPTS ############################################
	echo "Using ftp to retrieve and delete remote files..."
	#check the folder, otherwise it will delete everything off the main server..
	

	
	#ls
	#prompt
	#mget *.pdf
	#took out mdel *.pdf
	
	echo "Examining Files in $COPY_TO_DIRECTORY"
	for f in $COPY_TO_DIRECTORY/*.pdf
	do
	  # take action on each file. $f store current file name	  
	  #print
	  echo "Printing File: $f To: $PRINTER"
	  lpr -P $PRINTER $f
	  
	  # This will remove the file.....
	  echo "Deleting File: $f"
	  #rm "$f"
	done
	echo "Script Complete... now repeat until killed..."
  	sleep 50
done
