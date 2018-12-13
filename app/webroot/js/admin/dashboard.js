$(function(){

	function showTooltip(title, x, y, contents) {
        $('<div id="tooltip" class="chart-tooltip"><div class="date">' + title + '<\/div><div class="percentage">Percent: <span>' + x / 10 + '%<\/span><\/div><div class="visits">Visitors: <span>' + x * 12 + '<\/span><\/div><\/div>').css({
            position: 'absolute',
            display: 'none',
            top: y - 108,
            left: x - 76,
            'background-color': '#f8f8f8',
            border: '1px solid #d2d2d2'
        }).appendTo("body").fadeIn(200);
    }

    var d1 = [4.3, 5.1, 4.3, 5.2, 5.4, 4.7, 3.5, 4.1, 5.6, 7.4, 6.9, 7.1,
    7.9, 7.9, 7.5, 6.7, 7.7, 7.7, 7.4, 7.0, 7.1, 5.8, 5.9, 7.4,
    8.2, 8.5, 9.4, 8.1, 10.9, 10.4, 10.9, 12.4, 12.1, 9.5, 7.5,
    7.1, 7.5, 8.1, 6.8, 3.4, 2.1, 1.9, 2.8, 2.9, 1.3, 4.4, 4.2,
    3.0, 3.0], 

	d2 = [0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.1, 0.0, 0.3, 0.0,
	    0.0, 0.4, 0.0, 0.1, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0,
	    0.0, 0.6, 1.2, 1.7, 0.7, 2.9, 4.1, 2.6, 3.7, 3.9, 1.7, 2.3,
	    3.0, 3.3, 4.8, 5.0, 4.8, 5.0, 3.2, 2.0, 0.9, 0.4, 0.3, 0.5, 0.4], 

	options = {
	    series: {
	        lines: { 
	            show: true, 
	            fill: true, 
	            lineWidth: 2, 
	            steps: false, 
	            fillColor: { colors: [{opacity: 0.25}, {opacity: 0}] } 
	        },
	        points: { 
	            show: true, 
	            radius: 4, 
	            fill: true,
	            lineWidth: 1.5
	        }
	    }, 
	    tooltip: true, 
	    tooltipOpts: {
	        content: '%s: %y'
	    }, 
	    xaxis: {  mode: "time" , minTickSize: [1, "hour"]
        }, 
	    grid: { borderWidth: 0, hoverable: true },
	    legend: {
	        show: false
	    }
	};

    var dt1 = [], dt2 = [], st = new Date(2009, 9, 6).getTime();

	for( var i = 0; i < d2.length; i++ )
	{
	    dt1.push([st + i * 3600000, parseFloat( (d1[i]).toFixed( 3 ) )]);
	    dt2.push([st + i * 3600000, parseFloat( (d2[i]).toFixed( 3 ) )]);
	}

	var data = [
	    { data: dt1, color: '#089dcf', label: 'This month sales', lines: { lineWidth: 1.5 } }, 
	    { data: dt2, color: '#ef5f3c', label: 'Last month profit', points: { show: false }, lines: { lineWidth: 2, fill: false } }
	];

	$.plot($("#chartLine01"), data, options);

    var previousPoint = null;
    $("#chartLine01").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));
        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;

                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);

                    var d = new Date(item.datapoint[0]);

					var monthNames = ["January", "February", "March", "April", "May", "June",  
				  	"July", "August", "September", "October", "November", "December"];  
				    var current_month = d.getMonth();  
				    var month_name = monthNames[current_month]; 
					var day = d.getDate();

					var time = (d.getHours()<10?'0':'') + d.getHours() + ":" + (d.getMinutes()<10?'0':'') + d.getMinutes();

					var output = ((''+day).length<2 ? '0' : '') + day + ' ' +
					((''+month_name).length<2 ? '0' : '') + month_name + ', ' +
					d.getFullYear() + '<span class="clock">' + time + '</span>';

                showTooltip(output, item.pageX, item.pageY, item.series.label + " of " + x + " = " + y);
            }
        } else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });
	
	$('.calendar-event-list div.external-event').each(function() {
		// create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
		// it doesn't need to have a start or end
		var eventObject = {
			title: $.trim($(this).text()) // use the element's text as the event title
		};
		
		// store the Event Object in the DOM element so we can get to it later
		$(this).data('eventObject', eventObject);
		
		// make the event draggable using jQuery UI
		$(this).draggable({
			zIndex: 999,
			revert: true,      // will cause the event to go back to its
			revertDuration: 0  //  original position after the drag
		});
	});

	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
	$('#calendar').fullCalendar({
		header: {
		left: 'prev,next',
		center: 'title',
		right: 'month,agendaWeek,agendaDay'
		},
		buttonText: {
		prev: '<i class="icon-chevron-left cal_prev" />',
		next: '<i class="icon-chevron-right cal_next" />'
		},
		aspectRatio: 1.5,
		selectable: false,
		selectHelper: true,
		editable: true,
		droppable: true,
		theme: false,
		editable: true,
		droppable: true, // this allows things to be dropped onto the calendar !!!
		drop: function(date, allDay) { // this function is called when something is dropped
		
			// retrieve the dropped element's stored Event Object
			var originalEventObject = $(this).data('eventObject');
			
			// we need to copy it, so that multiple events don't have a reference to the same object
			var copiedEventObject = $.extend({}, originalEventObject);
			
			// assign it the date that was reported
			copiedEventObject.start = date;
			copiedEventObject.allDay = allDay;
			
			// render the event on the calendar
			// the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
			$('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
			
			// is the "remove after drop" checkbox checked?
			if ($('#drop-remove').is(':checked')) {
				// if so, remove the element from the "Draggable Events" list
				$(this).remove();
			}
			
		},
		events: [
		{
			title: 'All Day Event',
			start: new Date(y, m, 1)
		},
		{
			title: 'Long Event',
			start: new Date(y, m, d-5),
			end: new Date(y, m, d-2)
		},
		{
			id: 999,
			title: 'Repeating Event',
			start: new Date(y, m, d+8, 16, 0),
			allDay: false
		},
		{
			title: 'Meeting',
			start: new Date(y, m, d+12, 15, 0),
			allDay: false
		},
		{
		title: 'Doomsday',
						start: new Date(y, m, d+1, 19, 0),
						end: new Date(y, m, d+1, 22, 30),
						allDay: false
					},
		{
		title: 'We are alive',
					start: new Date(y, m, d+2, 19, 0),
					end: new Date(y, m, d+2, 22, 30),
					allDay: false
				},
				{
					title: 'Click for Google',
					start: new Date(y, m, 28),
					end: new Date(y, m, 29),
					url: 'http://google.com/default.htm'
				}
			],
		eventColor: '#56acab'
	});

});