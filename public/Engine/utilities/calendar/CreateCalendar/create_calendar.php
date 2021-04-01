<?
/* date settings */
$month = (int) ($_GET['month'] ? $_GET['month'] : date('m'));
$year = (int) ($_GET['year'] ? $_GET['year'] : date('Y'));

/* select month control */
$select_month_control = '';
for($x = 1; $x <= 12; $x++) {
$select_month_control.= ''.date('F',mktime(0,0,0,$x,1,$year)).'';
}
$select_month_control.='';

/* select year control */
$year_range = 7;
$select_year_control ='';
for($x = ($year-floor($year_range/2)); $x <= ($year+floor($year_range/2)); $x++) {
$select_year_control.= ''.$x.'';
}
$select_year_control.='';

/* “next month” control */
$next_month_link = 'Next Month >>';

/* “previous month” control */
$previous_month_link = '<< Previous Month';

/* bringing the controls together */
$controls =''.$select_month_control.$select_year_control.' '.$previous_month_link.' '.$next_month_link.' ';
echo '<link rel="stylesheet" href="calendar.css" type="text/css" media="all" />'; 
echo 'November 2009';
echo draw_calendar(11,2009);
?>

<?php
/* draws a calendar */
//http://davidwalsh.name/php-calendar
function draw_calendar($month,$year)
{
  /* draw table */
  $calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

  /* table headings */
  $headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
  $calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

  /* days and weeks vars now ... */
  $running_day = date('w',mktime(0,0,0,$month,1,$year));
  $days_in_month = date('t',mktime(0,0,0,$month,1,$year));
  $days_in_this_week = 1;
  $day_counter = 0;
  $dates_array = array();

  /* row for week one */
  $calendar.= '<tr class="calendar-row">';

  /* print "blank" days until the first of the current week */
  for($x = 0; $x < $running_day; $x++):
    $calendar.= '<td class="calendar-day-np">&nbsp;</td>';
    $days_in_this_week++;
  endfor;

  /* keep going with days.... */
  for($list_day = 1; $list_day <= $days_in_month; $list_day++):
    $calendar.= '<td class="calendar-day">';
      /* add in the day number */
      $calendar.= '<div class="day-number">'.$list_day.'</div>';

      /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
      $calendar.= str_repeat('<p>&nbsp;</p>',2);
      
    $calendar.= '</td>';
    if($running_day == 6):
      $calendar.= '</tr>';
      if(($day_counter+1) != $days_in_month):
        $calendar.= '<tr class="calendar-row">';
      endif;
      $running_day = -1;
      $days_in_this_week = 0;
    endif;
    $days_in_this_week++; $running_day++; $day_counter++;
  endfor;

  /* finish the rest of the days in the week */
  if($days_in_this_week < 8):
    for($x = 1; $x <= (8 - $days_in_this_week); $x++):
      $calendar.= '<td class="calendar-day-np">&nbsp;</td>';
    endfor;
  endif;

  /* final row */
  $calendar.= '</tr>';

  /* end the table */
  $calendar.= '</table>';
  
  /* all done, return result */
  return $calendar;
}

/* sample usages */
echo '<h2>July 2009</h2>';
echo draw_calendar(7,2009);

echo '<h2>August 2009</h2>';
echo draw_calendar(8,2009);
?>