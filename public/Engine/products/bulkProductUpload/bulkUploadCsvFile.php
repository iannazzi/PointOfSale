<?php

# This code is the only thing
# that should belong in the PHP
# document

header('Content-Disposition: attachment; filename=BulkProductUploadFormat.csv');
header('Content-type: text/csv');
readfile('BulkProductUploadFormat.csv');

?>