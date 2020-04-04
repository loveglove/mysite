var should = require('should');
var mockery = require('mockery');
var ModelUtil = require('./_util/model');

var getArgs = function(func) {
  // First match everything inside the function argument parens.
  var args = func.toString().match(/function\s.*?\(([^)]*)\)/)[1];

  // Split the arguments string into an array comma delimited.
  return args.split(',').map(function(arg) {
    // Ensure no inline comments are parsed and trim the whitespace.
    return arg.replace(/\/\*.*\*\//, '').trim();
  }).filter(function(arg) {
    // Ensure no undefined values are added.
    return arg;
  });
};

describe('Model Integrity',function(){
  var Flybits;
  before(function(){
    mockery.enable({
      useCleanCache: true,
      warnOnReplace: false,
      warnOnUnregistered: false
    });
    Flybits = require('../index.js');
  });
  after(function(){
    mockery.disable();
  });

  it('Content model implements interfaces',function(){
    var proto = Flybits.Content.prototype;
    var interfaces = proto._interfaces;
    for(var i = 0; i < interfaces.length; i++){
      var interface = Flybits.interface[interfaces[i]];
      if(interface){
        ModelUtil.checkProtoImplementation(proto,interface).should.be.true();
      }
    }
  });
  it('ContentData model implements interfaces',function(){
    var proto = Flybits.ContentData.prototype;
    var interfaces = proto._interfaces;
    for(var i = 0; i < interfaces.length; i++){
      var interface = Flybits.interface[interfaces[i]];
      if(interface){
        ModelUtil.checkProtoImplementation(proto,interface).should.be.true();
      }
    }
  });
  it('PagedData model implements interfaces',function(){
    var proto = Flybits.PagedData.prototype;
    var interfaces = proto._interfaces;
    for(var i = 0; i < interfaces.length; i++){
      var interface = Flybits.interface[interfaces[i]];
      if(interface){
        ModelUtil.checkProtoImplementation(proto,interface).should.be.true();
      }
    }
  });
  it('User model implements interfaces',function(){
    var proto = Flybits.User.prototype;
    var interfaces = proto._interfaces;
    for(var i = 0; i < interfaces.length; i++){
      var interface = Flybits.interface[interfaces[i]];
      if(interface){
        ModelUtil.checkProtoImplementation(proto,interface).should.be.true();
      }
    }
  });
  it('FlybitsIDP model implements interfaces',function(){
    var proto = Flybits.idp.FlybitsIDP.prototype;
    var interfaces = proto._interfaces;
    for(var i = 0; i < interfaces.length; i++){
      var interface = Flybits.interface[interfaces[i]];
      if(interface){
        ModelUtil.checkProtoImplementation(proto,interface).should.be.true();
      }
    }
  });
  it('AnonymousIDP model implements interfaces',function(){
    var proto = Flybits.idp.AnonymousIDP.prototype;
    var interfaces = proto._interfaces;
    for(var i = 0; i < interfaces.length; i++){
      var interface = Flybits.interface[interfaces[i]];
      if(interface){
        ModelUtil.checkProtoImplementation(proto,interface).should.be.true();
      }
    }
  });
  it('OAuthIDP model implements interfaces',function(){
    var proto = Flybits.idp.OAuthIDP.prototype;
    var interfaces = proto._interfaces;
    for(var i = 0; i < interfaces.length; i++){
      var interface = Flybits.interface[interfaces[i]];
      if(interface){
        ModelUtil.checkProtoImplementation(proto,interface).should.be.true();
      }
    }
  });
  it('SignedIDP model implements interfaces',function(){
    var proto = Flybits.idp.SignedIDP.prototype;
    var interfaces = proto._interfaces;
    for(var i = 0; i < interfaces.length; i++){
      var interface = Flybits.interface[interfaces[i]];
      if(interface){
        ModelUtil.checkProtoImplementation(proto,interface).should.be.true();
      }
    }
  });
});

describe('ContextPlugin Integrity',function(){
  var Flybits;
  before(function(){
    global.window = {};
    mockery.enable({
      useCleanCache: true,
      warnOnReplace: false,
      warnOnUnregistered: false
    });
    require('../index.js');
    Flybits = window.Flybits;
  });
  after(function(){
    delete global.window;
    mockery.disable();
  });

  it('Location context plugin implements interfaces',function(){
    var proto = Flybits.context.Location.prototype;
    var interfaces = proto._interfaces;
    for(var i = 0; i < interfaces.length; i++){
      var interface = Flybits.interface[interfaces[i]];
      if(interface){
        ModelUtil.checkProtoImplementation(proto,interface).should.be.true();
      }
    }
  });
  it('Connectivity context plugin implements interfaces',function(){
    var proto = Flybits.context.Connectivity.prototype;
    var interfaces = proto._interfaces;
    for(var i = 0; i < interfaces.length; i++){
      var interface = Flybits.interface[interfaces[i]];
      if(interface){
        ModelUtil.checkProtoImplementation(proto,interface).should.be.true();
      }
    }
  });
});

describe('Analytics Event Integrity', function(){
  var Flybits;
  before(function(){
    global.window = {};
    mockery.enable({
      useCleanCache: true,
      warnOnReplace: false,
      warnOnUnregistered: false
    });
    require('../index.js');
    Flybits = window.Flybits;
  });
  after(function(){
    delete global.window;
    mockery.disable();
  });

  it('Event implements interfaces', function(){
    var proto = Flybits.analytics.Event.prototype;
    var interfaces = proto._interfaces;
    for(var i = 0; i < interfaces.length; i++){
      var interface = Flybits.interface[interfaces[i]];
      if(interface){
        ModelUtil.checkProtoImplementation(proto,interface).should.be.true();
      }
    }
  });
});
