    <div id="productsDetails" class="well" >
		<div class="row">
			<div class="offset1 pull-left">
			<select class="input-large" id="products" data-bind="'value': selectedProductId,'event': {'change': updateProductPrice}">
				<option value="">Please Select a Product</option>
				@foreach ( $products as $product )
					<option data-price="{{$product->price}}" data-type="{{$product->is_machine_hire}}" value="{{$product->id}}" >{{$product->name}}</option>
				@endforeach
			</select>                                          
			</div>
			<div class="pull-right">Qty: <select class="input-mini" id="qty" data-bind="options: $root.qtys, value: selectedQty" ></select></div>
		</div>
		<div class="row">
			<div class="offset1 span3">Product Price: </div>
			<div class="span8">{{ Form::text('price', '', array('class'=>'input-small', 'id'=>"price", 'placeholder'=>"Price", 'data-bind'=>"'value': selectedPrice")) }}</div>		
		</div>
		<div class="row">
			<div class="offset1 span3">Hire Date: </div>
			<div class="span8">{{ Form::text('hire_date', '', array('class'=>'hire_date input-small', 'id'=>"hire_date", 'placeholder'=>"Hire Date", 'data-bind'=>"'value': selectedHireDate")) }} * <small>for machine hire only</small></div>		
		</div>
		<div class="row">
			<div class="offset1 pull-left">
			{{ Form::text('description', '', array('class'=>'input-xlarge', 'id'=>"description", 'placeholder'=>"Extra product description", 'data-bind'=>"'value': selectedDescription")) }}
			</div>
			<div class="pull-right">{{ Form::button('Add', array('class'=>'btn btn-small btn-info', 'id'=>"add", 'data-bind'=>"'click': updateSelectedInstance ")) }}</div>
		</div>
	</div>

