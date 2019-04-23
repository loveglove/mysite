// init the controller
var controller = new ScrollMagic({
    globalSceneOptions: {
        triggerHook: "onLeave"
    }
});

	// var test = new TimelineMax()
	// 	.add([TweenMax.fromTo("#parallaxTest .layer1", 1, {scale:3,autoAlpha:0.05, left:300},{left:-350, ease:Linear.easeNone}),
	// 			TweenMax.fromTo("#parallaxTest .layer2", 1, {scale:2,autoAlpha:0.3, left:150},{left:-175, ease:Linear.easeNone}),
	// 	]);
 //   var testscene = new ScrollScene({
 //        triggerElement: "#trigger2",
 //        duration: $(window).width()
 //   })
 //   .setTween(test)
 //   .addTo(controller);


	// ----- Title and About Animations ------
    var pinsection = new TimelineMax()
        .add(TweenMax.to("#wipe", 2, {transform: "translateY(0)"}))
        .add(TweenMax.to("#projects", 2, {transform: "translateY(0)"}));

    // section pin
    var scene0 = new ScrollScene({
        triggerElement: "section#pin",
        duration: 1100
    })
    .setTween(pinsection)
    .setPin("section#pin")
    .addTo(controller);


	// ----- Experience ------
	var tl1 = new TimelineMax()
	   // rotate icon
	   .add(TweenMax.to('#animation-1', 0.5, {
	        backgroundColor: 'rgb(200, 30, 50)',
	        scale: 0.5,
	        rotation: 360
	    }))
	   //fade in text
	   .add([
	   	TweenMax.from("#scene-1-title", 1, {autoAlpha: 0}),
	      TweenMax.to("#scene-1-title", 1, {autoAlpha: 1}),
	   ])
   var sc1 = new ScrollScene({
         triggerElement: '#scene-1',
         offset: -200,
     	})
     	.setClassToggle('#projects', 'scene-1-active')
     	.setTween(tl1)
     	.addTo(controller);


	// ----- Projects ------
   var tl2 = new TimelineMax()
	   // rotate icon
	   .add(TweenMax.to('#animation-2', 0.5, {
	        backgroundColor: 'rgb(255, 50, 0)',
	        scale: 0.5,
	        rotation: 360
	    }))
	   //fade in text
	   .add([
	   	TweenMax.from("#scene-2-title", 1, {autoAlpha: 0}),
	      TweenMax.to("#scene-2-title", 1, {autoAlpha: 1}),
	   ])
   var sc2 = new ScrollScene({
         triggerElement: '#scene-2',
         offset: -200,
     	})
     	.setClassToggle('#projects', 'scene-2-active')
     	.setTween(tl2)
     	.addTo(controller);


	// ----- Music ------
   var tl3 = new TimelineMax()
	   // rotate icon
	   .add(TweenMax.to('#animation-3', 0.5, {
	        backgroundColor: 'rgb(255, 160, 46)',
	        scale: 0.5,
	        rotation: 360
	    }))
	   //fade in text
	   .add([
	   	TweenMax.from("#scene-3-title", 1, {autoAlpha: 0}),
	      TweenMax.to("#scene-3-title", 1, {autoAlpha: 1}),
	   ])
   var sc3 = new ScrollScene({
         triggerElement: '#scene-3',
         offset: -200,
     	})
     	.setClassToggle('#projects', 'scene-3-active')
     	.setTween(tl3)
     	.addTo(controller);


	// ----- Thoughts ------
   var tl4 = new TimelineMax()
	   // rotate icon
	   .add(TweenMax.to('#animation-4', 0.5, {
	        backgroundColor: 'rgb(10, 180, 60)',
	        scale: 0.5,
	        rotation: 360
	    }))
	   //fade in text
	   .add([
	   	TweenMax.from("#scene-4-title", 1, {autoAlpha: 0}),
	      TweenMax.to("#scene-4-title", 1, {autoAlpha: 1}),
	   ])
   var sc4 = new ScrollScene({
         triggerElement: '#scene-4',
         offset: -200,
     	})
     	.setClassToggle('#projects', 'scene-4-active')
     	.setTween(tl4)
     	.addTo(controller);





