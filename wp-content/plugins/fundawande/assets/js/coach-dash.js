jQuery(document).ready( function($) {

    $('.review-activity').on('click', function(e) {
        $(this).parent().prevAll('.feedback-indicator').append('[Processing]');
    });

    $('.coach-filter').on('change', function(e) {
        var course = $('#courseSelect').val();
        var coach = $('#coachSelect').val();
        var unit = $('#moduleSelect').val();
        var user = $('#userSelect').val();

        /*
         * queryParameters -> handles the query string parameters
         * queryString -> the query string without the fist '?' character
         * re -> the regular expression
         * m -> holds the string matching the regular expression
         */
        var queryParameters = {}, queryString = location.search.substring(1),
            re = /([^&=]+)=([^&]*)/g, m;

        // Creates a map with the query string parameters
        while (m = re.exec(queryString)) {
            queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
        }

        // Add new parameters or update existing ones
        queryParameters.fw_course = course;
        queryParameters.coach = coach;
        queryParameters.module = unit;
        queryParameters.user = user;


        /*
         * Replace the query portion of the URL.
         * jQuery.param() -> create a serialized representation of an array or
         *     object, suitable for use in a URL query string or Ajax request.
         */
        location.search = $.param(queryParameters); // Causes page to reload
    });

    $('#data-table').DataTable( {

        "order": [[3, "asc" ]],
        "columns": [
            { "width": "10%" },
            { "width": "20%" },
            { "width": "20%" },
            { "width": "10%" },
            { "width": "10%" },
            { "width": "10%" },
            { "width": "10%" },
            { "width": "10%" }
        ],
        // scrollY:        '200px',
        scrollX:        false,
        // autoWidth : false,
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
                var select = $('<br><select><option value="">Response</option></select>')
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
