require('isomorphic-fetch');
var should = require('should');
var mockery = require('mockery');
var sinon = require('sinon');
require('should-sinon');

describe('Browser: SDK init',function(){
  var Flybits;
  before(function(){
    global.window = {};
    global.navigator = {
      userAgent: 'useragent/test'
    };
    mockery.enable({
      useCleanCache: true,
      warnOnReplace: false,
      warnOnUnregistered: false
    });
    require('../../index.js');
    Flybits = window.Flybits;
    return Flybits.init();
  });
  after(function(){
    delete global.window;
    delete global.navigator;
    mockery.disable();
  });

  it('should have user agent object',function(){
    Flybits.Session.userAgent.should.be.an.Object();
  });
});