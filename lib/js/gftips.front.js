//***************************************************************
// apply tooltip to chosen element
//***************************************************************

//***************************************************************
// start the engine
//***************************************************************

jQuery( document ).ready( function($) {

	$('body')
		.tooltip({
			container : 'body',
			selector  : '[data-toggle="tooltip"]'
		})
		.popover({
			container : 'body',
			selector  : '[data-toggle="popover"]'
		});

});
