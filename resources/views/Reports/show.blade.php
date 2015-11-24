@extends('Reports.master')

@section('title')
{{$Report->name}}
@endsection

@section('csslinks')
<link rel="stylesheet" href="../css/default.css">
<link type="text/css" rel="stylesheet" href="../css/jquery.dataTables.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap/3.3.2/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/1/daterangepicker-bs3.css" />
@endsection

@section('jslinks')
<script type="text/javascript">
ReportId={{$Report->id_bi_report}};
</script>
<script src="../Javascript/jquery.min.2.1.3.js"></script>
<script src="../Javascript/paperfold.min.js"></script>
<script src="../Javascript/jquery.dataTables.min.js"></script>
<script src="../Javascript/daterangepicker.js"></script>
<script src="../Javascript/moment.min.js"></script>
<script src="../Javascript/numeral.min.js"></script>
<script src="../Javascript/ConfigureReport.js"></script>
@endsection
@section('topbar')
<ul>
  <li style="display:inline"><img height="30px" width="30px" src="../images/outlines/search-100.png"</li>
  <li style="display:inline"><input id='searchbox' type="text"></li>
</ul>
@endsection
@section('content')
  <input type="hidden" id="token" value="{{ csrf_token() }}">

  <div id="rpt_Wrapper">
    <div id="rpt_Title" class="rpt_Paper">
        <h1>{{$Report->name}}</h1>
    </div>
    <div id="rpt_options" class="rpt_paper">
      <div style="float: clear;"></div>
      @if($HasFilter)
        <div id="openFilterOptions" style="float: right;padding-top: 16px;padding-bottom: 16px;">
          <img width="18" height="18" src="../images/filters.png"></img>
        </div>
        <div style="float: clear;"></div>
        <div id="showFilters" style="padding-top:20px;display:none" class="panel"></div>
      @endif
    </div>
    <div id="rpt_Table" class="panel panel-default">
      <div style="display:table;width:100%;padding-top:20px" class="panel-heading">

        <div style="display:table-row">
          <div id="header-left" style="display:table-cell"></div>
          <div id="header-right" style="display:table-cell"></div>
        </div>
      </div>
      <table id="reporttable" class="table">
        <thead id="reporthead"></thead>
          <tbody></tbody>
      </table>
    </div>
  </div>
  <div id="error"></div>
@endsection
