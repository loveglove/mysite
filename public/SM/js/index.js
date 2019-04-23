// init the controller
var controller = new ScrollMagic({
    globalSceneOptions: {
        triggerHook: "onLeave"
    }
});


// pinani
var pinani = new TimelineMax()
    // panel wipe uno
    .add(TweenMax.to("#wipe", 2, {transform: "translateY(0)"}))

    // panel wipe dos
    .add(TweenMax.to("#second-wipe", 2, {transform: "translateY(0)"}))

    // panel slide bounce
    .add(TweenMax.to("#slide", 2, {top: "0%", ease: Bounce.easeOut, delay: 0.2}))

    // wider
    .add([
        TweenMax.to("#wider", 2, {width: "+=800px"}),
        TweenMax.from("#wider", 0.1, {autoAlpha: 0}),
    ])

    // panel slide color
    .add([
        TweenMax.to("#wider", 0.2, {autoAlpha: 0}),
        TweenMax.to("#slide h3:first-child", 0.2, {autoAlpha: 0}),
        TweenMax.from("#slide h3:last-child", 0.2, {autoAlpha: 0})
    ])
    .add([
        TweenMax.to("#slide", 0.3, {backgroundColor: "yellow"}),
        TweenMax.to("#slide h3:last-child", 0.3, {color: "blue"})
    ])
    .add([
        TweenMax.to("#slide", 0.3, {backgroundColor: "green"}),
        TweenMax.to("#slide h3:last-child", 0.3, {color: "red"})
    ])
    .add([
        TweenMax.to("#slide", 0.3, {backgroundColor: "red"}),
        TweenMax.to("#slide h3:last-child", 0.3, {color: "white"})
    ])
    .add([
        TweenMax.to("#slide", 0.3, {backgroundColor: "#c7e1ff"}),
        TweenMax.to("#slide h3:last-child", 0.3, {color: "black"})
    ])

    // panel slide translateX
    .add(TweenMax.to("#slide-dos", 2, {transform: "translateX(0)"}))

    // panel unpinned
    .add(TweenMax.from("#unpin", 2, {top: "100%"}));


// panel section pin
new ScrollScene({
        triggerElement: "section#pin",
        duration: 1100
    })
    .setTween(pinani)
    .setPin("section#pin")
    .addTo(controller);