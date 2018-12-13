$(function(){

    var previousPoint;
 
    var d1 = [];
    for (var i = 0; i <= 10; i += 1)
        d1.push([i, parseInt(Math.random() * 30)]);
 
    var d2 = [];
    for (var i = 0; i <= 10; i += 1)
        d2.push([i, parseInt(Math.random() * 30)]);
 
    var d3 = [];
    for (var i = 0; i <= 10; i += 1)
        d3.push([i, parseInt(Math.random() * 30)]);
 
    var ds = new Array();
 
     ds.push({
        data:d1,
        bars: {
            show: true, 
            barWidth: 0.2, 
            order: 1
        },
        color: '#0099cc'
    });
    ds.push({
        data:d2,
        bars: {
            show: true, 
            barWidth: 0.2, 
            order: 2
        },
        color: '#f0623f'
    });
    ds.push({
        data:d3,
        bars: {
            show: true, 
            barWidth: 0.2, 
            order: 3
        },
        color: '#61b020'
    });
                
    //Display graph
    $.plot($("#chartdemo02"), ds, {
        grid:{ hoverable:true },
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
	$("#chartdemo02").bind("plothover", function (event, pos, item){
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