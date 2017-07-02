						<div class="row-fluid form-line">
							<div class="span12">Select Document Type: </div>
						</div>
						<div class="container-fluid">
							<ul class="nav nav-pills nav-stacked span2">
								<li role="presentation" class="active"><a data-toggle="pill" href="#birth_certificate" data-bind="click: $root.setDocumentType.bind($data, 'BirthCertificate')">Birth Certificate (Australian)</a></li>
								<li role="presentation"><a data-toggle="pill" href="#passport" data-bind="click: $root.setDocumentType.bind($data, 'Passport')">Passport (Australian)</a></li>
								<li role="presentation"><a data-toggle="pill" href="#drivers_licence" data-bind="click: $root.setDocumentType.bind($data, 'DriversLicence')">Drivers Licence</a></li>
								<li role="presentation"><a data-toggle="pill" href="#visa-document" data-bind="click: $root.setDocumentType.bind($data, 'VisaDocument')">Visa (with Non-Australian Passport)</a></li>
								<li role="presentation"><a data-toggle="pill" href="#medicare-card" data-bind="click: $root.setDocumentType.bind($data, 'MedicareDocument')">Medicare Card</a></li>
								<li role="presentation"><a data-toggle="pill" href="#immi-card" data-bind="click: $root.setDocumentType.bind($data, 'ImmiCardDocument')">Immigration Card</a></li>
								<li role="presentation"><a data-toggle="pill" href="#citizenship-card" data-bind="click: $root.setDocumentType.bind($data, 'CitizenshipCertificate')">Citizenship Certificate</a></li>
							</ul>
							<div class="tab-content span10">
								<div id="birth_certificate" class="tab-pane fade in active">
									<div class="row-fluid">
										<div class="span3">Certificate Number: </div><div class="span9"><input type="text" id="CertificateNumber" name="CertificateNumber" class="input-medium" data-bind="value: CertificateNumber" /></div>
									</div>		
									<div class="row-fluid">
										<div class="span3">Date Printed: </div><div class="span9"><input type="text" id="DatePrinted" name="DatePrinted" class="input-medium" data-bind="datepicker: DatePrinted, datepickerOptions: $root.datepickerOptions" /></div>
									</div>
									<div class="row-fluid">
										<div class="span3">Registration Date: </div><div class="span9"><input type="text" id="RegistrationDate" name="RegistrationDate" class="input-medium" data-bind="datepicker: RegistrationDate, datepickerOptions: $root.datepickerOptions" /></div>
									</div>
									<div class="row-fluid">
										<div class="span3">Registration Number: </div><div class="span9"><input type="text" id="RegistrationNumber" name="RegistrationNumber" class="input-medium" data-bind="value: RegistrationNumber" /></div>
									</div>
									<div class="row-fluid">
										<div class="span3">Registration State: </div><div class="span9">{{ Form::select('RegistrationState', $states, '', array('id'=>'RegistrationState', 'class'=>'input-medium', 'data-bind'=>"value: RegistrationState")) }}</div>
									</div>		
									<div class="row-fluid">
										<div class="span3">Registration Year: </div><div class="span9"><input type="text" id="RegistrationYear" name="RegistrationYear" class="input-small" data-bind="value: RegistrationYear" /></div>
									</div>
								</div>
								<div id="passport" class="tab-pane fade">
									<div class="row-fluid">
										<div class="span3">Document Number: </div><div class="span9"><input type="text" id="DocumentNumber" name="DocumentNumber" class="input-medium" data-bind="value: DocumentNumber" /></div>
									</div>
								</div>
								<div id="drivers_licence" class="tab-pane fade">
									<div class="row-fluid">
										<div class="span3">Licence Number: </div><div class="span9"><input type="text" id="LicenceNumber" name="LicenceNumber" class="input-medium" data-bind="value: LicenceNumber" /></div>
									</div>
									<div class="row-fluid">
										<div class="span3">State: </div><div class="span9">{{ Form::select('LicenceState', $states, '', array('id'=>'LicenceState', 'class'=>'input-medium', 'data-bind'=>"value: LicenceState")) }}</div>
									</div>
								</div>			
								
								<div id="medicare-card" class="tab-pane fade">
									<div class="row-fluid">
										<div class="span3">Name: </div><div class="span9"><input type="text" id="NameLine1" name="NameLine1" class="input-medium" data-bind="value: NameLine1" /></div>
									</div>	
									<!--	
									<div class="row-fluid">
										<div class="span3">Name Line 2: </div><div class="span9"><input type="text" id="NameLine2" name="NameLine2" class="input-medium" data-bind="value: NameLine2" /></div>
									</div>
									<div class="row-fluid">
										<div class="span3">Name Line 3: </div><div class="span9"><input type="text" id="NameLine3" name="NameLine3" class="input-medium" data-bind="value: NameLine3" /></div>
									</div>
									<div class="row-fluid">
										<div class="span3">Name Line 4: </div><div class="span9"><input type="text" id="NameLine4" name="NameLine4" class="input-medium" data-bind="value: NameLine4" /></div>
									</div>
									-->
									<div class="row-fluid">
										<div class="span3">Card Colour: </div><div class="span9">{{ Form::select('CardColour', array('Green'=>'Green','Blue'=>'Blue','Yellow'=>'Yellow'), '', array('id'=>'CardColour', 'class'=>'input-medium', 'data-bind'=>"value: CardColour")) }}</div>
									</div>		
									<div class="row-fluid">
										<div class="span3">Expiry Date: (Valid To)</div>
										<div class="span9">
										<!-- ko if: CardColour() != 'Green' -->
										{{ Form::select('ExpiryDay', array(''=>'Day','01'=>'01','02'=>'02','03'=>'03','04'=>'04','05'=>'05','06'=>'06','07'=>'07','08'=>'08','09'=>'09','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28','29'=>'29','30'=>'30','31'=>'31'), '', array('id'=>'ExpiryDay', 'class'=>'input-mini', 'data-bind'=>"value: ExpiryDay")) }}
										<!-- /ko -->
										{{ Form::select('ExpiryMonth', array(''=>'Month','01'=>'01','02'=>'02','03'=>'03','04'=>'04','05'=>'05','06'=>'06','07'=>'07','08'=>'08','09'=>'09','10'=>'10','11'=>'11','12'=>'12'), '', array('id'=>'ExpiryMonth', 'class'=>'input-small', 'data-bind'=>"value: ExpiryMonth")) }}
										{{ Form::select('ExpiryYear', array(''=>'Year','2015'=>'2015','2016'=>'2016','2017'=>'2017','2018'=>'2018','2019'=>'2019','2020'=>'2020','2021'=>'2021','2022'=>'2022'), '', array('id'=>'ExpiryYear', 'class'=>'input-small', 'data-bind'=>"value: ExpiryYear")) }}
										</div>
									</div>
									<div class="row-fluid">
										<div class="span3">Individual Ref Number: </div><div class="span9">{{ Form::select('IndividualRefNumber', array(''=>'Select','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9'), '', array('id'=>'IndividualRefNumber', 'class'=>'input-medium', 'data-bind'=>"value: IndividualRefNumber")) }}</div>
									</div>
									<div class="row-fluid">
										<div class="span3">Medicare Card Number: </div><div class="span9"><input type="text" id="MedicareCardNumber" name="MedicareCardNumber" class="input-medium" data-bind="value: MedicareCardNumber" /></div>
									</div>
								</div>
								
								<div id="immi-card" class="tab-pane fade">
									<div class="row-fluid">
										<div class="span3">Immi Card Number: </div><div class="span9"><input type="text" id="ImmiCardNumber" name="ImmiCardNumber" class="input-medium" data-bind="value: ImmiCardNumber" /></div>
									</div>
								</div>
								
								<div id="citizenship-card" class="tab-pane fade">
									<div class="row-fluid">
										<div class="span3">Acquisition Date: </div><div class="span9"><input type="text" id="AcquisitionDate" name="AcquisitionDate" class="input-medium" data-bind="datepicker: AcquisitionDate, datepickerOptions: $root.datepickerOptions" /></div>
									</div>
									<div class="row-fluid">
										<div class="span3">Stock Number: </div><div class="span9"><input type="text" id="StockNumber" name="StockNumber" class="input-medium" data-bind="value: StockNumber" /></div>
									</div>
								</div>
								
								<div id="visa-document" class="tab-pane fade">
									<div class="row-fluid">
										<div class="span3">Passport Number: </div><div class="span9"><input type="text" id="PassportNumber" name="PassportNumber" class="input-medium" data-bind="value: PassportNumber" /></div>
									</div>
									<div class="row-fluid">
										<div class="span3">Country Of Issue: </div><div class="span9">{{ Form::select('CountryOfIssue', $usi_visa_issue_countries, '', array('class'=>'input-large', 'data-bind'=>"value: CountryOfIssue")) }}</div>
									</div>		
								</div>
								
							</div><!-- tab content -->
						</div>
