/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function() {  
    $(".js-example-basic-multiple").select2();
    $("#status").prop("checked", true); 
    $('input[name=flag]').attr('checked',true);
    $('#startDate')
        .datepicker({
            format: 'yyyy/mm/dd'
        });
    $('#endDate')
        .datepicker({
            format: 'yyyy/mm/dd'
    });    
    $('input[name=endDate]').change(function() {
        var endDate=$('input[name=endDate]').val();
        var startDate=$('input[name=startDate]').val();
        if(startDate > endDate){
            $('input[name=startDate]').val(endDate);
        }
    });
   
    $('input[name=rate]').parent().parent().hide();
    $('input[name=budget]').parent().parent().hide();
    
    $("input:radio[name=status]").click(function() {
        var value = $(this).val();
         if (value==1){
              
                $('input[name=rate]').val(0);
                $('input[name=budget]').val(0);
                $('input[name=rate]').parent().parent().hide();
                $('input[name=budget]').parent().parent().hide();
                $('input[name=startDate]').parent().parent().show();
                $('input[name=endDate]').parent().parent().show();

        }
        else if (value == 2){
                
            $('input[name=startDate]').val(" ");
            $('input[name=endDate]').val(" ");
            $('input[name=startDate]').parent().parent().hide();
            $('input[name=endDate]').parent().parent().hide();
            $('input[name=rate]').parent().parent().show();
            $('input[name=budget]').parent().parent().show();
        }
    });
    
    $("form").submit(function(event){
    var value = $("input[type='radio']:checked").val();
   
     if (value==1){
        if($('input[name=startDate]').val() == "" || $('input[name=startDate]').val() == " "){
            alert("Enter Start Date");
           event.preventDefault();
        }
        else if($('input[name=endDate]').val() == "" || $('input[name=endDate]').val() == " "){
             alert("Enter End Date");
            event.preventDefault();
        }
       
        
    }
    else if (value == 2){
          if($('input[name=rate]').val() == "" || $('input[name=rate]').val() == 0){
            alert("Enter Rate");
            event.preventDefault();
        }
        else if($('input[name=budget]').val() == "" || $('input[name=budget]').val() == 0){
             alert("Enter Budget");
             event.preventDefault();
        }
    }
    });
  
    
});

