$(document).ready(function() {
    //getting the table that we will be working with
    var table = $('#example').DataTable( {
        "scrollY": "200px",
        "paging": false
    } );

    $('a.toggle-vis').on( 'click', function (e) {
        e.preventDefault();

        // Get the column by number
        var column = table.column( $(this).attr('data-column') );

        // Toggle the visibility
        column.visible( ! column.visible() );
    } );
} );


$(document).ready(function() {
    $('a.toggle-vis').on('click', function(e) {
        var column = $(this).attr('data-column');
        $('#example th:nth-child(' + column + '), #example td:nth-child(' + column + ')').toggle();
        $(this).toggleClass('btn-success btn-default')
    });
});