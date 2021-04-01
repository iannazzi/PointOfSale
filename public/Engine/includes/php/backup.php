<?php
//Database backup

function systemMysqlBackup()
{
	//untested
	include 'config.php';
	include 'opendb.php';

	$backupFile = $dbname . date("Y-m-d-H-i-s") . '.gz';
	$command = "mysqldump --opt -h $dbhost -u $dbuser -p $dbpass $dbname | gzip > $backupFile";
	system($command);
	include 'closedb.php';
}

function backup_pos_tables($tables = '*')
{
  
  $excluded_tables = array('pos_manufacturer_upc');
  $dbc = openPOSdb();
  $return = '';
  //mysql_select_db($name,$link);
  
  //get all of the tables
  if($tables == '*')
  {
    $tables = array();
    $result = @mysqli_query($dbc, 'SHOW TABLES');
    while($row = mysqli_fetch_row($result))
    {
      if(!in_array($row[0],$excluded_tables))
      {
      	$tables[] = $row[0];
      }
    }
  }
  else
  {
    $tables = is_array($tables) ? $tables : explode(',',$tables);
  }
  //cycle through
  foreach($tables as $table)
  {
    $result = @mysqli_query($dbc,'SELECT * FROM '.$table);
    $num_fields = mysqli_num_fields($result);
    
    $return.= 'DROP TABLE '.$table.';';
    $row2 = mysqli_fetch_row(mysqli_query($dbc,'SHOW CREATE TABLE '.$table));
    $return.= "\n\n".$row2[1].";\n\n";
    
    for ($i = 0; $i < $num_fields; $i++) 
    {
      while($row = mysqli_fetch_row($result))
      {
        $return.= 'INSERT INTO '.$table.' VALUES(';
        for($j=0; $j<$num_fields; $j++) 
        {
          $row[$j] = addslashes($row[$j]);
          $row[$j] = ereg_replace("\n","\\n",$row[$j]);
          if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
          if ($j<($num_fields-1)) { $return.= ','; }
        }
        $return.= ");\n";
      }
    }
    $return.="\n\n\n";
  }
  return $return;


}
function writeDataToFile($file_name, $path, $data)
{
   makeDir($path);
  //save file
  $handle = fopen($path .'/'.$file_name,'w+');
  fwrite($handle,$data);
  fclose($handle);
}
?>