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
  <div>
    <input type="button" id="btnFilter" style="margin-bottom:5px;margin-top:5px" class="btn btn-success" value="Filter"/>
    <input type="button" id="btnClearFilter" style="margin-bottom:5px;margin-top:5px" class="btn btn-success" value="Clear"/>
  </div>
</div>
@show
