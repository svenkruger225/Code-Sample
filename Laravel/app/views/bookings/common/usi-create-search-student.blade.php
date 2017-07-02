				<div class="modal hide search-results span12 pull-right" id="customers-search-results">
					<div class="modal-body row-fluid" data-bind="visible: search_results().length > 0">
					<div class="span11 table-responsive">
						<table class="table table-condensed table-striped table-hover">
							<thead><tr class="row-fluid">
							<th class="span1">Id</th><th class="span5">Name</th>
							<th class="span5">email <a class="btn btn-mini btn-danger pull-right" data-bind="click: closeSearchResultsModal">x</a></th>
							</tr></thead>
							<tbody data-bind="foreach: search_results">
							<tr class="row-fluid" data-bind="click: selectCustomer" style="cursor: pointer;">
								<td class="td-wrap" data-bind="text: id"></td>
								<td class="td-wrap"><span data-bind="text: first_name"></span> <span data-bind="text: last_name"></span></td>
								<td class="td-wrap" data-bind="text: email"></td>
							</tr>    
							</tbody>
						</table>
					</div>
					</div>
				</div>