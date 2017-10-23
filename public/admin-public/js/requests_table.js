// $('.table_date_picker').datepicker({
//     format: 'yyyy-mm-dd'       
//   });

$(document).ready(function() {

// DATE FILTER
  $('.table_filter').on('click', function () {
      requestTable.draw();
      assistanceTable.draw();
  } );

// PRINT BUTTON
  $(".btn-print")
    .click(function(event) {
             id = $(this).attr("id");
             src = "/index/print/id/" + id;
             $("#print_iframe").attr('src', src);
             $("#modal_print .modal-title").html("Request #" + id);
             $(".fa-print").attr("id",id);
         });

// REQUESTS TABLE FORMAT
  var requestTable = $('#requestTable').DataTable(
  {
    "sPaginationType": "full_numbers",
    "paging": true,
    "aaSorting": [[2,'desc'], [3,'desc']],  
    "columns": [
        { width: '3%'},
        { title: 'ID', width: '2%'},
        { title: 'Date', width: '15%'},
        { title: 'Time', width: '10%'}, 
        { title: 'Request Type', width: '10%'},
        { title: 'Client', width: '15%'},
        { title: 'Place', width: '15%'},
        { title: 'Status', width: '10%'},
        { title: 'Timer', width: '10%'},
        { title: 'Actions', width: '10%'}
    ],
    // Group Assistance's requests then by Client then by Place and then by Request Type
    "rowsGroup": [5, 6, 4],
    // Apply search function
    "fnPreDrawCallback": function( oSettings ) {
        $('#requestTable').DataTable().columns(2).search($('.table_date_picker').val());
      }
    }
  );

// ASSISTANCE REQUESTS TABLE FORMAT
  var assistanceTable = $('#assistanceTable').DataTable(
  {
    "sPaginationType": "full_numbers",
    "paging": true,
    "aaSorting": [[2,'desc'], [3,'desc']],  
    "columns": [
        { width: '3%'},
        { title: 'ID', width: '2%'},
        { title: 'Date', width: '15%'},
        { title: 'Time', width: '10%'}, 
        { title: 'Request Type', width: '10%'},
        { title: 'Client', width: '15%'},
        { title: 'Place', width: '15%'},
        { title: 'Status', width: '10%'},
        { title: 'Timer', width: '10%'},
        { title: 'Actions', width: '10%'}
    ],
    // Group Assistance's requests then by Client then by Place and then by Request Type
    "rowsGroup": [5, 6, 4],
    // Apply search function
    "fnPreDrawCallback": function( oSettings ) {
        $('#assistanceTable').DataTable().columns(2).search($('.table_date_picker').val());
      }
    }
  );
});