const SparkMD5 = require("spark-md5");
const FileSaver = require("file-saver");

var ROM = function(blob, loaded_callback) {
  var arrayBuffer;
  var u_array = [];
  var fileReader = new FileReader();

  fileReader.onload = function() {
    arrayBuffer = this.result;
  };

  fileReader.onloadend = function() {
    u_array = new Uint8Array(arrayBuffer);
    if (loaded_callback) loaded_callback(this);
  }.bind(this);

  fileReader.readAsArrayBuffer(blob);

  this.getMD5 = function() {
    return SparkMD5.ArrayBuffer.hash(arrayBuffer);
  };

  this.getOriginalArrayBuffer = function() {
    return arrayBuffer;
  };

  this.getData = function() {
    return u_array;
  };

  this.save = function(filename) {
    FileSaver.saveAs(new Blob([u_array]), filename);
  };
};

module.exports = ROM;
