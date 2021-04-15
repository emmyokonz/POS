$(document).ready( function ()
	{
		var table = $('#datatable').DataTable(
			{
				bInfo:false,
				paging:false,
				responsive: true,
				aaSorting: [],
				responsive: true,
				fixedHeader:
				{
					header: true,
					footer: true
				},
				"columnDefs": [
					{
						"orderable": false, "targets": -1,
					}
				],
				"lengthChange": false,
				fnDrawCallback: function(oSettings)
				{
					if(oSettings.aoData.length <= oSettings._iDisplayLength)
					{
						$(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
					}
				}
			});
//			new $.fn.dataTable.Fixheader(table);
		var tableTools = new $.fn.dataTable.Buttons( table,
			{
				"buttons": [
					{
						extend: 'pdf',
						text: '<i class="fa fa-file-pdf-o"></i>',
						titleAttr: 'Convert to pdf'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						titleAttr: 'Print data'
					},
					{
						extend: 'excel',
						text: '<i class="fa fa-file-excel-o"></i>',
						titleAttr: 'Export to excel'
					}
				]
			} );

		table.buttons().container().insertBefore('.actions #create');

		$('.buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-info btn-sm btn-outline hidden-sm hidden-xs');

	} );