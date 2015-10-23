@extends('Reports.master')

@section('csslinks')
<link rel="stylesheet" href="../public/css/default.css">
@endsection

@section('jslinks')
<script src="../public/Javascript/jquery.min.2.1.3.js"></script>
<script type="text/javascript">
  document.onkeydown = ShowKeyCode;
  function ShowKeyCode(evt) {
    document.getElementById('search').style.display = 'block'
    document.getElementById('tbxSearch').focus();
  }
</script>
<script type="text/javascript">
  function open_inTab() {
    if (document.getElementsByClassName("cmn-toggle cmn-toggle-round-flat").checked == true) {
      document.getElementById("rpt_link").target = "_blank";
    } else {
      document.getElementById("rpt_link").target = "_self";
    }
  }

</script>
@endsection

@section('topbar')
<li class="menu_li" data-icon="r"><a> Reports</a></li>
<li class="menu_li" data-icon="b"><a> Dashboard</a></li>
<li class="menu_li" data-icon="a"><a> Favourites</a></li>
<li class="menu_li" data-icon="c"><a> Scheduled</a></li>
@endsection

@section('content')
  @foreach ($reports as $report)
  <a id="rpt_link" href="" target="_blank">
    <div class="report_thumbnail">
      <div class="rpt_List">
        <li class="rpt_li" data-icon="g"/>
      </div>
      <a href="{{ route('reports.show', [$report->id_bi_report]) }}"><h4>{{$report->name}}'</h4></a>
      <p>Información de Contacto</p>
    </div>
  </a>
  @endforeach
@endsection
