/*jslint browser: true*/
/*global $, jQuery, alert*/

base_url = $('base').attr('href');
;(function ($) {

    "use strict";

    var body = $("body");
	
    $(function () {
        $(".preloader").fadeOut();
        $('#side-menu').metisMenu();
        
    });

    /* ===== Theme Settings ===== */

    $(".open-close").on("click", function () {
        body.toggleClass("show-sidebar").toggleClass("hide-sidebar");
        $(".sidebar-head .open-close i").toggleClass("ti-menu");
    });

    /* ===== Open-Close Right Sidebar ===== */

    $(".right-side-toggle").on("click", function () {
        $(".right-sidebar").slideDown(50).toggleClass("shw-rside");
        $(".fxhdr").on("click", function () {
            body.toggleClass("fix-header"); /* Fix Header JS */
        });
        $(".fxsdr").on("click", function () {
            body.toggleClass("fix-sidebar"); /* Fix Sidebar JS */
        });

        /* ===== Service Panel JS ===== */

        var fxhdr = $('.fxhdr');
        if (body.hasClass("fix-header")) {
            fxhdr.attr('checked', true);
        } else {
            fxhdr.attr('checked', false);
        }
    });

    /* ===========================================================
        Loads the correct sidebar on window load.
        collapses the sidebar on window resize.
        Sets the min-height of #page-wrapper to window size.
    =========================================================== */

    $(function () {
        var set = function () {
                var topOffset = 60,
                    width = (window.innerWidth > 0) ? window.innerWidth : this.screen.width,
                    height = ((window.innerHeight > 0) ? window.innerHeight : this.screen.height) - 1;
                if (width < 768) {
                    $('div.navbar-collapse').addClass('collapse');
                    topOffset = 100; /* 2-row-menu */
                } else {
                    $('div.navbar-collapse').removeClass('collapse');
                }

                /* ===== This is for resizing window ===== */

                if (width < 1170) {
                    body.addClass('content-wrapper');
                    $(".sidebar-nav, .slimScrollDiv").css("overflow-x", "visible").parent().css("overflow", "visible");
                } else {
                    body.removeClass('content-wrapper');
                }

                height = height - topOffset;
                if (height < 1) {
                    height = 1;
                }
                if (height > topOffset) {
                    $("#page-wrapper").css("min-height", (height) + "px");
                }
            },
            url = window.location,
            element = $('ul.nav a').filter(function () {

                return this.href === url || url.href.indexOf(this.href) === 0;
            }).addClass('active').parent().parent().addClass('in').parent();
     	
        if (element.is('li')) {
            element.addClass('active');
        }
        $(window).ready(set);
        $(window).bind("resize", set);
    });

    /* ===== Collapsible Panels JS ===== */

    (function ($, window, document) {
        var panelSelector = '[data-perform="panel-collapse"]',
            panelRemover = '[data-perform="panel-dismiss"]';
        $(panelSelector).each(function () {
            var collapseOpts = {
                    toggle: false
                },
                parent = $(this).closest('.panel'),
                wrapper = parent.find('.panel-wrapper'),
                child = $(this).children('i');
            if (!wrapper.length) {
                wrapper = parent.children('.panel-heading').nextAll().wrapAll('<div/>').parent().addClass('panel-wrapper');
                collapseOpts = {};
            }
            wrapper.collapse(collapseOpts).on('hide.bs.collapse', function () {
                child.removeClass('ti-minus').addClass('ti-plus');
            }).on('show.bs.collapse', function () {
                child.removeClass('ti-plus').addClass('ti-minus');
            });
        });

        /* ===== Collapse Panels ===== */

        $(document).on('click', panelSelector, function (e) {
            e.preventDefault();
            var parent = $(this).closest('.panel'),
                wrapper = parent.find('.panel-wrapper');
            wrapper.collapse('toggle');
        });

        /* ===== Remove Panels ===== */

        $(document).on('click', panelRemover, function (e) {
            e.preventDefault();
            var removeParent = $(this).closest('.panel');

            function removeElement() {
                var col = removeParent.parent();
                removeParent.remove();
                col.filter(function () {
                    return ($(this).is('[class*="col-"]') && $(this).children('*').length === 0);
                }).remove();
            }
            removeElement();
        });
    }(jQuery, window, document));

    /* ===== Tooltip Initialization ===== */

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    /* ===== Popover Initialization ===== */

    $(function () {
        $('[data-toggle="popover"]').popover();
    });

    /* ===== Task Initialization ===== */

    $(".list-task li label").on("click", function () {
        $(this).toggleClass("task-done");
    });
    $(".settings_box a").on("click", function () {
        $("ul.theme_color").toggleClass("theme_block");
    });

    /* ===== Collepsible Toggle ===== */

    $(".collapseble").on("click", function () {
        $(".collapseblebox").fadeToggle(350);
    });

    /* ===== Sidebar ===== */

    $('.slimscrollright').slimScroll({
        height: '100%',
        position: 'right',
        size: "5px",
        color: '#dcdcdc'
    });
    $('.slimscrollsidebar').slimScroll({
        height: '100%',
        position: 'left',
        size: "10px",
        color: 'rgba(0,0,0,0.9)'
    });
    $('.chat-list').slimScroll({
        height: '100%',
        position: 'right',
        size: "0px",
        color: '#dcdcdc'
    });

    /* ===== Resize all elements ===== */

    body.trigger("resize");

    /* ===== Visited ul li ===== */

    $('.visited li a').on("click", function (e) {
        $('.visited li').removeClass('active');
        var $parent = $(this).parent();
        if (!$parent.hasClass('active')) {
            $parent.addClass('active');
        }
        e.preventDefault();
    });

    /* ===== Login and Recover Password ===== */

    $('#to-recover').on("click", function () {
        $("#loginform").slideUp();
        $("#recoverform").fadeIn();
    });

    /* ================================================================= 
        Update 1.5
        this is for close icon when navigation open in mobile view
    ================================================================= */

    $(".navbar-toggle").on("click", function () {
        $(".navbar-toggle i").toggleClass("fa fa-menu").addClass("fa fa-times");
    });

	/* ================================================================= 
	        dropify
	    ================================================================= */
	
	if(jQuery().dropify){
		$('.dropify').dropify({});
	}
	
	
	/* ================================================================= 
	        Bootstrap-HTML5 Editor
	    ================================================================= */
	if(jQuery().wysihtml5){
	    $('.textarea').wysihtml5({
			"toolbar": {
				"fa": true,
				"html": false, //Button which allows you to edit the generated HTML. Default false
    			"link": false, //Button to insert a link. Default true
    			"image": false
			}
	    }); 
	}
	/* ================================================================= 
	        Bootstrap-select Editor
	    ================================================================= */
	if(jQuery().Selectpicker){
		$('.selectpicker').selectpicker({});
	}
	
  	$( ".selector" ).on( "sortchange", function( event, ui ) {
  		
  	} );

	
	$(document).ready(function() {
		$('#uploaded_message').delay(4000).hide();
	});
	
	$('.opendialog').on("click", function(e) {
		e.preventDefault();
		var url = $(this).data("url");
		load_page(url);

	});
	
	$('#pageContainer').on('show.bs.modal',  function (event) {
		var invoker = $(event.relatedTarget); // Button that triggered the modal
		var action = invoker.data('whatever');
	

		// Extract info from data-* attributes
		// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
		// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
		var modal = $(this);
		modal.find('.modal-title').text(action);
		
		modal.find('.modal-body .panel-heading').text('');
	});
	
	$(document).on('hidden.bs.modal','#pageContainer', function () {
		$('#page_loader').text('');
		$('.modal-backdrop').remove();
		$('#page_loader').find('.panel-body #newCategoryForm').removeAttr('data-submitandselect');
		//stop the loader when form is submited and modal is closed.
		$('.modal-footer #action').html('');
		$('.modal-body #panel_actions button').removeAttr('disabled');//enable the buttons in the form 
		$('.modal-body #panel_actions #saveandclose').removeClass('hide');//make the saveandclose button visible

	});
	
	$(document).on('submit','#pageContainer #newCategoryForm',function(event) {
		event.preventDefault();
		$('.modal-footer #action').html('<div class="fa fa-spin fa-spinner"></div> <i>please wait</i>');
		$('.modal-body #panel_actions button').attr('disabled','disabled');//disable the buttons in the form
		
		$.ajax({
			data: new FormData(this),
			type: 'POST',
			url:  global_vars.base_url+"ajax/new_category_ajax",
			contentType: false,
			cache: false,
			processData:false,
			dataType: 'json',
			timeout: 10000,
			success:function(data) {
				if (data.response == '200') {

					//let save the name of the newly created category to be used when repopulating the dropdown if needed.
					var new_category_name = $('#pageContainer .panel-body #name').val();
					demo.showNotification('success','top','center',data.message);
					
//					clear all form input fields
					$('#newCategoryForm')[0].reset();
					
//					if the dialog will need to polulate a dropdown and select the newly created action/item
					var insertandselect = $('#page_loader .panel-body #newCategoryForm').data('submitandselect');
					
					if ($.trim(insertandselect) == "1") {

						var dropdown = get_categories();
						if (Array.isArray(dropdown) ) {
							$('#productcategory'.split(" ").join("_").toLowerCase()).empty();
							$('#productcategory'.split(" ").join("_").toLowerCase()).append("<option value=''>Choose a Category</option>");
							for ( var i = 0; i<dropdown.length; i++) {
								var id = dropdown[i]['id'];
								var name = dropdown[i]['name'];
								if ($.trim(data.new_cat) == id) {
									$('#productcategory'.split(" ").join("_").toLowerCase()).append("<option selected='selected' value='"+id+"'>"+name+"</option>");
								} else {
									$('#productcategory'.split(" ").join("_").toLowerCase()).append("<option value='"+id+"'>"+name+"</option>");
								}

							}
						} else {
							demo.showNotification('danger','top','center',dropdown);
						}
					}
					
					//close modal if clicked save
					$('#pageContainer').modal('hide');

				} else {
					demo.showNotification('danger','top','center',data.message);
					
					//stop the loader when error is encountered.
					$('.modal-footer #action').html('');
					$('.modal-body #panel_actions button').removeAttr('disabled');//disable the buttons in the form
				}
			
			},
			error:function(request, status, error) {
				if (status  == 'timeout') {
					demo.showNotification('danger','top','center','Service timeout. Your network appears to be unstable');
				} else {
					demo.showNotification('danger','top','center','Error occured saving category');

				console.log("ajax call went wrong:" + request.responseText);
				}
			}
		});

	});
	
	$("#item").autocomplete({source: function (request, response) {
			$('.loader').removeClass('hidden');
			$.ajax({
				url: global_vars.base_url+"ajax/item_search",
				data: request,
				dataType: "json",
				type: "POST",
				success: function(data) {
					response(data);
					$('.loader').addClass('hidden');
				}
			});
		}, delay:10, minLength:0, select: function(event, ui) {
			if (ui.item) {
				$('#item').val(ui.item.value);
			}
			$("#add_item_form").submit();
		},
		error:function(request, status, error) {
			console.log("ajax call went wrong:" + request.responseText);
		}
		});
		
		$('#add_to_cart').click(function() {
			$("#add_item_form").submit();
		});
		
		$(".content-form").submit(function(event) {

			event.preventDefault();
			$('.loader').removeClass('hidden');
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: $(this).serialize(),
				dataType:'json',
				success: function(data) {
					if (data.status == '200') {
//						clear all form input fields
						$('#add_item_form')[0].reset();
						$('#cart_contents').html(data.content.cart_items);
						$('#subtotal').html(formatNumber(data.content.subtotal,0));
						$('#total').html(formatNumber(data.content.total,0));
						$('.loader').addClass('hidden');
					}else{
//						alert(data.error);
						demo.showNotification('danger','top','center',data.error);
						$('.loader').addClass('hidden');
					}
				},
				error:function(request, status, error) {
					console.log("ajax call went wrong:" + request.responseText);
				}
			});
		});
		
		$(document).on("change input",'.digitsonly',function(e) {
			var position = this.selectionStart - 1;
			//remove all but number and . and -
			var fixed = this.value.replace(/[^0-9\.]/g, '');
			if (fixed.charAt(0) === '.')
				//can't start with .
			fixed = fixed.slice(1);

			var pos = fixed.indexOf(".") + 1;
			if (pos >= 0)
				//avoid more than one .
			fixed = fixed.substr(0, pos) + fixed.slice(pos).replace('.', '');
			
			if (this.value !== fixed) {
				this.value = fixed;
				this.selectionStart = position;
				this.selectionEnd = position;
			}
		});
		
		$(document).on("change input",'.digitsminusonly',function(e) {
			var position = this.selectionStart - 1;
			//remove all but number and . and -
			var fixed = this.value.replace(/[^0-9\.-]/g, '');
			if (fixed.charAt(0) === '.')
				//can't start with .
			fixed = fixed.slice(1);

			var pos = fixed.indexOf(".") + 1;
			if (pos >= 0)
				//avoid more than one .
			fixed = fixed.substr(0, pos) + fixed.slice(pos).replace('.', '');
			
			var minus = fixed.indexOf("-") + 1;
				//avoid more than one -
			if (minus >= 0)
			fixed = fixed.substr(0, minus) + fixed.slice(minus).replace('-', '');

			if (this.value !== fixed) {
				this.value = fixed;
				this.selectionStart = position;
				this.selectionEnd = position;
			}
		});
		
		$(document).on("change input",'.qty , .price, #amountpaid',function(e) {
				
				var position = this.selectionStart - 1;
				//remove all but number and .
				var fixed = this.value.replace(/[^0-9\.]/g, '');
				if (fixed.charAt(0) === '.')
					//can't start with .
				fixed = fixed.slice(1);

				var pos = fixed.indexOf(".") + 1;
				if (pos >= 0)
					//avoid more than one .
				fixed = fixed.substr(0, pos) + fixed.slice(pos).replace('.', '');

				if (this.value !== fixed) {
					this.value = fixed;
					this.selectionStart = position;
					this.selectionEnd = position;
				}

				var amount = $('#qty_'+$(this).data('line')).val() * $('#price_'+$(this).data('line')).val();
				var subtotal =0;
				$('#amount_'+$(this).data('line')).text(formatNumber(amount,0));
				$('#amount_'+$(this).data('line')).attr('data-amount',(amount));
				$('.cart-item').each(function() {
					subtotal += parseFloat($(this).children('.amount').attr('data-amount'));
				});
				
				$('#subtotal').text(formatNumber(subtotal,0));
				if ($('#amountpaid').val() == '') {
					var total = (subtotal - 0);
				}else{
					
				var total = subtotal - parseFloat($('#amountpaid').val());
				}
				$('#total').text(formatNumber(total,0));
				$('#total').attr('data-total',total);
		});
		
		$(document).on("change blur",'.qty , .price',function(e) {
			var trigger = $(this);
			trigger.attr('disabled','disabled');
			$('.loader').removeClass('hidden');
			if ($('#price_'+$(this).data('line')).val() == '') {
				$('#price_'+$(this).data('line')).val(formatNumber(0,0))
			}
			var form_data = {"qty":$('#qty_'+$(this).data('line')).val(),'line':$(this).data('line'),'cart':$('#cart_name').val(),'price':$('#price_'+$(this).data('line')).val(),'paid':$('#amountpaid').val()};
			
			$.ajax({
				url: global_vars.base_url+"ajax/update_cart",
				data: form_data,
				type: 'POST',
				dataType: "json",
				success:function(data){
					if (data.status == 200) {
						
						trigger.removeAttr('disabled');
						$('.loader').addClass('hidden');
						
						
					}else{
						demo.showNotification('danger','top','center',data.error);
						$('.loader').addClass('hidden');
					}
				},
				error:function(request, status, error) {
					console.log("ajax call went wrong:" + request.responseText);
				}
			});
			
		});
	
		$(document).on("change blur",'#amountpaid',function(e) {
			var trigger = $(this);
			trigger.attr('disabled','disabled');
			$('.loader').removeClass('hidden');
			if ($('#price_'+$(this).data('line')).val() == '') {
				$('#price_'+$(this).data('line')).val(formatNumber(0,0))
			}
			var form_data = {'cart':$('#cart_name').val(),'total':$(trigger).val()};
			
			$.ajax({
				url: global_vars.base_url+"ajax/update_cart_payment",
				data: form_data,
				type: 'POST',
				dataType: "json",
				success:function(data){
					if (data.status == 200) {
						
						trigger.removeAttr('disabled');
						$('.loader').addClass('hidden');
						
						
					}else{
						demo.showNotification('danger','top','center',data.error);
						$('.loader').addClass('hidden');
					}
				},
				error:function(request, status, error) {
					console.log("ajax call went wrong:" + request.responseText);
				}
			});
			
		});
	
		$(document).on("click",'.remove_cart_item',function(e) {
			e.preventDefault();	
			var trigger = $(this);
			$('#qty_'+trigger.data('line')).attr('disabled','disabled');
			$('.loader').removeClass('hidden');
			$('#price_'+trigger.data('line')).attr('disabled','disabled');
			var form_data = {'line':trigger.data('line'),'cart':$('#cart_name').val()};
			
			$.ajax({
				url: global_vars.base_url+"ajax/remove_cart_item",
				data: form_data,
				type: 'POST',
				dataType: "json",
				success:function(data){
					if (data.status == 200) {
						$('#cart_contents').html(data.content.cart_items);
						$('#subtotal').html(formatNumber(data.content.subtotal,0));
						$('#total').html(formatNumber(data.content.total,0));
						$('.loader').addClass('hidden');
						demo.showNotification('success','top','center',data.message);
					}else{
					$('.loader').addClass('hidden');
						demo.showNotification('danger','top','center',data.error);
					}
				},
				error:function(request, status, error) {
					console.log("ajax call went wrong:" + request.responseText);
				}
			});
			
		});
	
		$(document).on("click",'.remove_all_cart_item , .cancle',function(e) {
			e.preventDefault();	
			var trigger = $(this);
			$('.qty').attr('disabled','disabled');
			$('.loader').removeClass('hidden');
			$('.price').attr('disabled','disabled');
			var form_data = {'line':trigger.data('line'),'cart':$('#cart_name').val(),'close_cart':"true"};
			
			$.ajax({
				url: global_vars.base_url+"ajax/remove_cart_item",
				data: form_data,
				type: 'POST',
				dataType: "json",
				success:function(data){
					if (data.status == 200) {
						$('#cart_contents').html(data.content.cart_items);
						$('#subtotal').html(formatNumber(data.content.subtotal,0));
						$('#total').html(formatNumber(data.content.total,0));
						$('.loader').addClass('hidden');
						demo.showNotification('success','top','center',data.message);
						setTimeout(function() {window.location.replace(trigger.attr('href'));}, 500);
						
						
					}else{
					$('.loader').addClass('hidden');
						demo.showNotification('danger','top','center',data.error);
					}
				},
				error:function(request, status, error) {
					console.log("ajax call went wrong:" + request.responseText);
				}
			});
			
		});
		
		$(document).on("click",'.save-cart',function(e) {
			e.preventDefault();
			var trigger = $(this);
			$('.qty').attr('disabled','disabled');
			$('.loader').removeClass('hidden');
			$('.price').attr('disabled','disabled');
			if ($(this).hasClass('savenew')) {
				var form_data = {'cart':$('#cart_name').val(),'save':'savenew'}
			}else if($(this).hasClass('saveclose')){
				var form_data = {'cart':$('#cart_name').val(),'save':'saveclose'}
			} else {
				var form_data = {'cart':$('#cart_name').val()}
			}
			
			$.ajax({
				url: global_vars.base_url+"ajax/save_cart",
				data: form_data,
				type: 'POST',
				dataType: "json",
				success:function(data){
					if (data.status == 200) {

						demo.showNotification('success','top','center',data.message);
						$('.loader').addClass('hidden');
						setTimeout(function(){
							window.location.replace(data.href);
						}, 500);
						
					}else{
					$('.loader').addClass('hidden');
						demo.showNotification('danger','top','center',data.error);
					}
				},
				error:function(request, status, error) {
					console.log("ajax call went wrong:" + request.responseText);
				}
			});
			
		});
		
		$(document).on('click', '.__new', function(e) {
			e.preventDefault();
			var data = {'cart':$(this).attr('data-sales')};
			var href = $(this).attr('href');
			var cart = $(this).attr('data-sales');
			//lets make an ajax call to check if our cart is empty
			$.ajax({
				url: global_vars.base_url+"ajax/cart_empty",
				data: data,
				type: 'POST',
				dataType: "json",
				success:function(data) {
					if (data.status == '200') {
						//cart is empty
						window.location.replace(href);
					}else{
					//cart is not empty'
						if (confirm('Please save the current transaction you are working on before creating a new one.')) {
							window.location.replace(href);
						} else {
							var dat = {'line':'1','cart':cart,'close_cart':"true"};
							$.ajax({
								url: global_vars.base_url+"ajax/remove_cart_item",
								data: dat,
								type: 'POST',
								dataType: "json",
								success: function(respons) {
									window.location.replace(href);
								}
							});
						}
							
						window.location.replace(href);
					}
				},
				error:function(request, status, error) {
					console.log("ajax call went wrong:" + request.responseText);
				}
			});
		});
		
		$(document).on("click",'.edit-cart',function(e) {
			e.preventDefault();	
			var trigger = $(this);
			$('.qty').attr('disabled','disabled');
			$('.loader').removeClass('hidden');
			$('.price').attr('disabled','disabled');
			if ($(this).hasClass('savenew')) {
				var form_data = {'cart':$('#cart_name').val(),'save':'savenew'}
			}else if($(this).hasClass('saveclose')){
				var form_data = {'cart':$('#cart_name').val(),'save':'saveclose'}
			} else {
				var form_data = {'cart':$('#cart_name').val()}
			}
			
			$.ajax({
				url: global_vars.base_url+"ajax/edit_cart",
				data: form_data,
				type: 'POST',
				dataType: "json",
				success:function(data){
					if (data.status == 200) {

						demo.showNotification('success','top','center',data.message);
						$('.loader').addClass('hidden');
						setTimeout(function(){
							window.location.replace(data.href);
						}, 500);
						
					}else{
					$('.loader').addClass('hidden');
						demo.showNotification('danger','top','center',data.error);
					}
				},
				error:function(request, status, error) {
					console.log("ajax call went wrong:" + request.responseText);
				}
			});
			
		});
		
		$("#peoplepayment").autocomplete({source: function (request, response) {
				$('.loader').removeClass('hidden');
				var people = $('#peoplepayment').data('people');
				var data= {'people':people,'term':request.term};
				$.ajax({
					url: global_vars.base_url+"ajax/people_search",
					data: data,
					dataType: "json",
					type: "POST",
					success: function(data) {
						response(data);
						$('.loader').addClass('hidden');
					}
				});
			}, delay:10, minLength:0, select: function(event, ui) {
				if (ui.item) {
					$('#peoplepayment').val(ui.item.value);
				}
				$('#balance').html(formatNumber(ui.item.balance,1));
			},
			error:function(request, status, error) {
				console.log("ajax call went wrong:" + request.responseText);
			}
		});
		
		$("#people").autocomplete({source: function (request, response) {
				$('.loader').removeClass('hidden');
				var people = $('#people').data('people');
				var data= {'people':people,'term':request.term};
				$.ajax({
					url: global_vars.base_url+"ajax/people_search",
					data: data,
					dataType: "json",
					type: "POST",
					success: function(data) {
						response(data);
						$('.loader').addClass('hidden');
					}
				});
			}, delay:10, minLength:0, select: function(event, ui) {
				if (ui.item) {
					$('#people').val(ui.item.value);
				}
				$("#add_people_form").submit();
			},
			error:function(request, status, error) {
				console.log("ajax call went wrong:" + request.responseText);
			}
		});
		
		
		$(".people-form").submit(function(event) {

			event.preventDefault();
			$('.loader').removeClass('hidden');
//			alert($(this).serialize());
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: $(this).serialize(),
				dataType:'json',
				success: function(data) {
					if (data.status == '200') {
						$('.people-form #people').val(data.people);
						$('.loader').addClass('hidden');
					} else {
						demo.showNotification('danger','top','center',data.error);
						$('.loader').addClass('hidden');
					}
				},
				error:function(request, status, error) {
					console.log("ajax call went wrong:" + request.responseText);
				}
			});
		});
})(jQuery);

function load_page(url)
{
	$.ajax({
		url: url,
		dataType: 'json',
		success: function(res) {
			// get the ajax response data
			var data = res;

			// update modal content here you may want to format data or update other modal elements here too
			$('#page_loader').html(data.page);
			$('#page_loader').find('.panel-header').html('');
			$('#page_loader').find('.panel-body #newCategoryForm').attr('data-submitandselect','1');
			$('#page_loader').find('.panel-body #panel_actions #saveandclose').attr('disabled','disabled');
			$('#page_loader').find('.panel-body #panel_actions #saveandclose').addClass('hide');
			

			$('#uploaded_message').html(data.message);
		},
		error:function(request, status, error) {
			console.log("ajax call went wrong:" + request.responseText);
		}
	});
}

function get_categories()
{
	var result = false;
	$.ajax({
		type:'POST',
		url:global_vars.base_url+'ajax/get_categories_ajax',
		async: false,
		timeout: 10000,
		dataType: 'json',
		success:function(data) {
			result = data;
		},error:function(request, status, error) {
			if (status  == 'timeout') {
				result = 'Service timeout. Your network appears to be unstable';
			} else {
				result = 'Error occured trying to fetch categories.';

			}
		}
	});
	return result;
}

function checkAll(bx)
	{
		var cbs = document.getElementsByTagName('input');
		for (var i=0; i < cbs.length; i++) {
			if (cbs[i].type == 'checkbox') {
				cbs[i].checked = bx.checked;
			}
		}
	}
function formatNumber(number,set_currency=true)
	{
//		var obj = document.getElementById('txtExample');
		var obj = number;
		var num = new NumberFormat();
		num.setInputDecimal('.');
		num.setNumber(obj); // obj.value is '-32-d322c23232'
		num.setPlaces('2', false);
		num.setCurrencyValue('₦');
		num.setCurrency(set_currency);
		num.setCurrencyPosition(num.LEFT_INSIDE);
		num.setNegativeFormat(num.LEFT_DASH);
		num.setNegativeRed(false);
		num.setSeparators(true, ',', ',');
		return num.toFormatted();
	}
$(function(){
	$("#datepicker, .datepicker").datepicker({
		showButtonPanel: true,
		changeMonth: true,
		changeYear: true,
		dateFormat: "DD, d MM, yy"
	});
	
});

$( function() {
	var dateFormat = "dd/mm/yy",
	from = $( "#from" )
	.datepicker({
		changeMonth: true,
		showButtonPanel: true,
		changeYear: true,
		dateFormat: "dd/mm/yy"
	})
	.on( "change", function() {
		to.datepicker( "option", "minDate", getDate( this ) );
	}),
	to = $( "#to" ).datepicker({
		changeMonth: true,
		showButtonPanel: true,
		changeYear: true,
		dateFormat: "dd/mm/yy"
	})
	.on( "change", function() {
		from.datepicker( "option", "maxDate", getDate( this ) );
	});

	function getDate( element )
	{
		var date;
		try {
			date = $.datepicker.parseDate( dateFormat, element.value );
		} catch( error ) {
			date = null;
		}

		return date;
	}
} );

/*
$(function(){
	$("#datepickerrange, .datepickerrange").datepicker({
		showButtonPanel: true,
		changeMonth: true,
		changeYear: true,
		dateFormat: "DD, d MM, yy"
	})
	.on( "change", function() {
		to.datepicker( "option", "minDate", getDate( this ) );
	}),
	to = $( "#to" ).datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 3
	})
	.on( "change", function() {
		from.datepicker( "option", "maxDate", getDate( this ) );
	});
})*/
	/*if ($('#page_loader .panel-body #newCategoryForm').data('submit') == '1') {
		var dropdown = get_categories();
		if (Array.isArray(dropdown) ) {
			$('#productCategory'.split(" ").join("_").toLowerCase()).empty();
			$('#productCategory'.split(" ").join("_").toLowerCase()).append("<option value='0'>Choose a Category</option>");
			for ( var i = 0; i<dropdown.length; i++) {
				var id = dropdown[i]['id'];
				var name = dropdown[i]['name'];
				alert(name+ '----------'+new_category_name);
				if (new_category_name == name) {
					$('#productCategory'.split(" ").join("_").toLowerCase()).append("<option selected='selected' value='"+id+"'>"+name+"</option>");
				} else {
					$('#productCategory'.split(" ").join("_").toLowerCase()).append("<option value='"+id+"'>"+name+"</option>");
				}

			}
		} else {
			demo.showNotification('danger','top','center',dropdown);
		}
	}*/