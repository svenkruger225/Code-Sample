var selected_record = null;
var TableManaged = function () {
    return {
        //main function to initiate the module
        init: function () {
            
            if (!jQuery().dataTable) {
                return;
            }
           // begin second table
            $('#sample_2').dataTable({
                "aLengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "All"] // change per page values here
                ],
                // set the initial value
                "iDisplayLength": 20,
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "_MENU_ records",
                    "oPaginate": {
                        "sPrevious": "Prev",
                        "sNext": "Next"
                    }
                },
                "aoColumnDefs": [{
                        'bSortable': false,
                        'aTargets': [0]
                    }
                ]
            });

            jQuery('#sample_2 .group-checkable').change(function () {
                var set = jQuery(this).attr("data-set");
                var checked = jQuery(this).is(":checked");
                jQuery(set).each(function () {
                    if (checked) {
                        $(this).attr("checked", true);
                    } else {
                        $(this).attr("checked", false);
                    }
                });
                jQuery.uniform.update(set);
            });
            jQuery('#sample_2_wrapper .dataTables_filter input').addClass("form-control input-small"); // modify table search input
            jQuery('#sample_2_wrapper .dataTables_length select').addClass("form-control input-xsmall"); // modify table per page dropdown
            //To-do fix issue in select 2 
            //jQuery('#sample_2_wrapper.dataTables_length select').select2(); // initialize select2 dropdown

           
            //generic function for edit and delete
            $("#edit, #delete , #reset").click(function(e)
            {
                if(selected_record != null)
                {
                    var action_link = e.target.dataset['action_link']+selected_record;
                    window.location = action_link;
                }
                else
                {
                    alert("Please select one record");
                }
            });
            
            $('body').on('click','.checkboxes', function (e) {
                if (e.target.checked) {
                    selected_record = e.target.value;
                } 
                else if (!e.target.checked && selected_record == e.target.value) {
                    selected_record = null;
                }

            });            

        }

    };
    

}();