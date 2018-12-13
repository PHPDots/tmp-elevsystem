$(function(){

    var previousPoint;
 

    //Display horizontal graph
    var d1_h = [];
    for (var i = 0; i <= 4; i += 1)
        d1_h.push([parseInt(Math.random() * 30),i ]);

    var d2_h = [];
    for (var i = 0; i <= 4; i += 1)
        d2_h.push([parseInt(Math.random() * 30),i ]);

    var d3_h = [];
    for (var i = 0; i <= 4; i += 1)
        d3_h.push([ parseInt(Math.random() * 30),i]);
                
    var ds_h = new Array();
    ds_h.push({
        data:d1_h,
        bars: {
            horizontal:true, 
            show: true, 
            barWidth: 0.2, 
            order: 1,
        }
    });
    ds_h.push({
        data:d2_h,
        bars: {
            horizontal:true, 
            show: true, 
            barWidth: 0.2, 
            order: 2
        }
    });
    ds_h.push({
        data:d3_h,
        bars: {
            horizontal:true, 
            show: true, 
            barWidth: 0.2, 
            order: 3
        }
    });

    $.plot($("#chartdemo03"), ds_h, {
        grid:{
            hoverable:true
        }
    });

    function showTooltip(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            //float: 'left',
            top:  y - 40,
            left: x - 30,
            color: '#afafaf',
            fontSize: '11px',
            fontFamily: 'Arial',
            fontWeight: 'normal',
            '-webkit-border-radius': '2px',
            '-moz-border-radius': '2px',
            'border-radius': '2px',
            padding: '4px 10px',
            'background-color': 'rgba(47, 47, 47, 0.95)'
        }).appendTo("body").fadeIn(200);
     }


    var previousPoint = null;
    $("#chartdemo03").bind("plothover", function (event, pos, item){
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));
        if (item) {
            if (previousPoint != item.dataIndex){
                previousPoint = item.dataIndex;

                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);

                    showTooltip(item.pageX, item.pageY, y);
                                                }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;
         }
    });

});