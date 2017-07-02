                <div class="well well-small">
					<label><input type="checkbox" name="SendSMS" value="1"  data-bind="checked: booking().SendSMS"/>  <span>Send student SMS</span></label>
					<label><input type="checkbox" name="SendEmail" value="1"  data-bind="checked: booking().SendEmail"/>  <span>Send student Email</span></label>
				</div>
                <h4>Select your Payment Type</h4>
                <div id="cbPaymentType" class="well well-small" >  
                    @if ($group !='agent')
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
                    @else
                         @foreach ($methods as $method)
                        <label>
                                @if ($method->code == 'CC')
                                    @if ($agentData['payment_type'] =='paynow' || $agentData['payment_type'] =='both')
                                        <input type="radio" name="PaymentMethod" value="{{$method->code}}" data-bind="checked: booking().PaymentMethod, click: displayCreditCardForm" class="cbPaymentCredit" /> 
                                        Credit Card (PayWay) [Visa / MasterCard] = <span class="online_total" data-bind="html: booking().OnLineTotal.Price"></span>
                                    @endif
                                @elseif ($method->code == 'LATER')
                                    @if ($agentData['payment_type'] =='paylater' || $agentData['payment_type'] =='both')
                                        <input type="radio" name="PaymentMethod" value="{{$method->code}}" data-bind="checked: booking().PaymentMethod, click: displayOtherPayment" class="cbPaymentOther" /> 
                                       {{$method->name}} = @if ($method->pay_type == 'online')<span class="online_total" data-bind="html: booking().OnLineTotal.Price"></span>@else <span class="offline_total" data-bind="html: booking().OffLineTotal.Price"></span>@endif
                                    @endif
                                @else
                                @endif
                        </label>
                        @endforeach
                    @endif
                </div>
