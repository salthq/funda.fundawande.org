jQuery(document).ready( function($) {

    $('.review-activity').on('click', function(e) {
        $(this).parent().prevAll('.feedback-indicator').append('[Processing]');
    });
    $('#data-table').DataTable( {
        responsive: true,
        "order": [[3, "asc" ]],
        "columns": [
            { "width": "10%" },
            { "width": "20%" },
            { "width": "20%" },
            { "width": "10%" },
            { "width": "10%" },
            { "width": "10%" },
            { "width": "10%" },
            { "width": "10%" },
            { "width": "10%" }
        ],
        // scrollY:        '200px',
        scrollX:        false,
        autoWidth : true,
        // scrollCollapse: true,
        paging:         true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        initComplete: function () {
            this.api().columns(1).every( function () {
                var column = this;
                var select = $('<br><select><option value="">All assessments</option></select>')
                    .appendTo( $(column.header()) )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );

            } );

            this.api().columns(2).every( function () {
                var column = this;
                var select = $('<br><select><option value="">All modules</option></select>')
                    .appendTo( $(column.header()) )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );


            } );

            this.api().columns(4).every( function () {
                var column = this;
                var select = $('<br><select><option value="">Feedback</option></select>')
                    .appendTo( $(column.header()) )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );


            } );

            this.api().columns(5).every( function () {
                var column = this;
                var select = $('<br><select><option value="">Status</option></select>')
                    .appendTo( $(column.header()) )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );


            } );
            this.api().columns(6).every( function () {
                var column = this;
                var select = $('<br><select><option value="">Grading type</option></select>')
                    .appendTo( $(column.header()) )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );


            } );

        }


    } );

});
