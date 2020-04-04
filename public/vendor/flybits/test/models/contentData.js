var should = require('should');
var mockery = require('mockery');
var ModelUtil = require('../_util/model');
var ContentDataPayload = require('../_assets/contentData');

describe('Content Data Model', function(){
  var Flybits;
  before(function(){
    mockery.enable({
      useCleanCache: true,
      warnOnReplace: false,
      warnOnUnregistered: false
    });
    Flybits = require('../../index.js');
  });
  after(function(){
    mockery.disable();
  });

  it('Serializer is not symmetric for data with pagination', function(){
    ModelUtil.deepEqual(ContentDataPayload.paginated, ContentDataPayload.paginated).should.be.true();
    
    var paginatedContentData = new Flybits.ContentData(ContentDataPayload.paginated);
    ModelUtil.deepEqual(paginatedContentData.toJSON(), ContentDataPayload.paginated).should.be.false();
  });

  it('Serializer is symmetric', function(){
    ModelUtil.deepEqual(ContentDataPayload.standard, ContentDataPayload.standard).should.be.true();

    var contentData = new Flybits.ContentData(ContentDataPayload.standard);
    ModelUtil.deepEqual(contentData.toJSON(), ContentDataPayload.standard).should.be.true();
  });
  
  it('Deserializer supports paged data', function(){
    var paginatedContentData = new Flybits.ContentData(ContentDataPayload.paginated);

    paginatedContentData.payload.videos.should.be.an.instanceof(Flybits.PagedData);
  })
});