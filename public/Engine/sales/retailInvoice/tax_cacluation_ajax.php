<?php
/*
	my thought is that because the tax calculations are so nasty that they should be done on the server
	so we should be passing the invoice body data here, along with discount information, and the tax calculation method
	we will re-calculate the body data and send it back
	
	now the original thought is as the user is typing the tax is updating, but this is just not realistic
	
	suppose there is a tax code update. well that means the browsers need to dump the code and start fresh, which means I need to version the javascript files, which I need to start doing anyway.
	
	So the user might need to push a 'calculate tax' button to run the tax calculation
	or we push the tax calculation else where
	or we check tax after the discount is added, the sale price is updated, the quantity is updated, or an item is added, or the tax category is updated
	
	it looks like what I should do is just provide the tax information back given the body data
	
	or version the javascript
	
	
*/

