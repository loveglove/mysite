// @author Justin Lam
// @version v2:3bc3fc0
;(function(undefined) {

/**
 * ES6 Promise object.
 * @external Promise
 * @see {@link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise|Promise API}
 * @see {@link http://www.ecma-international.org/ecma-262/6.0/#sec-promise-objects|Promise spec}
 */

/**
 * ISO 8601 date format.
 * @external ISO 8601
 * @see {@link https://en.wikipedia.org/wiki/ISO_8601| ISO 8601 spec}
 */

/**
 * This is the root namespace for the Flybits SDK.  If included in a browser environment it will be accessible from the `window` object.
 * @namespace
 */
var Flybits = {
  /**
   * Promise that resolves when the SDK has completed initialization.
   * @memberof Flybits
   * @member {external:Promise}
   */
  ready: null,
  /**
   * SDK state related utilities.
   * @namespace
   */
  store: {
    Property: {},
    Record: {}
  },
  /**
   * Reporting manager to facilitate the reporting of application events.
   * @namespace
   * @memberof Flybits
   */
  analytics: {},
  /**
   * Utility classes and reporting manager for collecting and reporting context about a user.
   * @namespace
   * @memberof Flybits
   */
  context: {},
  /**
   * Flybits API wrappers for core models
   * @namespace
   */
  api: {
    /**
     * Object that represents the paging status of a API query.
     * @typedef Paging
     * @memberof Flybits.api
     * @type Object
     * @property {number} offset Offset from first element of first page.
     * @property {number} limit Number of results per page.
     * @property {number} total Total number of objects that is available to page over.
     */
     /**
      * Result Object that is returned from a successful API query if the query is for multiple models.
      * @typedef Result
      * @memberof Flybits.api
      * @type Object
      * @property {BaseModel[]} result Resultant list of models from API request.
      * @property {function} [nextPageFn] If the resultant list of models is only a subset of total records, that is to say the paged result has additional pages, this function can be invoked to request the next page of results based on the initial query parameters.  If this is `undefined` there are no more pages to view.
      */
  },
  initFile: {},
  /**
   * Utilities for various aspects of the SDK
   * @namespace
   */
  util: {},
  /**
   * Interfaces to ensure SDK model integrity.
   * @namespace
   */
  interface: {},
  /**
   * Identity providers available for authentication.
   * @namespace
   */
  idp: {}
};

//defaults
Flybits.cfg = {
  HOST: 'http://api.flybits.com',
  CTXREPORTDELAY: 60000,
  analytics: {
    REPORTDELAY: 3600000,
    REPORTDELAYUNIT: 'milliseconds',
    MAXSTORESIZE: 100
  },
  store: {
    SDKPROPS: 'flb.sdk.properties',
    RESOURCEPATH: "./res",
    DEVICEID: 'flb_device',
    USERTOKEN: 'flb_usertoken',
    USER: 'flb_user',
    USERTOKENEXP: 'flb_usertoken_expiry',
    PROJECTID: 'flb_projectid',
    IDPTYPE: 'flb_idptype',
    ANALYTICSLASTREPORTED: 'flb_analytics_lastreported',
    ANALYTICSLASTREPORTATTEMPTED: 'flb_analytics_lastreportattempted',
    CONTEXTLASTREPORTED: 'flb_context_lastreported',
    CONTEXTLASTREPORTATTEMPTED: 'flb_context_lastreportattempted',
  },
  res: {
    CONTEXT: '/context/ctxdata',
    UNAUTHENTICATE: '/sso/auth/logout',
    AUTHENTICATE: '/sso/auth/authenticate',
    ANONYMOUSAUTH: '/sso/auth/anonymous',
    OAUTH: '/sso/auth/oauth',
    SIGNEDLOGIN: '/sso/auth/signedLogin',
    REFRESHAUTH: '/sso/auth/refreshToken',
    AUTHUSER: '/sso/auth/me',
    AUTHPROJECT: '/sso/auth/project',
    REQRESETEMAIL: '/sso/auth/sendResetPasswordEmail',
    REQCONFIRMEMAIL: '/sso/auth/sendConfirmEmail',
    CONTENTS: '/kernel/content/instances',
    RELEVANTCONTENTS: '/kernel/experiences/contents'
  },
};

/**
 * @memberof Flybits
 * @member {string} VERSION SDK version string
 */
Flybits.VERSION = "v2:3bc3fc0";

var initBrowserFileConfig = function(url){
  var def = new Flybits.Deferred();

  fetch(url).then(function(resp){
    if(resp.status !== 200){
      throw new Flybits.Validation().addError("Configuration file not found","Reverting to default configuration. No configuration found at:"+url,{
        code: Flybits.Validation.type.INVALIDARG,
        context: 'url'
      });
    }
    return resp.json();
  }).then(function(json){
    Flybits.cfg = Flybits.util.Obj.extend(Flybits.cfg,json);
    def.resolve(Flybits.cfg);
  }).catch(function(ex){
    if(ex instanceof Flybits.Validation){
      def.reject(ex);
    } else{
      def.reject(new Flybits.Validation().addError("Failed to read configuration file.","Reverting to default configuration. Configuration format incorrect at:"+url,{
        code: Flybits.Validation.type.MALFORMED,
        context: 'url'
      }));
    }
  });

  return def.promise;
};

var initServerFileConfig = function(filePath){
  if(!filePath){
    console.log('> config file path not provided: using defaults');
    return false;
  }

  try{
    var data = fs.readFileSync(filePath);
  } catch(e){
    throw new Error("Config file read failed: "+filePath);
  }
  try{
    Flybits.cfg = Flybits.util.Obj.extend(Flybits.cfg,JSON.parse(data));
  } catch(e){
    throw new Error("Malformed Config file: "+filePath);
  }
};

var restoreSessionDetails = function(){
  var def = new Flybits.Deferred();
  
  Flybits.store.Property.ready.then(function(){
    return Flybits.Session._restoreState();
  }).then(function(){
    if(!Flybits.Session._deviceID){
      Flybits.Session._deviceID = resolveDeviceID();
      return Flybits.Session._persistState();
    }
    return Promise.resolve();
  }).then(function(){
    def.resolve();
  }).catch(function(e){
    def.reject(e);
  });

  return def.promise;
};

var resolveDeviceID = function(){
  if(typeof window === 'object'){
    var agent = navigator.userAgent;
    var browserName = Flybits.util.Browser.getName(agent);
    return browserName || Flybits.util.Obj.guid();
  } else{
    return Flybits.util.Obj.guid();
  }
};

var setUserAgent = function(){
  var retObj = {
    physicalDeviceId: Flybits.Session._deviceID,
    sdkVersion: Flybits.VERSION
  };
  if(typeof window === 'object'){
    retObj.make = navigator.userAgent;
    retObj.deviceType = 'browser';
  } else{
    retObj.osVersion = process.version;
    retObj.deviceType = 'node';
  }

  Flybits.Session.userAgent = retObj;

  return retObj;
};

var setStaticDefaults = function(){
  Flybits.context.Manager.reportDelay = Flybits.cfg.CTXREPORTDELAY;
  Flybits.analytics.Manager.reportDelay = Flybits.cfg.analytics.REPORTDELAY;
  Flybits.analytics.Manager.reportDelayUnit = Flybits.cfg.analytics.REPORTDELAYUNIT;
  Flybits.analytics.Manager.maxStoreSize = Flybits.cfg.analytics.MAXSTORESIZE;
};

Flybits.initFile.server = function(configFileURL){
  initServerFileConfig(configFileURL);
  restoreSessionDetails().then(function(){
    setUserAgent();
    setStaticDefaults();
    deferredReady.resolve(Flybits.cfg);
  }).catch(function(e){
    deferredReady.reject(e);
  });

  return Flybits.ready;
};

/**
 * Initializes the Flybits SDK via URI.
 * @param {string} configFileURL URI of the configuration file that contains the `Object` to override SDK defaults.
 * @function initFile
 * @memberof Flybits
 * @return {external:Promise<Object,Flybits.Validation>} Promise that resolves when the SDK has completed initialization.  The same Promise can be accessed at {@link Flybits.ready}.
 */
Flybits.initFile.browser = function(configFileURL){
  initBrowserFileConfig(configFileURL).then(function(){
    return restoreSessionDetails();
  }).then(function(){
    setUserAgent();
    setStaticDefaults();
    deferredReady.resolve(Flybits.cfg);
  }).catch(function(e){
    deferredReady.reject(e);
  });

  return Flybits.ready;
};

/**
 * Configuration `Object` used to override default SDK settings.
 * @typedef ConfigObject
 * @memberof Flybits
 * @type Object
 * @property {string} HOST Flybits core host URL.
 * @property {number} CTXREPORTDELAY The period length between {@link Flybits.context.Manager} reporting in milliseconds.  After said period, the {@link Flybits.context.Manager|Context Manager} will gather all locally stored context values from all registered {@link Flybits.context.ContextPlugin|context plugins} and report them to the core.
 */

/**
 * Initializes the Flybits SDK.
 * @param {Flybits.ConfigObject=} configObj Configuration `Object` used to override SDK defaults.
 * @function
 * @return {external:Promise<Object,Flybits.Validation>} Promise that resolves when the SDK has completed initialization.  The same Promise can be accessed at {@link Flybits.ready}.
 */
Flybits.init = function(configObj){
  configObj = configObj || {};
  Flybits.cfg = Flybits.util.Obj.extend(Flybits.cfg,configObj);
  restoreSessionDetails().then(function(){
    setUserAgent();
    setStaticDefaults();
    deferredReady.resolve(Flybits.cfg);
  }).catch(function(e){
    deferredReady.reject(e);
  });

  return Flybits.ready;
};

/**
 * @classdesc A helper class for {@link external:Promise|ES6 Promises} which allows for deferred asynchronous task management.  Not all asynchronous operations can be wrapped in a promise callback.  Sometimes the resolution of a promise needs to be deferred for another entity to resolve or reject, hence the paradigm of the deferred `Object`.
 * @class
 * @memberof Flybits
 */
Flybits.Deferred = (function(){
  Promise.settle = function(promisesArr){
    var reflectedArr = promisesArr.map(function(promise){
      return promise.then(function(successResult){
        return {
          result: successResult,
          status: 'resolved'
        };
      },function(errorResult){
        return {
          result: errorResult,
          status: 'rejected'
        };
      });
    });
    return Promise.all(reflectedArr);
  };
  
  var Deferred = function(){
    var def = this;
    /**
     * @instance
     * @memberof Flybits.Deferred
     * @member {external:Promise} promise Instance of an ES6 Promise to be fulfilled.
     */
    this.promise = new Promise(function(resolve,reject){
      /**
       * @instance
       * @memberof Flybits.Deferred
       * @member {function} resolve Callback to be invoked when the asychronous task that initiated the promise is successfully completed.
       */
      def.resolve = resolve;
      /**
       * @instance
       * @memberof Flybits.Deferred
       * @member {function} reject Callback to be invoked when the asychronous task that initiated the promise has failed to complete successfully.
       */
      def.reject = reject;
    });

    this.then = this.promise.then.bind(this.promise);
    this.catch = this.promise.catch.bind(this.promise);
  };

  return Deferred;
})();

Flybits.util.Obj = (function(){
  var s4 = function(){
    return Math.floor((1 + Math.random()) * 0x10000)
               .toString(16)
               .substring(1);
  };

  var obj = {
    guid:function(tuples){
      var str = 'js';
      if(tuples && tuples > 0){
        for(var i = 0; i < tuples; i++){
          str += str !== 'js'?'-'+s4():s4();
        }
        return str;
      }
      return 'js' + s4() + s4() + '-' + s4() + '-' + s4() + '-' +
           s4() + '-' + s4() + s4() + s4();
    },
    removeObject:function(arr,obj,findCallback){
      var index = arr.indexOf(obj);

      if(findCallback){
        var objs = arr.filter(findCallback);
        if(objs.length > 0){
          index = arr.indexOf(objs[0]);
        }
      }

      if(index >= 0){
        return arr.splice(index,1);
      }
      return arr;
    },
    debounce: function(func, wait, immediate) {
      var timeout;
      return function() {
        var context = this, args = arguments;
        var later = function() {
          timeout = null;
          if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
      };
    },
    functionName: function(func){
      var result = /^function\s+([\w\$]+)\s*\(/.exec( func.toString() );
    
      return  result?result[ 1 ]:'';
    },
    isNull: function(obj){
      return obj === null || obj === undefined || obj === '';
    },
    /**
     * Transforms localized object structure into a locale mapping centered around a particular property
     * @param {Object} root Root localization structure
     * @function
     * @example
     * // From this
     * {
     *   en: {
     *     x: 'abc',
     *     y: 'asdf'
     *   },
     *   fr: {
     *     x: 'efg',
     *     y: 'jkl;'  
     *   }
     * }
     * // To this, if `propKey` is equal to `x`
     * {
     *   en: 'abc',
     *   fr: 'efg'
     * }
     */
    toLocaleValueMap: function(root, propKey) {
      var valueObj = {};
      var localeObj = root.locales || root.localizations;
      var localeKeys = localeObj?Object.keys(localeObj):[];
      for (var keyIndex = 0; keyIndex < localeKeys.length; keyIndex++) {
        var localeKey = localeKeys[keyIndex];
        var value = localeObj[localeKey][propKey];
        if (value) {
          valueObj[localeKey] = value;
        }
      }
      return valueObj;
    }
  };

  obj.extend = (function(){
    function isMergeableObject(val) {
      var nonNullObject = val && typeof val === 'object';

      return nonNullObject && Object.prototype.toString.call(val) !== '[object RegExp]' && Object.prototype.toString.call(val) !== '[object Date]';
    }

    function emptyTarget(val) {
      return Array.isArray(val) ? [] : {};
    }

    function cloneIfNecessary(value, optionsArgument) {
      var clone = optionsArgument && optionsArgument.clone === true;
      return (clone && isMergeableObject(value)) ? deepmerge(emptyTarget(value), value, optionsArgument) : value;
    }

    function defaultArrayMerge(target, source, optionsArgument) {
      var destination = target.slice();
      source.forEach(function (e, i) {
        if (typeof destination[i] === 'undefined') {
          destination[i] = cloneIfNecessary(e, optionsArgument);
        } else if (isMergeableObject(e)) {
          destination[i] = deepmerge(target[i], e, optionsArgument);
        } else if (target.indexOf(e) === -1) {
          destination.push(cloneIfNecessary(e, optionsArgument));
        }
      });
      return destination;
    }

    function mergeObject(target, source, optionsArgument) {
      var destination = optionsArgument && optionsArgument.mutate? target:{};
      if (isMergeableObject(target)) {
        Object.keys(target).forEach(function (key) {
          destination[key] = cloneIfNecessary(target[key], optionsArgument);
        });
      }
      Object.keys(source).forEach(function (key) {
        if (!isMergeableObject(source[key]) || !target[key]) {
          destination[key] = cloneIfNecessary(source[key], optionsArgument);
        } else {
          destination[key] = deepmerge(target[key], source[key], optionsArgument);
        }
      });
      return destination;
    }

    function deepmerge(target, source, optionsArgument) {
      var array = Array.isArray(source);
      var options = optionsArgument || { arrayMerge: defaultArrayMerge };
      var arrayMerge = options.arrayMerge || defaultArrayMerge;

      if (array) {
        return Array.isArray(target) ? arrayMerge(target, source, optionsArgument) : cloneIfNecessary(source, optionsArgument);
      } else {
        return mergeObject(target, source, optionsArgument);
      }
    }

    deepmerge.all = function deepmergeAll(array, optionsArgument) {
      if (!Array.isArray(array) || array.length < 2) {
        throw new Error('first argument should be an array with at least two elements');
      }

      // we are sure there are at least 2 values, so it is safe to have no initial value
      return array.reduce(function (prev, next) {
        return deepmerge(prev, next, optionsArgument);
      });
    };

    return deepmerge;
  })();

  return obj;
})();

Flybits.util.Api = (function(){
  var Deferred = Flybits.Deferred;
  var ObjUtil = Flybits.util.Obj;

  var defaultAjaxOpts = {
    method: 'GET',
    credentials: 'omit',
  };

  var api = {
    flbFetch: function(url,opts){
      opts = opts || {};
      return this.fetch(url,ObjUtil.extend({
        headers: {
          'Content-Type': 'application/json',
          'x-user-agent': JSON.stringify(Flybits.Session.userAgent),
          'x-authorization': Flybits.Session.userToken
        },
        credentials: 'omit',
        respType: 'json'
      },opts));
    },
    fetch: function(url,opts){
      opts = opts || {};
      var def = new Deferred();
      var ajaxOpts = ObjUtil.extend({},defaultAjaxOpts);
      var isJSON = opts.respType === 'json';
      delete opts.respType;
      ajaxOpts = ObjUtil.extend(ajaxOpts,opts);

      fetch(url,ajaxOpts).then(this.checkResult).then(this.getResultBody)
        .then(function(respObj){
          if(isJSON){
            try{
              respObj.body = api.parseResponse(respObj.body);
              def.resolve(respObj);
            } catch(e){
              def.reject(new Flybits.Validation().addError("Request Failed","Unexpected server response.",{
                code: Flybits.Validation.type.MALFORMED,
              }));
            }
          } else{
            def.resolve(respObj);
          }
        }).catch(function(resp){
          api.getResultBody(resp).then(function(resultObj){
            var parsedResp = api.parseErrorMsg(resultObj.body);
            def.reject(new Flybits.Validation().addError('Request error',parsedResp,{
              serverCode: resp.status
            }));
          });
        });

      return def.promise;
    },
    checkResult: function(resp){
      if(resp.status >= 200 && resp.status < 300){
        return resp;
      }
      throw resp;
    },
    getResultBody: function(resp){
      var def = new Deferred();
      if(resp && resp.text){
        var respHeaders = {};
        resp.headers.forEach(function(val,key){
          respHeaders[key] = val;
        });
        resp.text().then(function(str){
          def.resolve({
            body: str,
            headers: respHeaders
          });
        }).catch(function(e){
          def.reject(e);
        });
      } else{
        def.resolve({
          body: "",
          headers: {}
        });
      }

      return def.promise;
    },
    getResultJSON: function(resp){
      return resp.json();
    },
    toURLParams: function(obj){
      var keys = Object.keys(obj);
      var keyLength = keys.length;
      var str = "";
      while(keyLength--){
        var key = keys[keyLength];
        if(str !== ""){
          str += "&";
        }
        str += key + "=" + encodeURIComponent(obj[key]);
      }

      return str;
    },
    htmlEncode:function(value){
      /*global encodeURIComponent*/
      return encodeURIComponent(value);
    },
    htmlDecode:function(str){
      return str.replace(/&#?(\w+);/g, function(match, dec) {
        if(isNaN(dec)) {
          chars = {quot: 34, amp: 38, lt: 60, gt: 62, nbsp: 160, copy: 169, reg: 174, deg: 176, frasl: 47, trade: 8482, euro: 8364, Agrave: 192, Aacute: 193, Acirc: 194, Atilde: 195, Auml: 196, Aring: 197, AElig: 198, Ccedil: 199, Egrave: 200, Eacute: 201, Ecirc: 202, Euml: 203, Igrave: 204, Iacute: 205, Icirc: 206, Iuml: 207, ETH: 208, Ntilde: 209, Ograve: 210, Oacute: 211, Ocirc: 212, Otilde: 213, Ouml: 214, times: 215, Oslash: 216, Ugrave: 217, Uacute: 218, Ucirc: 219, Uuml: 220, Yacute: 221, THORN: 222, szlig: 223, agrave: 224, aacute: 225, acirc: 226, atilde: 227, auml: 228, aring: 229, aelig: 230, ccedil: 231, egrave: 232, eacute: 233, ecirc: 234, euml: 235, igrave: 236, iacute: 237, icirc: 238, iuml: 239, eth: 240, ntilde: 241, ograve: 242, oacute: 243, ocirc: 244, otilde: 245, ouml: 246, divide: 247, oslash: 248, ugrave: 249, uacute: 250, ucirc: 251, uuml: 252, yacute: 253, thorn: 254, yuml: 255, lsquo: 8216, rsquo: 8217, sbquo: 8218, ldquo: 8220, rdquo: 8221, bdquo: 8222, dagger: 8224, Dagger: 8225, permil: 8240, lsaquo: 8249, rsaquo: 8250, spades: 9824, clubs: 9827, hearts: 9829, diams: 9830, oline: 8254, larr: 8592, uarr: 8593, rarr: 8594, darr: 8595, hellip: 133, ndash: 150, mdash: 151, iexcl: 161, cent: 162, pound: 163, curren: 164, yen: 165, brvbar: 166, brkbar: 166, sect: 167, uml: 168, die: 168, ordf: 170, laquo: 171, not: 172, shy: 173, macr: 175, hibar: 175, plusmn: 177, sup2: 178, sup3: 179, acute: 180, micro: 181, para: 182, middot: 183, cedil: 184, sup1: 185, ordm: 186, raquo: 187, frac14: 188, frac12: 189, frac34: 190, iquest: 191, Alpha: 913, alpha: 945, Beta: 914, beta: 946, Gamma: 915, gamma: 947, Delta: 916, delta: 948, Epsilon: 917, epsilon: 949, Zeta: 918, zeta: 950, Eta: 919, eta: 951, Theta: 920, theta: 952, Iota: 921, iota: 953, Kappa: 922, kappa: 954, Lambda: 923, lambda: 955, Mu: 924, mu: 956, Nu: 925, nu: 957, Xi: 926, xi: 958, Omicron: 927, omicron: 959, Pi: 928, pi: 960, Rho: 929, rho: 961, Sigma: 931, sigma: 963, Tau: 932, tau: 964, Upsilon: 933, upsilon: 965, Phi: 934, phi: 966, Chi: 935, chi: 967, Psi: 936, psi: 968, Omega: 937, omega: 969}
          if (chars[dec] !== undefined){
            dec = chars[dec];
          }
        }
        return String.fromCharCode(dec);
      });
    },
    base64Decode: function(str){
      return decodeURIComponent(Array.prototype.map.call(atob(str), function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
      }).join(''));
    },
    parseResponse: function(rawResponse){
      return JSON.parse(rawResponse,function(key,val){
        if(typeof val === "string"){
          return api.htmlDecode(val);
        }
        return val;
      });
    },
    parseErrorMsg: function(rawResponse){
      try{
        var resp = this.parseResponse(rawResponse);
      } catch(e){
        return "Malformed server response";
      }
      var msg = null;
      resp = resp.error || resp.errors || resp;

      if(resp instanceof Array){
        resp = resp[0];
      }

      if(resp){
        return resp.exceptionMessage || resp.message || resp.messageJSON || resp.detail || "Unexpected error has occurred";
      }

      return msg;
    },
    parsePaging: function(jsonResp, paginationKey){
      paginationKey = paginationKey || 'pagination';
      return {
        offset: jsonResp[paginationKey].offset,
        limit: jsonResp[paginationKey].limit,
        total: jsonResp[paginationKey].totalRecords
      };
    },
    createNextPageCall: function(requestFunction,reqParams,paging){
      if(paging.offset + paging.limit >= paging.total){
        return null;
      }
      reqParams = reqParams?reqParams:{};
      return function(){
        reqParams.paging = {
          limit: paging.limit,
          offset: paging.offset + paging.limit
        };
        return requestFunction(reqParams);
      };
    }
  };

  return api;
})();

Flybits.util.Browser = (function(){
  var Deferred = Flybits.Deferred;

  var browser = {
    getCookie: function(key) {
      var value = "; " + document.cookie;
      var parts = value.split("; " + key + "=");
      if (parts.length == 2) {
        return parts.pop().split(";").shift();
      } else{
        return null;
      }
    },
    setCookie: function(key,value,expiryDateObj){
      var expires = "";
      if (expiryDateObj) {
        expires = "; expires=" + expiryDateObj.toGMTString();
      }
      document.cookie = key + "=" + value + expires + "; path=/";
    },
    getName: function(userAgentString){
      userAgentString = userAgentString?userAgentString.toLowerCase():"";
      var hasChrome = userAgentString.indexOf('chrome') > -1;
      var hasChromium = userAgentString.indexOf('chromium') > -1;
      var hasFirefox = userAgentString.indexOf('firefox') > -1;
      var hasSafari = (userAgentString.indexOf('applewebkit') > -1 || userAgentString.indexOf('safari') > -1) && !hasChrome && !hasChromium;
      var hasIE = userAgentString.indexOf('msie') > -1 || userAgentString.indexOf('.net') > -1;
      var hasOpera = userAgentString.indexOf('opr') > -1 || userAgentString.indexOf('opera') > -1;
      
      var isMobile = userAgentString.indexOf('mobi') > -1;

      var name;
      if(hasChrome){
        name = 'chrome';
      } else if(hasChromium){
        name = 'chromium';
      } else if(hasFirefox){
        name = 'firefox';
      } else if(hasSafari){
        name = 'safari';
      } else if(hasIE){
        name = 'ie';
      } else if(hasOpera){
        name = 'opera';
      }

      if(name && isMobile){
        name += '_mobile';
      }

      return name;
    }
  };

  return browser;
})();

Flybits.util.Geo = (function(){
  var geo = {
    _toDeg : function(rad) {
      return rad * 180 / Math.PI;
    },
    _toRad : function(deg) {
      return deg * Math.PI / 180;
    },
    getBoundingBox: function(latLngArr){
      if(!latLngArr || latLngArr.length < 3){
        throw new Flybits.Validation().addError("Invalid Argument","Must provide an array of lat,lng coordinates greater than 2.",{
          code: Flybits.Validation.type.INVALIDARG
        });
      }
      var latMin = latLngArr[0].lat;
      var latMax = latLngArr[0].lat;
      var lngMin = latLngArr[0].lng;
      var lngMax = latLngArr[0].lng;

      for(var i = 1; i < latLngArr.length; i++){
        var pt = latLngArr[i];
        latMin = pt.lat < latMin? pt.lat:latMin;
        latMax = pt.lat > latMax? pt.lat:latMax;
        lngMin = pt.lng < lngMin? pt.lng:lngMin;
        lngMax = pt.lng > lngMax? pt.lng:lngMax;
      }

      return {
        min: {
          lat: latMin,
          lng: lngMin
        },
        max: {
          lat: latMax,
          lng: lngMax
        }
      };
    },
    getCenter: function(latLngArr){
      if(!latLngArr || latLngArr.length < 3){
        throw new Flybits.Validation().addError("Invalid Argument","Must provide an array of lat,lng coordinates greater than 2.",{
          code: Flybits.Validation.type.INVALIDARG
        });
      }
      var bounds = this.getBoundingBox(latLngArr);
      return {
        lat: (bounds.max.lat + bounds.min.lat) / 2,
        lng: (bounds.max.lng + bounds.min.lng) / 2
      };
    },
    getBearing: function(pt1,pt2){
      var dLng = (pt2.lng-pt1.lng);
      var y = Math.sin(dLng) * Math.cos(pt2.lat);
      var x = Math.cos(pt1.lat)*Math.sin(pt2.lat) - Math.sin(pt1.lat)*Math.cos(pt2.lat)*Math.cos(dLng);
      var brng = this._toDeg(Math.atan2(y, x));
      return 360 - ((brng + 360) % 360);
    }
  };

  return geo;
})();

/**
 * Interface for implementing context plugins.
 * @memberof Flybits.interface
 * @interface
 */
Flybits.interface.ContextPlugin = {
  /**
   * Checks for availability of this plugin on the current platform.
   * @function
   * @memberof Flybits.interface.ContextPlugin
   * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without value if this context plugin is supported on the current platform.
   */
  isSupported: function(){},
  /**
   * Retrieves current value of this particular context plugin.
   * @function
   * @memberof Flybits.interface.ContextPlugin
   * @return {external:Promise<Object,Flybits.Validation>} Promise that resolves with context plugin specific data structure representing current value of context plugin.
   */
  getState: function(){},

  /**
   * Converts context value object into the server expected format.
   * @function
   * @memberof Flybits.interface.ContextPlugin
   * @param {Object} contextValue
   * @return {Object} Expected server format of context value.
   */
  _toServerFormat: function(contextValue){}
};

/**
 * Interface for implementing identity providers.
 * @memberof Flybits.interface
 * @interface
 */
Flybits.interface.IDP = {
  /**
   * Returns Flybits endpoint to report IDP payload.
   * @function getURL
   * @memberof Flybits.interface.IDP
   * @return {string} URL to authentication endpoint for this particular IDP
   */
  getURL: function(){},
  /**
   * Returns payload required for IDP to authenticate with Flybits.
   * @function getPayload
   * @memberof Flybits.interface.IDP
   * @return {Object} IDP specific payload used to authenticate with Flybits.
   * @throws {Flybits.Validation} If IDP parameters are incorrect an exception will be thrown in the form of a standard {@link Flybits.Validation|Validation} object.
   */
  getPayload: function(){},
};

/**
 * Interface for implementing data stores that operate on key/value pairs.
 * @memberof Flybits.interface
 * @interface
 */
Flybits.interface.KeyDataStore = {
  /**
   * Retrieves the amount of entries in the data store.
   * @function
   * @memberof Flybits.interface.KeyDataStore
   * @return {external:Promise<Object,Flybits.Validation>} Promise that resolves with the number of entries in the data store.
   */
  getSize: function(){},
  /**
   * Adds/Replaces/Removes an item in the data store.
   * @function
   * @memberof Flybits.interface.KeyDataStore
   * @param {string} id ID of item to be stored.
   * @param {Object} item Item to be stored in data store.  If `null` or `undefined` is supplied the related item that has provided `id` will be removed from the data store.
   * @return {external:Promise<Object,Flybits.Validation>} Promise that resolves without value if data is successfully set in data store.
   */
  set: function(id,item){},
  /**
   * Retrieves an item from the data store.
   * @function
   * @memberof Flybits.interface.KeyDataStore
   * @param {string} id ID of item to be retrieved.
   * @return {external:Promise<Object,Flybits.Validation>} Promise that resolves with data store item based on `id` if it exists.
   */
  get: function(id){},
  /**
   * Retrieves all item keys from the data store.
   * @function
   * @memberof Flybits.interface.KeyDataStore
   * @return {external:Promise<Object,Flybits.Validation>} Promise that resolves with all item keys in the data store.
   */
  getKeys: function(){},
  /**
   * Clears the entire data store of its entries.
   * @function
   * @memberof Flybits.interface.KeyDataStore
   * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without value if data clear is successful.
   */
  clear: function(){}
};

/**
 * Interface for models which have localized properties.
 * @memberof Flybits.interface
 * @interface
 */
Flybits.interface.Localizable = {
  /**
   * Parses server localized properties for a single locale object. For instance if a model has localized properties {'en':{},'fr':{}}, each object mapped to each locale key would pass through this function.
   * @function
   * @instance
   * @memberof Flybits.interface.Localizable
   * @param {Object} serverObj server locale object containing localized properties.
   * @return {Object} Server localized properties of a locale key parsed to SDK equivalent objects.
   */
  _fromLocaleJSON: function(serverObj){},
  /**
   * Maps SDK localized objects back to server equivalent objects.
   * @function
   * @instance
   * @memberof Flybits.interface.Localizable
   * @param {Object} appObj application locale object containing localized properties
   * @return {Object} SDK localized properties of a locale key parsed to server equivalent objects.
   */
  _toLocaleJSON: function(appObj){}
};

/**
 * Interface for implementing property stores (key-value pair storage).
 * @memberof Flybits.interface
 * @interface
 */
Flybits.interface.PropertyStore = {
  /**
   * Checks for availability of this property storage type.
   * @function
   * @memberof Flybits.interface.PropertyStore
   * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without value if storage type is supported and rejects if it is not.
   */
  isSupported: function(){},
  /**
   * Retrieves property value based on key.
   * @function
   * @memberof Flybits.interface.PropertyStore
   * @instance
   * @param key Key by which to fetch the requested property.
   * @return {external:Promise<string,Flybits.Validation>} Promise that resolves with property store value unless a problem occurs.
   */
  getItem: function(key){},

  /**
   * Converts context value object into the server expected format.
   * @function
   * @memberof Flybits.interface.PropertyStore
   * @instance
   * @param {string} key Key by which to store provided `value`
   * @param {string} value Value that is to be stored based on provided `key`
   * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without value if value has been successfully stored based on specified key.
   */
  setItem: function(key, value){},

  /**
   * Completely remove key and value from property store.
   * @function
   * @memberof Flybits.interface.PropertyStore
   * @instance
   * @param {string} key Key by which to store provided `value`
   * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without value if key and value have been successfully removed based on specified key.
   */
  removeItem: function(key){},

};

/**
 * Interface for SDK models that are abstracted from server models.
 * @memberof Flybits.interface
 * @interface
 */
Flybits.interface.Serializable = {
  /**
   * Parses raw server models into SDK model properties that implement this interface.
   * @function
   * @instance
   * @memberof Flybits.interface.Serializable
   * @param {Object} serverObj Raw server model.
   */
  fromJSON: function(serverObj){},
  /**
   * Maps SDK model properties to abstracted server models.
   * @function
   * @instance
   * @memberof Flybits.interface.Serializable
   * @returns {Object} Raw server model.
   */
  toJSON: function(){}
};

/**
 * @classdesc Base class from which all core Flybits classes are extended.
 * @class
 */
var BaseObj = (function(){
  function BaseObj(){};
  BaseObj.prototype = {
    implements: function(interfaceName){
      if(!this._interfaces){
        this._interfaces = [];
      }
      this._interfaces.push(interfaceName);
    }
  };

  return BaseObj;
})();

var Path = (function(){
  function Path(){
    this.nodes = [];
  };

  Path.prototype.getNode = function(className){
    return this.nodes.filter(function(obj){
      return Flybits.util.Obj.functionName(obj.constructor) === className;
    })[0];
  };

  Path.prototype.append = function(obj){
    this.nodes.push(obj);
    return this;
  };

  Path.prototype.serialize = function(){
    var pathStr = "";
    for(var i = 0; i < this.nodes.length; i++){
      pathStr += '/' + this.nodes[i]._pathName;
      if(this.nodes[i].id){
        pathStr += '/' + this.nodes[i].id;
      }
    }
    
    return pathStr;
  };

  return Path;
})();

/**
 * @classdesc Base class from which all core Flybits models are extended.
 * @class
 * @param {Object} serverObj Raw Flybits core model `Object` directly from API.
 */
var BaseModel = (function(){
  var BaseModel = function(serverObj){
    BaseObj.call(this);
    /**
     * @instance
     * @memberof BaseModel
     * @member {string} id Parsed ID of the Flybits core model.
     */
    this.id;

    if(serverObj && serverObj.id){
      this.id = serverObj.id;
    }
  };

  BaseModel.prototype = Object.create(BaseObj.prototype);
  BaseModel.prototype.constructor = BaseModel;

  BaseModel.prototype.reqKeys = {
    id: 'id'
  };

  return BaseModel;
})();

/**
 * @classdesc Flybits core Content model. A Content model instance represents the content created from a corresponding Content Template. That is to say it is the entity through which we can access/modify JSON data that fits the structure specified in the template.
 * @class
 * @memberof Flybits
 * @extends BaseModel
 * @implements {Flybits.interface.Serializable}
 * @implements {Flybits.interface.Localizable}
 * @param {Object} serverObj Raw Flybits core model `Object` directly from API.
 */
Flybits.Content = ( function(){
  var ApiUtil = Flybits.util.Api;

  /**
   * @typedef LocalizedObject
   * @memberof Flybits.Content
   * @type Object
   * @property {string} name Name of the Content instance.
   * @property {string} description Description of the Content instance.
   */

  function Content( serverObj ){
    BaseModel.call( this, serverObj );
    if( serverObj ){
      this.fromJSON( serverObj );
    }
  }

  Content.prototype = Object.create( BaseModel.prototype );
  Content.prototype.constructor = Content;
  Content.prototype.implements( 'Serializable' );
  Content.prototype.implements( 'Localizable' );

  Content.prototype.types = Content.types = {
    DEFAULT: 'default',
    SURVEY: 'SurveyQuestions'
  };
  Content.prototype._pathName = Content._pathName = 'content/instances';

  Content.prototype.reqKeys = Content.reqKeys = {
    name: 'name',
    description: 'description',
    createdDate: 'createdAt',
    lastModifiedDate: 'modifiedAt',
    id: 'id'
  };

  /***** Localizable function overloading *****/
  Content.prototype._fromLocaleJSON = function( serverObj ) {
    var retObj = {
      name: serverObj.name,
      description: serverObj.description
    };

    return retObj;
  };

  Content.prototype._toLocaleJSON = function ( appObj ) {
    var retObj = {
      name: appObj.name,
      description: appObj.description
    };

    return retObj;
  };

  Content.prototype.parseContents = function(serverObj){
    var paging = ApiUtil.parsePaging( serverObj );
    var contentObj = this;
    
    var allContentData = serverObj.data.map( function(obj){
      var routePath = new Path().append(contentObj);
      try{
        return new Flybits.ContentData( obj, routePath );
      } catch(e){
        console.error(e);
        throw new Validation().addError("Parse Failed","Failed to parse Content data model.",{
          code: Validation.type.MALFORMED,
          context: obj
        });
      }
    });

    return {
      result: allContentData,
      nextPageFn: ApiUtil.createNextPageCall( Flybits.api.ContentData.getAll, {
        contentID: contentObj.id
      }, paging )
    };
  };

  /**
   * Retrieve the data from the Content model instance.
   * @memberof Flybits.Content
   * @instance
   * @function getData
   * @return {external:Promise<Object,Flybits.Validation>} Promise that resolves with the Content instance's JSON payload object.
   */
  Content.prototype.getData = function(){
    var obj = this;
    if(this.data && this.data.result && this.data.result.length > 0){
      return Promise.resolve(this.data.result[0].payload);
    } else{
      var def = new Flybits.Deferred();
      Flybits.api.ContentData.getAll({
        contentID: this.id
      }).then(function(respObj){
        if(respObj && respObj.result && respObj.result.length > 0){
          obj.data = respObj;
          def.resolve(respObj.result[0].payload);
        } else{
          def.resolve();
        }
      }).catch(function(e){
        def.reject(e);
      });

      return def.promise;
    }
  };

  /***** Serializable function overloading *****/
  Content.prototype.fromJSON = function(serverObj){
    var obj = this;
    /**
     * @instance
     * @memberof Flybits.Content
     * @member {string} id - id value of the content instance.
     */
    obj.id = serverObj.id;
    /**
     * @instance
     * @memberof Flybits.Content
     * @member {string} tenantID - id value of the tenant(project) of instance
     */
    obj.tenantID = serverObj.tenantId;
    /**
     * @instance
     * @memberof Flybits.Content
     * @member {string} templateID - id value of the content template of the fetched content instance.
     */
    obj.templateID = serverObj.templateId;
    /**
     * @instance
     * @memberof Flybits.Content
     * @member {string} type - content template type from which this instance was created from.
     */
    obj.type = serverObj.templateType || Content.types.DEFAULT;
    /**
     * @instance
     * @memberof Flybits.Content
     * @member {string} iconURL - URL of the icon image associated with this content instance.
     */
    obj.iconURL = serverObj.iconUrl;
    /**
     * @instance
     * @memberof Flybits.Content
     * @member {Date} createdDate - Date of model creation.
     */
    obj.createdDate = serverObj.createdAt? new Date(serverObj.createdAt*1000):null;
    /**
     * @instance
     * @memberof Flybits.Content
     * @member {Date} lastModifiedDate - Date of last modification of model.
     */
    obj.lastModifiedDate = serverObj.modifiedAt? new Date(serverObj.modifiedAt*1000):null;
    /**
     * @instance
     * @memberof Flybits.Content
     * @see Flybits.Content.LocalizedObject
     * @member {Object} locales - Map of available locale keys to {@link Flybits.Content.LocalizedObject} objects containing information.
     * @example
     * { 
     *   en: {
     *     name: 'Falafel',
     *     description: 'Falafel is good'
     *   },
     *   fr: {
     *     name: 'Falafel',
     *     description: 'Falafel est bon'
     *   }
     * }
     */
    obj.locales = {};

    var localeKeys = serverObj.localizations ? Object.keys(serverObj.localizations) : [];
    localeKeys.forEach( function( key ) {
      obj.locales[key] = obj._fromLocaleJSON( serverObj.localizations[key] );
    });

    // if( localeKeys.length > 0 && this.defaultLang ) {
    //   this.defaultLocaleObj = this.locales[ this.defaultLang ];
    // }
    /**
     * @instance
     * @memberof Flybits.Content
     * @member {string} 
     */
    obj.linkedFields = serverObj.linkedFields;

    /**
     * @instance
     * @memberof Flybits.Content
     * @member {string[]}
     */
    obj.labels = serverObj.labels || [];

    obj.data = serverObj.content? this.parseContents(serverObj.content):undefined;
  };

  Content.prototype.toJSON = function(){
    var obj = this;
    
    var retObj = {
      iconUrl: obj.iconURL,
      localizations: {},
      labels: obj.labels,
    };

    if(obj.createdDate){
      retObj.createdAt = Math.round(obj.createdDate.getTime()/1000);
    }
    if(obj.lastModifiedDate){
      retObj.modifiedAt = Math.round(obj.lastModifiedDate.getTime()/1000);
    }

    if( Object.keys( obj.locales ).length > 0 ) {
      var localeKeys = Object.keys( obj.locales );
      localeKeys.forEach( function( key ) {
        retObj.localizations[key] = obj._toLocaleJSON( obj.locales[key] );
      });
    }

    /* id rendering: thrown in for just safety purpose
     * id validations(content instance, content template, tenant) moved to the content API
     * id is not passed in the body of JSON (PUT, POST) requests anymore (Part of Header and URL)
     */
    if( obj.id ) {
      retObj.id = obj.id;
    }

    if( obj.tenantID ) {
      retObj.tenantId = obj.tenantID;
    }

    if( obj.templateID ) {
      retObj.templateId = obj.templateID;
    }

    if( obj.type ){
      retObj.templateType = obj.type;
    }

    return retObj;
  };

  Content.getInstance = function(serverObj){
    var hasQuestions = serverObj.content && serverObj.content.data && serverObj.content.data.length && serverObj.content.data[0].questions;

    if((serverObj.templateType === Content.types.SURVEY && serverObj.surveyMetadata) || hasQuestions){
      return new Flybits.Survey(serverObj);
    } else{
      return new Content(serverObj);
    }
  };

  return Content;
})();

/**
 * @classdesc Flybits core Content Root Data model. This is the root data of a content instance
 * @class
 * @memberof Flybits
 * @extends BaseModel
 * @implements {Flybits.interface.Serializable}
 * @param {Object} serverObj Raw Flybits core model `Object` directly from API.
 */
Flybits.ContentData = ( function(){
  function ContentData( serverObj, path ){
    BaseModel.call( this, serverObj );
    this.id = serverObj._id || serverObj.id;
    delete serverObj._id;
    delete serverObj.id;
    this.path = path || new Path();
    this.path.append(this);

    if( serverObj ){
      this.fromJSON( serverObj );
    }
  }

  ContentData.prototype = Object.create( BaseModel.prototype );
  ContentData.prototype.constructor = ContentData;
  ContentData.prototype.implements('Serializable');

  ContentData.prototype._pathName = ContentData._pathName = 'data';

  var getRecursiveParseValue = function(value, path){
    if(value instanceof Object && !Array.isArray(value)){
      return recursiveObjParse(value, path);
    } else if(Array.isArray(value)){
      return value.map(function(obj){
        return getRecursiveParseValue(obj, path);
      });
    }
    return value;
  };

  var recursiveObjParse = function(parentObj, path){
    var retObj = {};
    var parentKeys = Object.keys(parentObj);
    parentKeys.forEach(function(key){
      var value = parentObj[key];
      if(key === '_id'){
        key = 'id';
      } else if(key === 'localizations'){
        key = 'locales';
      }

      var pageKey = key+'.pagination';
      if(parentObj.hasOwnProperty(pageKey)){
        retObj[key] = new Flybits.PagedData({
          data: value,
          paging: parentObj[pageKey],
          key: key,
          path: path
        });
      } else if(key.indexOf('.pagination') < 0){
        retObj[key] = getRecursiveParseValue(value, path);
      }
    });

    return retObj;
  };

  var getRecursiveSerializationValue = function(value){
    if(value instanceof Flybits.PagedData){
      return value.toJSON();
    } else if(value instanceof Object && !Array.isArray(value)){
      return recursiveObjSerialization(value);
    } else if(Array.isArray(value)){
      return value.map(function(obj){
        return getRecursiveSerializationValue(obj);
      });
    }
    
    return value;
  };

  var recursiveObjSerialization = function(parentObj){
    var serialized = {};
    var parentKeys = Object.keys(parentObj);
    parentKeys.forEach(function(key){
      var value = parentObj[key];
      key = key === 'locales'?'localizations':key;
      serialized[key] = getRecursiveSerializationValue(value);
    });

    return serialized;
  };

  /***** Serializable function overloading *****/
  ContentData.prototype.fromJSON = function( serverObj ){
    this.payload = recursiveObjParse(serverObj, this.path);
  };

  ContentData.prototype.toJSON = function(){
    return recursiveObjSerialization(this.payload);
  };

  return ContentData;
})();

/**
 * @classdesc Helper representation of nested Content data arrays that are pageable.
 * @class
 * @memberof Flybits
 * @extends BaseModel
 * @implements {Flybits.interface.Serializable}
 * @param {Flybits.PagedData.InputObject} inputObj.
 */
Flybits.PagedData = ( function(){
  var ApiUtil = Flybits.util.Api;
  var Deferred = Flybits.Deferred;

  /**
   * @typedef InputObject
   * @memberof Flybits.PagedData
   * @type Object
   * @property {Flybits.Path} path Helper object that tracks depth of paged array in ancestor hierarchy for API paging purposes.
   * @property {string} key Key name that paged data array is mapped to in parent object.
   * @property {Object[]} data Array of paged data objects.
   * @property {Object} paging Pagination information of data array.
   */

  function PagedData( inputObj ){
    BaseModel.call( this, inputObj );
    /**
     * Array of nested Content data values.  As more pages are retrieved from the server this array will continue be updated.
     * @instance
     * @memberof Flybits.PagedData
     * @member {Array} data
     */
    this.data = [];
    if(inputObj){
      this.path = inputObj.path;
      this.key = inputObj.key;
      var paging = {
        total: inputObj.paging.totalRecords,
        limit: inputObj.paging.limit,
        offset: inputObj.paging.offset
      };

      /**
       * Function to request this nested Content data's next page. Do not invoke directly as it will not update the {@link Flybits.PagedData.data|data} array.  Instead invoke {@link Flybits.PagedData#getNext}
       * @instance
       * @memberof Flybits.PagedData
       * @member {function} nextPageFn
       */
      this.nextPageFn = ApiUtil.createNextPageCall(Flybits.api.ContentData.getPagedData,{
        path: this.path,
        key: this.key
      },paging);
    }

    if(inputObj && inputObj.data){
      this.fromJSON(inputObj.data);
    }
  }

  PagedData.prototype = Object.create( BaseModel.prototype );
  PagedData.prototype.constructor = PagedData;
  PagedData.prototype.implements( 'Serializable' );

  /**
   * Used to check the existence of a next page of data.
   * @function hasNext
   * @instance
   * @memberof Flybits.PagedData
   * @return Truthy value depending on the existence of a {@link Flybits.PagedData.nextPageFn|next page}.
   */
  PagedData.prototype.hasNext = function(){
    return this.nextPageFn;
  };

  /**
   * Retrieves the next page of nested Content data and also appends it to this instance's {@link Flybits.PagedData.data|data array}.
   * @function getNext
   * @instance
   * @memberof Flybits.PagedData
   * @return {external:Promise<Array,Flybits.Validation>} Promise that resolves with the next page of nested Content data.
   */
  PagedData.prototype.getNext = function(){
    if(!this.hasNext()){
      return Promise.resolve([]);
    }
    var def = new Deferred();
    var pagedData = this;
    this.nextPageFn().then(function(resp){
      pagedData.nextPageFn = resp.nextPageFn;
      pagedData.data.push.apply(pagedData.data,resp.result);
      def.resolve(resp.result);
    });

    return def.promise;
  };

  PagedData.prototype.fetchAll = function(){
    var def = new Flybits.Deferred();
    var pagedData = this;

    var recursiveFetch = function(){
      if(pagedData.hasNext()){
        pagedData.getNext().then(function(){
          recursiveFetch();
        }).catch(function(e){
          def.reject(e);
        });
      } else{
        def.resolve(pagedData.data);
      }
    };
    recursiveFetch();

    return def.promise;
  };

  /***** Serializable function overloading *****/
  PagedData.prototype.fromJSON = function(dataArr){
    var obj = this;

    for(var i = 0; i < dataArr.length; i++){
      var val = dataArr[i];
      if(val.constructor === Object){
        if(val.hasOwnProperty('localizations')){
          val.locales = val.localizations;
          delete val.localizations;
        }
      }
      obj.data.push(val);
    }
  };

  PagedData.prototype.toJSON = function(){
    var obj = this;
    var retObj = [];

    for(var i = 0; i < obj.data.length; i++){
      var val = obj.data[i];
      if(val.constructor === Object){
        if(val.hasOwnProperty('locales')){
          val.localizations = val.locales;
          delete val.locales;
        }
      }
      retObj.push(val);
    }

    return retObj;
  };

  return PagedData;
})();

/**
 * @classdesc Flybits core Survey model. Contains questions and can facilitate survey submission.
 * @class
 * @extends Flybits.Content
 * @memberof Flybits
 * @param {Object} serverObj Raw Flybits core model `Object` directly from the API.
 */
Flybits.Survey = (function(){
  var Content = Flybits.Content;
  
  function Survey( serverObj ){
    Content.call( this, serverObj );
    /**
     * @instance
     * @memberof Flybits.Survey
     * @member {Flybits.SurveyQuestion[]} questions Questions of the survey
     */
    this.questions = [];
    var surveyMeta = serverObj.surveyMetadata || {};
    this.resultID = surveyMeta.surveyResultInstanceID;
    this.answerID = surveyMeta.surveyAnswersInstanceID;

    if(this.data){
      this.parseQuestions(this.data);
    }
  }

  Survey.prototype = Object.create( Content.prototype );
  Survey.prototype.constructor = Survey;
  Survey.prototype.implements( 'Serializable' );

  Survey.prototype.parseQuestions = function(dataObj){
    var dataRoot = {};
    if(dataObj && dataObj.result && dataObj.result.length){
      dataRoot = dataObj.result[0].payload;
    }

    if(dataRoot.questions && dataRoot.questions.data){
      this.questions = dataRoot.questions.data.map(function(q){
        return new Flybits.SurveyQuestion(q);
      });
    }
  };

  /**
   * Retrieves questions of the survey.
   * @function getQuestions
   * @instance
   * @memberof Flybits.Survey
   * @return {external:Promise<Flybits.SurveyQuestion[],Flybits.Validation>} An array containing the questions of the survey or a `Validation` object if unsuccessful.
   */
  Survey.prototype.getQuestions = function(){
    var survey = this;
    var def = new Flybits.Deferred();
    var pagedData;
    this.getData().then(function(dataRoot){
      pagedData = dataRoot.questions;
      return pagedData.fetchAll();
    }).then(function(){
      survey.questions = pagedData.data.map(function(q){
        return new Flybits.SurveyQuestion(q);
      });
      def.resolve(survey.questions);
    }).catch(function(e){
      def.reject(e);
    });

    return def.promise;
  };

  var createNextPageWrapperFn = function(fn,survey){
    return function(){
      var def = new Flybits.Deferred();
      var nextPageFn;
      
      fn().then(function(resp){
        var fetchPromises = resp.result.map(function(obj){
          return Flybits.SurveyResponse.parseResponse(obj, survey);
        });
        nextPageFn = resp.nextPageFn;
        return Promise.settle(fetchPromises);
      }).then(function(promises){
        def.resolve({
          result: promises.map(function(result){
            return result.result;
          }),
          contentID: survey.answerID,
          nextPageFn: createNextPageWrapperFn(nextPageFn,survey)
        });
      }).catch(function(e){
        def.reject(e);
      });

      return def.promise;
    };
  };

  /**
   * Retrieves previously submitted responses to the survey.  If you are an admin you will see all responses from all users, otherwise you will only see your own responses.
   * @function getResponses
   * @instance
   * @memberof Flybits.Survey
   * @returns {external:Promise<Flybits.api.Result,Flybits.Validation>} Promise that resolves with result object containing and array of {@link Flybits.SurveyResponse}.
   */
  Survey.prototype.getResponses = function(){
    var survey = this;

    return createNextPageWrapperFn(function(){
      return Flybits.api.ContentData.getAll({
        contentID: survey.answerID,
        paging: {
          limit: 2
        }
      });
    },survey)();
  };

  /**
   * Triggers the validation of all answers pertaining to all questions within the survey.
   * @function validate
   * @instance
   * @memberof Flybits.Survey
   * @return {Flybits.Validation} `Validation` instance pertaining to the result of validation checks on all question answers.
   */
  Survey.prototype.validate = function(){
    var validation = this.questions.reduce(function(accumalator, question){
      return accumalator.merge(question.validate());
    }, new Flybits.Validation());
    
    return validation;
  };

  /**
   * Submits a response to the survey which includes all answers to the associated questions.
   * @function submit
   * @instance
   * @memberof Flybits.Survey
   * @return {external:Promise<Flybits.api.Result,Flybits.Validation>} `Validation` instance if unsuccessful.
   */
  Survey.prototype.submit = function(){
    var def = new Flybits.Deferred();

    var answer = new SurveyAnswer({
      id: this.answerID,
      surveyID: this.id
    });
    for(var i = 0; i < this.questions.length; i++){
      answer.parseAnswer(this.questions[i]);
    }

    Flybits.api.ContentData.submitData(answer.data).then(function(resp){
      def.resolve(resp);
    }).catch(function(e){
      def.reject(e);
    });


    return def.promise;
  };

  return Survey;
})();
var SurveyAnswer = (function(){
  var Content = Flybits.Content;

  function SurveyAnswer(obj){
    Content.call(this, obj);
    this.surveyID = obj.surveyID;
    this.qAnswers = [];
    this.path = new Path().append(this);
    this.data = new Flybits.ContentData({},this.path);
    this.data.payload = {
      surveyId: this.surveyID,
      answers: this.qAnswers
    };
  }

  SurveyAnswer.prototype = Object.create(Content.prototype);
  SurveyAnswer.prototype.constructor = SurveyAnswer;
  SurveyAnswer.prototype._pathName = SurveyAnswer._pathName = 'content/survey/instances';

  SurveyAnswer.prototype.parseAnswer = function(question){
    if(question.id && !Flybits.util.Obj.isNull(question.answer)){

      this.qAnswers.push({
        questionNumber: question.id,
        answer: question.answer instanceof Array?question.answer:[question.answer+""]
      });
    }

    return this;
  };

  return SurveyAnswer;
})();
/**
 * @classdesc A SurveyChoice model instance represents a possible answer to a {@link Flybits.SurveyQuestion| SurveyQuestion}.
 * @class
 * @memberof Flybits
 * @param {Object} serverObj Raw `Object` directly from the API from within the question model.
 */
Flybits.SurveyChoice = (function(){
  /**
   * A dictionary mapping of locale keys to localized string values.  
   * @typedef LocalizedObject
   * @memberof Flybits.SurveyChoice
   * @type Object
   * @example
   * { 
   *   en: 'falafel',
   *   fr: 'falafel'
   * }
   */

  function SurveyChoice(serverObj){
    /**
     * @instance
     * @memberof Flybits.SurveyChoice
     * @member {string} key ID of the choice.
     */
    this.key = serverObj.key;
    /**
     * @instance
     * @memberof Flybits.SurveyChoice
     * @member {Flybits.SurveyChoice.LocalizedObject} title Localized display title for the answer choice.
     */
    this.title = Flybits.util.Obj.toLocaleValueMap(serverObj, 'value');
  }

  return SurveyChoice;
})();
/**
 * @classdesc Flybits core SurveyQuestion model. A SurveyQuestion model instance represents a question within a Survey instance.
 * @class
 * @memberof Flybits
 * @param {Object} serverObj Raw Flybits core model `Object` directly from the API.
 */
Flybits.SurveyQuestion = (function(){
  
  /**
   * A dictionary mapping of locale keys to localized string values.  
   * @typedef LocalizedObject
   * @memberof Flybits.SurveyQuestion
   * @type Object
   * @example
   * { 
   *   en: 'falafel',
   *   fr: 'falafel'
   * }
   */

  /**
   * UI Constraints based on question type.
   * @typedef UIConstraints
   * @memberof Flybits.SurveyQuestion
   * @type Object
   * @property {Object} number UI Constraints for the `NUMBER` type of question
   * @property {boolean} number.validation Flag indicating whether or not question has number constraints.
   * @property {number} number.min Minimum value for a number.
   * @property {number} number.max Maximum value for a number.
   * @property {number} number.increment Allowed answer step size. For instance if the `increment` was set to `2` and the `min` was set to `0`, the user should only be allowed to enter `0,2,4,6,...`
   */

  function SurveyQuestion( serverObj ){
    /**
     * @instance
     * @memberof Flybits.SurveyQuestion
     * @member {boolean} isRequired Flag to indicate whether or not this survey question is mandatory.
     */
    this.isRequired = false;
    /**
     * @instance
     * @memberof Flybits.SurveyQuestion
     * @member {number} id Unique identifier of survey question.
     */
    this.id =  0;
    /**
     * @instance
     * @memberof Flybits.SurveyQuestion
     * @member {Flybits.SurveyChoice[]} choices Predefined answer options for questions that only allow selection from a subset of responses. For instance questions that contain a dropdown or radio buttons/checkboxes.
     */
    this.choices = [];
    /**
     * @instance
     * @memberof Flybits.SurveyQuestion
     * @member {Flybits.SurveyQuestion.LocalizedObject} title The localized string label of the question.
     */
    this.title = {};
    /**
     * @instance
     * @memberof Flybits.SurveyQuestion
     * @member {string} type Question type that can be found in class {@link Flybits.SurveyQuestion.types|constants.}
     */
    this.type = 'shortAnswer';
    /**
     * @instance
     * @memberof Flybits.SurveyQuestion
     * @member {Flybits.SurveyQuestion.UIConstraints} meta Metadata for the question type.  This may include UI constraints based on question type.
     */
    this.meta = {
      number: {}
    };
    this.answer = undefined;
    this.validation = undefined;

    if(serverObj){
      this.fromJSON(serverObj);
    }
  }
  
  /**
   * @memberof Flybits.SurveyQuestion
   * @member {Object} types Mapping of question types
   * @constant
   * @property {string} SHORTANSWER Short answer that should be displayed as a single line input box.
   * @property {string} LONGANSWER Long answer that should be displayed as a text area input box.
   * @property {string} NUMBER Answer that only accepts numbers.
   * @property {string} MULTIPLECHOICE Type that allows the user to select only one answer out of multiple predefined {@link Flybits.SurveyQuestion#choices|choice options}.
   * @property {string} CHECKBOXES Type that allows the user to select multiple answers out of multiple predefined {@link Flybits.SurveyQuestion#choices|choice options}.
   * @property {string} DROPDOWN Type that allows the user to select only one answer out of multiple predefined {@link Flybits.SurveyQuestion#choices|choice options}.  This is different from {@link Flybits.SurveyQuestion.types|MULTIPLECHOICE} simply by its UI component.  The question choices should be displayed in a dropdown picker.
   * @property {string} DATE Simple type that should provide the user with a date picker.  Dates should be stored in the form of an {@link external:ISO 8601|ISO 8601} string.
   * @property {string} TIME Simple type that should provide the user with a time picker. Dates should be stored in the form of an {@link external:ISO 8601|ISO 8601} string.
   */
  SurveyQuestion.prototype.types = SurveyQuestion.types = {
    SHORTANSWER: 'shortAnswer',
    LONGANSWER: 'longAnswer',
    NUMBER: 'number',
    MULTIPLECHOICE: 'multipleChoice',
    CHECKBOXES: 'checkboxes',
    DROPDOWN: 'dropdown',
    DATE: 'date',
    TIME: 'time',
    LOCATION: 'location',
    FILEUPLOAD: 'fileUpload'
  };

  SurveyQuestion.prototype.fromJSON = function(serverObj){
    if (serverObj.metadata) {
      this.meta = JSON.parse(serverObj.metadata);
    }
    this.isRequired = this.meta.isRequired;
    this.id = serverObj.questionNumber;
    this.type = this.meta.uiType;
    this.title = Flybits.util.Obj.toLocaleValueMap(serverObj, 'question');

    if(serverObj.choices && serverObj.choices.length){
      this.choices = serverObj.choices.map(function(choice){
        return new Flybits.SurveyChoice(choice);
      });
    }
  };

  /**
   * Sets the answer of the survey question.
   * @function setAnswer
   * @instance
   * @memberof Flybits.SurveyQuestion
   * @param {string} answer Answer can be a singular string value or an array of string answers depending on question type.  Only CHECKBOXES type questions can have multiple answers.
   * @return {Flybits.SurveyQuestion} The `SurveyQuestion` instance the method has been invoked upon to allow for method chaining.
   */
  SurveyQuestion.prototype.setAnswer = function(answer){
    if(this.type !== SurveyQuestion.types.CHECKBOXES && answer instanceof Array){
      throw new Flybits.Validation().addError('Invalid answer','Only CHECKBOXES type questions can contain multiple answers.',{
        context: answer,
        code: Flybits.Validation.type.INVALIDARG
      });
    }

    this.answer = answer;

    return this;
  };

  /**
   * Triggers the validation of the question answer.
   * @function validate
   * @instance
   * @memberof Flybits.SurveyQuestion
   * @return {Flybits.Validation} `Validation` instance pertaining to the result of validation checks on this question's answer.
   */
  SurveyQuestion.prototype.validate = function(){
    var validator = SurveyValidation.getValidator(this);
    this.validation = validator.validate();
    return this.validation;
  };

  return SurveyQuestion;

})();
/**
 * @classdesc A SurveyResponse model instance represents a user submitted response to a {@link Flybits.Survey|Survey} instance.
 * @class
 * @memberof Flybits
 * @param {Object} serverObj Raw `Object` directly from the API.
 */
Flybits.SurveyResponse = (function(){
  /**
   * A map of a {@link Flybits.SurveyQuestion|SurveyQuestion} to its corresponding answer.
   * @typedef QuestionAnswerMap
   * @memberof Flybits.SurveyResponse
   * @type Object
   * @property {Flybits.SurveyQuestion} question Full survey question model.
   * @property {string|Flybits.SurveyChoice[]} answer Corresponding answer to question.  If question {@link Flybits.SurveyQuestion#type|type} is one of which that contains bucketed {@link Flybits.SurveyChoice|choice options}, then the `answer` will be an array of user selected {@link Flybits.SurveyChoice|choice options}.  Otherwise, primitive answers will simply be a `string`.
   */

  function SurveyResponse(serverObj){
    /**
     * @instance
     * @memberof Flybits.SurveyResponse
     * @member {string} id ID of reponse.  Equivalent to a {@link Flybits.ContentData|ContentData} ID.
     */
    this.id;
    /**
     * @instance
     * @memberof Flybits.SurveyResponse
     * @member {Flybits.SurveyResponse.QuestionAnswerMap[]} answers 
     */
    this.answers = [];
    if(serverObj){
      this.fromJSON(serverObj);
    }
  }

  SurveyResponse.prototype.fromJSON = function(serverObj){
    this.id = serverObj.id;
  };

  /**
   * Factory method that creates an instance and conveniently maps raw response IDs to {@link Flybits.SurveyQuestion|SurveyQuestion} entities. 
   * @memberof Flybits.SurveyResponse
   * @function
   * @param {object} serverObj 
   * @param {Flybits.Survey} survey 
   */
  SurveyResponse.parseResponse = function(serverObj,survey){
    var def = new Flybits.Deferred();
    var instance = new Flybits.SurveyResponse(serverObj);
    var pagedAnswers = serverObj.payload.answers;

    pagedAnswers.fetchAll().then(function(allAnswers){
      instance.answers = allAnswers.map(function(answerObj){
        var question = survey.questions.filter(function(question){
          return question.id === answerObj.questionNumber;
        })[0];
        var answer = answerObj.answer[0];
        if(question.choices.length){
          answer = answerObj.answer.map(function(id){
            return question.choices.filter(function(choice){
              return choice.key === id;
            })[0];
          });
        }
        return {
          answer: answer,
          question: question
        };
      });
      def.resolve(instance);
    }).catch(function(e){
      def.reject(e);
    });

    return def.promise;
  };

  return SurveyResponse;
})();
var SurveyValidation = (function(){
  
  /**
   * Base Validator class
   */
  var BaseValidator = (function(){
    function BaseValidator(question){
      this.question = question;
    }
  
    BaseValidator.prototype.validate = function(){
      var validation = new Flybits.Validation();
      if(this.question.isRequired){
        if(this.question.answer instanceof Array){
          var emptyAnswers = this.question.answer.filter(function(a){
            return Flybits.util.Obj.isNull(a);
          });
          if(!this.question.answer.length || emptyAnswers.length){
            validation.addError('Missing answer','This is a required question.',{
              code: Flybits.Validation.type.MISSINGARG,
              context: this.question
            });
          }
        } else if(Flybits.util.Obj.isNull(this.question.answer)){
          validation.addError('Missing answer','This is a required question.',{
            code: Flybits.Validation.type.MISSINGARG,
            context: this.question
          });
        }
      }
  
      return validation;
    };

    return BaseValidator;
  })();

  /**
   * Number Validator
   */
  var NumberValidator = (function(){
    function NumberValidator(question){
      BaseValidator.call(this, question);
    }

    NumberValidator.prototype = Object.create(BaseValidator.prototype);
    NumberValidator.prototype.constructor = NumberValidator;

    NumberValidator.prototype.validate = function(){
      var validation = BaseValidator.prototype.validate.call(this);
      if(isNaN(this.question.answer)){
        validation.addError('Invalid answer type','Answer must be a number.',{
          code: Flybits.Validation.type.INVALIDARG,
          context: this.question
        });
      }

      var questionMeta = this.question.meta;
      if(questionMeta && questionMeta.number){
        var numMeta = questionMeta.number;
        if(numMeta.hasOwnProperty('min') && this.question.answer < numMeta.min){
          validation.addError('Invalid range','Answer must be greater than or equal to '+numMeta.min,{
            code: Flybits.Validation.type.INVALIDARG,
            context: this.question
          });
        }
        if(numMeta.hasOwnProperty('max') && this.question.answer > numMeta.max){
          validation.addError('Invalid range','Answer must be less than or equal to '+numMeta.max,{
            code: Flybits.Validation.type.INVALIDARG,
            context: this.question
          });
        }
        if(numMeta.hasOwnProperty('increment') && (this.question.answer % (((numMeta.min || 0) + numMeta.increment) !== 0) || this.question.answer === (numMeta.min || 0))){
          validation.addError('Invalid range','Answer must be a multiple of '+((numMeta.min || 0)+numMeta.increment),{
            code: Flybits.Validation.type.INVALIDARG,
            context: this.question
          });
        }
      }

      return validation;
    }
  })();

  var MultipleChoiceValidator = (function(){
    function MultipleChoiceValidator(question){
      BaseValidator.call(this, question);
    }

    MultipleChoiceValidator.prototype = Object.create(BaseValidator.prototype);
    MultipleChoiceValidator.prototype.constructor = MultipleChoiceValidator;

    MultipleChoiceValidator.prototype.validate = function(){
      var validation = BaseValidator.prototype.validate.call(this);

      var nullAnswers = this.question.answer.filter(function(q){
        return Flybits.util.Obj.isEmpty(q);
      });
      
      if(nullAnswers.length){
        validation.addError('Invalid values','Cannot have null values',{
          code: Flybits.Validation.type.INVALIDARG,
          context: this.question
        });
      }

      return validation;
    };
  })();

  var SurveyValidation = {
    validatorMap: {
      shortAnswer: BaseValidator,
      longAnswer: BaseValidator,
      number: NumberValidator,  
      multipleChoice: BaseValidator,
      checkboxes: MultipleChoiceValidator,
      dropdown: BaseValidator
    },
    getValidator: function(surveyQuestion){
      var ValidatorClass = this.validatorMap[surveyQuestion.type] || BaseValidator;

      return new ValidatorClass(surveyQuestion);
    }
  };

  return SurveyValidation;
})();
/**
 * @classdesc Flybits core user model
 * @class
 * @memberof Flybits
 * @extends BaseModel
 * @implements {Flybits.interface.Serializable}
 * @param {Object} serverObj Raw Flybits core model `Object` directly from API.
 */
Flybits.User = (function(){
  var ObjUtil = Flybits.util.Obj;

  function User(serverObj){
    BaseModel.call(this,serverObj);
    if(serverObj){
      this.fromJSON(serverObj);
    }
  }
  User.prototype = Object.create(BaseModel.prototype);
  User.prototype.constructor = User;
  User.prototype.implements('Serializable');

  /**
   * @memberof Flybits.User
   * @constant {Object} reqKeys Map of model properties that can be used to order by and search for this model.  Currently comprising of, `id`, `email`, `firstName`, and `lastName`
   */
  User.prototype.reqKeys = User.reqKeys = ObjUtil.extend(BaseModel.prototype.reqKeys,{
    email: 'email',
    firstName: 'firstName',
    lastName: 'lastName'
  });

  User.prototype.fromJSON = function(serverObj){
    /**
     * @instance
     * @memberof Flybits.User
     * @member {string} email Registered email of the user.
     */
    this.email = serverObj.email;
    /**
     * @instance
     * @memberof Flybits.User
     * @member {string} firstName First name of the user.
     */
    this.firstName = serverObj.firstName;
    /**
     * @instance
     * @memberof Flybits.User
     * @member {string} lastName Last name of the user.
     */
    this.lastName = serverObj.lastName;

    /**
     * @instance
     * @memberof Flybits.User
     * @member {boolean} isConfirmed Flag indicating whether or not this user has successfully verified their account.
     */
    this.isConfirmed = serverObj.isConfirmed;
  };

  User.prototype.toJSON = function(){
    var retObj = {
      email: this.email,
      firstName: this.firstName,
      lastName: this.lastName,
      isConfirmed: this.isConfirmed
    };

    if(this.id){
      retObj.id = this.id;
    }

    return retObj;
  };

  return User;
})();

/**
 * @classdesc Standard class used across the SDK to indicate the state of an asynchronous validation event or error.  It is comprised of a `state` which indicates the result of an operation and also an `errors` array should the `state` be `false`.  This class is often used as the error object returned from API operations and also as the result of model based validation which can incur multiple errors at once.
 * @class
 * @memberof Flybits
 */
Flybits.Validation = (function(){
  /**
   * @typedef ValidationError
   * @memberof Flybits.Validation
   * @type Object
   * @property {string} header Generally a short and broad error message
   * @property {string} message A more in depth explanation of the error.
   * @property {string} context This is populated if an error occurs that relates to one of the input properties of an operation and will be the property's key.
   * @property {number} code An internal error code indicating error type. This property is only populated when errors that can be discerned by the SDK occur. Errors that occur server side and cannot be discerned by the SDK will populate an HTTP status code in the `serverCode` property.  For instance, if you forget to supply required property the `code` property would be populated with `Flybits.Validation.type.MISSINGARG`.  On the other hand if there's a server outage, the `serverCode` would be populated with a 404 or 500.
   * @property {number} serverCode This is populated with an HTTP status code when a server side error occurs that cannot be discerned by the SDK.
   */

  function Validation(){
    /**
     * @instance
     * @memberof Flybits.Validation
     * @member {boolean} state Indicates the resultant state of an asynchronous task.
     */
    this.state = true;
    /**
     * @instance
     * @memberof Flybits.Validation
     * @member {Flybits.Validation.ValidationError[]} errors An array of errors that have accumulated because of an asynchronous task.
     */
    this.errors = [];

    /**
     * @instance
     * @memberof Flybits.Validation
     * @member {string} stacktrace Stacktrace of where the Validation model was created.  Ideally it should mark where the error has occurred.  This can be explicitly marked by calling {@link Flybits.Validation#setStackTrace}
     */
    this.stacktrace = "";

    this.setStackTrace();
  };

  Validation.prototype = {
    /**
     * Used to add error objects to the `Validation` instance.
     * @function
     * @instance
     * @memberof Flybits.Validation
     * @param {string} header Generally a short and broad error message
     * @param {string} message A more in depth explanation of the error.
     * @param {Object} detailsObj Optional extra details about the error.
     * @param {string} detailsObj.context This is populated if an error occurs that relates to one of the input properties of an operation and will be the property's key.
     * @param {number} detailsObj.code An internal error code indicating error type. This property is only populated when errors that can be discerned by the SDK occur. Errors that occur server side and cannot be discerned by the SDK will populate an HTTP status code in the `serverCode` property.  For instance, if you forget to supply required property the `code` property would be populated with `Flybits.Validation.type.MISSINGARG`.  On the other hand if there's a server outage, the `serverCode` would be populated with a 404 or 500.
     * @param {number} detailsObj.serverCode This is populated with an HTTP status code when a server side error occurs that cannot be discerned by the SDK.
     * @return {Flybits.Validation} The `Validation` instance the method has been invoked upon to allow for method chaining.
     */
    addError: function(header,message,detailsObj){
      this.state = false;
      var retObj = {
        header: header,
        message: message
      };
      if(detailsObj){
        retObj.context = detailsObj.context;
        retObj.code = detailsObj.code;
        retObj.serverCode = detailsObj.serverCode;
      }
      this.errors.push(retObj);

      return this;
    },
    /**
     * Used to merge two validation objects together.
     * @function
     * @instance
     * @memberof Flybits.Validation
     * @param {Flybits.Validation} The `Validation` instance that is to be merged into this instance.
     * @return {Flybits.Validation} The `Validation` instance the method has been invoked upon to allow for method chaining.
     */
    merge: function(validation){
      Array.prototype.push.apply(this.errors,validation.errors);
      this.state = this.errors.length < 1;

      return this;
    },
    /**
     * Used to retrieve the first available error if available.
     * @function
     * @instance
     * @memberof Flybits.Validation
     * @return {Flybits.Validation.ValidationError} First available error if validation state is `false` and errors have been found.
     * @return {null} `null` if no errors are available.
     */
    firstError: function(){
      if(this.errors.length > 0){
        return this.errors[0];
      }
      return null;
    },
    /**
     * Sets the stack trace origin point.  This is useful for when the `Validation`
     * instance is created early in a code block but only sent in the promise 
     * rejection later in a code block.
     * @function
     * @instance
     * @memberof Flybits.Validation
     * @return {Flybits.Validation} The `Validation` instance the method has been invoked upon to allow for method chaining.
     */
    setStackTrace: function(){
      var flyError = new Error();
      flyError.name = "FlybitsError";
      this.stacktrace = flyError.stack;
      if(this.stacktrace){
        this.stacktrace = this.stacktrace.split('\n').filter(function(str){
          return str.indexOf('Validation') < 0;
        }).join('\n');
      }
      return this;
    },

    toJSON: function(){
      return {
        state: this.state,
        errors: this.errors,
        stacktrace: this.stacktrace
      };
    }
  };

  /**
   * @memberof Flybits.Validation
   * @member {Object} type A mapping of SDK error codes.
   * @constant
   * @property {number} MALFORMED This error is usually thrown when an input property supplied to an library operation is incorrectly formatted, or sometimes a server response is not recognized by the library.
   * @property {number} INVALIDARG This error is thrown when an input property supplied to an library operation is semantically incorrect.
   * @property {number} MISSINGARG This error is thrown when a required property is not supplied to an library operation.
   * @property {number} NOTFOUND Usually thrown when model retrieval has yielded no results with provided input parameters.
   * @property {number} CONNECTIONERROR Error thrown when the library loses connection to particular resources.
   * @property {number} UNAUTHENTICATED Error is thrown when library operation requires authentication and current session is not found or expired.
   * @property {number} RETRIEVALERROR This error is thrown when any retrieval library operation fails to complete.
   * @property {number} NOTSUPPORTED Error is thrown when an operation or entity is not supported by the library.
   * @property {number} UNEXPECTED Error is thrown when an operation failed due to unexpected behavior.
   */
  Validation.prototype.type = Validation.type = {};
  Validation.prototype.type.MALFORMED = Validation.type.MALFORMED = 1000;
  Validation.prototype.type.INVALIDARG = Validation.type.INVALIDARG = 1001;
  Validation.prototype.type.MISSINGARG = Validation.type.MISSINGARG = 1002;
  Validation.prototype.type.NOTFOUND = Validation.type.NOTFOUND = 1003;
  Validation.prototype.type.CONNECTIONERROR = Validation.type.CONNECTIONERROR = 1004;
  Validation.prototype.type.UNAUTHENTICATED = Validation.type.UNAUTHENTICATED = 1005;
  Validation.prototype.type.RETRIEVALERROR = Validation.type.RETRIEVALERROR = 1006;
  Validation.prototype.type.NOTSUPPORTED = Validation.type.NOTSUPPORTED = 1007;
  Validation.prototype.type.UNEXPECTED = Validation.type.UNEXPECTED = 1008;

  return Validation;
})();

Flybits.store.Property = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;
  var _ready = new Deferred();

  var resolveStorageEngine = function(){
    var def = new Deferred();
    var availableStorage = [ForageStore,IDBStore,LocalStorageStore,CookieStore,FileStore,MemoryStore];
    var checkStorageEngine = function(){
      if(availableStorage.length < 1){
        def.reject(new Validation().addError('No supported property storage engines'))
      }
      var CurEngine = availableStorage.shift();
      CurEngine.isSupported().then(function(){
        def.resolve(new CurEngine(Flybits.cfg.store.SDKPROPS));
      }).catch(function(){
        checkStorageEngine();
      });
    };
    checkStorageEngine();

    return def.promise;
  };

  var Property = {
    ready: _ready.promise,
    init: function(){
      var property = this;
      var engineInit = resolveStorageEngine();
      engineInit.then(function(engine){
        property.storageEngine = engine;
        _ready.resolve();
      });
      return engineInit;
    },
    remove: function(key){
      return this.storageEngine.removeItem(key);
    },
    set: function(key, value){
      if(!value){
        return this.remove(key);
      }
      return this.storageEngine.setItem(key, value);
    },
    get: function(key){
      return this.storageEngine.getItem(key);
    }
  };

  return Property;
})();

Flybits.store.Record = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;

  var resolveStorageEngine = function(storeName){
    var def = new Deferred();
    var availableStorage = [ForageStore,IDBStore,FileStore,MemoryStore];
    var checkStorageEngine = function(){
      if(availableStorage.length < 1){
        def.reject(new Validation().addError('No supported property storage engines'))
      }
      var CurEngine = availableStorage.shift();
      CurEngine.isSupported().then(function(){
        def.resolve(new CurEngine(storeName));
      }).catch(function(){
        checkStorageEngine();
      });
    };
    checkStorageEngine();

    return def.promise;
  };

  function RecordStore(storeName){
    BaseObj.call(this);
    var store = this;
    this.storeName = storeName || 'flb.records.'+Date.now();
    var def = new Deferred();
    this.ready = def.promise;

    var engineInit = resolveStorageEngine(this.storeName);
    engineInit.then(function(engine){
      store.storageEngine = engine;
      def.resolve();
    });
  };

  RecordStore.prototype = Object.create(BaseObj.prototype);
  RecordStore.prototype.constructor = RecordStore;

  RecordStore.prototype.remove = function(key){
    return this.storageEngine.removeItem(key);
  };
  RecordStore.prototype.set = function(key, value){
    if(!value){
      return this.remove(key);
    }
    return this.storageEngine.setItem(key, value);
  };
  RecordStore.prototype.get = function(key){
    return this.storageEngine.getItem(key);
  };
  RecordStore.prototype.keys = function(){
    return this.storageEngine.getKeys();
  };
  RecordStore.prototype.clear = function(){
    return this.storageEngine.clearAll();
  };

  return RecordStore;
})();
var CookieStore = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;
  var BrowserUtil = Flybits.util.Browser;

  function CookieStore(){
    BaseObj.call(this);
  };

  CookieStore.prototype = Object.create(BaseObj.prototype);
  CookieStore.prototype.constructor = CookieStore;
  CookieStore.prototype.implements('PropertyStore');

  CookieStore.prototype.isSupported = CookieStore.isSupported = function(){
    var def = new Deferred();
    var validation = new Validation();
    var support = typeof document === 'object' && 'cookie' in document;
    if(support){
      try{
        BrowserUtil.setCookie('flbstoresupport','true');
        BrowserUtil.setCookie('flbstoresupport','true',new Date(0));
        def.resolve();
      } catch(e){
        def.reject(validation.addError('Storage not supported','Access error:' + e,{
          context: 'cookie'
        }));
      }
    } else{
      def.reject(validation.addError('Storage not supported','Missing reference in namespace.',{
        context: 'cookie'
      }));
    }

    return def.promise;
  };

  CookieStore.prototype.getItem = function(key){
    return Promise.resolve(BrowserUtil.getCookie(key));
  };
  CookieStore.prototype.setItem = function(key, value){
    BrowserUtil.setCookie(key, value);
    return Promise.resolve();
  };
  CookieStore.prototype.removeItem = function(key){
    BrowserUtil.setCookie(key, '', new Date(0));
    return Promise.resolve();
  };

  return CookieStore;

})();
var FileStore = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;

  function FileStore(storeName){
    BaseObj.call(this);
    storeName = storeName || 'flb.general';
    this.storage = Persistence.create({
      dir: Flybits.cfg.store.RESOURCEPATH + '/'+storeName
    });
    this.storage.initSync();
  };

  FileStore.prototype = Object.create(BaseObj.prototype);
  FileStore.prototype.constructor = FileStore;
  FileStore.prototype.implements('PropertyStore');

  FileStore.prototype.isSupported = FileStore.isSupported = function(){
    var def = new Deferred();
    var validation = new Validation();
    var support = typeof fs === 'object' && typeof Persistence === 'object' && typeof __dirname === 'string';
    if(support){
      def.resolve();
    } else{
      def.reject(validation.addError('Storage not supported','Missing reference in namespace.',{
        context: 'fs'
      }));
    }

    return def.promise;
  };

  FileStore.prototype.getItem = function(key){
    return this.storage.getItem(key);
  };
  FileStore.prototype.setItem = function(key, value){
    if(!value){
      return this.removeItem(key);
    } else{
      return this.storage.setItem(key,value);
    }
  };
  FileStore.prototype.removeItem = function(key){
    return this.storage.removeItem(key);
  };
  FileStore.prototype.getKeys = function(){
    return Promise.resolve(this.storage.keys());
  };
  FileStore.prototype.clearAll = function(){
    return this.storage.clear();
  };

  return FileStore;

})();
var ForageStore = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;

  function ForageStore(storeName){
    BaseObj.call(this);
    this.store = localforage.createInstance({
      name: storeName
    });
  };

  ForageStore.prototype = Object.create(BaseObj.prototype);
  ForageStore.prototype.constructor = ForageStore;
  ForageStore.prototype.implements('PropertyStore');

  ForageStore.prototype.isSupported = ForageStore.isSupported = function(){
    var def = new Deferred();
    var validation = new Validation();
    var support = typeof window === 'object' && window.hasOwnProperty('localforage');
    if(support){
      localforage.setItem('flbstoresupport',true).then(function(){
        return localforage.removeItem('flbstoresupport');
      }).then(function(){
        def.resolve();
      }).catch(function(e){
        def.reject(validation.addError('Storage not supported','Access error:' + e,{
          context: 'localforage'
        }));
      });
    } else{
      def.reject(validation.addError('Storage not supported','No library detected',{
        context: 'localforage'
      }));
    }

    return def.promise;
  };

  ForageStore.prototype.getItem = function(key){
    return this.store.getItem(key);
  };
  ForageStore.prototype.setItem = function(key, value){
    return this.store.setItem(key, value);
  };
  ForageStore.prototype.removeItem = function(key){
    return this.store.removeItem(key);
  };
  ForageStore.prototype.getKeys = function(){
    return this.store.keys();
  };
  ForageStore.prototype.clearAll = function(){
    return this.store.clear();
  };

  return ForageStore;

})();
var IDB = function() {
  function toArray(arr) {
    return Array.prototype.slice.call(arr);
  }

  function promisifyRequest(request) {
    return new Promise(function(resolve, reject) {
      request.onsuccess = function() {
        resolve(request.result);
      };

      request.onerror = function() {
        reject(request.error);
      };
    });
  }

  function promisifyRequestCall(obj, method, args) {
    var request;
    var p = new Promise(function(resolve, reject) {
      request = obj[method].apply(obj, args);
      promisifyRequest(request).then(resolve, reject);
    });

    p.request = request;
    return p;
  }

  function promisifyCursorRequestCall(obj, method, args) {
    var p = promisifyRequestCall(obj, method, args);
    return p.then(function(value) {
      if (!value) return;
      return new Cursor(value, p.request);
    });
  }

  function proxyProperties(ProxyClass, targetProp, properties) {
    properties.forEach(function(prop) {
      Object.defineProperty(ProxyClass.prototype, prop, {
        get: function() {
          return this[targetProp][prop];
        },
        set: function(val) {
          this[targetProp][prop] = val;
        }
      });
    });
  }

  function proxyRequestMethods(ProxyClass, targetProp, Constructor, properties) {
    properties.forEach(function(prop) {
      if (!(prop in Constructor.prototype)) return;
      ProxyClass.prototype[prop] = function() {
        return promisifyRequestCall(this[targetProp], prop, arguments);
      };
    });
  }

  function proxyMethods(ProxyClass, targetProp, Constructor, properties) {
    properties.forEach(function(prop) {
      if (!(prop in Constructor.prototype)) return;
      ProxyClass.prototype[prop] = function() {
        return this[targetProp][prop].apply(this[targetProp], arguments);
      };
    });
  }

  function proxyCursorRequestMethods(ProxyClass, targetProp, Constructor, properties) {
    properties.forEach(function(prop) {
      if (!(prop in Constructor.prototype)) return;
      ProxyClass.prototype[prop] = function() {
        return promisifyCursorRequestCall(this[targetProp], prop, arguments);
      };
    });
  }

  function Index(index) {
    this._index = index;
  }

  proxyProperties(Index, '_index', [
    'name',
    'keyPath',
    'multiEntry',
    'unique'
  ]);

  proxyRequestMethods(Index, '_index', IDBIndex, [
    'get',
    'getKey',
    'getAll',
    'getAllKeys',
    'count'
  ]);

  proxyCursorRequestMethods(Index, '_index', IDBIndex, [
    'openCursor',
    'openKeyCursor'
  ]);

  function Cursor(cursor, request) {
    this._cursor = cursor;
    this._request = request;
  }

  proxyProperties(Cursor, '_cursor', [
    'direction',
    'key',
    'primaryKey',
    'value'
  ]);

  proxyRequestMethods(Cursor, '_cursor', IDBCursor, [
    'update',
    'delete'
  ]);

  // proxy 'next' methods
  ['advance', 'continue', 'continuePrimaryKey'].forEach(function(methodName) {
    if (!(methodName in IDBCursor.prototype)) return;
    Cursor.prototype[methodName] = function() {
      var cursor = this;
      var args = arguments;
      return Promise.resolve().then(function() {
        cursor._cursor[methodName].apply(cursor._cursor, args);
        return promisifyRequest(cursor._request).then(function(value) {
          if (!value) return;
          return new Cursor(value, cursor._request);
        });
      });
    };
  });

  function ObjectStore(store) {
    this._store = store;
  }

  ObjectStore.prototype.createIndex = function() {
    return new Index(this._store.createIndex.apply(this._store, arguments));
  };

  ObjectStore.prototype.index = function() {
    return new Index(this._store.index.apply(this._store, arguments));
  };

  proxyProperties(ObjectStore, '_store', [
    'name',
    'keyPath',
    'indexNames',
    'autoIncrement'
  ]);

  proxyRequestMethods(ObjectStore, '_store', IDBObjectStore, [
    'put',
    'add',
    'delete',
    'clear',
    'get',
    'getAll',
    'getKey',
    'getAllKeys',
    'count'
  ]);

  proxyCursorRequestMethods(ObjectStore, '_store', IDBObjectStore, [
    'openCursor',
    'openKeyCursor'
  ]);

  proxyMethods(ObjectStore, '_store', IDBObjectStore, [
    'deleteIndex'
  ]);

  function Transaction(idbTransaction) {
    this._tx = idbTransaction;
    this.complete = new Promise(function(resolve, reject) {
      idbTransaction.oncomplete = function() {
        resolve();
      };
      idbTransaction.onerror = function() {
        reject(idbTransaction.error);
      };
      idbTransaction.onabort = function() {
        reject(idbTransaction.error);
      };
    });
  }

  Transaction.prototype.objectStore = function() {
    return new ObjectStore(this._tx.objectStore.apply(this._tx, arguments));
  };

  proxyProperties(Transaction, '_tx', [
    'objectStoreNames',
    'mode'
  ]);

  proxyMethods(Transaction, '_tx', IDBTransaction, [
    'abort'
  ]);

  function UpgradeDB(db, oldVersion, transaction) {
    this._db = db;
    this.oldVersion = oldVersion;
    this.transaction = new Transaction(transaction);
  }

  UpgradeDB.prototype.createObjectStore = function() {
    return new ObjectStore(this._db.createObjectStore.apply(this._db, arguments));
  };

  proxyProperties(UpgradeDB, '_db', [
    'name',
    'version',
    'objectStoreNames'
  ]);

  proxyMethods(UpgradeDB, '_db', IDBDatabase, [
    'deleteObjectStore',
    'close'
  ]);

  function DB(db) {
    this._db = db;
  }

  DB.prototype.transaction = function() {
    return new Transaction(this._db.transaction.apply(this._db, arguments));
  };

  proxyProperties(DB, '_db', [
    'name',
    'version',
    'objectStoreNames'
  ]);

  proxyMethods(DB, '_db', IDBDatabase, [
    'close'
  ]);

  // Add cursor iterators
  // TODO: remove this once browsers do the right thing with promises
  ['openCursor', 'openKeyCursor'].forEach(function(funcName) {
    [ObjectStore, Index].forEach(function(Constructor) {
      Constructor.prototype[funcName.replace('open', 'iterate')] = function() {
        var args = toArray(arguments);
        var callback = args[args.length - 1];
        var nativeObject = this._store || this._index;
        var request = nativeObject[funcName].apply(nativeObject, args.slice(0, -1));
        request.onsuccess = function() {
          callback(request.result);
        };
      };
    });
  });

  // polyfill getAll
  [Index, ObjectStore].forEach(function(Constructor) {
    if (Constructor.prototype.getAll) return;
    Constructor.prototype.getAll = function(query, count) {
      var instance = this;
      var items = [];

      return new Promise(function(resolve) {
        instance.iterateCursor(query, function(cursor) {
          if (!cursor) {
            resolve(items);
            return;
          }
          items.push(cursor.value);

          if (count !== undefined && items.length == count) {
            resolve(items);
            return;
          }
          cursor.continue();
        });
      });
    };
  });

  var exp = {
    open: function(name, version, upgradeCallback) {
      var p = promisifyRequestCall(indexedDB, 'open', [name, version]);
      var request = p.request;

      request.onupgradeneeded = function(event) {
        if (upgradeCallback) {
          upgradeCallback(new UpgradeDB(request.result, event.oldVersion, request.transaction));
        }
      };

      return p.then(function(db) {
        return new DB(db);
      });
    },
    delete: function(name) {
      return promisifyRequestCall(indexedDB, 'deleteDatabase', [name]);
    }
  };

  return exp;
};

var IDBStore = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;

  var INTERNALSTORENAME = 'keyvaluepairs';

  function IDBStore(storeName){
    BaseObj.call(this);
    storeName = storeName || 'flb.general';
    this.storeName = storeName;
    this._ready = IDB().open(storeName, undefined,function(db){
      db.createObjectStore(INTERNALSTORENAME);
    });
  };

  IDBStore.prototype = Object.create(BaseObj.prototype);
  IDBStore.prototype.constructor = IDBStore;
  IDBStore.prototype.implements('PropertyStore');

  IDBStore.prototype.isSupported = IDBStore.isSupported = function(){
    var def = new Deferred();
    var validation = new Validation();
    var support = typeof window === 'object' && 
                  window.hasOwnProperty('indexedDB') && 
                  typeof navigator === 'object' &&
                  (!(/^Apple/.test(navigator.vendor)) ||
                  (/^Apple/.test(navigator.vendor) && /AppleWebKit[/]60.*Version[/][89][.]/.test(navigator.appVersion)));
    if(support){
      def.resolve();
    } else{
      def.reject(validation.addError('Storage not supported','No compatible interface detected',{
        context: 'indexedDB'
      }));
    }

    return def.promise;
  };

  IDBStore.prototype.getItem = function(key){
    return this._ready.then(function(db){
      return db.transaction(INTERNALSTORENAME).objectStore(INTERNALSTORENAME).get(key);
    });
  };
  IDBStore.prototype.setItem = function(key, value){
    return this._ready.then(function(db){
      var tx = db.transaction(INTERNALSTORENAME,'readwrite');
      tx.objectStore(INTERNALSTORENAME).put(value,key);
      return tx.complete;
    });
  };
  IDBStore.prototype.removeItem = function(key){
    return this._ready.then(function(db){
      var tx = db.transaction(INTERNALSTORENAME,'readwrite');
      tx.objectStore(INTERNALSTORENAME).delete(key);
      return tx.complete;
    });
  };
  IDBStore.prototype.getKeys = function(){
    return this._ready.then(function(db){
      var tx = db.transaction(INTERNALSTORENAME);
      var keys = [];
      var store = tx.objectStore(INTERNALSTORENAME);

      (store.iterateKeyCursor || store.iterateCursor).call(store, function(cursor){
        if (!cursor) return;
        keys.push(cursor.key);
        cursor.continue();
      });

      return tx.complete.then(function(){
        return keys;
      });
    });
  };
  IDBStore.prototype.clearAll = function(){
    return dbPromise.then(function(db){
      var tx = db.transaction(INTERNALSTORENAME, 'readwrite');
      tx.objectStore(INTERNALSTORENAME).clear();
      return tx.complete;
    });
  };

  return IDBStore;
})();
var LocalStorageStore = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;

  function LocalStorageStore(){
    BaseObj.call(this);
    this.store = localStorage;
  };

  LocalStorageStore.prototype = Object.create(BaseObj.prototype);
  LocalStorageStore.prototype.constructor = LocalStorageStore;
  LocalStorageStore.prototype.implements('PropertyStore');

  LocalStorageStore.prototype.isSupported = LocalStorageStore.isSupported = function(){
    var def = new Deferred();
    var validation = new Validation();
    var support = typeof window === 'object' && window.hasOwnProperty('localStorage');
    if(support){
      try {
        localStorage.setItem('flbstoresupport', true);
        localStorage.removeItem('flbstoresupport');
        def.resolve()
      } catch (e) {
        def.reject(validation.addError('Storage not supported','Access error:' + e,{
          context: 'localStorage'
        }));
      }
    } else{
      def.reject(validation.addError('Storage not supported','Missing reference in namespace.',{
        context: 'localStorage'
      }));
    }

    return def.promise;
  };

  LocalStorageStore.prototype.getItem = function(key){
    return Promise.resolve(this.store.getItem(key));
  };
  LocalStorageStore.prototype.setItem = function(key, value){
    this.store.setItem(key, value);
    return Promise.resolve();
  };
  LocalStorageStore.prototype.removeItem = function(key){
    this.store.removeItem(key);
    return Promise.resolve();
  };

  return LocalStorageStore;

})();
var MemoryStore = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;

  function MemoryStore(){
    BaseObj.call(this);
    this.store = {};
  };

  MemoryStore.prototype = Object.create(BaseObj.prototype);
  MemoryStore.prototype.constructor = MemoryStore;
  MemoryStore.prototype.implements('PropertyStore');

  MemoryStore.prototype.isSupported = MemoryStore.isSupported = function(){
    return Promise.resolve();
  };

  MemoryStore.prototype.getItem = function(key){
    return Promise.resolve(this.store[key]);
  };
  MemoryStore.prototype.setItem = function(key, value){
    this.store[key] = value;
    return Promise.resolve();
  };
  MemoryStore.prototype.removeItem = function(key){
    delete this.store[key];
    return Promise.resolve();
  };
  MemoryStore.prototype.getKeys = function(){
    return Promise.resolve(Object.keys(this.store));
  };
  MemoryStore.prototype.clearAll = function(){
    this.store = {};
    return Promise.resolve(this.store);
  };

  return MemoryStore;

})();
/**
 * @classdesc Abstract base class from which all identity providers are extended.
 * @memberof Flybits.idp
 * @implements {Flybits.interface.IDP}
 * @abstract
 * @class IDP
 * @param {Object} opts Configuration object to override default configuration
 * @param -
 */
Flybits.idp.IDP = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;

  function IDP(opts){
    if(this.constructor === Object){
      throw new Error('Abstract classes cannot be instantiated');
    }
    BaseObj.call(this);
    this.providerName = opts.providerName;
    this.projectID = opts.projectID;
  }

  IDP.prototype = Object.create(BaseObj.prototype);
  IDP.prototype.constructor = IDP;
  IDP.prototype.implements('IDP');

  /**
   * @abstract
   * @instance
   * @memberof Flybits.idp.IDP
   * @function getURL
   * @see Flybits.interface.IDP.getURL
   */

  /**
   * @abstract
   * @instance
   * @memberof Flybits.idp.IDP
   * @function getPayload
   * @see Flybits.interface.IDP.getPayload
   */

  return IDP;
})();

/**
 * @classdesc IDP class used to establish a session anonymously.
 * @memberof Flybits.idp
 * @extends Flybits.idp.IDP
 * @class AnonymousIDP
 * @param {Object} opts Configuration object to override default configuration.
 * @param {string} opts.projectID ID of the specific Flybits project for which to obtain authorization.
 */
Flybits.idp.AnonymousIDP = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;
  var ObjUtil = Flybits.util.Obj;

  function AnonymousIDP(opts){
    opts = opts || {};
    Flybits.idp.IDP.call(this,opts);
    this.providerName = "Anonymous";
  }

  AnonymousIDP.prototype = Object.create(Flybits.idp.IDP.prototype);
  AnonymousIDP.prototype.constructor = AnonymousIDP;

  /**
   * @memberof Flybits.idp.AnonymousIDP
   * @instance 
   * @function getURL
   */
  AnonymousIDP.prototype.getURL = function(){
    return Flybits.cfg.HOST + Flybits.cfg.res.ANONYMOUSAUTH;
  };

  /**
   * @memberof Flybits.idp.AnonymousIDP
   * @instance 
   * @function getPayload
   */
  AnonymousIDP.prototype.getPayload = function(){
    var validation = new Validation();
    var retObj = {
      projectId: this.projectID
    };

    if(!this.projectID){
      validation.addError('Missing project ID','Anonymous authentication can only be approved for a specific project ID',{
        code: Validation.type.MISSINGARG,
        context: 'projectID'
      });
    }

    if(!validation.state){
      throw validation;
    }

    return retObj;
  };  

  return AnonymousIDP;
})();

/**
 * @classdesc IDP class used to establish a session with a Flybits user.
 * @memberof Flybits.idp
 * @extends Flybits.idp.IDP
 * @class FlybitsIDP
 * @param {Object} opts Configuration object to override default configuration
 * @param {string} opts.email Email of user.
 * @param {string} opts.password Flybits user password.
 * @param {string} [opts.firstName] First name of user for Flybits user profile.  This field is only used when registering for a new Flybits user and will not update existing user profiles.  If either `firstName` or `lastName` is specified, both fields will become mandatory.
 * @param {string} [opts.lastName] Last name of user for Flybits user profile. This field is only used when registering for a new Flybits user and will not update existing user profiles.  If either `firstName` or `lastName` is specified, both fields will become mandatory.
 * @param {string} [opts.projectID] ID of the specific Flybits project for which to obtain authorization.
 */
Flybits.idp.FlybitsIDP = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;
  var Cfg = Flybits.cfg;
  var ApiUtil = Flybits.util.Api;

  function FlybitsIDP(opts){
    Flybits.idp.IDP.call(this,opts);
    this.providerName = 'Flybits';
    this.email = opts.email;
    this.password = opts.password;
    this.firstName = opts.firstName;    
    this.lastName = opts.lastName;
  }

  FlybitsIDP.prototype = Object.create(Flybits.idp.IDP.prototype);
  FlybitsIDP.prototype.constructor = FlybitsIDP;

  /**
   * @memberof Flybits.idp.FlybitsIDP
   * @instance 
   * @function getURL
   */
  FlybitsIDP.prototype.getURL = function(){
    return Flybits.cfg.HOST + Flybits.cfg.res.AUTHENTICATE;
  };

  /**
   * @memberof Flybits.idp.FlybitsIDP
   * @instance 
   * @function getPayload
   */
  FlybitsIDP.prototype.getPayload = function(){
    var validation = new Validation();
    var retObj = {
      email: this.email,
      password: this.password
    };

    if(!this.email){
      validation.addError('Missing email','No email address provided',{
        code: Validation.type.MISSINGARG,
        context: 'email'
      });
    }
    if(!this.password){
      validation.addError('Missing password','No email address provided',{
        code: Validation.type.MISSINGARG,
        context: 'password'
      });
    }
    if(this.firstName && !this.lastName){
      validation.addError('Missing last name','First name was supplied without an associated last name.',{
        code: Validation.type.MISSINGARG,
        context: 'lastName'
      });
    } else if(!this.firstName && this.lastName){
      validation.addError('Missing last name','First name was supplied without an associated last name.',{
        code: Validation.type.MISSINGARG,
        context: 'lastName'
      });
    } else if(this.firstName && this.lastName){
      retObj.firstName = this.firstName;
      retObj.lastName = this.lastName;
    }

    if(this.projectID){
      retObj.projectId = this.projectID;
    }

    if(!validation.state){
      throw validation;
    }

    return retObj;
  };

  /**
   * Requests Flybits to send a password reset email to the email address associated with the currently authenticated user.  This is only available to users who have been created by the {@link Flybits.idp.FlybitsIDP}.
   * @memberof Flybits.idp.FlybitsIDP
   * @function requestCurrentPasswordReset
   * @returns {external:Promise<undefined,Flybits.Validation>} Promise that resolves without value if a password reset email is sent.  Promise rejects with {@link Flybits.Validation|Validation} object if an error has occurred.  Possible errors can include invalid current session or if the current user session was not created using the {@link Flybits.idp.FlybitsIDP}.
   */
  FlybitsIDP.prototype.requestCurrentPasswordReset = FlybitsIDP.requestCurrentPasswordReset = function(){
    var def = new Deferred();
    var validation = new Validation();

    if(!Flybits.Session.user || !Flybits.Session.userToken){
      validation.addError('No valid session','',{
        code: Validation.type.UNAUTHENTICATED
      });
    }
    if(!Flybits.Session._idpType || Flybits.Session._idpType !== 'FlybitsIDP'){
      validation.addError('Invalid IDP type','Current session user was not created using the FlybitsIDP',{
        code: Validation.type.INVALIDARG
      });
    }

    if(!validation.state){
      def.reject(validation);
      return def.promise;
    }

    var email = Flybits.Session.user.email;
    return this.requestPasswordReset(email);
  };

  /**
   * Requests Flybits to send a password reset email to the specified email address.
   * @memberof Flybits.idp.FlybitsIDP
   * @function requestPasswordReset
   * @param {string} email Email address for Flybits user that would like to reset their password.
   * @returns {external:Promise<undefined,Flybits.Validation>} Promise that resolves without value if a password reset email is sent.  Promise rejects with {@link Flybits.Validation|Validation} object if an error has occurred.
   */
  FlybitsIDP.prototype.requestPasswordReset = FlybitsIDP.requestPasswordReset = function(email){
    var def = new Deferred();
    var url = Flybits.cfg.HOST + Flybits.cfg.res.REQRESETEMAIL;
    
    if(!email){
      def.reject(new Validation().addError('Missing email','',{
        code: Validation.type.MISSINGARG
      }));
      return def.promise;
    }

    ApiUtil.flbFetch(url,{
      method: 'POST',
      body: JSON.stringify({
        email: email
      }),
      respType: 'empty'
    }).then(function(){
      def.resolve();
    }).catch(function(e){
      def.reject(e);
    });

    return def.promise;
  };

  FlybitsIDP.prototype.requestConfirmationEmail = FlybitsIDP.requestConfirmationEmail = function(noProject){
    var def = new Deferred();
    var url = Flybits.cfg.HOST + Flybits.cfg.res.REQCONFIRMEMAIL;

    if(!Flybits.Session.userToken){
      return Promise.reject(new Validation().addError('Session not found.','Must have a valid session to request a confirmation email.',{
        code: Validation.type.UNAUTHENTICATED
      }));
    }

    var opts = {
      method: 'POST',
      respType: 'empty'
    };
    if(!noProject){
      opts.body = JSON.stringify({
        projectID: Flybits.Session._projectID
      });
    }

    ApiUtil.flbFetch(url,opts).then(function(){
      def.resolve();
    }).catch(function(e){
      def.reject(e);
    })

    return def.promise;
  };

  return FlybitsIDP;
})();

/**
 * @classdesc 
 * @memberof Flybits.idp
 * @extends Flybits.idp.IDP
 * @class OAuthIDP
 * @param {Object} opts Configuration object to override default configuration.
 * @param {string} opts.providerName name of OAuth provider.  Currently supported providers are listed as {@link Flybits.idp.OAuthIDP.provider|constants} of this IDP.
 * @param {string} opts.token OAuth token returned from third party IDP.
 * @param {string} [opts.projectID] ID of the specific Flybits project for which to obtain authorization.
 */
Flybits.idp.OAuthIDP = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;
  var ObjUtil = Flybits.util.Obj;

  function OAuthIDP(opts){
    Flybits.idp.IDP.call(this,opts);
    this.token = opts.token;
  }

  OAuthIDP.prototype = Object.create(Flybits.idp.IDP.prototype);
  OAuthIDP.prototype.constructor = OAuthIDP;

  /**
   * @memberof Flybits.idp.OAuthIDP
   * @member {Object} provider A mapping of available identity provider (IDP) names.
   * @constant
   * @property {string} FACEBOOK Internal string identifier for the Facebook IDP.
   * @property {string} GOOGLE Internal string identifier for the Google IDP.
   */
  OAuthIDP.prototype.provider = OAuthIDP.provider = {};
  OAuthIDP.prototype.provider.FACEBOOK = OAuthIDP.provider.FACEBOOK = 'facebook';
  OAuthIDP.prototype.provider.GOOGLE = OAuthIDP.provider.GOOGLE = 'gplus';

  /**
   * @memberof Flybits.idp.OAuthIDP
   * @instance 
   * @function getURL
   */
  OAuthIDP.prototype.getURL = function(){
    return Flybits.cfg.HOST + Flybits.cfg.res.OAUTH;
  };

  /**
   * @memberof Flybits.idp.OAuthIDP
   * @instance 
   * @function getPayload
   */
  OAuthIDP.prototype.getPayload = function(){
    var validation = new Validation();
    var retObj = {
      provider: this.providerName,
      accessToken: this.token
    };

    if(!this.providerName){
      validation.addError('Missing provider name','An OAuth provider name must be supplied',{
        code: Validation.type.MISSINGARG,
        context: 'providerName'
      });
    }
    if(!this.token){
      validation.addError('Missing token','No OAuth access token was supplied',{
        code: Validation.type.MISSINGARG,
        context: 'token'
      });
    }

    if(this.projectID){
      retObj.projectId = this.projectID;
    }

    if(!validation.state){
      throw validation;
    }

    return retObj;
  };  

  return OAuthIDP;
})();

/**
 * @classdesc IDP class used to establish a session using a secure signed token.
 * @memberof Flybits.idp
 * @extends Flybits.idp.IDP
 * @class SignedIDP
 * @param {Object} opts Configuration object to override default configuration.
 * @param {string} opts.accessToken Signed token used for identification.
 * @param {string} opts.signature Signature of token used for verification.
 */
Flybits.idp.SignedIDP = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;
  var ObjUtil = Flybits.util.Obj;

  function SignedIDP(opts){
    opts = opts || {};
    Flybits.idp.IDP.call(this,opts);
    this.providerName = "Signed";
    this.accessToken = opts.accessToken;
    this.signature = opts.signature;
  }

  SignedIDP.prototype = Object.create(Flybits.idp.IDP.prototype);
  SignedIDP.prototype.constructor = SignedIDP;

  /**
   * @memberof Flybits.idp.SignedIDP
   * @instance 
   * @function getURL
   */
  SignedIDP.prototype.getURL = function(){
    return Flybits.cfg.HOST + Flybits.cfg.res.SIGNEDLOGIN;
  };

  /**
   * @memberof Flybits.idp.SignedIDP
   * @instance 
   * @function getPayload
   */
  SignedIDP.prototype.getPayload = function(){
    var validation = new Validation();
    var retObj = {
      accessToken: this.accessToken,
      signature: this.signature
    };

    if(!this.accessToken){
      validation.addError('Missing access token','',{
        code: Validation.type.MISSINGARG,
        context: 'accessToken'
      });
    }

    if(!this.signature){
      validation.addError('Missing signature','',{
        code: Validation.type.MISSINGARG,
        context: 'signature'
      });
    }

    if(!validation.state){
      throw validation;
    }

    return retObj;
  };  

  return SignedIDP;
})();

/**
 * This is a utility class, do not use constructor.
 * @class
 * @classdesc Main utility class to establish and manage authenticated sessions with Flybits.
 * @memberof Flybits
 */
Flybits.Session = (function(){
  var ApiUtil = Flybits.util.Api;
  var Validation = Flybits.Validation;
  var Deferred = Flybits.Deferred;
  var User = Flybits.User;

  var deferredReady = new Flybits.Deferred();

  var Session = {
    /**
     * Promise that resolves when the SDK has a valid session. If disconnect is invoked
     * a new deferred promise will be set.
     * @memberof Flybits.Session
     * @member {external:Promise}
     */
    ready: deferredReady.promise,
    /**
     * @memberof Flybits.Session
     * @member {string} AUTHHEADER Header key that is used by the core to hold authorization tokens in HTTP requests and responses.
     * @constant
     */
    AUTHHEADER: 'x-authorization',
    /**
     * @instance
     * @memberof Flybits.Session
     * @member {Flybits.User} user {@link Flybits.User|User} currently authenticated to Flybits core.  Set to `null` if session is explicitly cleared such as after logout.
     */
    user: null,
    /**
     * @instance
     * @memberof Flybits.Session
     * @member {string} userToken Current session authorization token.  Set to `null` if session is explicitly cleared such as after logout.
     */
    userToken: null,
    userAgent: null,
    _idpType: undefined,
    _projectID: undefined,
    _deviceID: null,
    _userTokenRetrievedAt: null,
    _userTokenExpiry: null,
    /**
     * Establishes authenticated session with Flybits using an {@link Flybits.idp.IDP|IDP}
     * @memberof Flybits.Session
     * @function connect
     * @param {Flybits.idp.IDP} idp Instance of an identity provider that is an implementation of the abstract class {@link Flybits.idp.IDP}
     * @returns {external:Promise<Flybits.User,Flybits.Validation>} Promise that resolves with the {@link Flybits.User|User} model of authenticated User if session exists.  Promise rejects with {@link Flybits.Validation|Validation} object if an error has occurred.  Possible errors can include invalid/insufficient values to the {@link Flybits.idp.IDP|IDP} instance.
     */
    connect: function(idp){
      var session = this;
      var def = new Deferred();
      if(!idp instanceof Flybits.idp.FlybitsIDP){
        def.reject(new Validation().addError('Invalid IDP','The provided parameter was not an instance of Flybits.idp.IDP',{
          code: Validation.type.INVALIDARG
        }));
      }
      try{
        var authPayload = idp.getPayload();
        ApiUtil.flbFetch(idp.getURL(),{
          method: 'POST',
          body: JSON.stringify(authPayload)
        }).then(function(respObj){
          if(!respObj.headers[session.AUTHHEADER]){
            throw new Validation().addError('Session could not be refreshed','Missing/Unreadable token',{
              context: 'x-authorization',
              code: Validation.type.MALFORMED
            });
          }
          var user = new User(respObj.body);
          session.user = user;
          session.setUserToken(respObj.headers[session.AUTHHEADER]);
          session._idpType = resolveIDPTypeName(idp);
          session._projectID = idp.projectID;
          
          def.resolve(user);
        }).catch(function(e){
          def.reject(e);
        });
      } catch(e){
        def.reject(e);
      };
      
      return def.promise;
    },
    /**
     * Destroys any existing session with Flybits
     * @memberof Flybits.Session
     * @function disconnect
     * @returns {external:Promise<undefined,undefined>} Promise that resolves without value.
     */
    disconnect: function(){
      var session = this;
      var def = new Deferred();
      var url = Flybits.cfg.HOST + Flybits.cfg.res.UNAUTHENTICATE;
      var reqHeaders = {};
      reqHeaders[this.AUTHHEADER] = this.userToken;
      
      ApiUtil.flbFetch(url,{
        method: 'POST',
        headers: reqHeaders
      }).catch(function(){}).then(function(){
        session.clearSession();
        def.resolve();
      });

      return def.promise;
    },
    notifyReady: function(){
      deferredReady.resolve();
    },
    /**
     * Resets session ready promise.  Meant to be invoked before calling {@link Flybits.Session.bindSession|bindSession} or {@link Flybits.Session.refreshSession|refreshSession} if you wish to be notified when a valid session is ready in a modular way.
     * @memberof Flybits.Session
     * @function resetReady
     */
    resetReady: function(){
      deferredReady.reject();
      deferredReady = new Deferred();
      this.ready = deferredReady.promise;
    },
    /**
     * Verifies user session existence and viability.
     * @memberof Flybits.Session
     * @function resolveSession
     * @param {boolean} [doNotCheckServer=false] Flag to specify whether or not session resolution should make an AJAX call to verify session or simply draw from cache. `true` to bypass server and resolve session from cache, `false` if otherwise.  Note: `false` to check the server is the most accurate and also the default.
     * @returns {external:Promise<string,Flybits.Validation>} Promise that resolves with the current user's session token if session exists.
     */
    resolveSession: function(doNotCheckServer){
      var session = this;
      var def = new Deferred();

      if(this.userToken && doNotCheckServer){
        session.notifyReady();
        def.resolve(this.userToken);
      } else if(this.userToken && !doNotCheckServer){
        return this.refreshSession();
      } else{
        def.reject(new Validation().addError('Session not found.','',{
          code: Validation.type.UNAUTHENTICATED
        }));
      }

      return def.promise;
    },
    /**
     * Shifts the sliding window of the user session by refreshing the user's session token.
     * @memberof Flybits.Session
     * @function refreshSession
     * @returns {external:Promise<string,Flybits.Validation>} Promise that resolves with the current user's new  session token.
     */
    refreshSession: function(){
      var session = this;
      var def = new Deferred();
      var url = Flybits.cfg.HOST + Flybits.cfg.res.REFRESHAUTH;

      ApiUtil.flbFetch(url,{
        respType: 'empty'
      }).then(function(respObj){
        if(!respObj.headers[session.AUTHHEADER]){
          throw new Validation().addError('Session could not be refreshed','Missing/Unreadable token',{
            context: 'x-authorization',
            code: Validation.type.MALFORMED
          });
        } else{
          session.setUserToken(respObj.headers[session.AUTHHEADER]);
          def.resolve(session.userToken);
        }
      }).catch(function(e){
        def.reject(e);
      });

      return def.promise;
    },
    /**
     * Binds existing session to a new project.  In the Flybits platform sessions are project specific.  Also there exists non-project specific sessions for developer portal management.  Thus this method is used to bind an authenticated user to a new project.
     * @memberof Flybits.Session
     * @function bindSession
     * @param {string} projectID ID of project for which to bind new session to.
     * @returns {external:Promise<string,Flybits.Validation>} Promise that resolves with the current user's new  session token.
     */
    bindSession: function(projectID){
      var session = this;
      var def = new Deferred();
      var url = Flybits.cfg.HOST + Flybits.cfg.res.AUTHPROJECT + "?projectId="+projectID;
      var validation = new Validation();

      if(!this.user){
        validation.addError('No session','An existing session is required to bind to a project.',{
          code: Validation.type.UNAUTHENTICATED
        });
      }
      if(!projectID){
        validation.addError('Missing argument','A project ID is required to bind to a project.',{
          code: Validation.type.MISSINGARG,
          context: 'projectID'
        });
      }

      if(!validation.state){
        def.reject(validation);
        return def.promise;
      }

      ApiUtil.flbFetch(url).then(function(respObj){
        session._projectID = projectID;
        if(!respObj.headers[session.AUTHHEADER]){
          throw new Validation().addError('Session could not be set','Missing/Unreadable token',{
            context: 'x-authorization',
            code: Validation.type.MALFORMED
          });
        }
        session.setUserToken(respObj.headers[session.AUTHHEADER]);
        def.resolve(session.userToken);
      }).catch(function(e){
        def.reject(e);
      });

      return def.promise;
    },
    setUserToken: function(token){
      this.userToken = token;
      
      if(token){
        this.notifyReady();
        var jwtData = ApiUtil.base64Decode(token.split('.')[1]);
        var jwt = JSON.parse(jwtData);
        this._userTokenExpiry = jwt.exp?jwt.exp*1000:0;
        this._userTokenRetrievedAt = new Date().getTime();
        this._projectID = jwt.tenantID;
        this._persistState();
      } else{
        this._userTokenExpiry = null;
        this._userTokenRetrievedAt = null;
        this._projectID = null;
        this._persistState();
      }
    },
    clearSession: function(){
      this.user = null;
      this.setUserToken(null);
      this.resetReady();
    },
    _persistState: function(){
      var store = Flybits.store.Property;
      store.set(Flybits.cfg.store.USERTOKEN,this.userToken);
      store.set(Flybits.cfg.store.USERTOKENEXP,this._userTokenExpiry);
      store.set(Flybits.cfg.store.PROJECTID,this._projectID);
      store.set(Flybits.cfg.store.IDPTYPE,this._idpType);
      store.set(Flybits.cfg.store.DEVICEID,this._deviceID);
      store.set(Flybits.cfg.store.USER,this.user?this.user.toJSON():undefined);
    },
    _restoreState: function(){
      var def = new Deferred();
      var session = this;
      var store = Flybits.store.Property;
      var promises = [];
      promises.push(store.get(Flybits.cfg.store.USERTOKEN));
      promises.push(store.get(Flybits.cfg.store.USERTOKENEXP));
      promises.push(store.get(Flybits.cfg.store.PROJECTID));
      promises.push(store.get(Flybits.cfg.store.DEVICEID));
      promises.push(store.get(Flybits.cfg.store.IDPTYPE));
      promises.push(store.get(Flybits.cfg.store.USER));
      Promise.settle(promises).then(function(resultArr){
        if(resultArr[0].status){
          session.userToken = resultArr[0].result;
        }
        if(resultArr[1].status){
          session._userTokenExpiry = resultArr[1].result;
        }
        if(resultArr[2].status){
          session._projectID = resultArr[2].result;
        }
        if(resultArr[3].status){
          session._deviceID = resultArr[3].result;
        }
        if(resultArr[4].status){
          session._idpType = resultArr[4].result;
        }
        if(resultArr[5].status){
          session.user = new Flybits.User(resultArr[5].result);
        }
        def.resolve();
      });

      return def.promise;
    }
  };

  var resolveIDPTypeName = function(idp){
    var availableIDPs = Object.keys(Flybits.idp);

    for(var i = 1; i < availableIDPs.length; i++){
      if(idp instanceof Flybits.idp[availableIDPs[i]]){
        return availableIDPs[i];
      }
    }

    return '';
  };

  return Session;
})();

/**
 * This is a utility class, do not use constructor.
 * @class Manager
 * @classdesc Main manager to handle scheduled context retrieval from context sources and reporting to Flybits core.
 * @memberof Flybits.context
 */
Flybits.context.Manager = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;
  var ObjUtil = Flybits.util.Obj;
  var ApiUtil = Flybits.util.Api;
  var Session = Flybits.Session;

  var debouncedReportingRestart;

  var restoreTimestamps = function(){
    var lastReportedPromise = Flybits.store.Property.get(Flybits.cfg.store.CONTEXTLASTREPORTED).then(function(epoch){
      if(epoch){
        contextManager.lastReported = +epoch;
      }
    });
    var lastReportAttemptedPromise = Flybits.store.Property.get(Flybits.cfg.store.CONTEXTLASTREPORTATTEMPTED).then(function(epoch){
      if(epoch){
        contextManager.lastReportAttempted = +epoch;
      }
    });

    return Promise.settle([lastReportedPromise,lastReportAttemptedPromise]);
  };

  var contextManager = {
    /**
     * Array of registered context plugins.
     * @memberof Flybits.context.Manager
     * @member {Array<Flybits.context.ContextPlugin>} services
     */
    services: [],
    /**
     * @memberof Flybits.context.Manager
     * @member {number} reportDelay=60000 Delay in milliseconds before the next interval of context reporting begins.  Note that the timer starts after the previous interval's context reporting has completed.
     */
    reportDelay: 60000,
    _reportTimeout: null,
    /**
     * @memberof Flybits.context.Manager
     * @member {boolean} isReporting Flag indicating whether scheduled context reporting is enabled.
     */
    isReporting: false,
    /**
     * @memberof Flybits.context.Manager
     * @member {boolean} isReportSending Flag indicating if the manager is currently sending a context value report.
     */
    isReportSending: false,
    isRestarting: false,

    /**
     * @memberof Flybits.context.Manager
     * @member {number} lastReported Datetime of last successful report.
     */
    lastReported: null,
    /**
     * @memberof Flybits.context.Manager
     * @member {number} lastReportAttempted 
     */
    lastReportAttempted: null,

    /**
     * Initialization function to setup internal properties and will start reporting interval.
     * @memberof Flybits.context.Manager
     * @function
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves with no value when initialization has completed.
     */
    initialize: function(){
      var def = new Deferred();
      var manager = this;

      debouncedReportingRestart = Flybits.util.Obj.debounce(function(){
        manager.startReporting();
      },manager.reportDelay,true);

      restoreTimestamps().then(function(){
        return manager.startReporting();
      }).then(function(){
        def.resolve();
      }).catch(function(e){
        def.reject(e);
      });

      return def.promise;
    },

    /**
     * Used to register a context plugin with the manager to begin scheduled retrieval of context values.
     * @memberof Flybits.context.Manager
     * @function
     * @param {Flybits.context.ContextPlugin} contextPlugin Instance of a context plugin to be registered for scheduled retrieval and reporting.
     * @return {external:Promise<Flybits.context.ContextPlugin,Flybits.Validation>} Promise that resolves with the successfully registered context plugin if supported. Context plugin will now begin scheduled retrieval.
     */
    register: function(contextPlugin){
      var manager = this;
      var def = new Deferred();
      if(!(contextPlugin instanceof Flybits.context.ContextPlugin)){
        def.reject(new Validation().addError('Invalid Context Plugin','Provided parameter was not a valid Flybits.context.ContextPlugin instance.',{
          code: Validation.type.NOTSUPPORTED,
          context: 'contextPlugin'
        }));
        return def.promise;
      }

      contextPlugin.isSupported().then(function(){
        if(contextPlugin.refreshDelay === contextPlugin.refreshInterval.ONETIME){
          contextPlugin.collectState().then(function(){
            manager.services.push(contextPlugin);
            def.resolve(contextPlugin);
          }).catch(function(e){
            def.reject(e);
          });
        } else{
          contextPlugin.startService();
          manager.services.push(contextPlugin);
          def.resolve(contextPlugin);
        }
      }).catch(function(e){
        def.reject(e);
      });

      return def.promise;
    },
    /**
     * Used to unregister a context plugin with the manager to and stop its scheduled retrieval of context values.
     * @memberof Flybits.context.Manager
     * @function
     * @param {Flybits.context.ContextPlugin} contextPlugin Instance of a context plugin to be unregistered and have its scheduled retrieval and reporting stopped.
     * @return {Flybits.context.ContextPlugin} Context plugin instance that was unregistered.
     */
    unregister: function(contextPlugin){
      if(contextPlugin instanceof Flybits.context.ContextPlugin){
        ObjUtil.removeObject(this.services,contextPlugin);
        contextPlugin.stopService();
      }
      return contextPlugin;
    },
    /**
     * Unregisters all context plugins from the context manager.
     * @memberof Flybits.context.Manager
     * @function
     * @return {Flybits.context.Manager} Reference to the context manager to allow for method chaining.
     */
    unregisterAll: function(){
      this.stopAllServices();
      this.services = [];
      return this;
    },
    /**
     * Stops all scheduled retrieval services of registered context plugins.
     * @memberof Flybits.context.Manager
     * @function
     * @return {Flybits.context.Manager} Reference to the context manager to allow for method chaining.
     */
    stopAllServices: function(){
      var services = this.services;
      for (var i = 0; i < services.length; i++){
        services[i].stopService();
      }
      return this;
    },
    /**
     * Starts all scheduled retrieval services of registered context plugins.
     * @memberof Flybits.context.Manager
     * @function
     * @return {Flybits.context.Manager} Reference to the context manager to allow for method chaining.
     */
    startAllServices: function(){
      var services = this.services;
      for (var i = 0; i < services.length; i++){
        services[i].startService();
      }
      return this;
    },
    /**
     * Starts the scheduled service that continuously batch reports collected context data of registered context plugins.
     * @memberof Flybits.context.Manager
     * @function startReporting
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without a return value and rejects with a common Flybits Validation model instance.
     */
    startReporting: function(){
      var def = new Deferred();
      var manager = this;
      if(this.isRestarting){
        throw new Validation().addError('Already restarting service','',{
          code: Validation.type.UNEXPECTED,
          context: 'context.Manager'
        });
      }
      this.isRestarting = true;
      manager.stopReporting();

      var sessionPromise = Flybits.Session.userToken?Promise.resolve(Flybits.Session.userToken):Flybits.Session.resolveSession();
      sessionPromise.then(function(){
        var interval;
        interval = function(){
          manager.report().catch(function(e){}).then(function(){
            if(manager.isReporting){
              manager._reportTimeout = setTimeout(function(){
                interval();
              },manager.reportDelay);
            }
          });

          manager.isReporting = true;
        };
        interval();
        def.resolve();
      }).catch(function(e){
        def.reject(e);
      }).then(function(){
        manager.isRestarting = false;
      });

      return def.promise;
    },
    /**
     * Stops the scheduled service that continuously batch reports collected context data of registered context plugins.
     * @memberof Flybits.context.Manager
     * @function stopReporting
     * @return {Flybits.context.Manager} Reference to this context manager to allow for method chaining.
     */
    stopReporting: function(){
      this.isReporting = false;
      clearTimeout(this._reportTimeout);
      return this;
    },
    _gatherAllData: function(){
      var def = new Deferred();
      var services = this.services;
      var data = [];
      var serviceDeletions = [];
      var promises = [];

      for(var i = 0; i < services.length; i++){
        (function(service){
          var retrievalPromise = service._fetchCollected();
          promises.push(retrievalPromise);
          retrievalPromise.then(function(values){
            Array.prototype.push.apply(data,values.data);
            serviceDeletions.push({
              serviceRef: service,
              keys: values.keys
            });
          });
        })(services[i]);
      }

      Promise.settle(promises).then(function(){
        def.resolve({
          data: data,
          keys: serviceDeletions
        });
      });

      return def.promise;
    },
    _sendReport: function(data){
      var def = new Deferred();
      var url = Flybits.cfg.HOST + Flybits.cfg.res.CONTEXT;

      ApiUtil.flbFetch(url,{
        method: 'POST',
        body: JSON.stringify(data),
        respType: 'empty'
      }).then(function(result){
        def.resolve();
      }).catch(function(resp){
        def.reject(resp);
      });

      return def.promise;
    },
    _cleanupServices: function(serviceKeyArr){
      var def = new Deferred();
      var promises = [];

      for(var i = 0; i < serviceKeyArr.length; i++){
        (function(serviceKeyMap){
          promises.push(serviceKeyMap.serviceRef._deleteCollected(serviceKeyMap.keys));
        })(serviceKeyArr[i]);
      }

      Promise.settle(promises).then(function(){
        def.resolve();
      });

      return def.promise;
    },
    /**
     * Batch reports collected context data of registered context plugins.
     * @memberof Flybits.context.Manager
     * @function report
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without a return value and rejects with a common Flybits Validation model instance.
     */
    report: function(){
      var manager = this;
      var def = new Deferred();
      var serviceCleanup = [];

      this._gatherAllData().then(function(result){
        if(!result.data || !result.data.length){
          throw new Validation().addError('No context to upload.','',{
            code: Validation.type.NOTFOUND
          });
        }
        serviceCleanup = result.keys;
        manager.isReportSending = true;
        return manager._sendReport(result.data);
      }).then(function(){
        manager.lastReported = Date.now();
        Flybits.store.Property.set(Flybits.cfg.store.CONTEXTLASTREPORTED,manager.lastReported);
        return manager._cleanupServices(serviceCleanup);
      }).then(function(){
        def.resolve();
      }).catch(function(e){
        if(e instanceof Validation){
          def.reject(e);
        } else{
          def.reject(new Validation().addError('Context report failed.',e,{
            code: Validation.UNEXPECTED
          }));
        }
      }).then(function(){
        manager.lastReportAttempted = Date.now();
        Flybits.store.Property.set(Flybits.cfg.store.CONTEXTLASTREPORTATTEMPTED,manager.lastReportAttempted);
        manager.isReportSending = false;
      });

      return def.promise;
    },

    warmReporting: function(){
      var diff = this.lastReported? Date.now() - this.lastReported:this.reportDelay + 1;

      if(this.isReporting && !this.isReportSending && diff > this.reportDelay){
        debouncedReportingRestart.apply(this);
      }
    }
  };
  

  return contextManager;
})();

/**
 * @classdesc Abstract base class from which all context plugins are extended.
 * @memberof Flybits.context
 * @implements {Flybits.interface.ContextPlugin}
 * @abstract
 * @class ContextPlugin
 * @param {Object} opts Configuration object to override default configuration
 * @param {number} opts.refreshDelay {@link Flybits.context.ContextPlugin#refreshDelay}
 */
Flybits.context.ContextPlugin = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;

  function ContextPlugin(opts){
    if(this.constructor === Object){
      throw new Error('Abstract classes cannot be instantiated');
    }
    BaseObj.call(this);

    this._refreshTimeout = null;

    /**
     * @instance
     * @memberof Flybits.context.ContextPlugin
     * @member {number} [refreshDelay=60000] Delay in milliseconds before the next interval of context refreshing begins for this particular plugin.  Note that the timer starts after the previous interval's context refresh has completed.
     */
    this.refreshDelay = opts && opts.refreshDelay?opts.refreshDelay:this.refreshInterval.ONEMINUTE;

    /**
     * @instance
     * @memberof Flybits.context.ContextPlugin
     * @member {number} [maxStoreEntries=80] Maximum amount of entries this context type can store locally before old entries are flushed from the local store.
     */
    this.maxStoreSize = opts && opts.maxStoreSize?opts.maxStoreSize:80;

    /**
     * @instance
     * @memberof Flybits.context.ContextPlugin
     * @member {number} [maxStoreAge=86400000] Maximum age of an entry of this context type before it is flushed from the local store.  Default maximum age is 1 day old.
     */
    this.maxStoreAge = opts && opts.maxStoreAge?opts.maxStoreAge:86400000;

    /**
     * @instance
     * @memberof Flybits.context.ContextPlugin
     * @member {boolean} isServiceRunning flag indicating whether or not a context retrieval service is running for this context plugin.
     */
    this.isServiceRunning = false;

    this.collectHistorical = opts && opts.collectHistorical;

    /**
     * @instance
     * @memberof Flybits.context.ContextPlugin
     * @member {number} lastCollected Timestamp of the last context state retrieval and local storage;
     */
    this.lastCollected = -1;

    this.lastLocalValue = null;
    this.lastLocalKey = null;

    this._store = new Flybits.store.Record(this.TYPEID);
  }

  ContextPlugin.prototype = Object.create(BaseObj.prototype);
  ContextPlugin.prototype.constructor = ContextPlugin;
  ContextPlugin.prototype.implements('ContextPlugin');

  /**
   * @memberof Flybits.context.ContextPlugin
   * @member {Object} refreshInterval Common context refresh delays.
   * @constant
   * @property {number} ONETIME Indicates the value of the context should be fetched only a single time and should not be refreshed on a continuous interval.
   * @property {number} THIRTYSECONDS Common refresh delay of 30 seconds.
   * @property {number} ONEMINUTE Common refresh delay of 1 minute.
   * @property {number} ONEHOUR Common refresh delay of 1 hour.
   */
  ContextPlugin.prototype.refreshInterval = ContextPlugin.refreshInterval = {};
  ContextPlugin.prototype.refreshInterval.ONETIME = ContextPlugin.refreshInterval.ONETIME = -42;
  ContextPlugin.prototype.refreshInterval.THIRTYSECONDS = ContextPlugin.refreshInterval.THIRTYSECONDS = 1000*30;
  ContextPlugin.prototype.refreshInterval.ONEMINUTE = ContextPlugin.refreshInterval.ONEMINUTE = 1000*60*1;
  ContextPlugin.prototype.refreshInterval.ONEHOUR = ContextPlugin.refreshInterval.ONEHOUR = 1000*60*60;
  ContextPlugin.prototype.refreshInterval.ONEDAY = ContextPlugin.refreshInterval.ONEDAY = 1000*60*60*24;

  /**
   * @memberof Flybits.context.ContextPlugin
   * @member {string} TYPEID String descriptor to uniquely identify context data type on the Flybits context gateway.
   */
  ContextPlugin.prototype.TYPEID = ContextPlugin.TYPEID = 'ctx.sdk.generic';

  /**
   * Starts a scheduled service that continuously retrieves context data for this plugin.
   * @instance
   * @memberof Flybits.context.ContextPlugin
   * @function startService
   * @return {Flybits.context.ContextPlugin} Reference to this context plugin to allow for method chaining.
   */
  ContextPlugin.prototype.startService = function () {
    this.stopService();

    var interval;
    interval = function (contextPlugin) {
      contextPlugin.collectState().catch(function (e) { }).then(function () {
        if (contextPlugin.isServiceRunning) {
          contextPlugin._refreshTimeout = setTimeout(function () {
            interval(contextPlugin);
          }, contextPlugin.refreshDelay);
        }
      });

      contextPlugin.isServiceRunning = true;
    };
    interval(this);

    return this;
  };
    
  /**
   * Stops the scheduled service that continuously retrieves context data for this plugin.
   * @instance
   * @memberof Flybits.context.ContextPlugin
   * @function stopService
   * @return {Flybits.context.ContextPlugin} Reference to this context plugin to allow for method chaining.
   */
  ContextPlugin.prototype.stopService = function(){
    this.isServiceRunning = false;
    clearTimeout(this._refreshTimeout);
    return this;
  };

  /**
   * @abstract
   * @memberof Flybits.context.ContextPlugin
   * @function getState
   * @see Flybits.interface.ContextPlugin.getState
   */
  /**
   * @abstract
   * @memberof Flybits.context.ContextPlugin
   * @function isSupported
   * @see Flybits.interface.ContextPlugin.isSupported
   */

  /**
   * Force the immediate retrieval of context state from this `ContextPlugin` once and place store it into the local storage for later reporting.
   * @instance
   * @memberof Flybits.context.ContextPlugin
   * @function collectState
   * @return {external:Promise<undefined,undefined>} Promise that resolves when this `ContextPlugin` has retrieved and stored its current state.
   */
  ContextPlugin.prototype.collectState = function(){
    var def = new Deferred();
    var plugin = this;
    var store = this._store;
    var storeLength = 0;

    store.ready.then(function(){
      return store.keys();
    }).then(function(keys){
      storeLength = keys.length;
      return plugin.getState();
    }).then(function(e){
      if(!e){
        throw new Validation().addError('Empty context state collected','',{
          code: Validation.type.NOTFOUND
        });
      }
      return plugin._saveState(e);
    }).then(function(){
      plugin.lastCollected = new Date().getTime();
      if(storeLength >= plugin.maxStoreSize){
        plugin._validateStoreState();
      }
      def.resolve();
    }).catch(function(e){
      def.reject(e);
    });

    return def.promise;
  };

  /**
   * Allows for explicit injection of context values irrespective of automated collection.
   * @instance 
   * @memberof Flybits.context.ContextPlugin
   * @function setState
   * @param {Object} contextValue Context plugin specific data structure representing new value of context plugin.
   * @return {external:Promise<undefined,undefined>} Promise that resolves when this `ContextPlugin` stored provided state.
   */
  ContextPlugin.prototype.setState = function(contextValue){
    var def = new Deferred();
    var plugin = this;
    var store = this._store;
    var storeLength = 0;

    store.ready.then(function(){
      return store.keys();
    }).then(function(keys){
      storeLength = keys.length;
      return plugin._saveState(contextValue);
    }).then(function(){
      plugin.lastCollected = new Date().getTime();
      if(storeLength >= plugin.maxStoreSize){
        plugin._validateStoreState();
      }
      def.resolve();
    }).catch(function(e){
      console.error('>',plugin,e);
      def.reject();
    });

    return def.promise;
  };

  ContextPlugin.prototype._validateStoreState = function(){
    var plugin = this;
    var def = new Deferred();
    var promises = [];
    var store = this._store;
    store.keys().then(function(result){
      var keys = result;
      var now = new Date().getTime();
      var accessCount = keys.length - plugin.maxStoreSize;

      keys.sort(function(a,b){
        return (+(a.split('-')[1]))-(+(b.split('-')[1]));
      });

      if(accessCount > 0){
        for(var i = 0; i < accessCount; i++){
          promises.push(store.remove(keys.shift()));
        }
      }

      while(keys.length > 0 && (now - keys[0]) >= plugin.refreshInterval.ONEDAY){
        promises.push(store.remove(keys.shift()));
      }

      Promise.settle(promises).then(function(){
        def.resolve();
      });
    }).catch(function(){
      def.reject();
    });

    return def.promise;
  };
  ContextPlugin.prototype._fetchCollected = function(){
    var plugin = this;
    var def = new Deferred();
    var data = [];
    var keysToDelete = [];
    var store = this._store;
    store.keys().then(function(keys){
      var assembleData;
      assembleData = function(){
        if(keys.length <= 0){
          def.resolve({
            data: data,
            keys: keysToDelete
          });
          return;
        }
        var curKey = keys.pop();
        keysToDelete.push(curKey);

        store.get(curKey).then(function(item){
          var timestamp = +(curKey.split('-')[1]);
          data.push({
            timestamp: Math.round(timestamp/1000),
            dataTypeID: plugin.TYPEID,
            value: plugin._toServerFormat(item)
          });
        }).catch(function(){}).then(function(){
          assembleData();
        });
      };
      assembleData();
    }).catch(function(e){
      def.reject(e);
    });

    return def.promise;
  };
  ContextPlugin.prototype._deleteCollected = function(keys){
    var store = this._store;
    var def = new Deferred();
    var deleteData;
    deleteData = function(){
      if(keys.length <= 0){
        def.resolve();
        return;
      }
      var curKey = keys.pop();
      store.remove(curKey).catch(function(){}).then(function(){
        deleteData();
      });
    };
    deleteData();

    return def.promise;
  };
  ContextPlugin.prototype._generateKey = ContextPlugin._generateKey = function(){
    var time = new Date().getTime();
    var guid = Flybits.util.Obj.guid(1);
    return guid+'-'+time;
  };
  ContextPlugin.prototype._cleanHistorical = function(value){
    if(this.lastLocalKey && JSON.stringify(this.lastLocalValue) === JSON.stringify(value)){
      this._store.remove(this.lastLocalKey);
    }
  };
  ContextPlugin.prototype._saveState = function(value){
    var plugin = this;
    var storeKey = this._generateKey();
    
    if(!this.collectHistorical){
      this._cleanHistorical(value);
    }

    var storeSetPromise = this._store.set(storeKey,value);
    storeSetPromise.then(function(){
      plugin.lastLocalKey = storeKey;
      plugin.lastLocalValue = value;
      Flybits.context.Manager.warmReporting();
    });
    return storeSetPromise;
  };
  ContextPlugin.prototype._toServerFormat = function(item){
    if(item){
      item = Flybits.util.Obj.extend({},item);
      var keys = Object.keys(item);
      for(var i = 0; i < keys.length; i++){
        var key = keys[i];
        if(key.indexOf('.') > -1){
          item['query.'+key] = item[key];
          delete item[key];
        }
      }
    }
    return item;
  };

  return ContextPlugin;
})();

/**
 * @class Connectivity
 * @classdesc Utility class to retrieve state of browser connectivity.
 * @extends Flybits.context.ContextPlugin
 * @memberof Flybits.context
 * @param {Flybits.context.Connectivity.Options} opts Configuration object to override default configuration
 */
Flybits.context.Connectivity = (function(){
  /**
   * @typedef ConnectionState
   * @memberof Flybits.context.Connectivity
   * @type Object
   * @property {number} state Numerical state of connectivity. 0 for disconnected and 1 for connected.
   */
   /**
    * @typedef Options
    * @memberof Flybits.context.Connectivity
    * @type Object
    * @property {boolean} hardCheck=false Flag to indicate whether or not a HTTP network request is to be used to determine network connectivity as opposed to using browser's `navigator.onLine` property. If the browser does not support `navigator.onLine` a network request based connectivity check will be performed regardless of the state of this flag.
    * @property {number} refreshDelay {@link Flybits.context.ContextPlugin#refreshDelay}
    */
  var Deferred = Flybits.Deferred;
  var ObjUtil = Flybits.util.Obj;

  function Connectivity(opts){
    Flybits.context.ContextPlugin.call(this,opts);
    if(opts){
      this.opts = ObjUtil.extend({},this.opts,opts);
    }
  }

  Connectivity.prototype = Object.create(Flybits.context.ContextPlugin.prototype);
  Connectivity.prototype.constructor = Connectivity;

  /**
   * @memberof Flybits.context.Connectivity
   * @member {string} TYPEID String descriptor to uniquely identify context data type on the Flybits context gateway.
   */
  Connectivity.prototype.TYPEID = Connectivity.TYPEID = 'ctx.sdk.network';

  /**
   * Check to see if Connectivity retrieval is available on the current browser.
   * @memberof Flybits.context.Connectivity
   * @function isSupported
   * @override
   * @returns {external:Promise<undefined,Flybits.Validation>} Promise that resolves without value.  Promise rejects with a {@link Flybits.Validation} object with errors.
   */
  Connectivity.isSupported = Connectivity.prototype.isSupported = function(){
    var def = new Deferred();
    def.resolve();

    return def.promise;
  };

  /**
   * Retrieve browser's current connectivity status.
   * @memberof Flybits.context.Connectivity
   * @function getState
   * @override
   * @returns {external:Promise<Flybits.context.Connectivity.ConnectionState,Flybits.Validation>} Promise that resolves with {@link Flybits.context.Connectivity.ConnectionState|ConnectionState} object.  Promise rejects with a {@link Flybits.Validation} object with errors.
   */
  Connectivity.getState = Connectivity.prototype.getState = function(){
    var def = new Deferred();
    var plugin = this;
    if(typeof navigator === 'object' && 'onLine' in navigator && !this.opts.hardCheck){
      def.resolve({
        state: navigator.onLine?plugin.state.CONNECTED:plugin.state.DISCONNECTED
      });
    } else{
      fetch(Flybits.cfg.HOST+"/ping").then(function(resp){
        def.resolve({
          state: plugin.state.CONNECTED
        });
      }).catch(function(e){
        def.resolve({
          state: plugin.state.DISCONNECTED
        });
      });
    }

    return def.promise;
  };

  /**
   * Converts context value object into the server expected format.
   * @function
   * @memberof Flybits.context.Connectivity
   * @function _toServerFormat
   * @param {Object} contextValue
   * @return {Object} Expected server format of context value.
   */
  Connectivity._toServerFormat = Connectivity.prototype._toServerFormat = function(contextValue){
    return {
      connectionType: contextValue.state
    };
  };

  /**
   * @memberof Flybits.context.Connectivity
   * @member {Object} state Connection state constants.
   * @constant
   * @property {number} DISCONNECTED Indicates the state of being disconnected.
   * @property {number} CONNECTED Indicates the state of being connected.
   */
  Connectivity.state = Connectivity.prototype.state = {};
  Connectivity.state.DISCONNECTED = Connectivity.prototype.state.DISCONNECTED = -1;
  Connectivity.state.CONNECTED = Connectivity.prototype.state.CONNECTED = -99;

  /**
   * @memberof Flybits.context.Connectivity
   * @member opts
   * @type {Flybits.context.Connectivity.Options}
   */
  Connectivity.opts = Connectivity.prototype.opts = {
    hardCheck: false
  };

  return Connectivity;
})();

/**
 * @class Location
 * @classdesc Utility class to retrieve browser location.
 * @extends Flybits.context.ContextPlugin
 * @memberof Flybits.context
 * @param {Flybits.context.Location.Options} opts Configuration object to override default configuration
 */
Flybits.context.Location = (function(){
  /**
   * @typedef Geoposition
   * @memberof Flybits.context.Location
   * @type Object
   * @property {Object} coords
   * @property {number} coords.latitude
   * @property {number} coords.longitude
   * @property {number} timestamp
   */
   /**
    * @typedef Options
    * @memberof Flybits.context.Location
    * @type Object
    * @property {number} maximumAge=1000*60*20
    * @property {number} refreshDelay {@link Flybits.context.ContextPlugin#refreshDelay}
    */
  var Deferred = Flybits.Deferred;
  var ObjUtil = Flybits.util.Obj;

  function Location(opts){
    Flybits.context.ContextPlugin.call(this,opts);
    if(opts){
      this.opts = ObjUtil.extend({},this.opts,opts);
    }
  }

  Location.prototype = Object.create(Flybits.context.ContextPlugin.prototype);
  Location.prototype.constructor = Location;

  /**
   * @memberof Flybits.context.Location
   * @member {string} TYPEID String descriptor to uniquely identify context data type on the Flybits context gateway.
   */
  Location.prototype.TYPEID = Location.TYPEID = 'ctx.sdk.location';

  /**
   * Check to see if Location retrieval is available on the current browser.
   * @memberof Flybits.context.Location
   * @function isSupported
   * @override
   * @returns {external:Promise<undefined,Flybits.Validation>} Promise that resolves without value.  Promise rejects with a {@link Flybits.Validation} object with errors.  Possible errors can include the lack of support on webview/browser or the User explicitly denying Location retrieval.
   */
  Location.isSupported = Location.prototype.isSupported = function(){
    var def = new Deferred();

    if(typeof navigator === 'object' && navigator.geolocation){
      navigator.geolocation.getCurrentPosition(function(pos){
        def.resolve();
      },function(err){
        console.error('>',err);
        def.reject(new Flybits.Validation().addError('Location Sensing Not Supported',err.message?err.message:"User denied"));
      });
    } else{
      def.reject(new Flybits.Validation().addError('Location Sensing Not Supported',"Device GeoLocation API not found."));
    }

    return def.promise;
  };

  /**
   * Retrieve browser's current location.
   * @memberof Flybits.context.Location
   * @function getState
   * @override
   * @returns {external:Promise<Flybits.context.Location.Geoposition,Flybits.Validation>} Promise that resolves with browser's {@link Flybits.context.Location.Geoposition|Geoposition} object.  Promise rejects with a {@link Flybits.Validation} object with errors.  Possible errors can include the lack of support on webview/browser or the User explicitly denying Location retrieval.
   */
  Location.getState = Location.prototype.getState = function(){
    var def = new Deferred();

    navigator.geolocation.getCurrentPosition(function(pos){
      def.resolve({
        coords:{
          latitude: pos.coords.latitude,
          longitude: pos.coords.longitude,
          accuracy: pos.coords.accuracy
        },
        timestamp: pos.timestamp
      });
    },function(err){
      console.error('>',err);
      def.reject(new Flybits.Validation().addError('Location could not be resolved'));
    },this.opts);

    return def.promise;
  };

  /**
   * Converts context value object into the server expected format.
   * @function
   * @memberof Flybits.context.Location
   * @function _toServerFormat
   * @param {Object} contextValue
   * @return {Object} Expected server format of context value.
   */
  Location._toServerFormat = Location.prototype._toServerFormat = function(contextValue){
    return {
      lat: contextValue.coords.latitude,
      lng: contextValue.coords.longitude
    };
  };

  /**
   * @memberof Flybits.context.Location
   * @member opts
   * @type {Flybits.context.Location.Options}
   */
  Location.opts = Location.prototype.opts = {
    maximumAge: 1000 * 60 * 20
  };

  return Location;
})();

/**
 * @classdesc Model to represent an analytics event to be logged.
 * @class Event
 * @memberof Flybits.analytics
 * @extends BaseModel
 * @implements {Flybits.interface.Serializable}
 * @param {Object} serverObj Raw Flybits core model `Object` directly from API.
 */
Flybits.analytics.Event = (function(){
  var ObjUtil = Flybits.util.Obj;

  var Event = function(serverObj){
    BaseModel.call(this,serverObj);
    this._tmpID = ObjUtil.guid(1) + '-' + Date.now();
    this.type = this.types.DISCRETE;
    this.name = '';
    this.loggedAt = new Date();
    this.properties = {};
    this._internal = {};
    this._isInternal = false;

    if(serverObj){
      this.fromJSON(serverObj);
    }
  };
  Event.prototype = Object.create(BaseModel.prototype);
  Event.prototype.constructor = Event;
  Event.prototype.implements('Serializable');

  /**
   * @memberof Flybits.analytics.Event
   * @member {Object} types A mapping of event types.
   * @constant
   * @property {string} DISCRETE Event type for discrete one time events.
   * @property {string} TIMEDSTART Event type for the start of a timed event.
   * @property {string} TIMEDEND Event type for the end of a timed event.
   */
  Event.prototype.types = Event.types = {};
  Event.prototype.types.DISCRETE = Event.types.DISCRETE = 'event_discrete';
  Event.prototype.types.TIMEDSTART = Event.types.TIMEDSTART = 'event_timestart';
  Event.prototype.types.TIMEDEND = Event.types.TIMEDEND = 'event_timeend';

  Event.prototype.TIMEDREFID = Event.TIMEDREFID = 'timedRef';
  Event.prototype.OSTYPE = Event.OSTYPE = 'osType';
  Event.prototype.OSVERSION = Event.OSVERSION = 'osVersion';
  Event.prototype.UID = Event.UID = 'uid';

  Event.prototype._setProp = function(obj, key, value){
    if(typeof value === 'undefined' || value === null){
      delete obj[key];
      return this;
    }

    obj[key] = value;
    return this;
  };

  /**
   * Used to set custom properties on to an analytics event.
   * @function setProperty
   * @instance
   * @memberof Flybits.analytics.Event
   * @param {string} key Data key.
   * @param {Object} value Any valid JSON value to be stored based on provided key.  If `null` or `undefined` is provided then the provided key will be removed from the properties map.
   */
  Event.prototype.setProperty = function(key, value){
    return this._setProp(this.properties, key, value);
  };

  Event.prototype._setInternalProperty = function(key, value){
    return this._setProp(this._internal, key, value);
  };

  Event.prototype.fromJSON = function(serverObj){
    /**
     * @instance
     * @memberof Flybits.analytics.Event
     * @member {string} type Event type.
     */
    this.type = serverObj.type || this.types.DISCRETE;
    /**
     * @instance
     * @memberof Flybits.analytics.Event
     * @member {string} name Name of event.
     */
    this.name = serverObj.name;
    /**
     * @instance
     * @memberof Flybits.analytics.Event
     * @member {Date} loggedAt Date object instantiated at time of logging.
     */
    this.loggedAt = serverObj.loggedAt?new Date(serverObj.loggedAt):new Date();
    /**
     * @instance
     * @memberof Flybits.analytics.Event
     * @member {Object} properties Custom event properties.
     */
    this.properties = serverObj.properties || {};

    this._internal = serverObj.flbProperties || {};
    this._isInternal = serverObj.isFlybits || false;
  };

  Event.prototype.toJSON = function(){
    var obj = this;
    var retObj = {
      type: this.type,
      name: this.name,
      loggedAt: this.loggedAt.getTime(),
      properties: this.properties,
      flbProperties: this._internal,
      isFlybits: this._isInternal
    };

    return retObj;
  };

  return Event;
})();

/**
 * This is a utility class, do not use constructor.
 * @class Manager
 * @classdesc Main manager to collect and report events for later analysis.
 * @memberof Flybits.analytics
 */
Flybits.analytics.Manager = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;
  var Session = Flybits.Session;
  var Event = Flybits.analytics.Event;

  var restoreTimestamps = function(){
    var lastReportedPromise = Flybits.store.Property.get(Flybits.cfg.store.ANALYTICSLASTREPORTED).then(function(epoch){
      if(epoch){
        analyticsManager.lastReported = +epoch;
      }
    });
    var lastReportAttemptedPromise = Flybits.store.Property.get(Flybits.cfg.store.ANALYTICSLASTREPORTATTEMPTED).then(function(epoch){
      if(epoch){
        analyticsManager.lastReportAttempted = +epoch;
      }
    });

    return Promise.settle([lastReportedPromise,lastReportAttemptedPromise]);
  };

  var applyDefaultSysProps = function(event){
    if(typeof window === 'object'){
      event._setInternalProperty(Event.OSTYPE,'browser');
      if(window.hasOwnProperty('navigator')){
        event._setInternalProperty(Event.OSVERSION,navigator.userAgent);
      }
    } else{
      event._setInternalProperty(Event.OSTYPE,'node');
      if(typeof process !== 'undefined'){
        event._setInternalProperty(Event.OSVERSION,process.version);
      }
    }

    if(Session.user){
      event._setInternalProperty(Event.UID, Session.user.id);
    }
    return event;
  };

  var timeUnitMultiplier = {
    milliseconds: 1,
    seconds: 1000,
    minutes: 60*1000,
    hours: 60*60*1000,
    days: 24*60*60*1000
  };
  var resolveMilliseconds = function(value,units){
    var multiplier = timeUnitMultiplier[units] || 1;
    return value * multiplier;
  };

  var deferredReady = new Deferred();
  var analyticsManager = {
    ready: deferredReady.promise,
    /**
     * @memberof Flybits.analytics.Manager
     * @member {number} maxStoreSize=100 Maximum number of analytics events that can be stored locally before reporting to the server.  If any new events are created and the maximum has already been hit, the oldest records will be discarded first much like a queue.
     */
    maxStoreSize: 100,
    /**
     * @memberof Flybits.analytics.Manager
     * @member {number} reportDelay=3600000 Delay before the next interval of analytics reporting begins.  Note that the timer starts after the previous interval's analytics reporting has completed.  The delay unit type is defined in {@link Flybits.analytics.Manager.reportDelayUnit}
     */
    reportDelay: 3600000,
    /**
     * @memberof Flybits.analytics.Manager
     * @member {string} reportDelayUnit=milliseconds Unit of measure to be used for {@link Flybits.analytics.Manager.reportDelay}.  Possible values include: `milliseconds`, `seconds`, `minutes`,`hours`, `days`.
     */
    reportDelayUnit: 'milliseconds',
    /**
     * @memberof Flybits.analytics.Manager
     * @member {number} lastReported Epoch time (milliseconds) of when the manager last successfully reported analytics data.
     */
    lastReported: null,
    /**
     * @memberof Flybits.analytics.Manager
     * @member {number} lastReportAttempted Epoch time (milliseconds) of when the manager last attempted to report analytics data.  It is possible that this time does not coincide with {@link Flybits.analytics.Manager.lastReported} as the report may have failed or there was no analytics to report.
     */
    lastReportAttempted: null,
    _reportTimeout: null,
    _analyticsStore: null,
    _uploadChannel: null,
    _timedEventCache: {},
    /**
     * @memberof Flybits.analytics.Manager
     * @member {boolean} isReporting Flag indicating whether scheduled analytics reporting is enabled.
     */
    isReporting: false,
    /**
     * Restores Manager state properties, initializes default local storage and upload channel, and starts automated batch reporting of stored analytics.
     * @memberof Flybits.analytics.Manager
     * @function initialize
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without a return value and rejects with a common Flybits Validation model instance.
     */
    initialize: function(){
      var def = new Deferred();
      var manager = this;

      this._analyticsStore = new Flybits.analytics.RecordsStore({
        maxStoreSize: Flybits.cfg.analytics.MAXSTORESIZE
      });
      this._uploadChannel = new Flybits.analytics.DefaultChannel();
      
      this._analyticsStore._store.ready.then(function(){
        return restoreTimestamps();
      }).then(function(){
        return manager.startReporting();
      }).then(function(){
        deferredReady.resolve();
        def.resolve();
      }).catch(function(e){
        def.reject(e);
      });

      return def.promise;
    },
    /**
     * Stops the scheduled service that continuously batch reports collected analytics data if there exists any.
     * @memberof Flybits.analytics.Manager
     * @function stopReporting
     * @return {Flybits.analytics.Manager} Reference to this context manager to allow for method chaining.
     */
    stopReporting: function(){
      this.isReporting = false;
      clearTimeout(this._reportTimeout);
      return this;
    },
    /**
     * Starts the scheduled service that continuously batch reports collected analytics data if there exists any.
     * @memberof Flybits.analytics.Manager
     * @function startReporting
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without a return value and rejects with a common Flybits Validation model instance.
     */
    startReporting: function(){
      var def = new Deferred();
      var manager = this;
      manager.stopReporting();
      
      var sessionPromise = Flybits.Session.userToken?Promise.resolve(Flybits.Session.userToken):Flybits.Session.resolveSession();
      sessionPromise.then(function(){
        var interval;
        interval = function(){
          manager.report().catch(function(e){}).then(function(){
            if(manager.isReporting){
              manager._reportTimeout = setTimeout(function(){
                interval();
              },resolveMilliseconds(manager.reportDelay,manager.reportDelayUnit));
            }
          });

          manager.isReporting = true;
        };
        interval();
        def.resolve();
      }).catch(function(e){
        def.reject(e);
      });

      return def.promise;
    },
    /**
     * Batch reports collected analytics data if it exists.
     * @memberof Flybits.analytics.Manager
     * @function report
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without a return value and rejects with a common Flybits Validation model instance.
     */
    report: function(){
      var def = new Deferred();
      var manager = this;
      var eventTmpIDs;
      this._analyticsStore.getAllEvents().then(function(events){
        eventTmpIDs = events.map(function(evt){
          return evt._tmpID;
        });
        if(eventTmpIDs.length < 1){
          throw new Validation().addError('No analytics to upload.','',{
            code: Validation.type.NOTFOUND
          });
        }
        return manager._uploadChannel.uploadEvents(events);
      }).then(function(){
        manager.lastReported = Date.now();
        Flybits.store.Property.set(Flybits.cfg.store.ANALYTICSLASTREPORTED,manager.lastReported);
        return manager._analyticsStore.clearEvents(eventTmpIDs);
      }).then(function(){
        def.resolve();
      }).catch(function(e){
        if(e instanceof Validation && e.firstError().code !== Validation.type.NOTFOUND){
          console.error('> analytics report error',e);
        }
        def.reject(e);
      }).then(function(){
        manager.lastReportAttempted = Date.now();
        Flybits.store.Property.set(Flybits.cfg.store.ANALYTICSLASTREPORTATTEMPTED,manager.lastReportAttempted);
      });
      
      return def.promise;
    },
    /**
     * Log a discrete analytics event.
     * @memberof Flybits.analytics.Manager
     * @function logEvent
     * @param {string} eventName Name of event.
     * @param {Object} properties Map of custom properties.
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without a return value and rejects with a common Flybits Validation model instance.
     */
    logEvent: function(eventName, properties){
      var evt = new Event({
        name: eventName,
        type: Event.types.DISCRETE,
        properties: properties
      });

      applyDefaultSysProps(evt);

      return this._analyticsStore.addEvent(evt);
    },
    _logInt: function(eventName, properties, internalProperties){
      var evt = new Event({
        name: eventName,
        type: Event.types.DISCRETE,
        properties: properties,
        isFlybits: true,
        flbProperties: internalProperties
      });

      applyDefaultSysProps(evt);

      return this._analyticsStore.addEvent(evt);
    },
    /**
     * Log the start of timed analytics event.
     * @memberof Flybits.analytics.Manager
     * @function startTimedEvent
     * @param {string} eventName Name of event.
     * @param {Object} properties Map of custom properties.
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves with a reference ID of the timed start event for use when ending the timed event. The promise will reject with a common Flybits Validation model instance should any error occur.
     */
    startTimedEvent: function(eventName, properties){
      var manager = this;
      var def = new Deferred();
      var evt = new Event({
        name: eventName,
        type: Event.types.TIMEDSTART,
        properties: properties
      });
      applyDefaultSysProps(evt);
      evt._setInternalProperty(Event.TIMEDREFID, evt._tmpID);

      this._analyticsStore.addEvent(evt).then(function(){
        manager._timedEventCache[evt._tmpID] = evt;
        def.resolve(evt._tmpID);
      }).catch(function(e){
        def.reject(e);
      });

      return def.promise;
    },
    _logStartInt: function(eventName, properties, internalProperties){
      var manager = this;
      var def = new Deferred();
      var evt = new Event({
        name: eventName,
        type: Event.types.TIMEDSTART,
        properties: properties,
        isFlybits: true,
        flbProperties: internalProperties
      });
      applyDefaultSysProps(evt);
      evt._setInternalProperty(Event.TIMEDREFID, evt._tmpID);

      this._analyticsStore.addEvent(evt).then(function(){
        manager._timedEventCache[evt._tmpID] = evt;
        def.resolve(evt._tmpID);
      }).catch(function(e){
        def.reject(e);
      });

      return def.promise;
    },
    /**
     * Log the end of timed analytics event.
     * @memberof Flybits.analytics.Manager
     * @function endTimedEvent
     * @param {string} refID Reference ID of timed start event.
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without a return value and rejects with a common Flybits Validation model instance.
     */
    endTimedEvent: function(refID){
      var manager = this;
      var def = new Deferred();
      var startedEvt = manager._timedEventCache[refID];
      if(!refID || !startedEvt){
        def.reject(new Validation().addError('No Timed Event Found','No corresponding start event was found for provided reference.',{
          code: Validation.type.NOTFOUND,
          context: refID
        }));
        return def.promise;
      }

      var endEvt = new Event({
        name: startedEvt.name,
        type: Event.types.TIMEDEND,
        properties: startedEvt.properties
      });
      endEvt._internal = startedEvt._internal;
      endEvt._isInternal = startedEvt._isInternal;

      this._analyticsStore.addEvent(endEvt).then(function(){
        delete manager._timedEventCache[refID];
        def.resolve();
      }).catch(function(e){
        def.reject(e);
      });

      return def.promise;
    },
    _logEndInt: function(refID){
      return this.endTimedEvent(refID);
    }
  };

  return analyticsManager;

})();
/**
 * @classdesc Abstract base class from which all Analytics stores are extended.
 * @memberof Flybits.analytics
 * @abstract
 * @class AnalyticsStore
 * @param {Object} opts Configuration object to override default configuration
 * @param {number} opts.maxStoreSize {@link Flybits.analytics.AnalyticsStore#maxStoreSize}
 */
Flybits.analytics.AnalyticsStore = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;

  var AnalyticsStore = function(opts){
    if(this.constructor === Object){
      throw new Error('Abstract classes cannot be instantiated');
    }

    /**
     * @instance
     * @memberof Flybits.analytics.AnalyticsStore
     * @member {number} [maxStoreSize=100] Maximum amount of analytics events to store locally before old events are flushed from the local store.
     */
    this.maxStoreSize = opts && opts.maxStoreSize?opts.maxStoreSize:100;
  };
  AnalyticsStore.prototype = {
    implements: function(interfaceName){
      if(!this._interfaces){
        this._interfaces = [];
      }
      this._interfaces.push(interfaceName);
    },
    /**
     * Add an analytics event to the local persistent storage.
     * @abstract
     * @instance
     * @memberof Flybits.analytics.AnalyticsStore
     * @function addEvent 
     * @param {Flybits.analytics.Event} event Event object to be logged.
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without a return value and rejects with a common Flybits Validation model instance.
     */
    /**
     * Removes analytics events from local persistent store based on their IDs.
     * @abstract
     * @instance
     * @memberof Flybits.analytics.AnalyticsStore
     * @function clearEvents
     * @param {string[]} tmpIDs Generated temporary IDs of analytics events stored locally.
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without a return value and rejects with a common Flybits Validation model instance.
     */ 
    /**
     * Removes all analytics events from local persistent store.
     * @abstract
     * @instance
     * @memberof Flybits.analytics.AnalyticsStore
     * @function clearAllEvents
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without a return value and rejects with a common Flybits Validation model instance.
     */ 
    /**
     * Retrieves all analytics events from local persistent storage that is currently available.
     * @abstract
     * @instance
     * @memberof Flybits.analytics.AnalyticsStore
     * @function getAllEvents
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves with all analytics events currently persisted and rejects with a common Flybits Validation model instance.
     */ 
    /**
     * Retrieves an analytics event from local persistent storage that is currently available.
     * @abstract
     * @instance
     * @memberof Flybits.analytics.AnalyticsStore
     * @function getEvent
     * @param {string} tmpID Temporary ID of analytics event that is used as a key for local storage.
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves with an analytics event currently persisted and rejects with a common Flybits Validation model instance.
     */ 
  };

  return AnalyticsStore;
})();

/**
 * @class RecordsStore
 * @classdesc Default analytics store.
 * @extends Flybits.analytics.AnalyticsStore
 * @memberof Flybits.analytics
 */
Flybits.analytics.RecordsStore = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;
  var ObjUtil = Flybits.util.Obj;
  var Event = Flybits.analytics.Event;

  function RecordsStore(opts){
    Flybits.analytics.AnalyticsStore.call(this,opts);
    if(opts){
      this.opts = ObjUtil.extend({},opts);
    }

    this._store = new Flybits.store.Record(this.STOREID);
  }

  RecordsStore.prototype = Object.create(Flybits.analytics.AnalyticsStore.prototype);
  RecordsStore.prototype.constructor = RecordsStore;

  /**
   * @memberof Flybits.analytics.RecordsStore
   * @member {string} STOREID String key for local storage
   */
  RecordsStore.prototype.STOREID = RecordsStore.STOREID = 'flb.analytics';

  RecordsStore.prototype.addEvent = function(event){
    var def = new Deferred();
    var bStore = this;
    var store = this._store;
    var storeLength = 0;

    if(!(event instanceof Event)){
      def.reject(new Validation().addError('Invalid Argument','Must be an instance of an analytics Event',{
        code: Validation.type.INVALIDARG
      }));
      return def.promise;
    }

    store.keys().then(function(keys){
      storeLength = keys.length;
      return bStore._saveState(event);
    }).then(function(){
      if (storeLength >= bStore.maxStoreSize){
        bStore._validateStoreState().then(function(){
          def.resolve();
        });
      } else{
        def.resolve();
      }
    }).catch(function(e){
      def.reject(e);
    });

    return def.promise;
  };

  RecordsStore.prototype.getEvent = function(tmpID){
    var def = new Deferred();
    this._store.get(tmpID).then(function(res){
      if(res){
        var rehydratedEvt = new Event(res);
        rehydratedEvt._tmpID = tmpID;
        def.resolve(rehydratedEvt);
      } else{
        def.resolve();
      }
    }).catch(function(e){
      def.reject(e);
    });

    return def.promise;
  };

  RecordsStore.prototype.clearEvents = function(tmpIDs){
    var store = this._store;
    var def = new Deferred();
    var deleteData;
    deleteData = function(){
      if(tmpIDs.length <= 0){
        def.resolve();
        return;
      }
      var curKey = tmpIDs.pop();
      store.remove(curKey).catch(function(){}).then(function(){
        deleteData();
      });
    };
    deleteData();

    return def.promise;
  };

  RecordsStore.prototype.clearAllEvents = function(){
    return this._store.clear();
  };

  RecordsStore.prototype.getAllEvents = function(){
    var def = new Deferred();
    var data = [];
    var store = this._store;

    store.keys().then(function(keys){
      var mapData = function(){
        if(!keys.length){
          def.resolve(data);
          return;
        }
        var curKey = keys.pop();
        store.get(curKey).then(function(item){
          var rehydratedEvt = new Event(item);
          rehydratedEvt._tmpID = curKey;
          data.push(rehydratedEvt);
        }).catch(function(){}).then(function(){
          mapData();
        });
      };
      mapData();
    });

    return def.promise;
  };

  RecordsStore.prototype._saveState = function(event){
    return this._store.set(event._tmpID,event.toJSON());
  };

  RecordsStore.prototype._validateStoreState = function(){
    var bStore = this;
    var def = new Deferred();
    var promises = [];
    var store = this._store;
    store.keys().then(function(result){
      var keys = result;
      var now = new Date().getTime();
      var accessCount = keys.length - bStore.maxStoreSize;

      keys.sort(function(a,b){
        return (+(a.split('-')[1]))-(+(b.split('-')[1]));
      });

      if(accessCount > 0){
        for(var i = 0; i < accessCount; i++){
          promises.push(store.remove(keys.shift()));
        }
      }

      Promise.settle(promises).then(function(){
        def.resolve();
      });
    }).catch(function(){
      def.reject();
    });

    return def.promise;
  };


  return RecordsStore;
})();

/**
 * @classdesc Abstract base class from which all Analytics upload channels are extended.
 * @memberof Flybits.analytics
 * @abstract
 * @class UploadChannel
 * @param {Object} opts Configuration object to override default configuration
 */
Flybits.analytics.UploadChannel = (function(){
  var Session = Flybits.store.Session;

  var UploadChannel = function(opts){
    if(this.constructor === Object){
      throw new Error('Abstract classes cannot be instantiated');
    }
  };
  UploadChannel.prototype = {
    implements: function(interfaceName){
      if(!this._interfaces){
        this._interfaces = [];
      }
      this._interfaces.push(interfaceName);
    },
    /**
     * Uploads a list of Events to respective destinations.
     * @abstract
     * @instance
     * @memberof Flybits.analytics.UploadChannel
     * @function uploadEvents 
     * @param {Flybits.analytics.Event[]} events Event objects to be uploaded.
     * @return {external:Promise<undefined,Flybits.Validation>} Promise that resolves without a return value and rejects with a common Flybits Validation model instance.
     */
  };

  return UploadChannel;
})();

/**
 * @class DefaultChannel
 * @classdesc Default analytics upload channel.
 * @extends Flybits.analytics.UploadChannel
 * @memberof Flybits.analytics
 */
Flybits.analytics.DefaultChannel = (function(){
  var Deferred = Flybits.Deferred;
  var Validation = Flybits.Validation;
  var ApiUtil = Flybits.util.Api;
  var Session = Flybits.Session;
  var Event = Flybits.analytics.Event;

  var DefaultChannel = function(opts){
    Flybits.analytics.UploadChannel.call(this,opts);

    this.sessionKey = null;
    this.HOST = Flybits.cfg.analytics.CHANNELHOST;
    this.channelKey = Flybits.cfg.analytics.CHANNELKEY;
    this.appID = Flybits.cfg.analytics.APPID;
  };

  DefaultChannel.prototype = Object.create(Flybits.analytics.UploadChannel.prototype);
  DefaultChannel.prototype.constructor = DefaultChannel;

  DefaultChannel.prototype.initSession = function(){
    var def = new Deferred();
    var channel = this;
    var url = this.HOST + '/session?key=' + this.channelKey;
    ApiUtil.fetch(url,{
      method: 'GET',
      respType: 'json'
    }).then(function(resp){
      channel.sessionKey = resp.body.key;
      def.resolve();
    }).catch(function(resp){
      def.reject(resp);
    });

    return def.promise;
  };

  DefaultChannel.prototype._preparePayload = function(events){
    return {
      deviceID: Session.deviceID,
      data: events.map(function(obj){
        var rawObj = obj.toJSON();
        rawObj.properties['_uid'] = Session.user.id;
        return rawObj;
      })
    };
  };

  DefaultChannel.prototype._upload = function(payload){
    var def = new Deferred();
    var url = this.HOST + '/events';
    ApiUtil.fetch(url,{
      method: 'POST',
      headers: {
        key: this.sessionKey,
        appid: this.appID,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload),
      respType: "empty"
    }).then(function(resp){
      def.resolve();
    }).catch(function(resp){
      def.reject(resp);
    });

    return def.promise;
  };

  DefaultChannel.prototype.uploadEvents = function(events){
    var channel = this;
    var payload = this._preparePayload(events);
    var sessionPromise = Promise.resolve();
    if(!this.sessionKey){
      sessionPromise = this.initSession();
    }

    return sessionPromise.then(function(){
      return channel._upload(payload);
    });
  };

  return DefaultChannel;
})();

/**
 * This is a utility class, do not use constructor.
 * @class Content
 * @classdesc API wrapper class for the retrieval of {@link Flybits.Content} models from the Flybits core.
 * @memberof Flybits.api
 */
Flybits.api.Content = (function(){
  /**
   * Available request parameters to filter {@link Flybits.Content} in the Flybits core.
   * @typedef RequestParams
   * @memberof Flybits.api.Content
   * @type Object
   * @property {string} [id] ID of a {@link Flybits.Content}.
   * @property {string[]} [ids] List of {@link Flybits.Content} IDs.
   * @property {Flybits.api.Paging} [paging] Details to dictate which page, or subset, of results should be returned from the total records that match the query parameters.
   * @property {Object} [sortBy] Specification of server sorting of models requested.  Currently can only be used when {@link Flybits.api.Content.RequestParams.managed} is false. If {@link Flybits.api.Content.RequestParams.managed} is true, this sorting parameter will be ignored.
   * @property {string} sortBy.key String pertaining to a sorting parameter by which to order server model response by.  Current implementation only allows sorting by `evaluatedAt`, which is the time at which the `Content` was found to be relevant via `Context` rule evaluation.
   * @property {string} [sortBy.direction="descending"] Direction of ordering based on specified `key`.  Possible values are `descending` or `ascending`
   * @property {boolean} managed Flag to indicate the return of relevant `Content` instances or `Content` instances that are manageable by the requesting user based on permission levels.
   * @property {Object} [location]  Specification of location attributes required for querying Content instances.
   * @property {string} location.key Key name of the location object within the Content data.
   * @property {number} location.lat Latitiude by which to perform the query.
   * @property {number} location.lng Longitude by which to perform the query.
   * @property {number} [location.radius=10000] Radius of location search surrounding provided latitude and longitude.
   */
  var ObjUtil = Flybits.util.Obj;
  var ApiUtil = Flybits.util.Api;
  var Validation = Flybits.Validation;
  var Deferred = Flybits.Deferred;
  var Session = Flybits.store.Session;
  var Content = Flybits.Content;

  var lastPaging = null;

  function parseParams( reqParams ){
    var validation = new Validation();
    var req = {};
    if( !reqParams ){
      return req;
    }

    if(reqParams.managed){
      req.managementMode = true;
    }

    if( reqParams.paging ){
      if( !reqParams.paging.limit && !reqParams.paging.offset ){
        validation.addError('Missing Argument','paging request parameter object must contain at least a limit or offset property.',{
          code: Validation.type.MISSINGARG,
          context: 'paging.limit, paging.offset'
        });
      } else{
        if( reqParams.paging.limit > 0 ){
          req.limit = reqParams.paging.limit;
        }
        if( reqParams.paging.offset >= 0 ){
          req.offset = reqParams.paging.offset;
        }
      }
    }

    if( reqParams.sortBy ){
      var key = reqParams.sortBy.key;
      var order = reqParams.sortBy.order;

      if( !key ){
        validation.addError('Missing Argument','sortBy requires a key to sort by.',{
          code: Validation.type.MISSINGARG,
          context: 'sortBy.key'
        });
      } else if( key !== 'evaluatedAt' ){
        validation.addError('Invalid Argument','Content cannot be sorted by this property:'+key,{
          code: Validation.type.INVALIDARG,
          context: 'sortBy.key'
        });
      }

      req.orderby = 'evaluatedAt';

      if( order && !(order === 'asc' || order === 'ascending' || order === 'desc' || order === 'descending') ){
        validation.addError('Invalid Argument','Content sort direction invalid.  Supported values are: asc or ascending or desc or descending',{
          code: Validation.type.INVALIDARG,
          context: 'sortBy.order'
        });
      } else if(key && !order){
        order = 'desc'
      }

      if(order === 'ascending'){
        order = 'asc';
      } else if(order === 'descending'){
        order = 'desc';
      }
      req.sortorder = order;
    }

    if( reqParams.templateID ){
      req.templateId = reqParams.templateID;
    }

    if(reqParams.labels){
      if(reqParams.labels.constructor !== Array){
        validation.addError('Invalid Argument','Labels must be an array of strings',{
          code: Validation.type.INVALIDARG,
          context: 'labels'
        });
      } else if(reqParams.labels.length > 0){
        req.labels = reqParams.labels.join(',');
      }
    }

    if(reqParams.hasOwnProperty('data')){
      req.data = reqParams.data;
    }

    if(reqParams.location){
      if(!reqParams.location.key){
        validation.addError('Missing Argument','location query requires a key to sort by.',{
          code: Validation.type.MISSINGARG,
          context: 'location.key'
        });
      } else{
        req.loc = reqParams.location.key;
      }
      if(!reqParams.location.lat){
        validation.addError('Missing Argument','location query requires a latitude.',{
          code: Validation.type.MISSINGARG,
          context: 'location.lat'
        });
      } else{
        req.lat = reqParams.location.lat;
      }
      if(!reqParams.location.lng){
        validation.addError('Missing Argument','location query requires a longitude.',{
          code: Validation.type.MISSINGARG,
          context: 'location.lng'
        });
      } else{
        req.lng = reqParams.location.lng;
      }
      if(reqParams.location.radius){
        req.radius = reqParams.location.radius;
      } else{
        req.radius = 1000;
      }
    }

    if(!validation.state){
      throw validation;
    }

    return req;
  }

  var content = {

    /**
     * Helper to retrieve cached paging property.  After every API request for {@link Flybits.Content} models, the static paging cache is replaced by the paging properties of the latest request.
     * @memberof Flybits.api.Content
     * @function getPaging
     * @returns {Flybits.api.Paging} The last pagination object received from retrieving {@link Flybits.Content|Contents}.
     */
    getPaging: function(){
      return lastPaging;
    },
    
    /**
     * Retrieves {@link Flybits.Content} model with specified ID.
     * @memberof Flybits.api.Content
     * @function get
     * @param {string} id - ID of the {@link Flybits.Content} model.
     * @returns {external:Promise<Flybits.Content,Flybits.Validation>} Promise that resolves with {@link Flybits.Content} with specified ID.  Promise rejects with a {@link Flybits.Validation} object containing {@link Flybits.Validation.ValidationError|error} objects if request has failed or if a {@link Flybits.Content} was not found with supplied ID.
     */
    get: function( id, opts ){
      var api = this;
      var def = new Deferred();
      var url = Flybits.cfg.HOST + Flybits.cfg.res.CONTENTS;
      var data = {
        data: true
      };
      var localization = opts && opts.localization?opts.localization : 'all';

      if( !id || id === "" ){
        throw new Validation().addError('Missing Argument','No Content ID specified',{
          code: Validation.type.MISSINGARG,
          context: 'id'
        });
      }
      url = url + "/" + id + '?' + ApiUtil.toURLParams(data);

      ApiUtil.flbFetch( url, {
        method: "GET",
        headers: {
          'accept-language': localization
        }
      }).then( function( resp ){
        if( resp && resp.body ){
          var content; 
          try{
            content = Content.getInstance( resp.body );
          } catch(e){
            def.reject( new Validation().addError("Server model Parsing Failed","Failed to parse single content server model.",{
              code: Validation.type.MALFORMED,
              context: resp
            }));
          }

          def.resolve({
            result: content
          });
        } else {
          def.reject( new Validation().addError("Server Request Failed","Failed to retrieve single content from server",{
            code: Validation.type.MALFORMED
          }));
        }
      }).catch( function( resp ){
        def.reject( resp );
      });

      return def.promise;
    },

    /**
     * Retrieves {@link Flybits.Content} models by specified request parameters.
     * @memberof Flybits.api.Content
     * @function getAll
     * @param {Flybits.api.Content.RequestParams} requestParams Request parameter object to filter {@link Flybits.Content} models in the core.
     * @returns {external:Promise<Flybits.api.Result,Flybits.Validation>} Promise that resolves with a {@link Flybits.api.Result} object with a list of {@link Flybits.Content} models that meet the request parameters.  Promise rejects with a {@link Flybits.Validation} object containing {@link Flybits.Validation.ValidationError|error} objects if request has failed.
     */
    getAll: function( requestParams ){
      var def = new Deferred();
      var url = Flybits.cfg.HOST;
      var data = {
        data: true
      };
      var reqParamCopy = requestParams? ObjUtil.extend({}, requestParams): undefined;
      
      if(requestParams && requestParams.managed){
        url += Flybits.cfg.res.CONTENTS;
        delete requestParams.sortBy;
      } else if(requestParams && requestParams.location){
        url += Flybits.cfg.res.CONTENTS;
      } else{
        url += Flybits.cfg.res.RELEVANTCONTENTS;
      }
      var localization = 'all';
      
      if(requestParams){
        localization = requestParams.localization?requestParams.localization:'all';
        delete requestParams.localization;
        requestParams = ObjUtil.extend(data,requestParams);
        data = parseParams(requestParams);
      }
      
      data = ApiUtil.toURLParams( data );
      if( data !== "" ){
        url += "?" + data;
      }

      ApiUtil.flbFetch( url, {
        method: "GET",
        headers: {
          'accept-language': localization
        }
      }).then( function( resp ){
        if( resp && resp.body && resp.body.data && resp.body.data.length >= 0 ){
          var paging = ApiUtil.parsePaging( resp.body );
          lastPaging = paging;

          var contents = resp.body.data.map( function(obj){
            try{
              var contentInstance = Content.getInstance( obj );
              return contentInstance;
            } catch(e){
              def.reject( new Validation().addError("Request Failed","Failed to parse server model.",{
                code: Validation.type.MALFORMED,
                context: obj
              }));
            }
          });

          def.resolve({
            result: contents,
            nextPageFn: ApiUtil.createNextPageCall( Flybits.api.Content.getAll, reqParamCopy, paging )
          });
        } else {
          def.reject( new Validation().addError("Server Request Failed","Failed to retrieve contents from server",{
            code: Validation.type.MALFORMED
          }));
        }
      }).catch( function( resp ){
        def.reject( resp );
      });

      return def.promise;
    }
  };

  return content;
})();
/**
 * This is a utility class, do not use constructor.
 * @class ContentData
 * @classdesc API wrapper class for the retrieval of {@link Flybits.ContentData} and {@link Flybits.PagedData} models from Flybits core.
 * @memberof Flybits.api
 */
Flybits.api.ContentData = (function(){
  var ObjUtil = Flybits.util.Obj;
  var ApiUtil = Flybits.util.Api;
  var Validation = Flybits.Validation;
  var Deferred = Flybits.Deferred;
  var Session = Flybits.store.Session;
  var Content = Flybits.Content;
  var ContentData = Flybits.ContentData;
  var PagedData = Flybits.PagedData;

  var lastPaging = null;

  function parseParams( reqParams ){
    var req = {};
    if( !reqParams ){
      return req;
    }

    if( reqParams.paging ){
      var pagingFieldKeys = Object.keys( reqParams.paging );
      pagingFieldKeys.forEach( function( key ){
        var pagingKeyValue = reqParams.paging[key];
        if( pagingKeyValue.constructor === Object ){
          var pagingLimit = pagingKeyValue.limit;
          var pagingOffset = pagingKeyValue.offset;

          if( !pagingLimit && !pagingOffset ){
            throw new Validation().addError('Missing Argument','paging request parameter object must contain at least a limit or offset property.',{
              code: Validation.type.MISSINGARG,
              context: 'pagingLimit, pagingOffset'
            });
          }

          if( pagingLimit > 0 ){
            var pagingLimitKey = key+".limit";
            req[pagingLimitKey] = pagingLimit;
          }
          if( reqParams.paging[key].offset >= 0 ){
            var pagingOffsetKey = key+".offset";
            req[pagingOffsetKey] = pagingOffset;
          }
        } else {
          if( key === "limit" && pagingKeyValue > 0 ){
            req.limit = pagingKeyValue;
          }
          if( key === "offset" && pagingKeyValue >= 0 ){
            req.offset = pagingKeyValue;
          }
        }
      });
    }

    if( reqParams.sortBy ){
      var sortingFieldKeys = Object.keys( reqParams.sortBy );
      sortingFieldKeys.forEach( function( key ){
        var sortKeyValue = reqParams.sortBy[key];
        
        if( sortKeyValue.constructor === Object ){
          var sortKey = sortKeyValue.key;
          var sortOrder = sortKeyValue.order;

          if( !sortKey ){
            throw new Validation().addError('Missing Argument','orderBy requires a ContentData model property key to order by.',{
              code: Validation.type.MISSINGARG,
              context: 'orderBy.key'
            });
          }
          // @TODO: IMPLEMENT VALIDATION TO CHECK IF SORTING CAN BE DONE BY KEY SPECIFIED BY USER
          var sortFilterKey = key+".sortBy";
          req[sortFilterKey] = sortKey;

          if( sortOrder && !(sortOrder === 'asc' || sortOrder === 'desc') ){
            throw new Validation().addError('Invalid Argument','Content cannot be ordered by in such way(MUST BE asc or desc)',{
              code: Validation.type.INVALIDARG,
              context: 'orderBy.order'
            });
          }
          var sortFilterOrder = key+".order";
          req[sortFilterOrder] = sortOrder;
        } else {
          if( key === "key" ){
            req.key = sortKeyValue;
          }
          if( key === "order" ){
            req.key = sortKeyValue;
          }
        }
      });

      if(reqParams.hasOwnProperty('data')){
        req.data = reqParams.data;
      }
    }

    return req;
  }

  var content = {

    /**
     * Helper to retrieve cached paging property.  After every API request for {@link Flybits.ContentData} models, the static paging cache is replaced by the paging properties of the latest request.
     * @memberof Flybits.api.ContentData
     * @function getPaging
     * @returns {Flybits.api.Paging} The last pagination object received from retrieving {@link Flybits.ContentData}.
     */
    getPaging: function(){
      return lastPaging;
    },
    
    /**
     * Retrieves {@link Flybits.ContentData} model with specified ID.
     * @memberof Flybits.api.ContentData
     * @function get
     * @param {Object} requestParams Request parameters.
     * @param {string} requestParams.contentID Content ID to which the requested {@link Flybits.ContentData|ContentData} belongs to.
     * @param {string} requestParams.contentDataID Specific ID of the requested {@link Flybits.ContentData|ContentData}.
     * @param {Flybits.api.Paging} [requestParams.paging] Details to dictate which page, or subset, of results should be returned from the total records that match the query parameters.
     * @returns {external:Promise<Flybits.api.Result,Flybits.Validation>} Promise that resolves with result object containing specific {@link Flybits.ContentData}.
     */
    get: function( requestParams ){
      var def = new Deferred();
      var url = Flybits.cfg.HOST + Flybits.cfg.res.CONTENTS;
      var contentID = requestParams?requestParams.contentID:undefined;
      var contentDataID = requestParams?requestParams.contentDataID:undefined;
      var localization = requestParams && requestParams.localization?requestParams.localization:'all';

      if( !contentID || contentID === "" ){
        throw new Validation().addError('Missing Argument','No Content ID specified to find Data',{
          code: Validation.type.MISSINGARG,
          context: contentID
        });
      }

      if( !contentDataID || contentDataID === "" ){
        throw new Validation().addError('Missing Argument','No Content Data ID specified to find Data',{
          code: Validation.type.MISSINGARG,
          context: 'contentDataID'
        });
      }

      if(requestParams){
        var reqParamCopy = ObjUtil.extend({}, requestParams);
        delete requestParams.contentID;
        delete requestParams.localization;
        delete requestParams.contentDataID;
        requestParams = parseParams( requestParams );
      }

      url = url + "/" + contentID + "/data/" + contentDataID;
      requestParams = ApiUtil.toURLParams( requestParams );
      if( requestParams !== "" ){
        url += "?" + requestParams;
      }

      ApiUtil.flbFetch( url, {
        method: "GET",
        headers: {
          'accept-language': localization
        }
      }).then( function( resp ){
        if( resp && resp.body ){
          var contentData; 
          var routePath = new Path();
          routePath.append(new Content({id:contentID}));
          try{
            contentData = new ContentData( resp.body, routePath );
          } catch(e){
            def.reject( new Validation().addError("Server model Parsing Failed","Failed to parse single content server model.",{
              code: Validation.type.MALFORMED,
              context: resp
            }));
          }

          def.resolve({
            result: contentData
          });
        } else {
          def.reject( new Validation().addError("Server Request Failed","Failed to retrieve single content from server",{
            code: Validation.type.MALFORMED
          }));
        }
      }).catch( function( resp ){
        def.reject( resp );
      });

      return def.promise;
    },

    /**
     * Retrieves all {@link Flybits.ContentData} models from a {@link Flybits.Content}.
     * @memberof Flybits.api.ContentData
     * @function getAll
     * @param {Object} requestParams Request parameters.
     * @param {string} requestParams.contentID Content ID to which the requested {@link Flybits.ContentData|ContentData} models belongs to.
     * @param {Flybits.api.Paging} [requestParams.paging] Details to dictate which page, or subset, of results should be returned from the total records that match the query parameters.
     * @returns {external:Promise<Flybits.api.Result,Flybits.Validation>} Promise that resolves with result object containing array of {@link Flybits.ContentData}.
     */
    getAll: function( requestParams ){
      var def = new Deferred();
      var url = Flybits.cfg.HOST + Flybits.cfg.res.CONTENTS;
      var contentID = requestParams?requestParams.contentID:undefined;
      var localization = requestParams && requestParams.localization?requestParams.localization:'all';

      if( !contentID || contentID === "" ){
        throw new Validation().addError('Missing Argument','No Content ID specified to find Data',{
          code: Validation.type.MISSINGARG,
          context: 'contentID'
        });
      }

      if(requestParams){
        var reqParamCopy = ObjUtil.extend({},requestParams);
        delete requestParams.contentID;
        delete requestParams.localization;
        data = parseParams( requestParams );
      }

      url = url + "/" + contentID + "/data";

      data = ApiUtil.toURLParams( data );
      if( data !== "" ){
        url += "?" + data;
      }

      ApiUtil.flbFetch( url, {
        method: "GET",
        headers: {
          'accept-language': localization
        }
      }).then( function( resp ){
        
        if( resp && resp.body && resp.body.data && resp.body.data.length >= 0 ){
          var paging = ApiUtil.parsePaging( resp.body );
          lastPaging = paging;

          var allContentData = resp.body.data.map( function(obj){
            var routePath = new Path();
            routePath.append(new Content({id:contentID}));
            try{
              return new ContentData( obj, routePath );
            } catch(e){
              def.reject( new Validation().addError("Request Failed","Failed to parse server model.",{
                code: Validation.type.MALFORMED,
                context: obj
              }));
            }
          });

          def.resolve({
            result: allContentData,
            contentID: contentID,
            nextPageFn: ApiUtil.createNextPageCall( Flybits.api.ContentData.getAll, reqParamCopy, paging )
          });
        } else {
          def.reject( new Validation().addError("Server Request Failed","Failed to retrieve contents from server",{
            code: Validation.type.MALFORMED
          }));
        }
      }).catch( function( resp ){
        def.reject( resp );
      });

      return def.promise;
    },
    /**
     * The actual JSON payload that lies within {@link Flybits.ContentData}, that is specified in the Experience Studio, can contain pageable arrays in the form of {@link Flybits.PagedData}.  Thus this function is used to retrieve further pages within.
     * @memberof Flybits.api.ContentData
     * @function getPagedData
     * @param {Object} requestParams Request parameters.
     * @param {string} requestParams.key JSON key of the {@link Flybits.PagedData}.
     * @param {Flybits.Path} requestParams.path Path object that represents the nested depth of the pageable data.
     * @param {Flybits.api.Paging} [requestParams.paging] Details to dictate which page, or subset, of results should be returned from the total records that match the query parameters.
     * @returns {external:Promise<Flybits.api.Result,Flybits.Validation>} Promise that resolves with result object containing raw JSON objects of the requests nested page.
     */
    getPagedData: function(requestParams){
      var def = new Deferred();
      var url = Flybits.cfg.HOST + '/kernel';
      var localization = requestParams && requestParams.localization?requestParams.localization:'all';
      var validation = new Validation();
      
      if(!requestParams || !requestParams.path){
        validation.addError('Missing Argument','No Path provided for nested data',{
          code: Validation.type.MISSINGARG,
          context: 'path'
        });
      }

      if(!requestParams || !requestParams.key){
        validation.addError('Missing Argument','No field key provided for nested data array.',{
          code: Validation.type.MISSINGARG,
          context: 'key'
        });
      }

      if(!validation.state){
        throw validation.setStackTrace();
      }

      url += requestParams.path.serialize() + '?field=' + requestParams.key;
      var reqParamCopy = ObjUtil.extend({},requestParams);
      delete requestParams.localization;
      delete requestParams.path;
      delete requestParams.key;
      if(requestParams.paging){
        var reqParam = requestParams.paging;
        requestParams.paging = {};
        requestParams.paging[reqParamCopy.key] = reqParam;
      }


      var dataParams = parseParams(requestParams);
      dataParams = ApiUtil.toURLParams( dataParams );
      if( dataParams !== "" ){
        url += "&" + dataParams;
      }

      ApiUtil.flbFetch( url, {
        method: "GET",
        headers: {
          'accept-language': localization
        }
      }).then( function(resp){
        var list = resp.body[reqParamCopy.key];
        var listPaging = ApiUtil.parsePaging( resp.body, reqParamCopy.key+'.pagination');
        var tmpPagedData = new PagedData();
        tmpPagedData.fromJSON(list);

        def.resolve({
          result: tmpPagedData.data,
          nextPageFn: ApiUtil.createNextPageCall(Flybits.api.ContentData.getPagedData,reqParamCopy,listPaging)
        });
      }).catch(function(resp){
        def.reject(resp);
      });

      return def.promise;
    },
    /**
     * Submits JSON data to a content instance.
     * @memberof Flybits.api.ContentData
     * @function submitData
     * @param {Object} conatentData JSON body of data.  This body must match the template structure of the Content template 
     * @returns {external:Promise<Flybits.api.Result,Flybits.Validation>} Promise that resolves if successful and rejects with a `Validation` instance if it is not successful.
     */
    submitData: function(contentData){
      var def = new Deferred();
      var url = Flybits.cfg.HOST + '/kernel';
      url += contentData.path.serialize();
      var method = contentData.id? 'PUT' : 'POST';

      if(!(contentData instanceof Flybits.ContentData)){
        def.reject(new Validation().addError('Invalid argument','Can only submit ContentData types.',{
          code: Validation.type.INVALIDARG,
          context: contentData
        }));
      } else{
        ApiUtil.flbFetch(url, {
          method: method,
          body: JSON.stringify(contentData.toJSON())
        }).then(function(resp){
          def.resolve(resp);
        }).catch(function(e){
          def.reject(e);
        });
      }


      return def.promise;
    }
  };

  return content;
})();
/**
 * This is a utility class, do not use constructor.
 * @class User
 * @classdesc API wrapper class for the retrieval of {@link Flybits.User} models from Flybits core.
 * @memberof Flybits.api
 */
Flybits.api.User = (function(){
  var ApiUtil = Flybits.util.Api;
  var gutil = Flybits.util.Obj;
  var Validation = Flybits.Validation;
  var Deferred = Flybits.Deferred;
  var User = Flybits.User;
  var Session = Flybits.Session;

  var user = {
    /**
     * Fetches from the Flybits core the currently authenticated {@link Flybits.User}.
     * @memberof Flybits.api.User
     * @function getSignedInUser
     * @returns {external:Promise<Flybits.User,Flybits.Validation>} Promise that resolves with the {@link Flybits.User} model of the currently authenticated user. Promise rejects with a {@link Flybits.Validation} model if request fails to complete successfully.  This may occur if the user session has expired.
     */
    getSignedInUser: function(){
      var def = new Deferred();
      var url = Flybits.cfg.HOST + Flybits.cfg.res.AUTHUSER;
      var authToken = Session.userToken;
      var headerObj = {};
      headerObj[Session.AUTHHEADER] = authToken;

      ApiUtil.flbFetch(url,{
        method: 'GET',
        headers: headerObj,
      }).then(function(respObj){
        var signedInUser = new User(respObj.body);
        Session.user = signedInUser;
        def.resolve(signedInUser);
      }).catch(function(resp){
        def.reject(resp);
      });

      return def.promise;
    }
  };

  return user;
})();

/***** Export ******/

if(typeof window === 'object'){
  window.Flybits = Flybits;
  Flybits.initFile = Flybits.initFile.browser;
} else if(typeof exports === 'object' && typeof module !== 'undefined'){
  var fs = require('fs');
  var Persistence = require('node-persist');
  global.atob = require('atob');
  Flybits.initFile = Flybits.initFile.server;
  module.exports = Flybits;
} else if(typeof define === 'function' && define.amd){
  define(function(){
    Flybits.init = Flybits.init.server;
    return Flybits;
  });
} else{
  Flybits.init = Flybits.init.server;
  global.Flybits = Flybits;
}

if(typeof __dirname === 'string'){
  Flybits.cfg.store.RESOURCEPATH = __dirname+"/res";
}
Flybits.store.Property.init();
var deferredReady = new Flybits.Deferred();
Flybits.ready = deferredReady.promise;

})();
