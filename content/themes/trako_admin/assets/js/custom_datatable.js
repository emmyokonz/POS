;(function ($) {

	"use strict";
	
	var table = $('.printarea').DataTable({
		"paging":   true,
		"ordering": true,
		"info":     false,
	});

	var top_print = $('.sales').html();
	var datatable_title = $('.datatable_title').html();
	var datatable_message_top = $('.datatable_message_top').html();

	var buttons = new $.fn.dataTable.Buttons(table, {
		buttons: [

			{
				"extend": "print",
				"text": "<span class='fa fa-print'></span> <span class='hidden-xs'>Print Doc</span>",
				"className":"btn-success btn btn-sm",
				"title":datatable_title,
				"messageTop": datatable_message_top,
				orientation: 'landscape',
				pageSize: 'A4',
			},
			{
				"extend": "pdf",
				"text": "<span class='fa fa-file-pdf-o'></span> <span class='hidden-xs'>Export to PDF</span>",
				"className":"btn btn-sm btn-info",
				"title":datatable_title,
				orientation: 'portrait',
				pageSize: 'A4'
			},
			{
				'action': function(e, dt, node, config) {
//					window.location.replace(global_vars.__back);
					window.location.replace(window.history.back());
				},
				"text": "<span class='fa fa-angle-left'></span> <span class='hidden-xs'>Back</span>",
				"className":"btn btn-sm btn-default"
			}
		]
	}).container().appendTo($('#printtopbtn'));

})(jQuery);