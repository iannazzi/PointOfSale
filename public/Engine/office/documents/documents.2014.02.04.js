//timer
var save_poc_sec;
var start_poc_time;
$(function(){
InitPageTimer(tinyMCE);});
function InitPageTimer() 
{
    save_poc_sec = 6 * 1000;             //Save every minute
    start_poc_time = new Date().getTime();
    CheckTimerStatusAndSave();
}
function CheckTimerStatusAndSave() 
{
    //Check for session warning
    current_time = new Date().getTime();
    if (current_time > start_poc_time + save_poc_sec)
    {
        //save
        //saveDraft(tBodyId);
        console.log('autosaving.....');
        documentAutoSave();
        //alert('saved');
        //needToConfirm=false;
        //restart the timer
        InitPageTimer();
        //re-lock the page???
        //lock_entry();
    }
    else 
    {
    	//check the status every second
        recheck = setTimeout("CheckTimerStatusAndSave();", 1000);
    }
}
function documentAutoSave(tinyMCE)
{
	var post_string = {};
	post_string['pos_document_id'] = pos_document_id;
	post_string['document_text'] = tinyMCE.activeEditor.getContent();
	
	console.log(tinyMCE.activeEditor.getContent());
	var url = 'document_auto_save.php';
	$.ajax({
			type: 'POST',
			url: url,
			data: post_string,
			async: true,
			success: 	function(response) 
			{
				console.log(response);

			}
			});
}