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
        float: right;
        padding-top: 65px;
        padding-bottom: 65px;
    }

    .img-holder{
        width: 350px;
        height: auto;
        margin:0 auto;
    }
    #update{
        float: right;
    }
    .card-header{
/*        background-color: #7469EF !important;
*/   }
    .bs1{
        box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
    }


    .fb-logo{
        width:30px;
        height:auto;
        margin-right:10px;
    }
</style>

<div class="container" style="padding:0px;">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline-info mt-3">

            <div class="card-header">
                    <h2 class="m-b-0 text-white"><img src="{{ asset('images/Flybits-icon-large-trans_WHITE-ALL.png') }}" class="fb-logo" /><small>Flybits Exeperience Template Manager</small></h2>
                </div>
                <!-- credentials -->
                <div class="card-body">
                    <form class="input-form">
                        <div class="form-group row">
                            <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="A valid JWT of an admin user from a project you wish to create templates in">JWT:</label>
                            <div class="col-md-10">
                                <input type="text" id="jwt" name="jwt" class="form-control" value="{{old('jwt')}}" onkeyup='saveValue(this);' />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Change only if not using the demo stack">API Host:</label>
                            <div class="col-md-10">
                                <input type="text" id="host" name="host" class="form-control" value="https://api.demo.flybits.com" />
                            </div>
                        </div>
                    </form>
                    <hr>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link d-flex active" data-toggle="tab" href="#edit" role="tab" aria-selected="false">
                          <span>Edit</span>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link d-flex" data-toggle="tab" href="#create" role="tab" aria-selected="false">
                          <span>Create</span>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link d-flex" data-toggle="tab" href="#delete" role="tab" aria-selected="false">
                          <span>Delete</span>
                        </a>
                      </li>
                    </ul>
                </div>

                <!-- start tabbed content -->
                <div class="tab-content">

                    <!-- start tab 1 -->
                    <div id="edit" class="tab-pane active" role="tabpanel">

                        <div class="card-body">
                            <form id="info-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="form-group row">
                                            <label class="control-label text-right col-md-3" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The UUID value of the specific template to be edited">Template ID:</label>
                                            <div class="col-md-9">
                                                <input type="text" id="tempID" name="tempID" class="form-control" value="" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button id="get-template-btn" class="btn btn-lg  btn-outline-info" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Click to fetch the template for ID entered"><i class="icon-wrench"></i>&nbsp&nbspGet Template</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div id="edit-area" class="card-body" style="display:none;">
                            <div id="template-editor">
                                <form id="template-form">
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The template name to be displayed in the library">Name:</label>
                                        <div class="col-md-10">
                                            <input type="text" id="name" name="name" class="form-control" value="{{old('name')}}" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The description of the template displayed in the library">Description:</label>
                                        <div class="col-md-10">
                                            <input type="text" id="description" name="description" class="form-control" value="{{old('description')}}" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The category keyword the template will belong to">Category:</label>
                                        <div class="col-md-10">
                                            <input type="text" id="category" name="category" class="form-control" value="{{old('category')}}" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The subcategory keyword the template will display under">Subcategory:</label>
                                        <div class="col-md-10">
                                            <input type="text" id="subcategory" name="subcategory" class="form-control" value="{{old('subcategory')}}" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Any filter tags to associate with the template (comma separated words)">Tags:</label>
                                        <div class="col-md-10">
                                            <input type="text" id="tags" name="tags" class="form-control" value="{{old('tags')}}" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The template banner image">Image:</label>
                                        <div class="col-md-10">
                                            <input type="text" id="image" name="image" class="form-control" value="{{old('image')}}" />
                                        </div>
                                        
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-12 mx-auto">
                                            <img id="icon-img" src="" class="img-holder mx-auto d-block bs1" style="display:none;"/>
                                        </div>
                                    </div>
                                    <hr>
                                    <br>
                                    <div class="form-group row" style="margin-left:70px;">
                                        <div class="col-md-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="rule" value="rule">
                                                <label class="form-check-label" for="rule">Copy Rule</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="actions" value="actions">
                                                <label class="form-check-label" for="actions">Copy Actions</label>
                                            </div>
                                        </div> 
                                        <div class="col-md-8"> 
                                            <div class="row">
                                                <label class="control-label text-left col-md-5">From Experience Instance ID:</label>
                                                <div class="col-md-7 text-left">
                                                    <input type="text" id="instance" name="instance" class="form-control" value="" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="form-group row">
                                        <div class="col-md-12" >
                                            <button id="update-btn" class="btn btn-lg btn-outline-info" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to update the template details" style="float:right;"><i class="icon-magic-wand"></i>&nbsp&nbspUpdate Template</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- end tab 1 -->

                    <!-- start tab 2 -->
                    <div id="create" class="tab-pane" role="tabpanel">
                        <div class="card-body">
                            <form id="create-form">
                                @csrf
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The UUID value of the example experience (in same project) to be converted to a template. Leave this field blank to create an empty template">Instance ID:</label>
                                    <div class="col-md-10">
                                        <input type="text" id="instance2" name="instance" class="form-control" value="" />
                                    </div>
                                </div>
                                              
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The template name to be displayed in the library">Name:</label>
                                    <div class="col-md-10">
                                        <input type="text" id="name2" name="name" class="form-control" value="{{old('name')}}" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The description of the template displayed in the library">Description:</label>
                                    <div class="col-md-10">
                                        <input type="text" id="description2" name="description" class="form-control" value="{{old('description')}}" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The category keyword the template will belong to">Category:</label>
                                    <div class="col-md-10">
                                        <input type="text" id="category2" name="category" class="form-control" value="{{old('category')}}" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The subcategory keyword the template will display under">Subcategory:</label>
                                    <div class="col-md-10">
                                        <input type="text" id="subcategory2" name="subcategory" class="form-control" value="{{old('subcategory')}}" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Any filter tags to associate with the template (comma separated words)">Tags:</label>
                                    <div class="col-md-10">
                                        <input type="text" id="tags2" name="tags" class="form-control" value="{{old('tags')}}" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label text-right col-md-2" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The template banner image">Image:</label>
                                    <div class="col-md-10">
                                        <input type="text" id="image2" name="image" class="form-control" value="{{old('image')}}" />
                                    </div>
                                    
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12 mx-auto">
                                        <img id="icon-img2" src="" class="img-holder mx-auto d-block bs1" style="display:none;"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <button id="create-btn" class="btn btn-lg btn-outline-info"  style="float:right;" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to create a new template from the instance"><i class="icon-magic-wand"></i>&nbsp&nbspCreate New Template</button>
                                    </div>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                    <!-- end tab 2 -->

                    <!-- start tab 3 -->
                    <div id="delete" class="tab-pane" role="tabpanel">
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="form-group row">
                                            <label class="control-label text-right col-md-3" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="The UUID value of the specific template to be edited">Template ID:</label>
                                            <div class="col-md-9">
                                                <input type="text" id="tempID-del" name="tempID" class="form-control" value="" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button id="delete-template-btn" class="btn btn-lg btn-outline-danger" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Click to permanently delete the template for ID entered"><i class="icon-trash"></i>&nbsp&nbspDelete Template</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- end tab 3 -->

                </div>
                <!-- end tabbed content -->

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



     $("#get-template-btn").click(function( event ) {
        
        event.preventDefault();
        
        if($("#jwt").val() == ""){
            
            $.toast({
                heading: 'JWT Missing',
                text: 'You must provide a valid JWT from a Flybits project first',
                position: 'top-right',
                loaderBg:'#FFD997',
                icon: 'warning',
                hideAfter: 5000, 
            });

        }else{

            if($("#tempID").val() == ""){
            
                $.toast({
                    heading: 'Template ID Missing',
                    text: 'You must provide a the ID of a template you wish to edit',
                    position: 'top-right',
                    loaderBg:'#FFD997',
                    icon: 'warning',
                    hideAfter: 5000, 
                });

            }else{

                var formData = $("#info-form").serializeArray();
                formData.push({name: 'jwt', value: $("#jwt").val()});
                formData.push({name: 'host', value: $("#host").val()});

                console.log(formData);

                $.ajax({
                    url: '/apps/flybits/api/templates/get',
                    type: "POST",
                    dataType: "json",
                    data: formData,
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
                                position: 'top-right',
                                loaderBg:'#2ab9c9',
                                icon: 'success',
                                hideAfter: 5000, 
                            });

                            $("#edit-area").show();

                        }else{

                            $.toast({
                                heading: 'API Error Occured',
                                text: 'Flybits API response: ' + data.message,
                                position: 'top-right',
                                loaderBg:'#FFD997',
                                icon: 'warning',
                                hideAfter: 5000, 
                            });

                        }
                    },
                    error: function(error){
                        $.toast({
                            heading: 'Update Error Occured',
                            text: 'Something went wrong attempting to get the template, please check JWT, Host, and ID are correct.',
                            position: 'top-right',
                            loaderBg:'#FFD997',
                            icon: 'warning',
                            hideAfter: 5000, 
                        });
                        console.log(error);
                    }
                }); 
            }
        }
    });






   
     $("#update-btn").click(function( event ) {
        event.preventDefault();
        
        if($("#name").val() == "" || $("#category").val() == "" || $("#subcategory").val() == "" || $("#image").val() == ""){
            
            $.toast({
                heading: 'Template Data Missing',
                text: 'You must include a name, category, subcategory, and image at the very least',
                position: 'top-right',
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

            var data = {
                template : template,
                jwt : $("#jwt").val(),
                host : $("#host").val(),
                tempID : $("#tempID").val(),
                token : $('input[name="csrf-token"]').attr('value'),
                rule : $("#rule").is(":checked"),
                actions: $("#actions").is(":checked"),
                instance: $("#instance").val()
            }
            
            console.log("SUBMIT DATA...");
            console.log(data);

            $.ajax({
                url: '/apps/flybits/api/templates/update',
                type: "POST",
                dataType: "json",
                data: data,
                success: function(data){
                    
                    console.log("RETURN FROM BACKEND..");
                    console.log(data);
 
                    if(data.status){
          
                        $.toast({
                            heading: 'Template Updated',
                            text: 'Successfully updated the template!',
                            position: 'top-right',
                            loaderBg:'#2ab9c9',
                            icon: 'success',
                            hideAfter: 5000, 
                        });

                    }else{

                        $.toast({
                            heading: 'API Error Occured',
                            text: 'Flybits API response: ' + data.message,
                            position: 'top-right',
                            loaderBg:'#FFD997',
                            icon: 'warning',
                            hideAfter: 5000, 
                        });

                    }
                },
                error: function(error){
                    $.toast({
                        heading: 'Update Error Occured',
                        text: 'Something went wrong attempting to update the template, please try again',
                        position: 'top-right',
                        loaderBg:'#FFD997',
                        icon: 'warning',
                        hideAfter: 5000, 
                    });
                   console.log(error);
                }
            }); 

        }

    });






    $("#create-btn").click(function( event ) {
        event.preventDefault();
        
        if($("#name2").val() == "" || $("#category2").val() == "" || $("#subcategory2").val() == "" || $("#image2").val() == ""){
            
            $.toast({
                heading: 'Template Data Missing',
                text: 'You must include a name, category, subcategory, and image at the least',
                position: 'top-right',
                loaderBg:'#FFD997',
                icon: 'warning',
                hideAfter: 5000, 
            });

        }else{


            var tags = [
                "category-" + $("#category2").val(),
                "subcategory-" + $("#subcategory2").val()
            ];

            var tagStrings = $("#tags2").val().split(',');
            var tags = tags.concat(tagStrings);

            var data = {
                jwt : $("#jwt").val(),
                host : $("#host").val(),
                instance : $("#instance2").val(),
                token : $('input[name="csrf-token"]').attr('value'),
                instance: $("#instance2").val(),
                name : $("#name2").val(),
                description: $("#description2").val(),
                image : $("#image2").val(),
                tags : tags
            }
            
            console.log("SUBMIT DATA...");
            console.log(data);

            $.ajax({
                url: '/apps/flybits/api/templates/create',
                type: "POST",
                dataType: "json",
                data: data,
                success: function(data){
                
                    console.log(data);
 
                    if(data.status){
          
                        $.toast({
                            heading: 'Template Create',
                            text: 'Successfully create a new template!',
                            position: 'top-right',
                            loaderBg:'#2ab9c9',
                            icon: 'success',
                            hideAfter: 5000, 
                        });

                    }else{

                        $.toast({
                            heading: 'API Error Occured',
                            text: 'Flybits API response: ' + data.message,
                            position: 'top-right',
                            loaderBg:'#FFD997',
                            icon: 'warning',
                            hideAfter: 5000, 
                        });

                    }
                },
                error: function(error){
                    $.toast({
                        heading: 'Create Error Occured',
                        text: 'Something went wrong attempting to create the template, please try again',
                        position: 'top-right',
                        loaderBg:'#FFD997',
                        icon: 'warning',
                        hideAfter: 5000, 
                    });
                   console.log(error);
                }
            }); 

        }

    });


    


    $("#delete-template-btn").click(function( event ) {
        
        event.preventDefault();

        if($("#tempID-del").val() == ""){
            
            $.toast({
                heading: 'Template ID Missing',
                text: 'You must provide a the ID of a template you wish to delete',
                position: 'top-right',
                loaderBg:'#FFD997',
                icon: 'warning',
                hideAfter: 5000, 
            });

        }else{

            var tempID = $("#tempID-del").val();

            var data = {
                jwt : $("#jwt").val(),
                host : $("#host").val(),
                tempID : tempID,
                token : $('input[name="csrf-token"]').attr('value')
            }

            $.ajax({
                url: '/apps/flybits/api/templates/delete',
                type: "POST",
                dataType: "json",
                data: data,
                success: function(data){
                    
                    console.log(data);
                    if(data.status){
        
                        $.toast({
                            heading: 'Template Deleted',
                            text: 'The template has been permanently deleted',
                            position: 'top-right',
                            loaderBg:'#2ab9c9',
                            icon: 'success',
                            hideAfter: 5000, 
                        });

                    }else{

                        $.toast({
                            heading: 'API Error Occured',
                            text: 'Flybits API response: ' + data.message,
                            position: 'top-right',
                            loaderBg:'#FFD997',
                            icon: 'warning',
                            hideAfter: 5000, 
                        });

                    }
                },
                error: function(error){
                    $.toast({
                        heading: 'Update Error Occured',
                        text: 'Something went wrong attempting to delete the template, please check JWT, Host, and ID are correct.',
                        position: 'top-right',
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

     $("#image2").on("change", function(){
        $("#icon-img2").attr("src", $("#image2").val());
     })

    function saveValue(e){
        var id = e.id;
        var val = e.value;
        localStorage.setItem(id, val);
    }

    function getSavedValue  (v){
        if (!localStorage.getItem(v)) {
            return "";
        }
        return localStorage.getItem(v);
    }

    $(function () {
    
        $('[data-toggle="popover"]').popover();
        document.getElementById("jwt").value = getSavedValue("jwt");    // set the value to this input

    })



</script>

@endsection