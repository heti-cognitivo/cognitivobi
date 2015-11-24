@section('AllFilters')
<div class="cd-panel from-right">
  <header class="cd-panel-header">
		<h1>Filtros</h1>
	</header>
  <div class="cd-panel-container">
		<div class="cd-panel-content">
  @foreach ($Filters as $filter)
    <div class="filters">
    @if($filter['format'] == "TEXT")
      <input type="hidden" id="hdnType" value="string"></input>
      <label for={{$filter['dbcolname']}} class="lable">{{$filter['dispname']}}</label>
      <input id={{$filter['dbcolname']}} type="text" class="typeahead" data-role="tagsinput" value="" />
    @elseif($filter['format'] == "NUMERIC")
      <input type="hidden" id="hdnType" value="numeric"/>
      <label for={{$filter['dbcolname']}} class="lable">{{$filter['dispname']}}</label>
      <input id={{$filter['dbcolname']}} type="text" class="typeahead" data-role="tagsinput" value="" />
    @else($filter['format'] == "DATE")
      <input type="hidden" id="hdnType" value="date"/>
      <label for={{$filter['dbcolname']}} class="lable">{{$filter['dispname']}}</label>
      <input id={{$filter['dbcolname']}} type="text" value="" />
      <script type="text/javascript">
      $(function() {
          $('input[id="{{$filter['dbcolname']}}"]').daterangepicker({
              singleDatePicker: false,
              showDropdowns: true,
               ranges: {
                 'Hoy': [moment(), moment()],
                 'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                 'Ultimo 7 Dias': [moment().subtract(6, 'days'), moment()],
                 'Ultimo 30 Dias': [moment().subtract(29, 'days'), moment()],
                 'Este Mes': [moment().startOf('month'), moment().endOf('month')],
                 'Ultimo Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
              },
              locale: {
                  applyLabel: 'Okay',
                  cancelLabel: 'Cancelar',
                  fromLabel: 'Desde',
                  toLabel: 'Hasta',
                  customRangeLabel: 'Custom',
                  daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
                  monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Deciembre'],
                  firstDay: 1
              }
          });
      });
</script>
    @endif
  </div>
  @endforeach
  <div>
    <input type="button" id="btnFilter" style="margin-bottom:5px;margin-top:5px" class="btn btn-success" value="Filter"/>
    <input type="button" id="btnClearFilter" style="margin-bottom:5px;margin-top:5px" class="btn btn-success" value="Clear"/>
  </div>
</div>
</div>
</div>
@show
