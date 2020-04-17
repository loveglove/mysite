@extends('layouts.app')

@section('pageTitle', 'Loft Bot')

@section('css')
<!-- chartist CSS -->
<link href="{{ asset('/theme_app/assets/plugins/chartist-js/dist/chartist.min.css') }}" rel="stylesheet">
<link href="{{ asset('/theme_app/assets/plugins/chartist-js/dist/chartist-init.css') }}" rel="stylesheet">
<link href="{{ asset('/theme_app/assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css') }}" rel="stylesheet">

<!-- Datatables -->
<link href="https://cdn.datatables.net/rowreorder/1.2.6/css/rowReorder.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet">

@endsection


@section('content')
<style>
    .add-ct-btn {
        position: absolute;
        right: 10px;
        top: 45px;
    }
    .add-ct-btn .btn {
        line-height: 1.2;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline-info">
                <div class="card-header">
                    <h1 class="m-b-0 text-white"><i class="mdi mdi-robot"></i> <small>Loft.Bot</small></h1>
                </div>
                <div class="card-body">

                    <!-- Add Contact -->
                    <h2 class="add-ct-btn" data-toggle="tooltip" title="Manage Contacts" data-placement="left">
                        <button type="button" data-toggle="modal" data-target="#contacts-modal" class="mytooltip btn btn-circle btn-lg btn-success waves-effect waves-dark"><i class="ti-user"></i></button>
                    </h2>


                    <!-- Controls -->
                    <div class="row m-3 mb-4">
                        <!-- stat -->
                        <div class="col-lg-2 col-md-6">
                            <div class="row mb-4 mb-sm-0">
                                <div class="col-6">
                                    <div class="chart-text">
                                        <h6 class="m-b-0"><small>THIS MONTH</small></h6>
                                        <h1 class="m-t-0 text-info">{{ $this_month_count }}</h1>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="chart-text">
                                        <h6 class="m-b-0"><small>LAST MONTH</small></h6>
                                        <h1 class="m-t-0 text-primary">{{ $last_month_count }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end stats -->

                        <!-- start settings -->
                        <div class="col-lg-10 col-md-6">
                            <form id="settingsForm" class="form">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-7">
                                        <div class="demo-radio-button">
                                            <input name="mode" type="radio" id="radio_1" value="1" {{ ($building->mode == 1) ? "checked" : "" }} class="radio-col-light-blue">
                                            <label for="radio_1">Multi Dial</label> 
                                            <input name="mode" type="radio" id="radio_2" value="2" {{ ($building->mode == 2) ? "checked" : "" }} class="radio-col-light-blue">
                                            <label for="radio_2">Auto Open</label>
                                            <input name="mode" type="radio" id="radio_3" value="3" {{ ($building->mode == 3) ? "checked" : "" }} class="radio-col-light-blue">
                                            <label for="radio_3">Passcode</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="input-group mb-2 mb-lg-0">
                                            <span class="input-group-btn"><button class="btn btn-info" type="button"><i class="ti-key"></i></button></span>
                                            <input id="passcode" type="text" name="passcode" class="form-control" placeholder="Passcode..." value="{{ $building->entry_code }}" {{ ($building->mode != 3) ? "disabled" : "" }}>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 d-none d-lg-block">
                                        <button class="btn btn-block btn-info" type="submit">Save Settings</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-9">
                                        <div class="input-group mb-2 mb-lg-0">
                                            <span class="input-group-btn"><button class="btn btn-info" type="button"><i class="ti-comment"></i> </button></span>
                                            <input type="text" name="message" class="form-control" placeholder="Welcome message..." value="{{ $building->entry_message }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="input-group mb-2 mb-lg-0">
                                            <span class="input-group-btn"><button class="btn btn-info" type="button">#</button></span>
                                            <input type="text" name="digits" class="form-control" placeholder="Entry Digits..." value="{{ $building->entry_digit }}">
                                        </div>
                                    </div>
                                    <div class="col-12 d-block d-lg-none mt-4 mb-lg-0">
                                        <button class="btn btn-block btn-info" type="submit">Save Settings</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- end settings -->
                    </div>
                    <!-- end controls -->

                    <hr>
                    <!-- start chart -->
                    <div class="card-body m-b-20">
                        <div class="d-flex flex-wrap">
                            <div>
                                <h3 class="card-title d-none d-sm-block">Last Entry: {{ $last_entry->created_at->diffForHumans() }}</h3>
                            </div>
                            <div class="ml-auto align-self-center">
                                <ul class="list-inline m-b-0">
                                    <li>
                                        <h6 class="text-muted text-success"><i class="fa fa-circle font-10 m-r-10 "></i>Daily Entries</h6> </li>
                                    <!-- <li>
                                        <h6 class="text-muted text-info"><i class="fa fa-circle font-10 m-r-10"></i>Recurring Payments</h6> </li> -->
                                </ul>
                            </div>
                        </div>
                        <div class="campaign ct-charts"><div class="chartist-tooltip" style="top: 63px; left: 576px;"></div></div>
                        <div class="row text-center">
                            <div class="col-4 m-t-20">
                                <h1 class="m-b-0 font-light">{{ $morning }}</h1><small>Morning</small></div>
                            <div class="col-4 m-t-20">
                                <h1 class="m-b-0 font-light">{{ $afternoon }}</h1><small>Afternoon</small></div>
                            <div class="col-4 m-t-20">
                                <h1 class="m-b-0 font-light">{{ $evening }}</h1><small>Evening</small></div>
                        </div>
                    </div>
                    <!-- end chart -->

                    <!-- start table -->
                    <div class="table-responsive">
                        <table id="entryTable" class="display nowrap table table-hover table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>LoftBot #</th>
                                    <th>When</th>
                                    <th>Time</th>
                                    <th>Date</th>
                                    <th>Owner</th>
                                    <th>Mode</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->building->loftbotNumber() }}</td>
                                    <td>{{ $log->created_at->diffForHumans() }}</td>
                                    <td>{{ $log->created_at->format('g:i a') }}</td>
                                    <td data-sort="{{ $log->created_at }}">{{ $log->created_at->format('D F jS, Y') }}</td>
                                    <td>{{ $log->building->contact->name }}</td>
                                    <td>{{ $log->mode() }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- end table -->

                </div>
            </div>
        </div>

    </div>
</div>



<!-- start modal -->
<div id="contacts-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Manage Contacts</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">Recipient:</label>
                        <input type="text" class="form-control" id="recipient-name">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="control-label">Message:</label>
                        <textarea class="form-control" id="message-text"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger waves-effect waves-light">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- /.modal -->

@endsection


@section('scripts')

<!-- chartist chart -->
<script src="{{ asset('/theme_app/assets/plugins/chartist-js/dist/chartist.min.js') }}"></script>
<script src="{{ asset('/theme_app/assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js') }}"></script>

<!-- datatables -->
<script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

<script>
$(function () {
    
    var chart_values = JSON.parse('{{ json_encode($chart_values) }}').reverse(); //numbers
    var chart_dates = {!! json_encode($chart_dates) !!}; //strings
    chart_dates.reverse().pop()
    chart_dates.push('Today');

    $('#entryTable').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        pageLength: 5,
        "order": [[ 3, "desc" ]],
        buttons: [
            { extend: 'copy', className: 'btn btn-info' },
            { extend: 'csv', className: 'btn btn-info' },
            { extend: 'excel', className: 'btn btn-info' },
            { extend: 'pdf', className: 'btn btn-info' },
            { extend: 'print', className: 'btn btn-info' },
        ]
    })

    $('input:radio[name="mode"]').change(
        function(){
            console.log(this.value);
            if (this.value == '3') {
                $("#passcode").prop("disabled", false);
            }else{
                $("#passcode").prop("disabled", true);
            }
        }
    );

    $('#settingsForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url:'/apps/operator/save',
            data: $('#settingsForm').serialize(),
            success:function(data){
                console.log(data);
                $.toast({
                    heading: 'Settings Updated',
                    text: 'Your loftbot settings have been saved',
                    position: 'top-right',
                    loaderBg:'#2ab9c9',
                    icon: 'success',
                    hideAfter: 3500, 
                    stack: 6
                });
            }
        });
    });


    // ============================================================== 
    // Chart
    // ============================================================== 
    var chart = new Chartist.Line('.campaign', {
          labels: chart_dates,
          series: [chart_values]
          },{
          low: 0,
          showArea: true,
          fullWidth: true,
          plugins: [
            Chartist.plugins.tooltip()
          ],
            axisY: {
            onlyInteger: true
            , scaleMinSpace: 40    
            , offset: 20
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
        chart.on('draw', function(data) {
            if (data.type === 'line' || data.type === 'area') {
                data.element.animate({
                    d: {
                        begin: 500 * data.index,
                        dur: 1000,
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

});

</script>


@endsection