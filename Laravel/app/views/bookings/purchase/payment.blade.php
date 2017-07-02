					@foreach ($methods as $method)
                    <label>
						@if ($method->code == 'CC')
                        <input type="radio" name="PaymentMethod" value="{{$method->code}}" data-bind="checked: purchase().PaymentMethod, click: displayCreditCardForm" class="cbPaymentCredit" /> 
						Credit Card (PayWay) [Visa / MasterCard] = <span class="online_total" data-bind="html: purchase().Total"></span>
						@else
                        <input type="radio" name="PaymentMethod" value="{{$method->code}}" data-bind="checked: purchase().PaymentMethod, click: displayOtherPayment" class="cbPaymentOther" /> 
						{{$method->name}} = <span class="online_total" data-bind="html: purchase().Total"></span>
						@endif
                    </label>
					@endforeach
