var ModelUtil = {
  checkProtoImplementation: function(proto,interface){
    var interfaceKeys = Object.keys(interface);
    for(var i = 0; i < interfaceKeys.length; i++){
      var functionName = interfaceKeys[i];
      if(!proto[functionName] || proto[functionName].length !== interface[functionName].length){
        return false;
      }
    }
    return true;
  },
  deepEqual: function(a, b) {
    var isArray = Array.isArray;
    var keyList = Object.keys;
    var hasProp = Object.prototype.hasOwnProperty;

    if (a === b){
      return true;
    }

    if (a && b && typeof a == 'object' && typeof b == 'object') {
      var arrA = isArray(a);
      var arrB = isArray(b);
      var i;
      var length;
      var key;

      if (arrA && arrB) {
        length = a.length;
        if (length != b.length) {
          return false;
        }
        for (i = length; i-- !== 0;){
          if (!ModelUtil.deepEqual(a[i], b[i])) {
            return false;
          }
        }
        return true;
      }

      if (arrA != arrB){
        return false;
      }

      var dateA = a instanceof Date;
      var dateB = b instanceof Date;
      if (dateA != dateB) {
        return false;
      }
      if (dateA && dateB) {
        return a.getTime() == b.getTime();
      }

      var regexpA = a instanceof RegExp;
      var regexpB = b instanceof RegExp;
      if (regexpA != regexpB) {
        return false;
      }
      if (regexpA && regexpB) {
        return a.toString() == b.toString();
      }

      var keys = keyList(a);
      length = keys.length;

      if (length !== keyList(b).length){
        return false;
      }

      for (i = length; i-- !== 0;){
        if (!hasProp.call(b, keys[i])) {
          return false;
        }
      }

      for (i = length; i-- !== 0;) {
        key = keys[i];
        if (!ModelUtil.deepEqual(a[key], b[key])) {
          return false;
        }
      }

      return true;
    }

    return a!==a && b!==b;
  }
};

module.exports = ModelUtil;