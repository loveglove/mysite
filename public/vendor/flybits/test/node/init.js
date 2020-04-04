var should = require('should');
var mockery = require('mockery');

describe('Node: SDK init',function(){
  var Flybits;
  before(function(){
    mockery.enable({
      useCleanCache: true,
      warnOnReplace: false,
      warnOnUnregistered: false
    });
    Flybits = require('../../index.js');
    return Flybits.init();
  });
  after(function(){
    mockery.disable();
  });

  it('should have user agent object',function(){
    Flybits.Session.userAgent.should.be.an.Object();
  });
});
