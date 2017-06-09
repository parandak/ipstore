<?php
?>
<!DOCTYPE html>
<html>
<head>
  <script src="https://cdn.rawgit.com/konvajs/konva/1.6.3/konva.min.js"></script>
  <meta charset="utf-8">
  <title></title>
  <style>
    body {
      margin: 0;
      padding: 0;
      overflow: hidden;
      background-color: #F0F0F0;
    }
  </style>
</head>
<body>
  <div id="container"></div>
  <script>
    var width = window.innerWidth;
    var height = window.innerHeight;
    function update(activeAnchor) {
        var group = activeAnchor.getParent();
        var topLeft = group.get('.topLeft')[0];
        var topRight = group.get('.topRight')[0];
        var bottomRight = group.get('.bottomRight')[0];
        var bottomLeft = group.get('.bottomLeft')[0];
        var image = group.get('Image')[0];
        var anchorX = activeAnchor.getX();
        var anchorY = activeAnchor.getY();
        // update anchor positions
        switch (activeAnchor.getName()) {
            case 'topLeft':
                topRight.setY(anchorY);
                bottomLeft.setX(anchorX);
                break;
            case 'topRight':
                topLeft.setY(anchorY);
                bottomRight.setX(anchorX);
                break;
            case 'bottomRight':
                bottomLeft.setY(anchorY);
                topRight.setX(anchorX);
                break;
            case 'bottomLeft':
                bottomRight.setY(anchorY);
                topLeft.setX(anchorX);
                break;
        }
        image.position(topLeft.position());
        var width = topRight.getX() - topLeft.getX();
        var height = bottomLeft.getY() - topLeft.getY();
        if(width && height) {
            image.width(width);
            image.height(height);
        }
    }
    function addAnchor(group, x, y, name) {
        var stage = group.getStage();
        var layer = group.getLayer();
        var anchor = new Konva.Circle({
            x: x,
            y: y,
            stroke: '#666',
            fill: '#ddd',
            strokeWidth: 2,
            radius: 8,
            name: name,
            draggable: true,
            dragOnTop: false
        });
        anchor.on('dragmove', function() {
            update(this);
            layer.draw();
        });
        anchor.on('mousedown touchstart', function() {
            group.setDraggable(false);
            this.moveToTop();
        });
        anchor.on('dragend', function() {
            group.setDraggable(true);
            layer.draw();
        });
        // add hover styling
        anchor.on('mouseover', function() {
            var layer = this.getLayer();
            document.body.style.cursor = 'pointer';
            this.setStrokeWidth(4);
            layer.draw();
        });
        anchor.on('mouseout', function() {
            var layer = this.getLayer();
            document.body.style.cursor = 'default';
            this.setStrokeWidth(2);
            layer.draw();
        });
        group.add(anchor);
    }
    var stage = new Konva.Stage({
        container: 'container',
        width: width,
        height: height
    });
    var layer = new Konva.Layer();
    stage.add(layer);
    // darth vader
    var darthVaderImg = new Konva.Image({
        width: 700,
        height: 350
    });
    // yoda
    var yodaImg = new Konva.Image({
        width: 160,
        height: 160
    });
    var darthVaderGroup = new Konva.Group({
        x: 180,
        y: 50,
        draggable: true
    });
    layer.add(darthVaderGroup);
    darthVaderGroup.add(darthVaderImg);
    addAnchor(darthVaderGroup, 0, 0, 'topLeft');
    addAnchor(darthVaderGroup, 700, 0, 'topRight');
    addAnchor(darthVaderGroup, 700, 350, 'bottomRight');
    addAnchor(darthVaderGroup, 0, 350, 'bottomLeft');
    var yodaGroup = new Konva.Group({
        x: 20,
        y: 110,
        draggable: true
    });
    layer.add(yodaGroup);
    yodaGroup.add(yodaImg);
    addAnchor(yodaGroup, 0, 0, 'topLeft');
    addAnchor(yodaGroup, 160, 0, 'topRight');
    addAnchor(yodaGroup, 160, 160, 'bottomRight');
    addAnchor(yodaGroup, 0, 160, 'bottomLeft');
    var imageObj1 = new Image();
    imageObj1.onload = function() {
        darthVaderImg.image(imageObj1);
        layer.draw();
    };
    imageObj1.src = 'https://www.luxresorts.com/media/2601545/Maldives_Hotels_Resorts_LUX_Maldives_Boat_Trip.jpg';
    var imageObj2 = new Image();
    imageObj2.onload = function() {
        yodaImg.image(imageObj2);
        layer.draw();
    };
    imageObj2.src = '/s1/qr';
  </script>
</body>
</html>

