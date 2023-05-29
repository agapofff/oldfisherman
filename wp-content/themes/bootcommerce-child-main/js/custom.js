jQuery(document).ready(function ($) {
    // $.ajaxSetup({ 
        // cache: true
    // });
    
    
    // LOADING
    var loadingTimer;
    var loading = function (show = true) {
            if (show) {
                $('#loader').show();
                loadingTimer = setTimeout(function () {
                    loading(false);
                }, 3000);
            } else {
                $('#loader').hide();
            }
        }
    $('form').on('beforeSubmit', function () {
        loading(false);
    });
    $(window).on('beforeunload', function () {
        loading();
        $('#fade').fadeIn('fast');
        $('.modal').modal('hide');
    });
    $(window).on('load', function () {
        loading(false);
    });
    $(document).on('click', '#loader', function () {
        loading(false);
    });
    loading(false);
	
    
	// уведомления
	toastr.options = {
		tapToDismiss: true,
		positionClass: 'toast-bottom-right',
		newestOnTop: false,
		preventDuplicates: true,
		escapeHtml: false,
		closeButton: false,
		closeDuration: 3600,
	};
    
    
	// модальные окна
	$(document).on('click', '*[data-toggle="lightbox"], .lightbox', function(e) {
		e.preventDefault();
		showLoader();
		$(this).ekkoLightbox({
			alwaysShowClose: true,
			onShown: function(){
				hideLoader();
			},
			onContentLoaded: function(){
				// articlesOwlCarousel();
				$('.ekko-lightbox-container').find('meta, base, link').remove();
				$('.ekko-lightbox-container').find('iframe').wrap('<div class="video"></div>');
				hideLoader();
			},
			onNavigate: function(){
				showLoader();
			}
		});
		$('[data-toggle="tooltip"]').tooltip('hide');
	});
    

    // DROPDOWN on hover
    function dropdownHover() {
        $('#nav-main .dropdown-toggle').removeAttr('data-bs-toggle');
        $('#nav-main .dropdown').hover(function () {
            $(this).find('.dropdown-toggle').addClass('show');
            $(this).find('.dropdown-menu').addClass('show');
        }, function () {
            $(this).find('.dropdown-toggle').removeClass('show');
            $(this).find('.dropdown-menu').removeClass('show');        
        });
    }
    dropdownHover();
    
    
	// добавление в корзину
	$('body').bind('adding_to_cart', function() {
		loading();
	});
	$('body').bind('added_to_cart', function() {
		loading(false);
		if (location.href.includes('/cart')) {
			updateCart();
		}
		toastr.info('Товар добавлен в корзину');
	});
    
    
    // избранное
	$('body').bind('tinvwl_wishlist_button_clicked', function() {
		loading();
	});
	$('body').bind('tinvwl_wishlist_added_status', function(event, html, status) {
		loading(false);
	});
    
    
    // language switcher
    function languageSwitcherFormatter() {
        $('#menu-languages > li > a')
            .attr('data-bs-toggle', 'dropdown')
            .attr('aria-expanded', 'false')
            .addClass('dropdown-toggle');
        $('#menu-languages')
            .find('ul')
            .addClass('dropdown-menu dropdown-menu-end')
            .find('a')
            .attr('data-pjax', 0);
    }
    languageSwitcherFormatter();
    
    
    // PJAX
    var pjax = new Pjax({
        elements: 'a:not([data-pjax="0"])',
        selectors: [
            'title',
            'meta[name=description]',
            'body',
        ],
    });
    document.addEventListener('pjax:send', function () {
        loading();
    });
    document.addEventListener('pjax:complete', function () {
        loading(false);
        clearTimeout(loadingTimer);
        $(window).lazyLoadXT();
        dropdownHover();
        languageSwitcherFormatter();
    });
});
