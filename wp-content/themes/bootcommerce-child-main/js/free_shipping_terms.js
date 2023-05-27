jQuery(document).ready(function($){
	$('#free_shipping_terms_delivery').after('<div id="free_shipping_terms_delivery_table" class="free_shipping_terms_table" data-term="delivery"></div><div><button type="button" id="free_shipping_terms_delivery_add_button" class="button-primary free_shipping_terms_add_button" data-term="delivery">Добавить</button>');
	
	$('#free_shipping_terms_postamat').after('<div id="free_shipping_terms_postamat_table" class="free_shipping_terms_table" data-term="postamat"></div><div><button type="button" id="free_shipping_terms_postamat_add_button" class="button-primary free_shipping_terms_add_button" data-term="postamat">Добавить</button>');
	
	freeShippingTermsGetTableRow = function(term, min, max, sum){
		return '<div class="free_shipping_terms_row" style="margin-bottom: 10px;">' + 
					'<div style="display: inline-block; margin-right:20px">' + 
						'<label style="line-height:27px; margin-right:7px">от</label>' + 
						'<input type="number" min="0" max="999999999" class="free_shipping_terms_field min" value="' + min + '" style="width:auto">' + 
					'</div>' + 
					'<div style="display: inline-block; margin-right:20px">' + 
						'<label style="line-height:27px; margin-right:7px">от</label>' + 
						'<input type="number" min="0" max="999999999" class="free_shipping_terms_field max" value="' + max + '" style="width:auto">' + 
					'</div>' + 
					'<div style="display: inline-block; margin-right:20px">' + 
						'<label style="line-height:27px; margin-right:7px">сумма</label>' + 
						'<input type="number" min="0" max="999999999" class="free_shipping_terms_field sum" value="' + sum + '" style="width:auto">' + 
					'</div>' + 
				'</div>';
	}
	
	$(document).on('click', '.free_shipping_terms_add_button', function(){
		var term = $(this).data('term'),
			tableRow = freeShippingTermsGetTableRow(term, 0, 0, 0);
		$('#free_shipping_terms_' +  term + '_table').append(tableRow);
	});
	
	freeShippingTermsParseFields = function(){
		$('.free_shipping_terms').each(function(){
			var term = $(this).attr('id').replace('free_shipping_terms_', ''),
				terms = $(this).val();
				
			if (terms !== ''){
				var vals = JSON.parse(terms);
				for (var i = 0; i < vals.length; i++){
					var tableRow = freeShippingTermsGetTableRow(term, vals[i].min, vals[i].max, vals[i].sum);
					$('#free_shipping_terms_' +  term + '_table').append(tableRow);
				}
			}
		});
	}
	freeShippingTermsParseFields();
	
	
	freeShippingTermsSetValues = function(){
		$('.free_shipping_terms_table').each(function(){
			var term = $(this).data('term'),
				vals = [];
			
			$(this).find('.free_shipping_terms_row').each(function(k, v){
console.log(k);
				vals.push({
					min: $(this).find('input.min').val(),
					max: $(this).find('input.max').val(),
					sum: $(this).find('input.sum').val()
				});
			});
			
			$('#free_shipping_terms_' + term).val(JSON.stringify(vals));
		});
	}
	
	$(document).on('input', '.free_shipping_terms_field', function(){
		freeShippingTermsSetValues();
	});
});