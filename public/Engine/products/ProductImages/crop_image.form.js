//crop_image.form.js
//Craig Iannazzi 2-16-2012
//This file helps to create all the images needed for our web store



jQuery(function($)
{
	switch (product_type)
	{
		case 'Bra':
			$('#target').Jcrop(
			{
				onChange:   showCoords1,
				onSelect:   showCoords1,
				onRelease:  clearCoords1,
				aspectRatio: 3/5
			  });
			$('#target2').Jcrop(
			{
				onChange:   showCoords2,
				onSelect:   showCoords2,
				onRelease:  clearCoords2,
				aspectRatio: 1
			  });
			$('#target3').Jcrop(
			{
				onChange:   showCoords3,
				onSelect:   showCoords3,
				onRelease:  clearCoords3,
				aspectRatio: 1
			  });
			  
			break;
		case 'Swim':
			$('#target').Jcrop(
			{
				onChange:   showCoords,
				onSelect:   showCoords,
				onRelease:  clearCoords,
				aspectRatio: 3/5
			  });
			  break;
		case 'Accessories':
			$('#target').Jcrop(
			{
				onChange:   showCoords,
				onSelect:   showCoords,
				onRelease:  clearCoords,
				aspectRatio: 1
			  });
			  break;
	}
	
		  

});
// Simple event handler, called from onChange and onSelect
// event handlers, as per the Jcrop invocation above
function showCoords1(c)
{

      $('#x1_1').val(c.x);
      $('#y1_1').val(c.y);
      $('#x1_2').val(c.x2);
      $('#y1_2').val(c.y2);
      $('#w1').val(c.w);
      $('#h1').val(c.h);
};
function showCoords2(c)
{

      $('#x2_1').val(c.x);
      $('#y2_1').val(c.y);
      $('#x2_2').val(c.x2);
      $('#y2_2').val(c.y2);
      $('#w2').val(c.w);
      $('#h2').val(c.h);
};
function showCoords3(c)
{

      $('#x3_1').val(c.x);
      $('#y3_1').val(c.y);
      $('#x3_2').val(c.x2);
      $('#y3_2').val(c.y2);
      $('#w3').val(c.w);
      $('#h3').val(c.h);
};
function showCoords(c)
{

      $('#x1').val(c.x);
      $('#y1').val(c.y);
      $('#x2').val(c.x2);
      $('#y2').val(c.y2);
      $('#w').val(c.w);
      $('#h').val(c.h);
};
function clearCoords()
{
      $('#coords input').val('');
      $('#h').css({color:'red'});
      window.setTimeout(function(){
        $('#h').css({color:'inherit'});
      },500);
};
function clearCoords1()
{
      $('#coords input').val('');
      $('#h1').css({color:'red'});
      window.setTimeout(function(){
        $('#h1').css({color:'inherit'});
      },500);
};
function clearCoords2()
{
      $('#coords input').val('');
      $('#h2').css({color:'red'});
      window.setTimeout(function(){
        $('#h2').css({color:'inherit'});
      },500);
};
function clearCoords3()
{
      $('#coords input').val('');
      $('#h3').css({color:'red'});
      window.setTimeout(function(){
        $('#h3').css({color:'inherit'});
      },500);
};
function change_crop_ratio(control)
{

	switch(parseInt(control.value))
	{
		case 0:
			$('#target').Jcrop(
			{
				aspectRatio: 1
      		});
		  break;
		case 1:
		  	$('#target').Jcrop(
			{
				aspectRatio: 3/5
      		});
		  break;
	}


}
function validateForm(product_id, panty_id	)
{
	
	if (document.getElementById(product_id).value == '') 
	{
		alert("Product ID was left blank");
		return false;
	}
	
	if ( panty_id === undefined ) 
	{
  	}
  	else
  	{
  		if (document.getElementById(panty_id).value == '') 
		{
			alert("Panty ID was left blank");
			return false;
		}
		if (document.getElementById(panty_id).value == document.getElementById(product_id).value) 
		{
			alert("Error: Bra ID and Panty ID are entered the same - they can't be");
			return false;
		}
	}
  	
  	
	
	/* this does not work in safari
	//check that all input values are filled in
	formID = 'coords';
	
	var elem = document.getElementById(formID).elements;
	for(var i = 0; i < elem.length; i++)
	{
		if (elem[i].value == null || elem[i].value =="")
		{
			alert(elem[i].name + "was left blank - all fields must be filled in");
			return false;
		}
		
	} 
	*/
	
}



