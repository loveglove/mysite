@extends('layouts.app')

@section('pageTitle', 'Flybits')

@section('css')

<!-- Datatables -->
<link href="https://cdn.datatables.net/rowreorder/1.2.6/css/rowReorder.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet">

@endsection


@section('content')
<style>
    .error-text{
        color:red;
        display: block;
    }
    #generate-msg,
    #content-msg{
        margin-left: 15px;
        font-size: 18px;
    }
    .alert{
        margin-bottom: 5px;
    }
        .loader {
      border: 16px solid #f3f3f3; /* Light grey */
      border-top: 16px solid #3498db; /* Blue */
      border-radius: 50%;
      width: 100px;
      height: 100px;
      animation: spin 2s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
</style>

<div class="container" style="padding:0px;">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline-info mt-3">
                <div class="card-header">
                    <h2 class="m-b-0 text-white"><i class="mdi mdi-compass"></i> <small>Flybits Content Engagement Rule Generator</small></h2>
                </div>
                <div class="card-body">
                    <form id="content-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-3" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Flybits token is required">JWT:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="jwt" name="jwt" class="form-control" value="{{old('jwt')}}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-3" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Change if connecting to a non US region hosted project">Host:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="host" name="host" class="form-control" value="https://api.demo.flybits.com" />
                                    </div>
                                </div>
                            </div>
                        </div>
        <!--                 <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-3" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Flybits user email is required">Email:</label>
                                    <div class="col-md-9">
                                        <input type="email" id="email" name="email" class="form-control" value="{{old('email')}}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-3" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Flybits project ID is required">Project ID:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="project" name="project" class="form-control" value="{{old('project')}}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-3" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Flybits user password is required">Password:</label>
                                    <div class="col-md-9">
                                        <input type="password" id="password" name="password" class="form-control" value="{{old('password')}}" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-3" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Change if connecting to a non US region hosted project">Host:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="host" name="host" class="form-control" value="https://api.demo.flybits.com" />
                                    </div>
                                </div>
                            </div>
                        </div> -->
    
                        <br>
                        <button id="content" class="btn btn-lg btn-outline-info" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to fetch the content items for the project ID entered above"><i class="icon-wrench"></i>&nbsp&nbspGet Content</button>
                        <span id="content-msg"></span>
                        <a id="showcontent" data-toggle="modal" data-target="#myModal" class="btn btn-sm btn-outline-secondary float-right mt-3" style="display:none;">Show Content IDs</a>
                        <br>
                        <br>
     <!--                    <div class="row">
                            <div class="col-12">
                                <label for="password">JWT</label>
                                <input type="text" id="jwt-returned" name="jwt" placeholder="JWT" class="form-control" readonly data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The JWT for the user credentials above will be displayed here."/>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                <table id="user_table" class="table table-bordered" >
                                    <thead>
                                        <tr>
                                            <th width="25%" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="This abbreviation will be appened to the engagement rule name for identification purposes in Experience Studio">Content Abreviation</th>
                                            <th width="50%" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Select the content you would like to associate the user engagement too.">Content Name</th>
                                            <th width="25%" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Create a rule when a user HAS engaged with the selected content, or when a user HAS NOT engaged with the selected content. Or create BOTH engagement states">States</th>
                                        </tr>
                                    </thead>
                                   <tbody>
                                        <tr>
                                            <td><input id="name-1" type="text" name="name1" class="form-control name" value="{{old('name1')}}" /></td>
                                            <td>
                                                <select id="id-1" name="id1" class="form-control content-selector custom-select" >
                                                    <option value="" selected>-- Select Content --</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="states1" class="form-control custom-select">
                                                  <option value="2" selected>Both</option>
                                                  <option value="1">Has Engaged</option>
                                                  <option value="0">Not Engaged</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input id="name-2" type="text" name="name2" class="form-control name" value="{{old('name2')}}"  /></td>
                                            <td>
                                                <select id="id-2" name="id2" class="form-control content-selector custom-select" >
                                                    <option value="" selected>-- Select Content --</option>
                                                </select>
                                            </td>
                                            <td><select name="states2" class="form-control custom-select">
                                                  <option value="2" selected>Both</option>
                                                  <option value="1">Has Engaged</option>
                                                  <option value="0">Not Engaged</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input id="name-3" type="text" name="name3" class="form-control name" value="{{old('name3')}}" /></td>
                                            <td>
                                                <select id="id-3" name="id3" class="form-control content-selector custom-select" >
                                                    <option value="" selected>-- Select Content --</option>
                                                </select>
                                            </td>
                                            <td><select name="states3" class="form-control custom-select">
                                                  <option value="2" selected>Both</option>
                                                  <option value="1">Has Engaged</option>
                                                  <option value="0">Not Engaged</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input id="name-4" type="text" name="name4" class="form-control name" value="{{old('name4')}}"  /></td>
                                            <td>
                                                <select id="id-4" name="id4" class="form-control content-selector custom-select" >
                                                    <option value="" selected>-- Select Content --</option>
                                                </select>
                                            </td>
                                            <td><select name="states4" class="form-control custom-select">
                                                   <option value="2" selected>Both</option>
                                                  <option value="1">Has Engaged</option>
                                                  <option value="0">Not Engaged</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input id="name-5" type="text" name="name5" class="form-control name" value="{{old('name5')}}"  /></td>
                                            <td>
                                                <select id="id-5" name="id5" class="form-control content-selector custom-select" >
                                                    <option value="" selected>-- Select Content --</option>
                                                </select>
                                            </td>
                                            <td><select name="states5" class="form-control custom-select">
                                                  <option value="2" selected>Both</option>
                                                  <option value="1">Has Engaged</option>
                                                  <option value="0">Not Engaged</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input id="name-6" type="text" name="name6" class="form-control name" value="{{old('name6')}}" /></td>
                                            <td>
                                                <select id="id-6" name="id6" class="form-control content-selector custom-select" >
                                                    <option value="" selected>-- Select Content --</option>
                                                </select>
                                            </td>
                                            <td><select name="states6" class="form-control custom-select">
                                                  <option value="2" selected>Both</option>
                                                  <option value="1">Has Engaged</option>
                                                  <option value="0">Not Engaged</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input id="name-7" type="text" name="name7" class="form-control name" value="{{old('name7')}}" /></td>
                                            <td>
                                                <select id="id-7" name="id7" class="form-control content-selector custom-select" >
                                                    <option value="" selected>-- Select Content --</option>
                                                </select>
                                            </td>
                                            <td><select name="states7" class="form-control custom-select">
                                                  <option value="2" selected>Both</option>
                                                  <option value="1">Has Engaged</option>
                                                  <option value="0">Not Engaged</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input id="name-8" type="text" name="name8" class="form-control name" value="{{old('name8')}}" /></td>
                                            <td>
                                                <select id="id-8" name="id8" class="form-control content-selector custom-select" >
                                                    <option value="" selected>-- Select Content --</option>
                                                </select>
                                            </td>
                                            <td><select name="states8" class="form-control custom-select">
                                                  <option value="2" selected>Both</option>
                                                  <option value="1">Has Engaged</option>
                                                  <option value="0">Not Engaged</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input id="name-9" type="text" name="name9" class="form-control name" value="{{old('name9')}}" /></td>
                                            <td>
                                                <select id="id-9" name="id9" class="form-control content-selector custom-select" >
                                                    <option value="" selected>-- Select Content --</option>
                                                </select>
                                            </td>
                                            <td><select name="states9" class="form-control custom-select">
                                                  <option value="2" selected>Both</option>
                                                  <option value="1">Has Engaged</option>
                                                  <option value="0">Not Engaged</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input id="name-10" type="text" name="name10" class="form-control name" value="{{old('name10')}}" /></td>
                                            <td>
                                                <select id="id-10" name="id10" class="form-control content-selector custom-select" >
                                                    <option value="" selected>-- Select Content --</option>
                                                </select>
                                            </td>
                                            <td><select name="states10" class="form-control custom-select">
                                                  <option value="2" selected>Both</option>
                                                  <option value="1">Has Engaged</option>
                                                  <option value="0">Not Engaged</option>
                                                </select>
                                            </td>
                                        </tr>
                                   </tbody>
                                </table>
                                </div>
                                <button type="submit" name="save" id="submit" class="btn btn-lg btn-outline-info" value="Generate Rules" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to begin generating the rules for the selected content instances"><i class="icon-magic-wand"></i>&nbsp&nbspGenerate Rules</button><span id="generate-msg"></span>
                            </div>
                        </div>
                    </form>
                <br>
                <h3> Results: </h3>

                <div id="results">
                    <div class="loader" style="display:none;"></div> 
                </div>
            </div>
        </div>
    </div>


    <!-- content modal -->
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Content IDs</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div id="modal-info" class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->




</div>
<!-- end container -->


@endsection


@section('scripts')

<!-- datatables -->
<script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

<script>


    $( "#content-form" ).on( "submit", function( event ) {
        event.preventDefault();
        console.log($("#id-1").val());
        if($("#id-1").val() == null || $("#id-1").val() == ""){
            
            $.toast({
                heading: 'No Content Selected',
                text: 'You must select at least one content instance to proceed with generating rules',
                position: 'top-center',
                loaderBg:'#FFD997',
                icon: 'warning',
                hideAfter: 5000, 
            });

        }else{


            $("#generate-msg").html("Generating rules, please wait a moment...");
            $(".loader").show();

            $.ajax({
                url: '/apps/flybits/api/engagement',
                type: "POST",
                dataType: "json",
                data: $(this).serialize(),
                success: function(data){;
                    console.log(data);

                    $.each(data, function (i, item) {
                        
                        var alert = "alert-danger";
                        var error = "";
                        if(item.status){
                            alert = "alert-success";
                        }else{

                            var obj = JSON.parse(item.response);
                            error = obj.error.exceptionMessage;

                        }
                        var html = '<div class="alert ' + alert + ' alert-block"><strong>'+ item.message +'</strong><br><small>' + error + '<small></div>';
                        $('#results').prepend(html);
                    });

                    $("#generate-msg").html("");
                    $(".loader").hide();

                    $.toast({
                        heading: 'Rules Generated',
                        text: 'Successfully created rules, see results below',
                        position: 'top-center',
                        loaderBg:'#2ab9c9',
                        icon: 'success',
                        hideAfter: 5000, 
                    });

                },
                error: function(error){
                    $("#generate-msg").html("An error has occured attempting to generate rules");
                    $.toast({
                        heading: 'Could Not Create Rules',
                        text: 'Something went wrong generating the rules, please try again.',
                        position: 'top-center',
                        loaderBg:'#FFD997',
                        icon: 'warning',
                        hideAfter: 5000, 
                    });
                   console.log(error);
                }
            }); 

        }

    });





    $( "#content" ).click(function( event ) {
        
        event.preventDefault();

        if($("#jwt").val() == ""){
            $.toast({
                heading: 'Credentials Missing',
                text: 'You need to provide a Flybits user JWT to continue',
                position: 'top-center',
                loaderBg:'#FFD997',
                icon: 'warning',
                hideAfter: 5000, 
            });
        }else{

            $("#content-msg").html("Fetching content, one moment please...")

            $.ajax({
                url: '/apps/flybits/api/setProjectJWT',
                type: "GET",
                dataType: "json",
                data: $("#content-form").serialize() ,
                success: function(data){
                    console.log(data);

                    if(data.status){

                        // $("#jwt").val(data.jwt)

                        $('.content-selector').html('');
                        
                        $.each(data.content, function (i, item) {
                            $('.content-selector').prepend($('<option>', { 
                                value: item.id,
                                text : item.name 
                            }));
                            $('#modal-info').prepend(item.id + '&nbsp&nbsp|&nbsp&nbsp ' + item.name + '<br>');
                        });

                        $('.content-selector').prepend('<option value="" selected>-- Select Content --</option>');

                        $("#content-msg").html(data.content.length + " content instances found. Select content below to continue...");

                        $("#showcontent").show();

                        $.toast({
                            heading: 'Content Fetched Sucessfully',
                            text: 'Content for this project has been found and added to your table selection',
                            position: 'top-center',
                            loaderBg:'#2ab9c9',
                            icon: 'success',
                            hideAfter: 5000, 
                        });
                    }else{
                        $("#content-msg").html(data.error);
                        console.log(data.response);
                    }
                },
                error: function(error){
                   console.log(error);
                }
            }); 
        }
    });

    // $( "#content" ).click(function( event ) {
        
    //     event.preventDefault();

    //     if($("#project").val() == "" || $("#email").val() == "" || $("#password").val() == ""){
    //         $.toast({
    //             heading: 'Credentials Missing',
    //             text: 'You need to specify a Flybits user E-mail, Password and Project-ID to continue',
    //             position: 'top-center',
    //             loaderBg:'#FFD997',
    //             icon: 'warning',
    //             hideAfter: 5000, 
    //         });
    //     }else{

    //         $("#content-msg").html("Fetching content, one moment please...")

    //         $.ajax({
    //             url: '/apps/flybits/api/setProject',
    //             type: "GET",
    //             dataType: "json",
    //             data: $("#content-form").serialize() ,
    //             success: function(data){
    //             	console.log(data);

    //             	if(data.status){

    //                     $("#jwt").val(data.jwt)

    //                     $('.content-selector').html('');
                        
	   //                  $.each(data.content, function (i, item) {
	   //                      $('.content-selector').prepend($('<option>', { 
	   //                          value: item.id,
	   //                          text : item.name 
	   //                      }));
    //                         $('#modal-info').prepend(item.id + '&nbsp&nbsp|&nbsp&nbsp ' + item.name + '<br>');
	   //                  });

    //                     $('.content-selector').prepend('<option value="" selected>-- Select Content --</option>');

	   //                  $("#content-msg").html(data.content.length + " content instances found. Select content below to continue...");

    //                     $("#showcontent").show();

    //                     $.toast({
    //                         heading: 'Content Fetched Sucessfully',
    //                         text: 'Content for this project has been found and added to your table selection',
    //                         position: 'top-center',
    //                         loaderBg:'#2ab9c9',
    //                         icon: 'success',
    //                         hideAfter: 5000, 
    //                     });
	   //              }else{
	   //              	$("#content-msg").html(data.error);
    //                     console.log(data.response);
	   //              }
	   //          },
    //             error: function(error){
    //                console.log(error);
    //             }
    //         }); 
    //     }
    // });

$(function () {
  
    $('[data-toggle="popover"]').popover();


    $('.content-selector').on('change', function(){
        var id = this.id.split("-")[1];
        var name = $(this).find('option:selected').text();
        var abr = name.split("-")[0].trim();
        $("#name-"+id).val(abr);
    });

})



</script>

@endsection