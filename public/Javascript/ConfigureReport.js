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
				         if(ConfigVars.grouping && ConfigVars.grouping.length>0)
				           ShowGrouping(this);
								 if(ConfigVars.aggregates &&ConfigVars.aggregates.length>0)
		 								sumGroups(this);
								 if(ConfigVars.headers)
										PopulateHeaders();
									FormatColumns();
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
					switch(filter['type']){
						case "numeric":
							if(filterValue.charAt(0) != ""){
								filter['operator'] = $.trim(filterValue.charAt(0));
								filterValue = filterValue.substring(1,filterValue.length);
							}
							else{
								filter['operator'] = "=";
								filterValue = filterValue.substring(1,filterValue.length);
							}
							break;
						case "string":
							filter['operator'] = ' like ';
							break;
						case "date":
							filter['operator'] = ' between ';
							filterValue = filterValue.split("-");
							break;
						default:
							break;
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
					dataTable = $('#reporttable').DataTable();
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
					$("#showFilters").toggle('slow');

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

		if(ConfigVars.headers && ConfigVars.headers.length > 0){
			for(var i=0;i<ConfigVars.headers.length;i++){
				if(i%2==0)
					$("#header-left").html("<h4>" + ConfigVars.headers[i].name +
																": </h4><h4>" + ConfigVars.headers[i].value + "</h4>");
				else(i%2==1)
					$("#header-right").html("<h4>" + ConfigVars.headers[i].name +
																": </h4><h4>" + ConfigVars.headers[i].value + "</h4>");
			}
		}
	}
	function ShowGrouping(table){
    var api = table.api();
    var rows = api.rows( {page:'current'} ).nodes();
    var last=null;
		var GroupName = "";
		var DispName = null;
		var DispNameCnt = 0;
		for(var i=0;i<ConfigVars.grouping.length;i++){
			DispName = ConfigVars.grouping[i].dispname.split(",");
			var ColIndex = Array();
			DispNameCnt = 0;
			if(DispName.length > 1){
				for(DispNameCnt = 0; DispNameCnt<DispName.length;DispNameCnt++){
					ColIndex[DispNameCnt] = api.column(DispName[DispNameCnt] + ":name").index();
					api.columns(ColIndex[DispNameCnt]).visible(false);
				}
			}
			else {
				ColIndex[DispNameCnt] = api.column(ConfigVars.grouping[i].dispname + ":name").index();
				api.columns(ColIndex).visible(false);
			}
			var level = ConfigVars.grouping[i].level;
			api.column(ColIndex[0], {page:'current'} ).data().each( function ( group, i ) {
	      if ( last !== group ) {
					GroupName = "";
					if(ColIndex.length > 1){
						ColIndex.forEach(function(ind){
							GroupName = GroupName + " " + api.column(ind,{page:'current'}).data()[i];
						});
						$(rows).eq( i ).before(
	                  '<tr class="group'+level+'"><td colspan="2"><p style="font-weight:700; text-indent:'+(level * 10)+'px">'
										+GroupName+'</p></td></tr>'
	              );
						}
						else{
							$(rows).eq( i ).before(
											'<tr class="group'+level+'"><td colspan="2"><p style="font-weight:700; text-indent:'+(level * 10)+'px">'
											+group+'</p></td></tr>'
									);
						}
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
		var HasRunningTotal = false;
		ConfigVars.aggregates.forEach(function(Col){
			ColIndex = $('#reporttable th:contains("' + Col.dispname + '")').index();
			Aggregate[ColIndex] = Array();
			Aggregate[ColIndex]['Result'] = 0;
			Aggregate[ColIndex]['Type'] = Col.func
			if(Col.func == "SUM")
				HasRunningTotal = true;
		});
		if(ConfigVars.grouping.length >0){
		for(var level=ConfigVars.maxgrouplevel;level>=0;level--){
			if(HasRunningTotal && level == 0){
				$("#reporttable tbody tr.group1").each(function(index) {
					var row = $(this).nextUntil('.group1').last();
					for(var ColIndex in Aggregate){
						if(!isNaN($(row).find('td:nth-child('+ (parseInt(ColIndex) + 1) +')').html())){
							Aggregate[ColIndex]['Result'] = Aggregate[ColIndex]['Result'] +
																			parseFloat($(row).find('td:nth-child('+ (parseInt(ColIndex) + 1) +')').html());
						}
					}
				});
				$("#reporttable tbody tr:last").eq( 0 ).after(
								'<tr class="runningtotal"><td>Running Total</td></tr>');
				for(var i=2;i<=ColCount;i++){
					if(Aggregate.hasOwnProperty(i-1)){
					if(typeof Aggregate[i-1] !== "undefined"){
						$("#reporttable tbody tr:last").append('<td class="rpt_summary" style="text-align:right"> '
																 + Aggregate[i-1]['Result'].toFixed(2) + '</td>');
					}}
					else {
						$("#reporttable tbody tr:last").append('<td></td>');
					}
				}
				for(var ColIndex in Aggregate){
					if(Aggregate.hasOwnProperty(ColIndex))
					Aggregate[ColIndex]['Result'] = 0;
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
							Aggregate[ColIndex]['Result'] = Aggregate[ColIndex]['Result'] +
																			parseFloat($(row).find('td:nth-child('+ (parseInt(ColIndex) + 1) +')').html());
							}
						}
					});

					var $NewRow = $("<tr class='sum'><td class='rpt_summary tag'>Suma</td>");
					for(var i=2;i<=ColCount;i++){
						if(Aggregate.hasOwnProperty(i-1)){
						if(typeof Aggregate[i-1]!== "undefined"){
							$NewRow.append('<td class="rpt_summary" style="text-align:right"> '
						                       + Aggregate[i-1]['Result'].toFixed(2) + '</td>');
						}}
						else {
							$NewRow.append('<td class="rpt_summary" style="text-align:right"></td>');
						}
					}
					$(this).nextUntil('.group'+level.toString()).last().after($NewRow);
					for(var ColIndex in Aggregate){
						if(Aggregate.hasOwnProperty(ColIndex))
							Aggregate[ColIndex]['Result'] = 0;
						}
					});
			}
		}
		else{

			$("#reporttable tbody tr:last").eq( 0 ).after(
							'<tr class="runningtotal"><td>Running Total</td></tr>');
			$("#reporttable tbody tr").each(function(index) {
				for(var ColIndex in Aggregate){
					if(Aggregate.hasOwnProperty(ColIndex)){
						if(!isNaN($(this).find('td:nth-child('+ (parseInt(ColIndex) + 1) +')').html())){
							Aggregate[ColIndex]['Result'] = Aggregate[ColIndex]['Result'] +
																			parseFloat($(this).find('td:nth-child('+ (parseInt(ColIndex) + 1) +')').html());
																			console.log(Aggregate[ColIndex]['Result']);
						}
					}
				}
			});
			for(var i=2;i<=ColCount;i++){
				if(Aggregate.hasOwnProperty(i-1)){
				if(typeof Aggregate[i-1] !== "undefined"){
					$("#reporttable tbody tr:last").append('<td class="rpt_summary" style="text-align:right"> '
															 + Aggregate[i-1]['Result'].toFixed(2) + '</td>');
				}}
				else {
					$("#reporttable tbody tr:last").append('<td></td>');
				}
			}
			for(var ColIndex in Aggregate){
				if(Aggregate.hasOwnProperty(ColIndex))
				Aggregate[ColIndex]['Result'] = 0;
			}
		}
	}
	function FormatColumns(){
		numeral.language('es', {
		    delimiters: {
		        thousands: '.',
		        decimal: ','
		    },
		    abbreviations: {
		        thousand: 'k',
		        million: 'm',
		        billion: 'b',
		        trillion: 't'
		    },
		    ordinal : function (number) {
		        return number === 1 ? 'er' : 'ème';
		    },
		    currency: {
		        symbol: '₲'
		    }
		});
		numeral.language('es');
		$("#reporttable tbody tr").each(function(index) {
			var row = $(this);
			$.each(ConfigVars.formats,function(ColName,Format){
				ColIndex = $('#reporttable th:contains("' + ColName + '")').index();

				var number = numeral($(row).find('td:nth-child('+ (parseInt(ColIndex) + 1) +')').html());
				switch(Format){
					case "CURRENCY":
						$(row).find('td:nth-child('+ (parseInt(ColIndex) + 1) +')').html(number.format('$0,0.00'));
						break;
				}
			});
		});
	}
	$("#searchbox").on("keyup search input paste cut", throttle(function() {
			dataTable = $('#reporttable').DataTable();
			dataTable.search(this.value).draw();
	}));
	function throttle(f, delay){
			var timer = null;
			return function(){
					var context = this, args = arguments;
					clearTimeout(timer);
					timer = window.setTimeout(function(){
							f.apply(context, args);
					},
					delay || 500);
			};
	}
});
