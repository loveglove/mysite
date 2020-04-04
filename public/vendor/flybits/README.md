![Flybits Inc.](https://flybits.eu/resources/images/flybits-logo-rgb.svg)
# Flybits.js

**@version 2.4.14**

Flybits.js is an isomorphic JavaScript SDK that can be used to build [Flybits](http://flybits.com) enabled applications on both the client and server side.  That is to say, Flybits.js has been designed for creating mobile web applications, specifically [Progressive Web Apps](https://developers.google.com/web/progressive-web-apps/), and also Flybits enabled microservices.

## Table of Contents
1. [Compatibility](#compatibility)
2. [Getting Started](#getting-started)
3. [Fundamentals](#fundamentals)
    1. [Promises](#promises)
    2. [Standardized Errors](#standardized-errors)
4. [Authentication](#authentication) 
5. [Basic Data Consumption](#basic-data-consumption)
    1. [Retrieving Content](#retrieving-content)
    2. [Content Pagination](#content-pagination)
    3. [Nested Paged Data](#nested-paged-data)
6. [Context Management](#context-management)
    1. [Registering Available Plugins](#registering-available-plugins)
    2. [Creating a Custom Client-Side Context Plugin](#creating-a-custom-client-side-context-plugin)
    3. [Parameterized Context Attributes](#parameterized-context-attributes)

## Compatibility

To achieve client/server agnosticism, this SDK utilizes the new [ES6 Promise](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise) [(spec)](http://www.ecma-international.org/ecma-262/6.0/#sec-promise-objects) object and the upcoming standard [Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API) [(spec)](https://fetch.spec.whatwg.org/).  Both have polyfill support readily available for platforms who have yet to implement them.

To maintain compatibility until all modern browsers and node environments catch up, it is recommended to include the polyfills below by default.

Browser:
* Promise Polyfill: [stefanpenner/es6-promise](https://github.com/stefanpenner/es6-promise)
* Fetch Polyfill: [github/fetch](https://github.com/github/fetch)

Node:
* Promise Polyfill: [stefanpenner/es6-promise](https://github.com/stefanpenner/es6-promise)
* Fetch Polyfill: [matthew-andrews/isomorphic-fetch](https://github.com/matthew-andrews/isomorphic-fetch)

## Getting Started

**Fetch, Include, Initialize**

1. Fetch the SDK

    The SDK is available using the [Node Package Manager(npm)](https://www.npmjs.com/package/flybits)
    ```shell
    $ npm install flybits --save
    ```

2. Include the SDK

    Browser:
    ```html
    <script src="node_modules/flybits/dist/flybits.js"></script>
    ```
    Node:
    ```javascript
    var Flybits = require('flybits');
    ```

3. Initialize the SDK

    Initialization is required to configure environment properties such as reporting delays, and host URLs among other things.  There are two ways of initializing the Flybits SDK.  One is by providing a JavaScript `Object` that will override the SDK default configuration:
    
    ```javascript
    Flybits.init({
      HOST: '//v3.flybits.com',
      CTXREPORTDELAY: 5000,
    }).then(function(cfg){
      /** cfg is the initialized state of the SDK configuration.
          It is not required to be used, it is only returned for your convenience.*/

      //start working with SDK
    }).catch(function(validationObj){
      // handle error
    });
    ```

    Another method of initialization is by providing a URI to a JSON file with which the SDK will read from to retrieve an `Object` to override SDK default configuration.

    ```javascript
    Flybits.initFile('//resource.source.com/config.json').then(function(cfg){
      /** cfg is the initialized state of the SDK configuration.
          It is not required to be used, it is only returned for your convenience.*/

      // start working with SDK
    }).catch(function(validationObj){
      // handle error
    });
    ```

## Fundamentals
The SDK is comprised of two key models, `Content` and `Context`.  `Content` is primarily what the end-user will consume on their respective digital channels and comes from the Flybits core.  This can be anything from push notifications to static/dynamic elements required to render a page/component in an application.  `Context` is what defines the complete state of the user and encompasses the who/what/where/when/why/how about the user.  `Context` can be reported to Flybits from all different channels from server applications to other end-clients, using this SDK to send `Context` is simply one of the possible methods.

You may be wondering where does the intelligence take place?  This is the main benefit of Flybits being a Context-as-a-Service platform.  End-clients built using this SDK can essentially become a dumb client that reports `Context` and fetches `Content`.  It is the Flybits Context Engine in conjunction with Context Rules created in our Experience studio that will determine what `Content` is returned to the end-client.  There does not need to be any hardcoded business logic for data segmentation on the end-client.

This is a key differentiator in that you can now build personalized applications that take into account contextual factors that lie outside of your end-users' devices.  Personalization can now be more than simply a location or a step count.  `Context` that is fed into Flybits can come from any source proprietary or public and all your application needs to do is fetch `Content` to receive relevant and personalized data.

#### Promises

A `Promise` represents an operation that has yet to be completed but will in the future.

All asynchronous operations in the SDK return a `Promise` which give developers full power and flexibility to manage chained operations, parallel model retrieval and deferred actions.

#### Standardized Errors

All handled errors in the Flybits SDK can be caught by appending a `.catch()` callback onto any promise and will invoke the callback with an instance of the `Flybits.Validation` class.
```javascript
Flybits.api.Content.getAll().catch(function(validation){
  //handle error
});
```
The `Flybits.Validation` class comprises of a `state` property an `errors` array containing any and all errors that has been incurred by an SDK operation, and a `type` static property holding error code constants. The `state` property indicates the result of an operation. In the case of a `.catch()` callback it will always be false.

## Authentication
In order to authenticate to Flybits one must use an Identity Provider(`IDP`).  This SDK comes with a few default identity provider facilitating classes.  If custom IDPs are required one can simply extend the `IDP` abstract class.

Below is a simple example of authenticating with a Flybits managed user account.

```javascript
// Flybits managed IDP
var idp = new Flybits.idp.FlybitsIDP({
  password: 'pass123',
  email: 'email@company.com',
  projectID: 'E097EC76-AFA5-436D-8954-1287E220BBCB'
});

/**
 * IDP for OAuth 
 * var idp = new Flybits.idp.OAuthIDP({
 *   token: '12312231',
 *   providerName: Flybits.idp.OAuthIDP.provider.FACEBOOK,
 *   projectID: 'E097EC76-AFA5-436D-8954-1287E220BBCB'
 * });
 */ 

/**
 * Anonymous login 
 * var idp = new Flybits.idp.AnonymousIDP({
 *   projectID: 'E097EC76-AFA5-436D-8954-1287E220BBCB'
 * });
 */ 

Flybits.Session.connect(idp);
```

## Basic Data Consumption
Here are some examples of common workflows when consuming data from the Flybits core assuming User has already been authenticated.

### Retrieving Content
This is the standard call that most applications will perform to request `Content` for their views.  It's just this simple because all the intelligence and data segmentation is performed by the Flybits Context Engine and abstracted away from the end-clients.  The below call will return all relevant `Content` instances for the current end-user.

```javascript
Flybits.api.Content.getAll().then(function(resultObj){
  // do things with the relevant Content instances
  console.log(resultObj.result);
});
```

To retrieve the JSON body from a `Content` instance, whose values were defined in the Experience Studio, simply invoke the `getData` function on the instance.  The reason this is an asynchronous function is because it is possible to fetch relavent `Content` instances without their body, however by default the SDK will request their bodies.  Invoking `getData` will either resolve with its JSON body immediately or if the instance does not contain a body make an API request to retrieve it.  Regardless, this logic is abstracted away from the user of the SDK.

```javascript
Flybits.api.Content.getAll().then(function(resultObj){
  // assuming there is a Content instance in the response
  var contentInstance = resultObj.result[0];

  // retrieve JSON body
  return contentInstance.getData();
}).then(function(obj){
  // do stuff with body of Content instance.
});
```

### Content Pagination
A tedious aspect of consuming lengthy amounts of models from an API is pagination.  Most pageable responses from the SDK will return a response `Object` that comprises of a `result` array of the requested models and a `nextPageFn`.  If the `nextPageFn` is not `undefined`, then the resultant dataset has another page.  Simply invoke the `nextPageFn` function to perform another asynchronous request for the next page of data.

This is super helpful with infinite scrolling use cases as it's simple to check for the existence of a `nextPageFn` as opposed to dealing with limits/offsets/totals and keeping track of what request parameters were used to perform the first request.

Note: if you really need the paging details, the paging `Object` of the last request can be retrieved using the accessor at `Flybits.api.Content.getPaging()`.

```javascript
Flybits.api.Content.getAll().then(function(resultObj){
  // do things with the first page of relevant Content instances
  console.log(resultObj.result);

  // fetch next page of Content instances if it exists
  return resultObj.nextPageFn? resultObj.nextPageFn():{};
}).then(function(resultObj){
  // do things with the second page of relevant Content instances
  console.log(resultObj.result);

  // fetch next page of Content instances if it exists
  return resultObj.nextPageFn? resultObj.nextPageFn():{};
});
```

### Nested Paged Data
Depending on the `Content` template an instance was created from, the JSON body of a `Content` instance may have a property mapping to an array with pagination.  In this case the SDK will replace the array with an instance of the `PagedData` class.  Within the class is a `data` property that maps to the array of JSON data.  To check if the `PagedData` has more pages, you can either check the existence of the `nextPageFn` on the `PagedData` instance or you can invoke the `hasNext` function.  To properly retrieve the next page of data, invoke `getNext`.  This function will resolve with an array of the next page of data, and it will also append the next page to the `PagedData` instance's internal `data` array.  Thus if you are using a data-binding UI framework no work is needed to update your view.  If your view is bound to the `data` property of the `PagedData` instance, simply invoke `getNext` and your view should update automatically.

```javascript
// assuming previous Content instance retrieval
var contentInstance;

contentInstance.getData().then(function(obj){
  /**
   *  assuming Content JSON body
   *  {
   *    firstName: "Bob",
   *    lastName: "Loblaw",
   *    pets: PagedData
   *  }
   */

   // bind view to obj.pets.data
   console.log(obj.pets.data);
   if(obj.pets.hasNext()){
     obj.pets.getNext();
   } 
});
```

## Context Management
The nature of the web platform up to now has been quite sandboxed.  That is to say a web application is unaware of its surroundings and has no access to device sensors available to native mobile applications.  Sensors such as the gyroscope, bluetooth beacons, and network adapter details are among some of the examples that a typical web application cannot access.  However, the platform is growing and the need for native device access is being acknowledged by major browser vendors.  This SDK has been designed in anticipation for that growth and thus context collection and reporting is highly extensible with the Flybits JS SDK.

An important note to take into consideration is that the definition of *context* isn't simply the state of a user's gyroscope.  The true meaning of *context* is the complete state of being of a user.  Who is that user?  What have they previously done?  The answer to these questions is much larger in scope and cannot simply be answered by sensors on a mobile device.  That is why it is important to realize that just because your application is on the Web, doesn't mean it can't be contextualized.

The Flybits platform allows for server side Context Plugins to be built as microservices that connect proprietary data sources and report them as context parameters to the Flybits core.  Similiarily on the client side, the client mobile SDKs (iOS, Android, JavaScript) allow for developers to extend a `ContextPlugin` abstract class to report custom context parameters to the Core.  Any proprietary data that existing applications are privy to can be reported to the Core through the mobile SDK.  From that point, Context Rules can be defined on the graphical Experience Studio to augment mobile experiences without further development.

### Registering Available Plugins
All Context plugins that extend the `ContextPlugin` abstract class will inherit the ability to periodically collect context values and have them stored locally.  Upon registering the plugin with the `Flybits.context.Manager` periodic collection will begin.  The `Flybits.context.Manager` will then periodically gather all collected context values and report them to the Flybits core.  The period of collection for each context plugin can be configured individually as well as the period of reporting of the `Flybits.context.Manager`.  

The `Flybits.context.Manager` also implements a debouncing algorithm so that if the length of one period has passed since it reported context to the core and new context values are collected locally, it will begin reporting immediately.  However, it will still respect the frequency of the reporting period and not report more than once per period.  For example, let us say there have been 4 context values from several different context plugins collected locally and the `Flybits.context.Manager` reporting period length is 2000ms.  Because there are context values to report the manager will gather them all together and report them to the core at 0ms.  After 2000ms has passed, the manager will check for locally collected context values, if there are none no report will occur.  However if a context plugin collects values at the 2500ms mark, because the minimum of 2000ms has passed since the last report, the manager will awaken and perform a context report immediately instead of waiting for the 4000ms mark.  This algorithm basically ensures a maximum performance implication on the client application in terms of network utilization but at the same time allows for reactivity should context state change suddenly.

This SDK includes some basic context plugins.  This list will grow as native device access grows across browser vendors:
* Location
* Connectivity

Below is an example as to how one can register existing or custom plugins and begin reporting to the Flybits Core.  The basic workflow is that context values are collected from the individual context plugin instances.  Then they are batched together and reported to the Flybits Core.

```javascript
// Instantiate context plugins
var x = new Flybits.context.Location({
  maxStoreSize: 50,
  refreshDelay: 10000
});
var y = new Flybits.context.Connectivity({
  maxStoreSize: 50,
  refreshDelay: 5000
});
/**
 * Remember that every custom context plugin must extend the ContextPlugin
 * abstract class.
 */
var z = new CustomContextPlugin({
  maxStoreSize: 50,
  refreshDelay: 1000
});

/**
 * Register plugin instances.  Once registered they will begin collecting
 * context data into a local persistent storage database.  Note the Manager
 * will not yet report these context values to the Flybits Core.
 */
var xStart = Flybits.context.Manager.register(x);
var yStart = Flybits.context.Manager.register(y);
var zStart = Flybits.context.Manager.register(z);


Promise.all([xStart,yStart,zStart]).then(function(){
  /**
   * It is only after explicit initialization that the context manager will
   * begin reporting values to the Core.
   */
  Flybits.context.Manager.initialize();
});
```

All Context plugins also allow for immediate injection of context values for use cases that do not fit a passive periodic collection model.  Note that the data structure of the `Object` to be injected must either match the expected server format of the plugin created in the Flybits Developer Portal or match the expected parameter format in the SDK plugin object's `_toServerFormat` function.  More Information can be found in the [Creating a Custom Client-Side Context Plugin](#creating-a-custom-client-side-context-plugin) section.

```javascript
// Instantiate plugin services
var x = new Flybits.context.Location({
  maxStoreSize: 50,
  refreshDelay: 5000
});
var z = new CustomContextPlugin({
  maxStoreSize: 50,
  refreshDelay: 1000
});

var xStart = Flybits.context.Manager.register(x);
var zStart = Flybits.context.Manager.register(z);

// inject context values
x.setState({
  coords: {
    accuracy: 1217,
    latitude: 43.6711263,
    longitude: -79.4140225
  },
  timestamp: 1501785980332
});

z.setState({
  page_view: 'Agenda',
  view_count: 23
});
```

### Creating a Custom Client-Side Context Plugin
If your application is privy to proprietary information you would like to report to Flybits as a context parameter, you must extend the `ContextPlugin` abstract class.  Proprietary information can include anything including user information or flags that can be used to segment your user base.

Below is an example of how to extend the `ContextPlugin` abstract class.  You may wish to encapsulate the class definition below in a closure to allow for private static entities.

```javascript
// Plugin constructor
var CustomContextPlugin = function(opts){
  // call parent abstract class constructor
  Flybits.context.ContextPlugin.call(this,opts);
  // custom initialization code here
};

// copy parent abstract class prototype
CustomContextPlugin.prototype = Object.create(Flybits.context.ContextPlugin.prototype);
CustomContextPlugin.prototype.constructor = CustomContextPlugin;

/**
 * This is the unique plugin name that can be found on the context plugin's info page in 
 * the Developer Portal project settings.
 */
CustomContextPlugin.prototype.TYPEID = CustomContextPlugin.TYPEID = 'ctx.sdk.contextpluginname';

/**
 * You must override the support check method to account for cases where your
 * custom plugin can potentially not be compatible with all user browser platforms.
 */
CustomContextPlugin.isSupported = CustomContextPlugin.prototype.isSupported = function(){
  var def = new Flybits.Deferred();
  // custom check to see if the custom context plugin is supported on users' platform
  def.resolve();
  return def.promise;
};

/**
 * Main function that is called whenever the context manager collects context.
 */
CustomContextPlugin.getState = CustomContextPlugin.prototype.getState = function(){
  var def = new Flybits.Deferred();
  // custom code to retrieve and return proprietary data
  def.resolve();
  return def.promise;
};

/**
 * Optional override of the transformation function used to convert objects collected from getState() or
 * injected by setState().  You do not need to implement this function if your context value
 * data structure is identical to the server expected key/map structure of the plugin.  This
 * server structure is defined from the Developer Portal project settings.
 */
CustomContextPlugin.prototype._toServerFormat = function(item){
  /**
   * Invoking the super class method before manipulating the `item` argument
   * is recommended in order to accomodate for Flybits specific server format.
   * Namely it is required for parsing parameterized context attributes.
   */
  //item = Flybits.context.ContextPlugin.prototype._toServerFormat.call(this,item);

  return {
    propertyKey: item.contextValueKey
  };
};
```

### Parameterized Context Attributes
As you may already be aware, context data objects are JSON objects that contain a key value mapping of a context plugin's attribute name and its current value that is to be reported. Available attributes are defined when creating a custom context plugin in the Flybits Developer Portal.  For times when there are variable subcategories to classify an attribute's value, you can define an attribute in the Developer Portal to be "parameterized".

For example if you'd like to capture context values for the viewing of pages in your application and you may not know all the pages available ahead of time or there may be very many pages, it is not very convenient to create an attribute for every page name when defining your custom context plugin:

```javascript
// inconvenient multiple static attribute definitions
ctx.tenant.views.homePage
ctx.tenant.views.agendaPage
ctx.tenant.views.surveyPage
// ...
/**
 * `views` is the plugin name
 * attribute name for every possible page of application
 */
```

If you were to use a parameterized attribute named `page`, you would only need a single attribute to facilitate the same as the above example:

```javascript
// convenient single attribute definition allowing for dynamic arguments
ctx.tenant.views.page.{pageName}
/**
 * `views` is the plugin name
 * `page` is the attribute name
 * `pageName` is the variable parameter
 */
```

So now when you are setting or collecting context values, you can do something like this:

```javascript
var z = new CustomContextPlugin({
  maxStoreSize: 50,
  refreshDelay: 1000
});

z.setState({
  page.agenda: 'viewed',
  page.home: 'viewed',
  page.survey: 'canceled'
  x:3
});
```

In this example as the pages of your application grows you can continue to report new `pageName` arguments without having to update your context plugin definition.