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

    .btn-sq{
        padding-top: 70px;
        padding-bottom: 70px;
    }

    .img-holder{
        width: 325px;
        height: auto;
        margin:0 auto;
    }
</style>

<div class="container" style="padding:0px;">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline-info mt-3">
                <div class="card-header">
                    <h2 class="m-b-0 text-white"><i class="mdi mdi-compass"></i> <small>Flybits Templates Manager</small></h2>
                </div>
                <div class="card-body">
                    <form id="info-form">
                        @csrf
                        <div class="row">

                            <div class="col-md-9">
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-3" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Flybits token is required">JWT:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="jwt" name="jwt" class="form-control" value="{{old('jwt')}}" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-3" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Change if connecting to a non US region hosted project">Host:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="host" name="host" class="form-control" value="https://api.demo.flybits.com" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-3" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The UUID value of the specific template to be edited">Template ID:</label>
                                    <div class="col-md-9">
                                        <input type="text" id="tempID" name="tempID" class="form-control" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button id="content" class="btn btn-lg btn-primary btn-sq" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to fetch the template for ID entered">Get Template</button>
                            </div>
                        </div>
                    </form>
                </div>
                <hr>
                <div class="card-body">

                    <div id="template-editor">
                        <form id="template-form">
                            <div class="form-group row">
                                <label class="control-label text-right col-md-2">Name:</label>
                                <div class="col-md-10">
                                    <input type="text" id="name" name="name" class="form-control" value="{{old('name')}}" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label text-right col-md-2">Description:</label>
                                <div class="col-md-10">
                                    <input type="text" id="description" name="description" class="form-control" value="{{old('description')}}" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label text-right col-md-2">Category:</label>
                                <div class="col-md-10">
                                    <input type="text" id="category" name="category" class="form-control" value="{{old('category')}}" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label text-right col-md-2">Subcategory:</label>
                                <div class="col-md-10">
                                    <input type="text" id="subcategory" name="subcategory" class="form-control" value="{{old('subcategory')}}" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label text-right col-md-2">Tags:</label>
                                <div class="col-md-10">
                                    <input type="text" id="tags" name="tags" class="form-control" value="{{old('tags')}}" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label text-right col-md-2">Image:</label>
                                <div class="col-md-10">
                                    <input type="text" id="image" name="image" class="form-control" value="{{old('image')}}" />
                                </div>
                                
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12 mx-auto">
                                    <img id="icon-img" src="" class="img-holder mx-auto d-block" style="display:none;"/>
                                </div>
                            </div>
                            <br>
                            <button id="update" class="btn btn-lg btn-primary" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Update the template details">Update Template</button>
                        </form>

                    </div>

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

     $( "#content" ).click(function( event ) {
        event.preventDefault();

        
        if($("#jwt").val() == "" || $("#tempID").val() == ""){
            
            $.toast({
                heading: 'JWT or Template ID Missing',
                text: 'You must provide a JWT for the project and template ID of the template ',
                position: 'top-center',
                loaderBg:'#FFD997',
                icon: 'warning',
                hideAfter: 5000, 
            });

        }else{

            $.ajax({
                url: '/apps/flybits/api/templates/get',
                type: "POST",
                dataType: "json",
                data: $("#info-form").serialize(),
                success: function(data){
                    
                    console.log(data);
 
                    if(data.status){

                        templateJSON = data.response;
                        var category = $(data.response.tags)[0].split("-")[1];
                        var subcategory = $(data.response.tags)[1].split("-")[1];
                        var tags = $(data.response.tags).splice(2, data.response.tags.length).toString();

                        $("#name").val(data.response.name);
                        $("#description").val(data.response.desc);
                        $("#category").val(category);
                        $("#subcategory").val(subcategory);
                        $("#tags").val(tags);
                        $("#image").val(data.response.icon);
                        $("#icon-img").attr("src", data.response.icon).show();

                        $.toast({
                            heading: 'Template Fetched',
                            text: 'Successfully fetched the template, see results below',
                            position: 'top-center',
                            loaderBg:'#2ab9c9',
                            icon: 'success',
                            hideAfter: 5000, 
                        });

                    }else{

                        $.toast({
                            heading: 'Error Occured',
                            text: 'Could not complete the Flybits API request, see console for details',
                            position: 'top-center',
                            loaderBg:'#FFD997',
                            icon: 'warning',
                            hideAfter: 5000, 
                        });

                    }
                },
                error: function(error){
                    $.toast({
                        heading: 'Error Occured',
                        text: 'Something went wrong attempting to get the template, please check JWT and ID are correct.',
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





   
     $( "#update" ).click(function( event ) {
        event.preventDefault();

        
        if($("#name").val() == "" || $("#category").val() == "" || $("#subcategory").val() == "" || $("#image").val() == ""){
            
            $.toast({
                heading: 'Template Data Missing',
                text: 'You must include a name, category, subcategory, and image at the least',
                position: 'top-center',
                loaderBg:'#FFD997',
                icon: 'warning',
                hideAfter: 5000, 
            });

        }else{

            var template = {};
            template.name = $("#name").val();
            template.description = $("#description").val();
            template.imageIcon = $("#image").val();

            var tags = [
                "category-" + $("#category").val(),
                "subcategory-" + $("#subcategory").val()
            ];

            var tagStrings = $("#tags").val().split(',');
            var tags = tags.concat(tagStrings);
            template.tags = tags;

            console.log(template);

            var data = {
                template : template,
                jwt : $("#jwt").val(),
                host : $("#host").val(),
                tempID : $("#tempID").val(),
                token : $('input[name="csrf-token"]').attr('value')
            }

            $.ajax({
                url: '/apps/flybits/api/templates/update',
                type: "POST",
                dataType: "json",
                data: data,
                success: function(data){
                    
                    console.log(data);
 
                    if(data.status){
          
                        $.toast({
                            heading: 'Template Updated',
                            text: 'Successfully updated the template!',
                            position: 'top-center',
                            loaderBg:'#2ab9c9',
                            icon: 'success',
                            hideAfter: 5000, 
                        });

                    }else{

                        $.toast({
                            heading: 'Error Occured',
                            text: 'Could not complete the Flybits API request, see console for details',
                            position: 'top-center',
                            loaderBg:'#FFD997',
                            icon: 'warning',
                            hideAfter: 5000, 
                        });

                    }
                },
                error: function(error){
                    $.toast({
                        heading: 'Error Occured',
                        text: 'Something went wrong attempting to update the template, please try again',
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

     $("#image").on("change", function(){
        $("#icon-img").attr("src", $("#image").val());
     })


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