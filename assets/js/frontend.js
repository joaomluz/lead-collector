/**
 * Plugin Template frontend js.
 *
 *  @package WordPress Plugin Template/JS
 */

jQuery(document).ready(function($) {
	
	jQuery(document).ready(function($) {
		$('.wordpress-ajax-form-lead-collector').on('submit', function(e) {
			e.preventDefault();
			$('#submit-lead-collector').hide();
			$('#loading-lead-collector').show()
			var $form = $(this);
			$.post($form.attr('action'), $form.serialize(), function(data) {
				if(data) {
					$('.wordpress-ajax-form-lead-collector').hide();
					$('#success-lead-collector').show();
				} else{
					//TODO: Handle error.
				}
			}, 'json');
		});
		
		jQuery("#sc_budget").inputmask('integer', {
			radixPoint:".",
			groupSeparator: ",",
			autoGroup: true,
			digits: 0,
			digitsOptional: false,
			placeholder: '0',
			rightAlign: false,
			prefix: '$',
			onBeforeMask: function (value, opts) {
			  return value;
			}
		});
		jQuery("#sc_phone").inputmask("(99)9999-9999");
	
	});
 
});
