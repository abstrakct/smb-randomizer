const SparkMD5 = require("spark-md5");
const FileSaver = require("file-saver");

var NewROM = function(data) {
  var arrayBuffer = data;

  this.getData = function() {
    return arrayBuffer;
  };

  this.getMD5 = function() {
    return SparkMD5.ArrayBuffer.hash(arrayBuffer);
  };

  this.save = function(filename) {
    FileSaver.saveAs(new Blob([arrayBuffer]), filename);
  };
};

module.exports = NewROM;
