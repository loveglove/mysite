// ----------- Init the Controller ---------------
var controller = new ScrollMagic({
    globalSceneOptions: {
        triggerHook: "onLeave"
    }
});


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



//we set visibility:hidden in the CSS to avoid an initial flash - make them visible now, but the from() tweens are going to essentially hide them anyway because their stroke position/length will be 0.
TweenLite.set(".gray-line, .green-line, .green-line-thin, .plugin-stroke", {visibility:"visible"});

var tl = new TimelineLite({onUpdate:updateSlider}),
    $slider = $("#slider");

//animate the plugin text first, drawing to 100%
tl.from(".plugin-stroke", 4, {drawSVG:0, ease:Power1.easeInOut})
  //now animate the logo strokes (note we use "102% as FireFox 34 miscalculates the length of a few strokes)
  .fromTo(".gray-line, .green-line, .green-line-thin", 3, {drawSVG:0}, {drawSVG:"102%"}, "-=1")
  //fade in the real logo and the rest of the text
  .to("#Plugin, #ByGreenSock, #logo", 1, {autoAlpha:1, ease:Linear.easeNone})
  //hide the logo strokes that are now obscured by the real logo (just to improve rendering performance)
  .set("#lines", {visibility:"hidden"});

//--- SLIDER ---
$slider.slider({
  range: false,
  min: 0,
  max: 100,
  step: 0.02,
  value:0,
  slide: function ( event, ui ) {
    tl.progress( ui.value / 100 ).pause();
  }
});
function updateSlider() {
	$slider.slider("value", tl.progress() * 100);
}
var $replayIcon = $("#replayIcon"),
    $replay = $("#replay").mouseenter(function(){
      TweenLite.to($replayIcon, 0.4, {rotation:"+=360"});
      TweenLite.to($replay, 0.4, {opacity:1});
    }).mouseleave(function(){
      TweenLite.to($replay, 0.4, {opacity:0.65});
    }).click(function(){
