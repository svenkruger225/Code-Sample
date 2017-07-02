					@foreach ($methods as $method)
                    <label>
						@if ($method->code == 'CC')
                        <input type="radio" name="PaymentMethod" value="{{$method->code}}" data-bind="checked: booking().PaymentMethod, click: displayCreditCardForm" class="cbPaymentCredit" /> 
						Credit Card (PayWay) [Visa / MasterCard] = <span class="online_total" data-bind="html: booking().OnLineTotal.Price"></span>
						@else
                        <input type="radio" name="PaymentMethod" value="{{$method->code}}" data-bind="checked: booking().PaymentMethod, click: displayOtherPayment" class="cbPaymentOther" /> 
						{{$method->name}} = @if ($method->pay_type == 'online')<span class="online_total" data-bind="html: booking().OnLineTotal.Price"></span>@else <span class="offline_total" data-bind="html: booking().OffLineTotal.Price"></span>@endif
						@endif
                    </label>
					@endforeach
