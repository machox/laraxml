$(document).ready(function() {
        var table1;
        var table2;
        table1 = $('#myTable1').DataTable();
        table2 = $('#myTable2').DataTable();
        $('#detailContent').hide();
        getdata();
        function getdata() {
            $.get(APP_URL + "/home/getemployees", function(data){
            if (typeof table1 !== 'undefined' || table1) {
                table1.destroy();
            }
            if (typeof table2 !== 'undefined' || table2) {
                table2.destroy();
            }
            table1 = $('#myTable1').DataTable({
                aaData: data,
                order: [[ 1, 'asc' ]],
                columnDefs: [
                    { 
                        "render": function ( data, type, row ) {
                            return '<a href="#" class="btn btn-link name-link">'+data+'</a>';
                        },
                        "className": "name-cell",
                        "orderable": false, 
                        "targets": 0 
                    },
                    { "orderable": false, "targets": 2 },
                    { 
                        "render": function ( data, type, row ) {
                            if(data == 1) {
                                return '<a href="#" class="btn btn-success status-link">Masuk</a>';
                            } else if(data == 2) {
                                return '<a href="#" class="btn btn-danger status-link">Cuti</a>';
                            } else {
                                return '<a href="#" class="btn btn-warning status-link">Libur</a>';
                            }
                        },
                        "className": "text-center status-cell",
                        "orderable": false, 
                        "targets": 3 
                    }
                ],
                aoColumns : [
                    {"mData" : "name"},
                    {"mData" : "city"},
                    {"mData" : "department"},
                    {"mData" : "status"}
                ],
                drawCallback: function ( settings ) {
                    var api = this.api();
                    var rows = api.rows( {page:'current'} ).nodes();
                    var last=null;
         
                    api.column(1, {page:'current'} ).data().each( function ( group, i ) {
                        if ( last !== group ) {
                            $(rows).eq( i ).before(
                                '<tr class="group"><td colspan="4">'+group+'</td></tr>'
                            );
         
                            last = group;
                        }
                    } );
                },
                paging: false
            });

            // Order by the grouping
            $('#myTable1 tbody').on( 'click', 'tr.group', function () {
                var currentOrder = table1.order()[0];
                if ( currentOrder[0] === 1  && currentOrder[1] === 'asc' ) {
                    table1.order( [ 1, 'desc' ] ).draw();
                }
                else {
                    table1.order( [ 1, 'asc' ] ).draw();
                }
            } );

            $('#myTable1 tbody').on( 'click', '.status-link', function () {
                var data = table1.row( $(this).parents('tr') ).data();
                showStatus(data);
                return true;
            } );

            $('#myTable1 tbody').on( 'click', '.name-link', function () {
                var data = table1.row( $(this).parents('tr') ).data();
                showDetail(data);
                return true;
            } );

            table2 = $('#myTable2').DataTable({ 
                aaData: data,
                order: [[ 2, 'asc' ]],
                columnDefs: [
                    { 
                        "render": function ( data, type, row ) {
                            return '<a href="#" class="btn btn-link name-link">'+data+'</a>';
                        },
                        "className": "name-cell",
                        "orderable": false, 
                        "targets": 0 
                    },
                    { "orderable": false, "targets": 1 },
                    { 
                        "render": function ( data, type, row ) {
                            if(data == 1) {
                                return '<a href="#" class="btn btn-success status-link">Masuk</a>';
                            } else if(data == 2) {
                                return '<a href="#" class="btn btn-danger status-link">Cuti</a>';
                            } else {
                                return '<a href="#" class="btn btn-warning status-link">Libur</a>';
                            }
                        },
                        "className": "text-center status-cell",
                        "orderable": false, 
                        "targets": 3 
                    }
                ],
                aoColumns : [
                    {"mData" : "name"},
                    {"mData" : "city"},
                    {"mData" : "department"},
                    {"mData" : "status"}
                ],
                drawCallback: function ( settings ) {
                    var api = this.api();
                    var rows = api.rows( {page:'current'} ).nodes();
                    var last=null;
         
                    api.column(2, {page:'current'} ).data().each( function ( group, i ) {
                        if ( last !== group ) {
                            $(rows).eq( i ).before(
                                '<tr class="group"><td colspan="4">'+group+'</td></tr>'
                            );
         
                            last = group;
                        }
                    } );
                },
                paging: false
            });

            // Order by the grouping
            $('#myTable2 tbody').on( 'click', 'tr.group', function () {
                var currentOrder = table2.order()[0];
                if ( currentOrder[0] === 2  && currentOrder[1] === 'asc' ) {
                    table2.order( [ 2, 'desc' ] ).draw();
                }
                else {
                    table2.order( [ 2, 'asc' ] ).draw();
                }
            } );

            $('#myTable2 tbody').on( 'click', '.status-link', function () {
                var data = table1.row( $(this).parents('tr') ).data();
                showStatus(data);
                return true;
            } );

            $('#myTable2 tbody').on( 'click', '.name-link', function () {
                var data = table1.row( $(this).parents('tr') ).data();
                showDetail(data);
                return true;
            } );

        }, 'json');
    }


    var $detailModal = $('#detailEmployee').modal({
      backdrop: true,
      show: false,
      keyboard: false
    });

    var showDetail = function (data) {
        var status;
        if(data.status == 1) {
            status = '<span class="label label-success">Masuk</span>';
        } else if(data.status == 2) {
            status = '<span class="label label-danger">Cuti</span>';
        } else {
            status = '<span class="label label-default">Libur</span>';
        }
        var avatar = data.avatar;
        if(!avatar) avatar = "https://x1.xingassets.com/assets/frontend_minified/img/users/nobody_m.original.jpg";
      $detailModal
        .find('#nameE').text(data.name).end()
        .find('#avatarE').attr("src", avatar).end()
        .find('#idE').html(data.id).end()
        .find('#emailE').html(data.email).end()
        .find('#positionE').html(data.position).end()
        .find('#departmentE').html(data.department).end()
        .find('#cityE').html(data.city).end()
        .find('#statusE').html(status).end()
        .find('#detailContent').show().end()
        .find('.js-loading').addClass('hidden').end()
        .modal('show');
    };    

    var $statusModal = $('#statusEmployee').modal({
      backdrop: true,
      show: false,
      keyboard: false
    });

    var showStatus = function (data) {
      $statusModal
        .find('#statusId').val(data.status).end()
        .find('#HiddenId').val(data.id).end()
        .modal('show');
    };

    $('#formStatus').submit(function(event) {
        event.preventDefault();
        var formData = {
            'id'      : $('#HiddenId').val(),
            'status'  : $('#statusId').val()
        };
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN':  $('input[name="_token"]').val()
             }
        });
        $( document ).ajaxStart(function(){
            $('#message-error').hide();
            $('#message-success').hide();
            $('#message-info').fadeIn();
        });
        $.ajax({
            type        : 'POST',
            url         : APP_URL + '/home/changestatus',
            data        : formData,
            dataType    : 'json',
            encode      : true
        })
        .done(function(data) {
            if(data.status) {
                $('#message-info').hide();
                $('#message-error').hide();
                $('#message-success').fadeIn();
                window.setTimeout(getdata, 500);
                $statusModal.modal('toggle'); 
            } else {
                $('#message-success').hide();
                $('#message-info').hide();
                $('#error-m').html(data.message);
                $('#message-error').fadeIn();
            }
        })
        .fail(function() { 
            $('#message-success').hide();
            $('#message-info').hide();
            $('#error-m').html('Error !');
            $('#message-error').fadeIn();
        });
    });

} );