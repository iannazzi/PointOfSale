function updatePOLog()
{
	  log = document.getElementById('po_log').value;
	  $.ajax({
	  type: 'POST',
	  url: "update_log.php",
	  data: { pos_purchase_order_id:pos_purchase_order_id , log:log }
,
	  async: true,
	  success: 	function(response) {
	  document.getElementById('po_log').value = '';
	  alert('Added Comment, Refreshing Page');
	  location.reload(); }
	});
}