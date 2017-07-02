		<div class="modal hide search-suburb-results span5 pull-right" id="suburbs-search-results">
			<div class="modal-body row-fluid" data-bind="visible: $root.suburbs().length > 0">
				<div class="span11 table-responsive">
					<table class="table table-condensed table-striped table-hover">
						<thead><tr class="row-fluid">
						<th class="span5">Suburb</th>
						<th class="span2">State</th>
						<th class="span2">PostCode <a class="btn btn-mini btn-danger pull-right" data-bind="click: $root.closeSuburbResultsModal">x</a></th>
						</tr></thead>
						<tbody data-bind="foreach: $root.suburbs">
						<tr class="row-fluid" data-bind="click: $root.processSelectSuburb" style="cursor: pointer;">
							<td class="td-wrap" data-bind="text: name"></td>
							<td class="td-wrap" data-bind="text: state.abbreviation"></td>
							<td class="td-wrap" data-bind="text: postcode"></td>
						</tr>    
						</tbody>
					</table>
				</div>
			</div>
		</div>
