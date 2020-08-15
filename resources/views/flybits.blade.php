@extends('layouts.app')

@section('pageTitle', 'Flybits')

@section('css')

<!-- Datatables -->
<link href="https://cdn.datatables.net/rowreorder/1.2.6/css/rowReorder.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet">

@endsection


@section('content')

<div class="container" style="padding:0px;">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline-info mt-3">
                <div class="card-header">
                    <h1 class="m-b-0 text-white"><i class="mdi mdi-compass"></i> <small>Flybits Engagement Rule Generator</small></h1>
                </div>
                <div class="card-body">

                <div class="table-responsive">
                    <form method="post" action="/apps/flybits/api">
                        <label for="jwt">JWT</label>
                        <input type="text" name="jwt" class="form-control" value="{{old('jwt')}}" />
                        <label for="host">Host</label>
                        <input type="text" name="host" class="form-control" value="https://v3.flybits.com" />
                        <br>
                        <br>
                        @csrf
                        <table class="table table-bordered table-striped" id="user_table">
                            <thead>
                                <tr>
                                    <th width="25%">Content Abrv</th>
                                    <th width="50%">Content ID</th>
                                    <th width="25%">States</th>
                                </tr>
                            </thead>
                           <tbody>
                                <tr>
                                    <td><input type="text" name="name1" class="form-control clear" value="{{old('name1')}}" /></td>
                                    <td><input type="text" name="id1" class="form-control clear" value="{{old('id1')}}" /></td>
                                    <td><select name="states1" class="form-control">
                                          <option value="0">Not Eng</option>
                                          <option value="1">Has Eng</option>
                                          <option value="2">Both</option>
                                        </select>
                                    </td>
                                <tr>
                                <tr>
                                    <td><input type="text" name="name2" class="form-control clear" value="{{old('name2')}}" /></td>
                                    <td><input type="text" name="id2" class="form-control clear" value="{{old('id2')}}" /></td>
                                    <td><select name="states2" class="form-control">
                                          <option value="0">Not Eng</option>
                                          <option value="1">Has Eng</option>
                                          <option value="2">Both</option>
                                        </select>
                                    </td>
                                <tr>
                                <tr>
                                    <td><input type="text" name="name3" class="form-control clear" value="{{old('name3')}}" /></td>
                                    <td><input type="text" name="id3" class="form-control clear" value="{{old('id3')}}" /></td>
                                    <td><select name="states3" class="form-control">
                                          <option value="0">Not Eng</option>
                                          <option value="1">Has Eng</option>
                                          <option value="2">Both</option>
                                        </select>
                                    </td>
                                <tr>
                                <tr>
                                    <td><input type="text" name="name4" class="form-control clear" value="{{old('name4')}}" /></td>
                                    <td><input type="text" name="id4" class="form-control clear" value="{{old('id4')}}" /></td>
                                    <td><select name="states4" class="form-control">
                                          <option value="0">Not Eng</option>
                                          <option value="1">Has Eng</option>
                                          <option value="2">Both</option>
                                        </select>
                                    </td>
                                <tr>
                                <tr>
                                    <td><input type="text" name="name5" class="form-control clear" value="{{old('name5')}}" /></td>
                                    <td><input type="text" name="id5" class="form-control clear" value="{{old('id5')}}" /></td>
                                    <td><select name="states5" class="form-control">
                                          <option value="0">Not Eng</option>
                                          <option value="1">Has Eng</option>
                                          <option value="2">Both</option>
                                        </select>
                                    </td>
                                <tr>
                                <tr>
                                    <td><input type="text" name="name6" class="form-control clear" value="{{old('name6')}}" /></td>
                                    <td><input type="text" name="id6" class="form-control clear" value="{{old('id6')}}" /></td>
                                    <td><select name="states6" class="form-control">
                                          <option value="0">Not Eng</option>
                                          <option value="1">Has Eng</option>
                                          <option value="2">Both</option>
                                        </select>
                                    </td>
                                <tr>
                           </tbody>
                        </table>
                        <input type="submit" name="save" id="submit" class="btn btn-lg btn-primary" value="Generate Rules" /> <span id="genmsg" style="display:none;">Please wait while the rules are generated...</span>
                        <button id="clear" class="btn btn-default float-right">Clear Fields </button>
                    </form>
                </div>
                <br>
                <h3> Results: </h3>

                @if(isset($results))
                @foreach($results as $r)
                    @if($r["status"])
                        <div class="alert alert-success alert-block">
                            <strong>{{ $r["message"] }}</strong>
                        </div>
                    @else
                        <div class="alert alert-danger alert-block">
                            <strong>{{ $r["message"] }}</strong>
                        </div>
                    @endif
                @endforeach
                @endif

                </div>
            </div>
        </div>
    </div>



</div>
<!-- end container -->


@endsection


@section('scripts')

<!-- datatables -->
<script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

<script>
    
$( "#submit" ).click(function( event ) {
  $("#genmsg").show();
});

$( "#clear" ).click(function( event ) {
  event.preventDefault();
  $(".clear").val("");
});

</script>

@endsection