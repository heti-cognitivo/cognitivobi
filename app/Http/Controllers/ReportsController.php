<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Input;
use Response;
use View;
use App\Bi_Report;
use App\DataTypes;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $reports = Bi_Report::all();
        return view ('Reports.index',compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Bi_Report $Report)
    {
        $HasFilter = false;
        foreach ($Report->Bi_Report_Details()->get() as $Detail) {
          if(!is_null($Detail->filter_by))
            $HasFilter = true;
        }
        return view('Reports.show', compact('Report','HasFilter'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Bi_Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, Bi_Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Bi_Report $report)
    {
        //
    }
    /*
      Throw Report Data To the Report.Show View.
      This method will be called from an ajax call from Report.Show view.
      This method returns a json response with column names and data.
    */
    public function GetData()
    {
        $Report = Bi_Report::find(Input::get('id'));
        $Data = DB::select(DB::raw($Report->query));
        $ColumnCnt=0;
        $IsGrouping = false;
        $Columns = array();
        $ColumnGroup = array();
        $Aggregates = array();
        $Headers = array();
        $ColName = "";
        $error = "";
        $GrpCnt = 0;
        $AggCnt = 0;
        $HeaderCnt = 0;
        $MaxGroupLevel = $Report->Bi_Report_Details()->get()->max("group");
        foreach ($Report->Bi_Report_Details()->get() as $Detail) {
          if(!is_null($Detail->display_column)){
            $ColName = $Detail->display_column;
          }
          else{
            $ColName = $Detail->name_column;
          }
          $Columns [$ColumnCnt] ["name"] = $ColName;
          if (DataTypes::getLabel($Detail->format_column) == "NUMERIC") {
  					$Columns [$ColumnCnt] ["className"] = "dt-right";
  					$Columns [$ColumnCnt] ["type"] = "num-html";
  				} else {
  					$Columns [$ColumnCnt] ["className"] = "dt-left";
  					$Columns [$ColumnCnt] ["type"] = "string";
  				}
          $Columns [$ColumnCnt] ["targets"] = $ColumnCnt;
          $Columns [$ColumnCnt] ["data"] = $Detail->name_column;
          if(!is_null($Detail->is_output)){
            $Columns [$ColumnCnt] ["visible"] = $Detail->is_output;
          }
          $ColumnCnt++;
          if(!is_null($Detail->group)){
            $ColumnGroup[$GrpCnt]["dispname"] = $ColName;
            $ColumnGroup[$GrpCnt]["level"] = $Detail->group;
            $GrpCnt++;
          }
          if(!is_null($Detail->aggregate_by)){
            $Aggregates[$AggCnt]["dispname"] = $ColName;
            $Aggregates[$AggCnt]["func"] = $Detail->aggregate_by;
            $AggCnt++;
          }
          if(!is_null($Detail->is_header) && $Detail->is_header){
            $Headers[$HeaderCnt]["name"] = $ColName;
            $dbColumn = $Detail->name_column;
            $Headers[$HeaderCnt]["value"] = $Data[0]->$dbColumn;
            $HeaderCnt++;
          }
        }
        $Response = array('data'=>$Data,
                        'columns'=>$Columns,
                        'grouping'=>$ColumnGroup,
                        'aggregates'=>$Aggregates,
                        'maxgrouplevel'=>$MaxGroupLevel,
                        'headers'=>$Headers);
        return Response::json($Response);
    }
    public function GetFilters()
    {
      $Report = Bi_Report::find(Input::get('id'));
      $FilterCnt = 0;
      $Filters = array();
      $ColName = "";
      foreach ($Report->Bi_Report_Details()->get() as $Detail) {
        if(!is_null($Detail->filter_by)){
          if(!is_null($Detail->display_column)){
            $ColName = $Detail->display_column;
          }
          else{
            $ColName = $Detail->name_column;
          }
          $Filters[$FilterCnt]["dispname"] = $ColName;
          $Filters[$FilterCnt]["dbcolname"] = $Detail->name_column;
          $Filters[$FilterCnt]["format"] = DataTypes::getLabel($Detail->format_column);
          $FilterCnt++;
        }
      }
      $view = view('Reports.filters', compact('Filters'));
      return (string)$view;
    }
    public function ProcessFilters()
    {
      $WhereClause = "where";
      $PatternGroup = "/\bgroup by\b/i";
  		$PatternOrder = "/\border by\b/i";
  		$PatternWhere = "/\b where \b/i";
      $Report = Bi_Report::find(Input::get('id'));
      $Filters = Input::get('JsnFilters');
      $isConditionFirst=true;
      if (preg_match ( $PatternWhere, $Report->query )) {
  			$sWhereClause = "";
  			$isConditionFirst = false;
  		}
      foreach($Filters as $Filter){
        switch ($Filter ["type"]) {
          case "string" :
  					if (! $isConditionFirst) {
  						$WhereClause .= " and " . $Filter ["selector"] . $Filter ["operator"] . "'%" . $Filter ["value"] . "%'";
  					} else {
  						$WhereClause .= " " . $Filter ["selector"] . $Filter ["operator"] . "'%" . $Filter ["value"] . "%'";
  					}
  					break;
          case "date" :
  					$Filter ["value"] = date ( 'Y-m-d', strtotime ( str_replace ( '-', '/', $Filter ["value"] ) ) );
  					if (! $isConditionFirst) {
  						$WhereClause .= " and " . $Filter ["selector"] . $Filter ["operator"] . "'" . $Filter ["value"] . "'";
  					} else {
  						$WhereClause .= " " . $Filter ["selector"] . $Filter ["operator"] . "'" . $Filter ["value"] . "'";
  					}
  					break;
  				case "numeric" :
  					if (! $isConditionFirst) {
  						$WhereClause .= " and " . $Filter ["selector"] . $Filter ["operator"] . $Filter ["value"];
  					} else {
  						$WhereClause .= " " . $Filter ["selector"] . $Filter ["operator"] . $Filter ["value"];
  					}
  					break;
        }
        $isConditionFirst = false;
      }
      $index = - 1;
      if (preg_match ( $PatternGroup, $Report->query, $matches, PREG_OFFSET_CAPTURE ) ||
          preg_match ( $PatternOrder, $Report->query, $matches, PREG_OFFSET_CAPTURE )) {
  			$index = $matches [0] [1];
  		} else {
  			$index = strlen ( $sQuery );
  		}
  		$Query = substr_replace ( $Report->query, $WhereClause . " ", $index, 0 );
      $Data = DB::select(DB::raw($Query));
      $Response = array("data"=>$Data);
      return Response::json($Response);
    }
    public function ClearFilters(){
      $Report = Bi_Report::find(Input::get('id'));
      $Data = DB::select(DB::raw($Report->query));
      $Response = array("data"=>$Data);
      return Response::json($Response);
    }
}
