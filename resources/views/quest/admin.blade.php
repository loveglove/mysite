@extends('layouts.app')

@section('pageTitle', 'Quest')

@section('css')


@endsection


@section('content')


<div class="container" style="padding:0px;">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline-info mt-3">
                <div class="card-header">
                    <h1 class="m-b-0 text-white"><i class="ti-crown"></i> <small>Quest: Control</small></h1>
                </div>
                <div class="card-body">

      
                <div class="row mb-5">

                  <div class="col-md-6 col-12 mb-5">
                        <button id="step_1" class="btn btn-primary btn-block btn-lg step-button" 
                            data-text="Test message, do you read me loud and clear?">
                            <i class="mdi mdi-play"></i> 
                            Test Message
                        </button>
                    </div>

                    <div class="col-md-6 col-12 mb-5">
                        <button id="step_2" class="btn btn-primary btn-block btn-lg step-button" 
                            data-text="To begin your days quest, head 315m W by 145m S, 
                            Use the compass app provided by your handsome guide, 
                            and follow the big arrow on the screen until you reach 
                            your first landmark! You will receive your next update when you arrive">
                            <i class="mdi mdi-play"></i> 
                            Start Message
                        </button>
                    </div>

                    <div class="col-md-6 col-12 mb-5">
                        <button id="step_3" class="btn btn-primary btn-lg btn-block step-button" 
                            data-text="You made it to the bridge! congratulations you're quite the adventurous one. 
                            There are two bridges ahead. If you can answer 2 of the following 3 questions correctly, 
                            you can win a small treasure.">
                            <i class="mdi mdi-play"></i>
                            The Bridge Message
                        </button>
                        <button id="step_4" class="btn btn-primary btn-lg btn-block step-button" 
                            data-text='🔘 Q1) What is the name of the bridge? 
🔘 Q2) Which bridge is longer?
🔘 Q3) Name 2 of the 3 parks they connect.'>
                            <i class="mdi mdi-play"></i>
                            Bridge Questions
                        </button>
                        <p>Answers: 1) Garrison Crossing 2) North 52m (South 49m) 3) Ordinance, South Stanely, Fort York Grounds </p>
                    </div>


                    <div class="col-md-6 col-12 mb-5">
                        <button id="step_5" class="btn btn-primary btn-lg btn-block step-button" 
                            data-text="Riding the wheels of steel 🚲 can be a daunting task.. so lets spend some time getting use to it. When you're ready... record how many meters you covered with your feet off the ground. For every 5 meters earn $1 dollar on a gift card to amazon. 💵"
                            <i class="mdi mdi-play"></i>
                            Bike Practise
                        </button>
                        <p>Answers: 5m = 1 dollar  (distance approx 100m)</p>
                    </div>


                    <div class="col-md-6 col-12 mb-5">
                        <button id="step_6" class="btn btn-primary btn-lg btn-block step-button" 
                            data-text="🏁 For the next part you will need to travel some distance on your quest. During this time you can earn more small treasures ⭐ and maybe even a big treasure too 🎁. Here's what to look on for and snap a photo of when you find it.🎥"
                            <i class="mdi mdi-play"></i>
                            Treasure Hunt Message
                        </button>
                        <button id="step_7" class="btn btn-primary btn-lg btn-block step-button" 
                            data-text="🔘 If you can collect 15 business names each starting with a unique letter of the alphabet you earn a small treasure ⭐, if you can get all 26 you get a large treasure 🎁.
🔘 If you find evidence of various cultures, capture a word of each language and if you get 5 unique languages you win a small prize ⭐ (english NOT included)
🔘 Snap a photo of 5 different animals a long the way and earn a small prize ⭐"
                            <i class="mdi mdi-play"></i>
                            Treasure Hunt Rules
                        </button>
                        <p> 🔘 If you can collect 15 business names each starting with a unique letter of the alphabet you earn a price, if you can get all 26 you get a large prize.<br><br>
🔘 If you find evidence of various cultures, show me a word of each language and if you get 5 unique languages you win a small prize (english not included). <br><br>
🔘 Snap a photo of 5 different animals (bird, squirrel, dog, cat)  small prize
</p>

                        <button id="step_8" class="btn btn-primary btn-lg btn-block step-button" 
                            data-text="🚲 Bike Bonus: For each major water front park past on bike, earn a small prize ⭐
🚶 Walking Bonus: for Every 10 steps logged down the waterfron path, earn 1 amazon gift card dollar 💵"
                            <i class="mdi mdi-play"></i>
                            Bonus Plays
                        </button>
                         <p>🔘 Bike Bonus: For each park earn a small gift
🔘 Walking Bonus: for Every 10 steps earn 1 amazon dollar (only on queens quay)
</p>
                    </div>


                    <div class="col-md-6 col-12 mb-5">
                        <button id="step_9" class="btn btn-primary btn-lg btn-block step-button" 
                            data-text="It is time to replenish! 🍴🍪🍓 Use the compass app to find our next plotted destination and enjoy a drink, a snack. And the view! But you’ll have to find me first!"
                            <i class="mdi mdi-play"></i>
                            PicNic Message
                        </button>
                        <p>Use GPS</p>
                    </div>


                    <div class="col-md-6 col-12 mb-5">
                        <button id="step_10" class="btn btn-primary btn-lg btn-block step-button" 
                            data-text="Time to find Time. Head north east to old cobled streets and locate an antique 4 sided clock 🕑. By the TIME you find your way there I'm sure you will be THIRSTY 🍻"
                            <i class="mdi mdi-play"></i>
                            Distilery
                        </button>
                        <p>Use GPS</p>
                    </div>


                    <div class="col-md-6 col-12 mb-5">
                        <button id="step_11" class="btn btn-primary btn-lg btn-block step-button" 
                            data-text="What year did the Gotterham and Worts factory shut down? Guess a year within +/- one decade on the correct answer and win a small treasure ⭐. Guess a year within the same decade as the actual answer, and win large treasure 🎁"></i>
                            Clock Trivia
                        </button>
                        <p>Answer: 1990</p>
                        <button id="step_12" class="btn btn-primary btn-lg btn-block step-button" 
                            data-text="The correct answer is 1990.  Gooderham and Worts was convicted of tax evasion in 1928 and had to pay a fine of $439,744. In 1987, the firm was sold to the British concern Allied Lyons. By 1990, the manufacturing facility had been closed down because it was obsolete"></i>
                            Clock Answer
                        </button>
                    </div>


                     <div class="col-md-6 col-12 mb-5">
                        <button id="step_13" class="btn btn-primary btn-lg btn-block step-button" 
                            data-text="Your quest is nearly complete, head due West to some of the worlds finest foods 🍤 and collect your provisions for your adventerours feast this evening 🍖. Safe Travels 🗻"
                            <i class="mdi mdi-play"></i>
                            End
                        </button>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>



@endsection


@section('scripts')

<script>

    
$(".step-button").on('click', function(event){

    var textmsg = $(this).data("text");
    var step = $(this).attr('id').split("_")[1];

    console.log("Step " + step +" button clicked with message: " + textmsg);

    $.ajax({
        url:'/apps/quest/sms',
        data: { "text" : textmsg },
        success:function(data){
            
            console.log("Message Sent");
            
            $.toast({
                heading: 'STEP ' + step + ' DELIVERED',
                text: 'Your quest sms for step ' +step+ ' was sent to the number',
                position: 'top-right',
                loaderBg:'#2ab9c9',
                icon: 'success',
                hideAfter: 10000, 
                stack: 3
            });

        }
    });


});



</script>


@endsection