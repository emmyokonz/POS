/*jslint browser: true*/
/*global $, jQuery, alert*/

$(document).ready(function () {

		"use strict";

		$('.textarea_editor').wysihtml5();
	
		$('#copy_msg').on('click',function(){
				$('#mail-cc').removeClass('hidden');
				$('#copy_msg').removeClass('col-md-1');
				$('#copy_msg').addClass('hidden');
				$('#recipients').removeClass('col-md-9');
				$('#recipients').addClass('col-md-10');
			});
	
		$('#delete_msg').click(function(e){
				var canceks=confirm('Do you want to delete this information?');
				if(!canceks){
					e.preventDefault();
				}
			});
			
		$('.delete_msg').click(function(e){
				var canceks=confirm('Do you want to delete this information?');
				if(!canceks){
					e.preventDefault();
				}
			});
			
		[].slice.call(document.querySelectorAll('.sttabs')).forEach(function(el) {
				new CBPFWTabs(el);
			});
	
		$('#checkbox0').on('click',function(){
				$('input:checkbox').not(this).prop('checked',this.checked);
			});
			
		// delegate calls to data-toggle="lightbox"
		$(document).delegate('*[data-toggle="lightbox"]:not([data-gallery="navigateTo"])', 'click', function(event) {
				event.preventDefault();
				return $(this).ekkoLightbox({
						onShown: function() {
							if (window.console) {
								return console.log('Checking our the events huh?');
							}
						},
						onNavigate: function(direction, itemIndex) {
							if (window.console) {
								return console.log('Navigating ' + direction + '. Current item: ' + itemIndex);
							}
						}
					});
			});
		//Programatically call
		$('#open-image').click(function(e) {
				e.preventDefault();
				$(this).ekkoLightbox();
			});
		$('#open-youtube').click(function(e) {
				e.preventDefault();
				$(this).ekkoLightbox();
			});
		// navigateTo
		$(document).delegate('*[data-gallery="navigateTo"]', 'click', function(event) {
				event.preventDefault();
				var lb;
				return $(this).ekkoLightbox({
						onShown: function() {
							lb = this;
							$(lb.modal_content).on('click', '.modal-footer a', function(e) {
									e.preventDefault();
									lb.navigateTo(2);
								});
						}
					});
			});
	});