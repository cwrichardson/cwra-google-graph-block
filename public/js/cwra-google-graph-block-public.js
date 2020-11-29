// Load the Visualization API and the corechart package.
google.charts.load('current', {'packages':['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.charts.setOnLoadCallback(initializeData);

// Query a CSV for the data
function initializeData() {
	let graphs = document.getElementsByClassName("cwraggbp");

	for (let i = 0; i < graphs.length; i++) {
		let graph = graphs.item(i);

		let opts = {sendMethod: 'auto',
			csvColumns: ['string', 'number', 'number'],
			csvHasHeader: true};
		let query = new google.visualization.Query(
		    cwraggbp.contentdir + '/'
		        + graph.dataset.cwraggbpSrc,
		    opts);

		console.log("Useing query ", query);

		// Use an "in between" anonymous function, so we can pass
		// more than just the callback function
		query.send(function (response) {
			handleQueryResponse(response, graph);
		});
	}
}

// when we get a response, draw the chart
function handleQueryResponse(response, graph) {
	let chart;
	console.log("Got response ", response);
	console.log("for graph ", graph);

	if (response.isError()) {
		alert('Error in query: '
		    + response.getMessage()
		    + ' '
		    + response.getDetailedMessage());
		return;
	}

	let data = response.getDataTable();
	console.log("Drawing chart of type ", graph.dataset.cwraggbpType);
	switch ( graph.dataset.cwraggbpType ) {
	    case 'area':
		chart = new google.visualization.AreaChart(graph);
		break;
	    case 'bar':
		chart = new google.visualization.BarChart(graph);
		break;
	    case 'column':
		chart = new google.visualization.ColumnChart(graph);
		break;
	    // skip Line, use as default
	    case 'pie':
		chart = new google.visualization.PieChart(graph);
		break;
	    case 'scatter':
		chart = new google.visualization.ScatterChart(graph);
		break;
	    default:
		chart = new google.visualization.LineChart(graph);
	}
	chart.draw(data, {width: 400, height: 240, is3D: true});
}

(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );
