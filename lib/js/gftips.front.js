//***************************************************************
// apply tooltip to chosen element
//***************************************************************

//***************************************************************
// start the engine
//***************************************************************

jQuery( document ).ready( function($) {

	$('body')
		.tooltip({
			selector  : '[data-toggle="tooltip"]'
		})
		.popover({
			selector  : '[data-toggle="popover"]'
		});

});
