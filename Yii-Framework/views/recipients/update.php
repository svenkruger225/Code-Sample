<?php echo $this->renderPartial('_form', array('model'=>$model, 'countries' => $countries, 'banks' => $banks, 'cities' => $cities)); ?>

<script language="javascript">
    var city  = '<?php echo $model->city; ?>';
    var bank  = '<?php echo $model->bank; ?>';
    $(document).ready(function(){
        $.ajax({
                  type: 'POST',
                  url: '<?php echo CController::createUrl('recipients/choice') ?>',
                  data: {country : $("#Recipients_country").val(),payment_method:$("#Recipients_payment_method").val()},
                  dataType: 'json',
                  success: function(data){
                                $("#city_list").hide();
                                $("#bank_list").hide();
                                $("#payment_error").hide();
                                // clear html to avoid appending
                                $("#Recipients_city").html("");
                                $("#Recipients_bank").html("");
                                if(data.city)
                                {
                                    $.each(data.city,function(key,val){
                                        if(key == city)
                                        {
                                            var option = "<option selected='selected' value="+key+">"+val+"</option>";
                                        }
                                        else
                                        {
                                            var option = "<option value="+key+">"+val+"</option>";
                                        }
                                         $("#Recipients_city").append(option);
                                    });
                                    $("#city_list").show();
                                }
                                else if(data.bank)
                                {
                                    $.each(data.bank,function(key,val){
                                        if(key == bank)
                                        {
                                            var option = "<option selected='selected' value="+key+">"+val+"</option>";
                                        }
                                        else
                                        {
                                            var option = "<option value="+key+">"+val+"</option>";
                                        }
                                         $("#Recipients_bank").append(option);
                                    });
                                    $("#bank_list").show();
                                }
                                else
                                {
                                    $("#payment_error").show();
                                }
                  }
                });

            });
</script>