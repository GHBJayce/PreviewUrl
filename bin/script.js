// var fs = require('fs');
var sys = require('system');
var page = require('webpage').create();

page.settings.userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36';

page.viewportSize = {
    width: 640,
    height: 480
};

page.clipRect = {
    top: 0,
    left: 0,
    width: 640,
    height: 480,
};

page.open(sys.args[1], function () {
    // page.render('./picture.png');
    // page.zoomFactor = 0.7;
    // var path = './output.txt';
    // var file = fs.open(path, 'w');
    // file.write(page.renderBase64());
    // file.close();
    console.log(page.renderBase64());
    phantom.exit();
});