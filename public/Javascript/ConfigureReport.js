	var dataTable;
  var ConfigVars;
  var sublevels = 0;
  var ColumnChart;
	var folded;
  $(document).ready(function() {
		$.ajax({
    type: 'POST',
    url: 'getdata',
    data: {
				'_token': $('#token').val(),
        'id': ReportId
			},
	    success: function(ReportConfigVars){
	         ConfigVars = JSON.parse(JSON.stringify(ReportConfigVars));
					 console.log(JSON.stringify(ConfigVars));
					 CreateTableHeader(ConfigVars.columns);
					 dataTable = $('#reporttable').DataTable({
				       destroy:true,
				       data:ConfigVars.data,
				       columnDefs:ConfigVars.columns,
				       paging:false,
				       order:[[0,'asc']],
						   drawCallback:function(settings){
				         if(ConfigVars.grouping)
				           ShowGrouping(this);
								 if(ConfigVars.aggregates)
		 								sumGroups(this);
								if(ConfigVars.headers)
										PopulateHeaders();
				       }
						});
	    },
	    error: function(data) {
					 var errors = $.parseJSON(JSON.stringify(data.responseText));
					 $("#error").html(errors);
					 console.log(errors);
	    }
		});
		$(document).on('click', '#btnFilter', function (e) {
			validationSuccess = true;
			jsonFilters = [];
			$('.filters').each(function(){
				if($(this).children('input:text').val()){
					filter = {};
					filterValue = $(this).children('input:text').val();
					filter['selector'] = $(this).children('input:text').attr('id');
					filter['type'] = $(this).children('input:hidden#hdnType').val();
					if(filter['type'] == 'numeric' || filter['type'] == 'date'){
						if(filterValue.charAt(0) != ""){
							filter['operator'] = $.trim(filterValue.charAt(0));
							filterValue = filterValue.substring(1,filterValue.length);
						}
					}
					if(filter['type'] == 'string'){
						filter['operator'] = ' like ';
					}
					filter['value'] = filterValue;
					jsonFilters.push(filter);
				}
			});
			$.ajax({
				type:'POST',
				url:'processfilters',
				data:{
					'_token':$('#token').val(),
					'id':ReportId,
					'JsnFilters': jsonFilters
				},
				success:function(Data){
					Data = JSON.parse(JSON.stringify(Data));
					dataTable.clear();
					dataTable.rows.add(Data.data);
					dataTable.draw();
				},
				error:function(data){
					var errors = $.parseJSON(JSON.stringify(data.responseText));
					$("#openFilterOptions").html(errors);
					console.log(errors);
				}
			});
		});
		$(document).on('click', '#btnClearFilter', function (e) {
			$.ajax({
				type:'POST',
				url:'clearfilters',
				data:{
					'_token':$('#token').val(),
					'id':ReportId
				},
				success:function(Data){
					Data = JSON.parse(JSON.stringify(Data));
					dataTable.clear();
					dataTable.rows.add(Data.data);
					dataTable.draw();
				},
				error:function(data){
					var errors = $.parseJSON(JSON.stringify(data.responseText));
					$("#openFilterOptions").html(errors);
					console.log(errors);
				}
			});
		});
		$( "#openFilterOptions" ).on( "click", function(e) {
			if($("#showFilters").is(':hidden')){
				$.ajax({
					type: 'POST',
					url: 'getfilters',
					data: {
							'_token': $('#token').val(),
							'id': ReportId
						},
					success:function(Filters){
						$("#showFilters").html(Filters);
						e.preventDefault();
						$("#showFilters").toggle('slow');
					},
					error:function(data){
						var errors = $.parseJSON(JSON.stringify(data.responseText));
						$("#openFilterOptions").html(errors);
						console.log(errors);
					}
				});
			}
			else{
				$("#showFilters").toggle('slow');
			}
	});
	function PopulateHeaders(){
		console.log(ConfigVars.headers.length);
		if(ConfigVars.headers && ConfigVars.headers.length > 0){
			for(var i=0;i<ConfigVars.headers.length;i++){
				if(i%2==0)
					$("#header-left").html("<label>" + ConfigVars.headers[i].name +
																": </label><label>" + ConfigVars.headers[i].value + "</label>");
				else(i%2==1)
					$("#header-right").html("<label>" + ConfigVars.headers[i].name +
																": </label><label>" + ConfigVars.headers[i].value + "</label>");
			}
		}
	}
	function ShowGrouping(table){
    var api = table.api();
    var rows = api.rows( {page:'current'} ).nodes();
    var last=null;
		for(var i=0;i<ConfigVars.grouping.length;i++){
			var ColIndex = api.column(ConfigVars.grouping[i].dispname + ":name").index();
			api.columns(ColIndex).visible(false);
			var level = ConfigVars.grouping[i].level;
			api.column(ColIndex, {page:'current'} ).data().each( function ( group, i ) {
	      if ( last !== group ) {
						$(rows).eq( i ).before(
	                  '<tr class="group'+level+'"><td><p style="text-indent:'+(level * 10)+'px">'
										+group+'</p></td></tr>'
	              );
	        last = group;
	      }
	    });
		}
		$('tr.group' + (ConfigVars.maxgrouplevel).toString()).each(function(index, elem) {
			var rowsGroup= $(this).nextUntil('.group' + ConfigVars.maxgrouplevel);
			$(rowsGroup).each(function(index){
				$("td:eq(0)",this).css("text-indent",(ConfigVars.maxgrouplevel + 1)*10);
			});
		});
  }
	function CreateTableHeader(Columns){
  var TblData = document.getElementById('reporthead');
  var TableHeadingRow = document.createElement('tr');
  Columns = JSON.stringify(Columns);
  Columns = JSON.parse(Columns);
	if(typeof Columns.length !== "undefined"){
		for (var i = 0 ; i < Columns.length ; i++) {
	      var TableHeader = document.createElement('th');
	      TableHeader.innerHTML = Columns[i].name;
	      TableHeadingRow.appendChild(TableHeader);
	    }
	}
	else {
		var TableHeader = document.createElement('th');
		TableHeader.innerHTML = Columns.name;
		TableHeadingRow.appendChild(TableHeader);
	}
    TblData.appendChild(TableHeadingRow);
  }
	function sumGroups(table) {
		var ColIndex;
		var Aggregate = Array();
		var api = table.api();
		var ColCount = $('#reporttable').find('thead th').length;
		ConfigVars.aggregates.forEach(function(Col){
			ColIndex = $('#reporttable th:contains("' + Col.dispname + '")').index();
			Aggregate[ColIndex] = 0;
		});
		for(var level=ConfigVars.maxgrouplevel;level>=0;level--){
			if(level == 0){
				$("#reporttable tbody tr.group1").each(function(index) {
					for(var ColIndex in Aggregate){
						Aggregate[ColIndex] = Aggregate[ColIndex] +
																		parseFloat($(this).find('td:nth-child('+ (parseInt(ColIndex) + 1) +')').html());
					}
				});
				$("#reporttable tbody tr").eq( 0 ).before(
								'<tr class="runningtotal"><td>Running Total</td></tr>');
				for(var i=1;i<ColCount;i++){
					if(typeof Aggregate[i] !== "undefined"){
						$("#reporttable tbody tr").eq( 0 ).append('<td class="rpt_summary" style="text-align:right"> '
																 + Aggregate[i].toFixed(2) + '</td>');
					}
					else {
						$("#reporttable tbody tr").eq( 0 ).append('<td></td>');
					}
				}
				for(var ColIndex in Aggregate){
					if(Aggregate.hasOwnProperty(ColIndex))
					Aggregate[ColIndex] = 0;
				}
			}
				$("#reporttable tbody tr.group"+level.toString()).each(function(index) {
					var rowsSum = $(this).nextUntil('.group'+level.toString());
					if(level != ConfigVars.maxgrouplevel){
						rowsSum = $(rowsSum).find("[class^=group]");
					}
					$(rowsSum).each(function(index){
						var row = $(this);
						for(var ColIndex in Aggregate){
							if(Aggregate.hasOwnProperty(ColIndex)){
							Aggregate[ColIndex] = Aggregate[ColIndex] +
																			parseFloat($(row).find('td:nth-child('+ (parseInt(ColIndex) + 1) +')').html());
							}
						}
					});
					for(var i=1;i<ColCount;i++){
						if(typeof Aggregate[i] !== "undefined"){
							$(this).append('<td class="rpt_summary" style="text-align:right"> '
						                       + Aggregate[i].toFixed(2) + '</td>');
						}
						else {
							$(this).append('<td></td>');
						}
					}
					for(var ColIndex in Aggregate){
						if(Aggregate.hasOwnProperty(ColIndex))
						Aggregate[ColIndex] = 0;
					}
				});
		}
	}
});
