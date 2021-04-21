$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
       return null;
    }
    else{
       return results[1] || 0;
    }
}

Number.prototype.padLeft = function (n,str){
    return Array(n-String(this).length+1).join(str||'0')+this;
}

function changeURL(stage){
  var url = window.location.href;
  var urlBase = url.split('?')[0]; //get the url up to ?
  var newURL = urlBase + '?stage=' + stage;

  window.history.pushState(null, null, newURL);
}

function drawStage(stage, ctx, ttx, one, all, stageData) {
  stage = parseInt(stage);

  ctxw = 256;
  imgw = 128;
  imgh = 128;
  tw = 8;
  th = 8;
  zw = ctxw/tw;

  console.time('draw');
  ctx.clearRect(0,0, 256,384);
  for (i = 0; i < stageData[stage].length; i+=2) {
    data = stageData[stage][i];
    flip = stageData[stage][i+1];

    sx = (data * tw) % imgw;
    sy = parseInt((data * tw) / imgw) * th;

    dy = parseInt((i/2)/zw) * tw;
    dx = ((i/2) % zw) * th;

    ttx.clearRect(0,0, tw,th);
    switch(flip) {
      case 4:
        ttx.save();
        ttx.translate(tw,0);
        ttx.scale(-1,1);
        break;
      case 8:
        ttx.save();
        ttx.translate(0,th);
        ttx.scale(1,-1);
        break;
      case 12:
        ttx.save();
        ttx.translate(tw,th);
        ttx.scale(-1,-1);
        break;
      default:
        // no flips
    }
    ttx.drawImage(all, sx,sy,tw,th, 0,0,tw,th);
    ctx.drawImage(one, dx,dy);
    if (flip != 0) ttx.restore();
  }
  console.timeEnd('draw');
}

$(document).ready(function() {

  var stage = parseInt($.urlParam('stage'));
  if (stage == undefined || isNaN(stage)) {
    stage = 0;
    changeURL((stage).padLeft(3));
    $('#selector').trigger('change');
  }

  stageData.forEach(function(item, index) {
    p = (index).padLeft(3);
    $('#selector').append($('<option>', {value:p, text:p}));
  });
  $("#selector").val((stage).padLeft(3));

  var all = new Image();
  all.src = 'tiles.png';
  var ctx = document.getElementById('screen').getContext('2d');

  var one = document.createElement('canvas');
  one.setAttribute('width', 8);
  one.setAttribute('height', 8);
  var ttx = one.getContext('2d');

  drawStage(stage, ctx, ttx, one, all, stageData);

  // $('#save').on("click", function () {
  //   event.preventDefault();
  //   can.toBlobHD(function(blob) {
  //     saveAs(blob, "stage"+stage+".png");
  //   }, "image/png");
  // });

  // can.toBlobHD(function(blob) {
  //   saveAs(blob, "stage"+stage+".png");
  // }, "image/png");

  $('#selector').focus().select();
  $('#prev').prop('disabled', (stage == 0));
  $('#next').prop('disabled', (stage == 990));

  $('#selector').on('change focus', function() {
    stage = $("#selector").val();
    $('#prev').prop('disabled', (stage == 0));
    $('#next').prop('disabled', (stage == 990));

    changeURL(stage);
    drawStage(stage, ctx, ttx, one, all, stageData);
  });

  $("#prev").on('click', function() {
    event.preventDefault();

    index = $("#selector").prop('selectedIndex');
    $("#selector").prop('selectedIndex', index-1);
    stage = $("#selector").val();

    $('#selector').trigger('change');
  });

  $("#next").on('click', function() {
    event.preventDefault();

    index = $("#selector").prop('selectedIndex');
    $("#selector").prop('selectedIndex', index+1);
    stage = $("#selector").val();

    $('#selector').trigger('change');
  });

});
