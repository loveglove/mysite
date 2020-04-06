@extends('layouts.app')

@section('pageTitle', 'Loft Bot')

@section('css')
<!-- chartist CSS -->
<link href="{{ asset('/theme_app/assets/plugins/chartist-js/dist/chartist.min.css') }}" rel="stylesheet">
<link href="{{ asset('/theme_app/assets/plugins/chartist-js/dist/chartist-init.css') }}" rel="stylesheet">
<link href="{{ asset('/theme_app/assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css') }}" rel="stylesheet">
@endsection


@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline-info">
                <div class="card-header">
                    <h2 class="m-b-0 text-white"><i class="icon-social-reddit"></i> Loft.Bot</h2>
                </div>
                <div class="card-body">

                    <!-- Stats -->
                    <div class="d-flex m-t-30 m-b-30">
                        <div class="d-flex m-r-20 m-l-10 hidden-md-down">
                            <div class="chart-text m-r-10">
                                <h6 class="m-b-0"><small>THIS MONTH</small></h6>
                                <h1 class="m-t-0 text-info">{{ $this_month_count }}</h1>
                            </div>
                        </div>
                        <div class="d-flex m-r-20 m-l-10 hidden-md-down">
                            <div class="chart-text m-r-10">
                                <h6 class="m-b-0"><small>LAST MONTH</small></h6>
                                <h1 class="m-t-0 text-primary">{{ $last_month_count }}</h1>
                            </div>
                        </div>
                        <div class="m-l-10">
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="demo-radio-button">
                                        <input name="group1" type="radio" id="radio_1" class="radio-col-light-blue">
                                        <label for="radio_1">Multi Dial</label>
                                        <input name="group1" type="radio" id="radio_2" class="radio-col-light-blue">
                                        <label for="radio_2">Auto Open</label>
                                        <input name="group1" type="radio" id="radio_3" class="radio-col-light-blue">
                                        <label for="radio_3">Passcode</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-group">
                                        <span class="input-group-btn"><button class="btn btn-info" type="button">#</button></span>
                                        <input type="text" class="form-control" placeholder="Passcode...">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <button class="btn btn-block btn-info" type="button">Save Settings</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-9">
                                    <div class="input-group">
                                        <span class="input-group-btn"><button class="btn btn-info" type="button"><i class="ti-comment"></i> </button></span>
                                        <input type="text" class="form-control" placeholder="Welcome message...">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="input-group">
                                        <span class="input-group-btn"><button class="btn btn-info" type="button">#</button></span>
                                        <input type="text" class="form-control" placeholder="Entry Digits...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end stats -->

                    <!-- start table -->
                    <div class="table-responsive">
                        <table id="entryTable" class="display nowrap table table-hover table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>LoftBot #</th>
                                    <th>Owner</th>
                                    <th>Mode</th>
                                    <th>When</th>
                                    <th>Time</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->building->loftbotNumber() }}</td>
                                    <td>{{ $log->building->contact->name }}</td>
                                    <td>{{ $log->mode() }}</td>
                                    <td>{{ $log->created_at->diffForHumans() }}</td>
                                    <td>{{ $log->created_at->format('g:i a') }}</td>
                                    <td>{{ $log->created_at->format('l F jS, Y') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- end table -->

                    <!-- start chart -->
                    <div class="card-body">
                                <div class="d-flex flex-wrap">
                                    <div>
                                        <h3 class="card-title">Entries</h3>
                                        <h6 class="card-subtitle">Last Entry Was: </h6>
                                    </div>
                                    <div class="ml-auto align-self-center">
                                        <ul class="list-inline m-b-0">
                                            <li>
                                                <h6 class="text-muted text-success"><i class="fa fa-circle font-10 m-r-10 "></i>Open Rate</h6> </li>
                                            <li>
                                                <h6 class="text-muted text-info"><i class="fa fa-circle font-10 m-r-10"></i>Recurring Payments</h6> </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="campaign ct-charts"><div class="chartist-tooltip" style="top: 63px; left: 576px;"></div><svg xmlns:ct="http://gionkunz.github.com/chartist-js/ct" width="100%" height="100%" class="ct-chart-line" style="width: 100%; height: 100%;"><g class="ct-grids"><line x1="30" x2="30" y1="15" y2="245" class="ct-grid ct-horizontal"></line><line x1="110.71428571428571" x2="110.71428571428571" y1="15" y2="245" class="ct-grid ct-horizontal"></line><line x1="191.42857142857142" x2="191.42857142857142" y1="15" y2="245" class="ct-grid ct-horizontal"></line><line x1="272.1428571428571" x2="272.1428571428571" y1="15" y2="245" class="ct-grid ct-horizontal"></line><line x1="352.85714285714283" x2="352.85714285714283" y1="15" y2="245" class="ct-grid ct-horizontal"></line><line x1="433.57142857142856" x2="433.57142857142856" y1="15" y2="245" class="ct-grid ct-horizontal"></line><line x1="514.2857142857142" x2="514.2857142857142" y1="15" y2="245" class="ct-grid ct-horizontal"></line><line x1="595" x2="595" y1="15" y2="245" class="ct-grid ct-horizontal"></line><line y1="245" y2="245" x1="30" x2="595" class="ct-grid ct-vertical"></line><line y1="168.33333333333331" y2="168.33333333333331" x1="30" x2="595" class="ct-grid ct-vertical"></line><line y1="91.66666666666666" y2="91.66666666666666" x1="30" x2="595" class="ct-grid ct-vertical"></line><line y1="15" y2="15" x1="30" x2="595" class="ct-grid ct-vertical"></line></g><g><g class="ct-series ct-series-a"><path d="M30,245L30,245C43.452,238.611,83.81,214.333,110.714,206.667C137.619,199,164.524,202.833,191.429,199C218.333,195.167,245.238,207.944,272.143,183.667C299.048,159.389,325.952,54.611,352.857,53.333C379.762,52.056,406.667,154.278,433.571,176C460.476,197.722,487.381,202.833,514.286,183.667C541.19,164.5,581.548,81.444,595,61L595,245Z" class="ct-area" x1="NaN"></path><path d="M30,245C43.452,238.611,83.81,214.333,110.714,206.667C137.619,199,164.524,202.833,191.429,199C218.333,195.167,245.238,207.944,272.143,183.667C299.048,159.389,325.952,54.611,352.857,53.333C379.762,52.056,406.667,154.278,433.571,176C460.476,197.722,487.381,202.833,514.286,183.667C541.19,164.5,581.548,81.444,595,61" class="ct-line"></path><line x1="30" y1="245" x2="30.01" y2="245" class="ct-point" ct:value="0"></line><line x1="110.71428571428571" y1="206.66666666666666" x2="110.72428571428571" y2="206.66666666666666" class="ct-point" ct:value="5"></line><line x1="191.42857142857142" y1="199" x2="191.4385714285714" y2="199" class="ct-point" ct:value="6"></line><line x1="272.1428571428571" y1="183.66666666666666" x2="272.1528571428571" y2="183.66666666666666" class="ct-point" ct:value="8"></line><line x1="352.85714285714283" y1="53.33333333333334" x2="352.8671428571428" y2="53.33333333333334" class="ct-point" ct:value="25"></line><line x1="433.57142857142856" y1="176" x2="433.58142857142855" y2="176" class="ct-point" ct:value="9"></line><line x1="514.2857142857142" y1="183.66666666666666" x2="514.2957142857142" y2="183.66666666666666" class="ct-point" ct:value="8"></line><line x1="595" y1="61" x2="595.01" y2="61" class="ct-point" ct:value="24"></line></g><g class="ct-series ct-series-b"><path d="M30,245L30,245C43.452,241.167,83.81,223.278,110.714,222C137.619,220.722,164.524,236.056,191.429,237.333C218.333,238.611,245.238,238.611,272.143,229.667C299.048,220.722,325.952,182.389,352.857,183.667C379.762,184.944,406.667,233.5,433.571,237.333C460.476,241.167,487.381,206.667,514.286,206.667C541.19,206.667,581.548,232.222,595,237.333L595,245Z" class="ct-area" x1="NaN"></path><path d="M30,245C43.452,241.167,83.81,223.278,110.714,222C137.619,220.722,164.524,236.056,191.429,237.333C218.333,238.611,245.238,238.611,272.143,229.667C299.048,220.722,325.952,182.389,352.857,183.667C379.762,184.944,406.667,233.5,433.571,237.333C460.476,241.167,487.381,206.667,514.286,206.667C541.19,206.667,581.548,232.222,595,237.333" class="ct-line"></path><line x1="30" y1="245" x2="30.01" y2="245" class="ct-point" ct:value="0"></line><line x1="110.71428571428571" y1="222" x2="110.72428571428571" y2="222" class="ct-point" ct:value="3"></line><line x1="191.42857142857142" y1="237.33333333333334" x2="191.4385714285714" y2="237.33333333333334" class="ct-point" ct:value="1"></line><line x1="272.1428571428571" y1="229.66666666666666" x2="272.1528571428571" y2="229.66666666666666" class="ct-point" ct:value="2"></line><line x1="352.85714285714283" y1="183.66666666666666" x2="352.8671428571428" y2="183.66666666666666" class="ct-point" ct:value="8"></line><line x1="433.57142857142856" y1="237.33333333333334" x2="433.58142857142855" y2="237.33333333333334" class="ct-point" ct:value="1"></line><line x1="514.2857142857142" y1="206.66666666666666" x2="514.2957142857142" y2="206.66666666666666" class="ct-point" ct:value="5"></line><line x1="595" y1="237.33333333333334" x2="595.01" y2="237.33333333333334" class="ct-point" ct:value="1"></line></g></g><g class="ct-labels"><foreignObject style="overflow: visible;" x="30" y="250" width="80.71428571428571" height="20"><span class="ct-label ct-horizontal ct-end" style="width: 81px; height: 20px" xmlns="http://www.w3.org/2000/xmlns/">1</span></foreignObject><foreignObject style="overflow: visible;" x="110.71428571428571" y="250" width="80.71428571428571" height="20"><span class="ct-label ct-horizontal ct-end" style="width: 81px; height: 20px" xmlns="http://www.w3.org/2000/xmlns/">2</span></foreignObject><foreignObject style="overflow: visible;" x="191.42857142857142" y="250" width="80.7142857142857" height="20"><span class="ct-label ct-horizontal ct-end" style="width: 81px; height: 20px" xmlns="http://www.w3.org/2000/xmlns/">3</span></foreignObject><foreignObject style="overflow: visible;" x="272.1428571428571" y="250" width="80.71428571428572" height="20"><span class="ct-label ct-horizontal ct-end" style="width: 81px; height: 20px" xmlns="http://www.w3.org/2000/xmlns/">4</span></foreignObject><foreignObject style="overflow: visible;" x="352.85714285714283" y="250" width="80.71428571428572" height="20"><span class="ct-label ct-horizontal ct-end" style="width: 81px; height: 20px" xmlns="http://www.w3.org/2000/xmlns/">5</span></foreignObject><foreignObject style="overflow: visible;" x="433.57142857142856" y="250" width="80.71428571428567" height="20"><span class="ct-label ct-horizontal ct-end" style="width: 81px; height: 20px" xmlns="http://www.w3.org/2000/xmlns/">6</span></foreignObject><foreignObject style="overflow: visible;" x="514.2857142857142" y="250" width="80.71428571428578" height="20"><span class="ct-label ct-horizontal ct-end" style="width: 81px; height: 20px" xmlns="http://www.w3.org/2000/xmlns/">7</span></foreignObject><foreignObject style="overflow: visible;" x="595" y="250" width="30" height="20"><span class="ct-label ct-horizontal ct-end" style="width: 30px; height: 20px" xmlns="http://www.w3.org/2000/xmlns/">8</span></foreignObject><foreignObject style="overflow: visible;" y="168.33333333333331" x="10" height="76.66666666666667" width="10"><span class="ct-label ct-vertical ct-start" style="height: 77px; width: 10px" xmlns="http://www.w3.org/2000/xmlns/">0k</span></foreignObject><foreignObject style="overflow: visible;" y="91.66666666666664" x="10" height="76.66666666666667" width="10"><span class="ct-label ct-vertical ct-start" style="height: 77px; width: 10px" xmlns="http://www.w3.org/2000/xmlns/">10k</span></foreignObject><foreignObject style="overflow: visible;" y="15" x="10" height="76.66666666666666" width="10"><span class="ct-label ct-vertical ct-start" style="height: 77px; width: 10px" xmlns="http://www.w3.org/2000/xmlns/">20k</span></foreignObject><foreignObject style="overflow: visible;" y="-15" x="10" height="30" width="10"><span class="ct-label ct-vertical ct-start" style="height: 30px; width: 10px" xmlns="http://www.w3.org/2000/xmlns/">30k</span></foreignObject></g><defs><linearGradient id="gradient" x1="0" y1="1" x2="0" y2="0"><stop offset="0" stop-color="rgba(255, 255, 255, 1)"></stop><stop offset="1" stop-color="rgba(38, 198, 218, 1)"></stop></linearGradient></defs></svg></div>
                                <div class="row text-center">
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h1 class="m-b-0 font-light">5098</h1><small>Total Sent</small></div>
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h1 class="m-b-0 font-light">4156</h1><small>Mail Open Rate</small></div>
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h1 class="m-b-0 font-light">1369</h1><small>Click Rate</small></div>
                                </div>
                            </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')

<!-- chartist chart -->
<script src="{{ asset('/theme_app/assets/plugins/chartist-js/dist/chartist.min.js') }}"></script>
<script src="{{ asset('/theme_app/assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js') }}"></script>

<script>
$(function () {

    $('#entryTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', className: 'btn btn-info' },
            { extend: 'csv', className: 'btn btn-info' },
            { extend: 'excel', className: 'btn btn-info' },
            { extend: 'pdf', className: 'btn btn-info' },
            { extend: 'print', className: 'btn btn-info' },
        ]
    })


    // ============================================================== 
    // Chart
    // ============================================================== 
    
    var chart = new Chartist.Line('.campaign', {
          labels: [1, 2, 3, 4, 5, 6, 7, 8],
          series: [
            [0, 5, 6, 8, 25, 9, 8, 24]
            , [0, 3, 1, 2, 8, 1, 5, 1]
          ]}, {
          low: 0,
          high: 28,
          showArea: true,
          fullWidth: true,
          plugins: [
            Chartist.plugins.tooltip()
          ],
            axisY: {
            onlyInteger: true
            , scaleMinSpace: 40    
            , offset: 20
            , labelInterpolationFnc: function (value) {
                return (value / 1) + 'k';
            }
        },
        });

        // Offset x1 a tiny amount so that the straight stroke gets a bounding box
        // Straight lines don't get a bounding box 
        // Last remark on -> http://www.w3.org/TR/SVG11/coords.html#ObjectBoundingBox
        chart.on('draw', function(ctx) {  
          if(ctx.type === 'area') {    
            ctx.element.attr({
              x1: ctx.x1 + 0.001
            });
          }
        });

        // Create the gradient definition on created event (always after chart re-render)
        chart.on('created', function(ctx) {
          var defs = ctx.svg.elem('defs');
          defs.elem('linearGradient', {
            id: 'gradient',
            x1: 0,
            y1: 1,
            x2: 0,
            y2: 0
          }).elem('stop', {
            offset: 0,
            'stop-color': 'rgba(255, 255, 255, 1)'
          }).parent().elem('stop', {
            offset: 1,
            'stop-color': 'rgba(38, 198, 218, 1)'
          });
        });
    

    // ============================================================== 
    // This is for the animation
    // ==============================================================
    
    for (var i = 0; i < chart.length; i++) {
        chart[i].on('draw', function(data) {
            if (data.type === 'line' || data.type === 'area') {
                data.element.animate({
                    d: {
                        begin: 500 * data.index,
                        dur: 500,
                        from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                        to: data.path.clone().stringify(),
                        easing: Chartist.Svg.Easing.easeInOutElastic
                    }
                });
            }
            if (data.type === 'bar') {
                data.element.animate({
                    y2: {
                        dur: 500,
                        from: data.y1,
                        to: data.y2,
                        easing: Chartist.Svg.Easing.easeInOutElastic
                    },
                    opacity: {
                        dur: 500,
                        from: 0,
                        to: 1,
                        easing: Chartist.Svg.Easing.easeInOutElastic
                    }
                });
            }
        });
    }

});

</script>


@endsection