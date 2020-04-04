@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs profile-tab" role="tablist">
                    <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#settings" role="tab" aria-expanded="true">Loft Bot</a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#log" role="tab" aria-expanded="false">Entry Log</a> </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- first tab -->
                    <div class="tab-pane active" id="settings" role="tabpanel" aria-expanded="true">
                        <div class="card-body">
                            <p> Loft Bot Settings </p>
                        </div>
                    </div>
                    <!-- end first tab -->
                    <!--second tab-->
                    <div class="tab-pane" id="log" role="tabpanel" aria-expanded="false">
                        <div class="card-body">
                            <p> Door Entry Table </p>
                        </div>
                    </div>
                    <!-- end second tab -->
                </div>
                <!-- end Nav tabs -->
            </div>
        </div>

        <div id="content-area" class="col-lg-4 col-xlg-3 col-md-5">

            <div class="card">
                <div class="card-body">
                    <center class="m-t-30"> <img src="{{ Auth::user()->avatar }}" class="img-circle" width="150">
                        <h4 class="card-title m-t-10">{{ Auth::user()->name }}</h4>
                        <h6 class="card-subtitle">Accoubts Manager Amix corp</h6>
                        <div class="row text-center justify-content-md-center">
                            <div class="col-4"><a href="javascript:void(0)" class="link"><i class="icon-people"></i> <font class="font-medium">254</font></a></div>
                            <div class="col-4"><a href="javascript:void(0)" class="link"><i class="icon-picture"></i> <font class="font-medium">54</font></a></div>
                            <div class="col-4"><a href="javascript:void(0)" class="link"><i class="icon-people"></i> <font class="font-medium">54</font></a></div>
                        </div>
                    </center>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title m-t-10">Content Title</h4>
                    <h6 class="card-subtitle">Content Subtitle</h6>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js"></script> 
<script src="/vendor/flybits/dist/flybits.js"></script>
<script>

Flybits.init({
  HOST: '//v3.flybits.com',
  CTXREPORTDELAY: 5000,
}).then(function(cfg){
    /** cfg is the initialized state of the SDK configuration.
      It is not required to be used, it is only returned for your convenience.*/

    // Anonymous login 
    var idp = new Flybits.idp.AnonymousIDP({
        projectID: 'B0757CAF-56E1-450B-A994-B9000EA97CA9'
    });
    Flybits.Session.connect(idp);

    //start working with SDK
    Flybits.api.Content.getAll().then(function(resultObj){

        console.log(resultObj.result);
		// assuming there is a Content instance in the response
		// var contentInstance = resultObj.result[0];
		// retrieve JSON body

		resultObj.result.forEach(function(contentInstance){
			var content = contentInstance.getData();
			var title = '';
			var subtitle = '';
			console.log(content);
			// switch(content.type) {
			//   case "event-information":
			//     	console.log(content);
			//     break;
			//   case "articles":
			//     	console.log(content);
			//     break;
			//  case "actionable-offer":
			//     	console.log(content);
			//     break;
			// 	default:
			//     console.log("More content found with no renderable type");
			// }

			// var html = `<div class="card">
			//                 <div class="card-body">
			//                     <h4 class="card-title m-t-10">`+ title + `</h4>
			//                     <h6 class="card-subtitle">` + subtitle + `</h6>
			//                 </div>
			//             </div>`;
			// $("#content-area").append(html);
		});

    }).then(function(obj){
        // do stuff with body of Content instance.
        console.log(obj);
    });

}).catch(function(validationObj){
  // handle error
  console.log(validationObj);
});

</script>

@endsection