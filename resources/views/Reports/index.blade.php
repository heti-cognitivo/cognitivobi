@extends('Reports.master')

@section('csslinks')
<link rel="stylesheet" href="../public/css/default.css">
<link rel="stylesheet" href="../public/css/modal.css">
@endsection

@section('jslinks')
<script src="../public/Javascript/jquery.min.2.1.3.js"></script>
<script src="../public/Javascript/modal.js"></script>
<script src="../public/Javascript/modernizr.js"></script>
<script type="text/javascript">
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
  <a id="rpt_link" href="#">
    <div class="report_thumbnail">
      <div class="rpt_List">
        <li class="rpt_li" data-icon="g"/>
      </div>
      <!--<a id="showFilters"><h4>{{$report->name}}'</h4></a>-->
      <a href="{{ route('reports.show', [$report->id_bi_report]) }}"><h4>{{$report->name}}'</h4></a>
    </div>
  </a>
  @endforeach
@endsection
<div class="cd-user-modal"> <!-- this is the entire modal form, including the background -->
  <div class="cd-user-modal-container"> <!-- this is the container wrapper -->
    <div id="cd-filters" class="is-selected"> <!-- log in form -->
      <form class="cd-form">
        <p class="fieldset">
          <label class="image-replace cd-email" for="signin-email">E-mail</label>
          <input class="full-width has-padding has-border" id="signin-email" type="email" placeholder="E-mail">
          <span class="cd-error-message">Error message here!</span>
        </p>

        <p class="fieldset">
          <label class="image-replace cd-password" for="signin-password">Password</label>
          <input class="full-width has-padding has-border" id="signin-password" type="text" placeholder="Password">
          <a href="#0" class="hide-password">Hide</a>
          <span class="cd-error-message">Error message here!</span>
        </p>

        <p class="fieldset">
          <input type="checkbox" id="remember-me" checked="">
          <label for="remember-me">Remember me</label>
        </p>

        <p class="fieldset">
          <input class="full-width" type="submit" value="Login">
        </p>
      </form>
    </div> <!-- cd-login -->
    <a href="#0" class="cd-close-form">Close</a>
  </div> <!-- cd-user-modal-container -->
</div>
