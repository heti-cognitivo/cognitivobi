@section('AllFilters')
<div class="cd-filter-content">
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
    @endif
  </div>
  @endforeach
  <div style="text-align:center">
    <input type="button" id="btnFilter" value="Filter"/>
    <input type="button" id="btnClearFilter" value="Clear"/>
  </div>
</div>
@show
