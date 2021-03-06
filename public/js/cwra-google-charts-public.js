// Load the Visualization API and the corechart package.
google.charts.load('current', {'packages':['corechart', 'controls']});

// Set a callback to run when the Google Visualization API is loaded.
google.charts.setOnLoadCallback(initializeData);

// Query a CSV for the data
function initializeData() {
	let graphs = document.getElementsByClassName("cwragc_chart");

	for (let i = 0; i < graphs.length; i++) {
		let opts;
		let query;
		let graph = graphs.item(i);

		// XXX make this based on configuration
		if (graph.dataset.cwragcTitle == "Comparisons") {
			opts = {sendMethod: 'auto',
				csvColumns: ['string', 'number', 'number',
				    'number', 'number'],
				csvHasHeader: true};
		} else {
			opts = {sendMethod: 'auto',
				csvColumns: ['string', 'number', 'number'],
				csvHasHeader: true};
		}

		query = new google.visualization.Query(
		    cwragc.contentdir + '/'
		        + graph.dataset.cwragcSrc,
		    opts);

		// Use an "in between" anonymous function, so we can pass
		// more than just the callback function
		query.send(function (response) {
			handleQueryResponse(response, graph);
		});
	}
}

// what can we configure for this kind of chart?
function getOptions( type ) {
	const MAIN = { 'BackgroundColor': 'backgroundColor',
	    'ChartArea': 'chartArea', 
	    'EnableInteractivity': 'enableInteractivity',
	    'FontName': 'fontName', 'ForcelFrame': 'forcelFrame',
	    'Height': 'height', 'Legend': 'legend', 'TextStyle': 'textStyle',
	    'Title': 'title', 'Width': 'width' };
	const LINE = {};
	const PIE = {};

	options = {...MAIN};
	console.log("Main options are ", options);
	switch ( type ) {
	    case 'line':
	    	options = {...options, ...LINE};
		break;
	    case 'pie':
	    	options = {...options, ...PIE};
		break;
	}
	console.log("Options are now ", options);

	return options;
}

// when we get a response, draw the chart
function handleQueryResponse(response, graph) {
	let comparison = false;
	let baseId;
	let chart, chartType = 'LineChart';
	let options = {}
	let config = {};
	let avgDays = 7;

	if (response.isError()) {
		alert('Error in query: '
		    + response.getMessage()
		    + ' '
		    + response.getDetailedMessage());
		return;
	}

	// XXX make this configurable
	if (graph.dataset.cwragcTitle == "Comparisons") {
		comparison = true;
	}

	baseId = graph.id;
	let dashboardId = baseId + '_dashboard_div';
	let controlId = baseId + '_control_div';
	let colControlId = baseId + '_col_control_div';
	let rangeControlId = baseId + '_range_control_div';

	switch ( graph.dataset.cwragcType ) {
	    case 'area':
		chartType = 'AreaChart';
		break;
	    case 'bar':
		chartType = 'BarChart';
		break;
	    case 'column':
		chartType = 'ColumnChart';
		break;
	    // skip Line, use as default
	    case 'pie':
		chartType = 'PieChart';
		break;
	    case 'scatter':
		chartType = 'ScatterChart';
		break;
	}

	// XXX need to make this configurable
	let isAverageable = { '1': true };
	let defaultColumns = { '1': true };
	let viewColumns = {};
	let view;

	if (comparison) {
		isAverageable = { '5': true,
		    '6': true,
		    '7': true,
		    '8': true }
		defaultColumns = { '5': true,
		    '7': true }
	}

	let dashboard = new google.visualization.Dashboard(
	    document.getElementById(dashboardId));

	/*
	 * control range of chart
	 */
	let control = new google.visualization.ControlWrapper({
	    'controlType': 'ChartRangeFilter',
	    'containerId': controlId,
	    'options': {
	    	'filterColumnIndex': 0,
		'ui': {
		    'chartType': chartType,
		    'chartOptions': {
		    	'chartArea': {'width': '90%'},
			'hAxis': {'baseLineColor': 'none'}
		    },
		    // 1 day in milliseconds
		    'minRangeSize': 86400000
	    	}
	    },
	    'state': {'range': { 'start': new Date(2020, 8, 1),
	        'end': new Date()}}
	});

	options = getOptions(graph.dataset.cwragcType);
	for (const key in options) {
		if (graph.dataset['cwragc' + key]) {
			config = {...config,
			    [options[key]]: graph.dataset['cwragc' + key] };
		}
	}

	let vAxisConfig = { 'vAxis': {'viewWindow': {'min': 0}} };

	config = {...config,
	    	'chartArea': {'height': '80%', 'width': '90%'},
		'hAxis': {'slantedText': false},
		...vAxisConfig,
		'legend': {'position': 'in'}
	}

	chart = new google.visualization.ChartWrapper({
	    'chartType': chartType,
	    'containerId': baseId,
	    'options': config
	});

	let data = response.getDataTable();

	//rename columns
	if (comparison) {
		data.setColumnLabel(0, 'Date');
		data.setColumnLabel(1, 'Cumulative Cases');
		data.setColumnLabel(2, 'Cumulative Recovered');
		data.setColumnLabel(3, 'Cumulative Deaths');
		data.setColumnLabel(4, 'Cumulative Tests');
	} else {
		data.setColumnLabel(0, 'Date');
		data.setColumnLabel(1, 'Incremental');
		data.setColumnLabel(2, 'Cumulative');
	}

	/*
	 * add javascript user controls (not google chart controls)
	 */
	let columns = [];
	// list of columns from which we can select
	// skip column 0
	for (let i = 1; i < data.getNumberOfColumns(); i++) {
		columns.push(data.getColumnLabel(i));
	}
	if (comparison) {
		columns.push("Cases");
		columns.push("Recovered");
		columns.push("Deaths");
		columns.push("Tests");
	}

	for (let i = 0; i < columns.length; i++) {
		let newDiv = document.createElement('div');
		newDiv.setAttribute('class', 'custom-control custom-checkbox'
		    + ' custom-control-inline');

		let id = colControlId + '_' + columns[i];
		let label = document.createElement('label');
		let txt = document.createTextNode(columns[i]);
		label.setAttribute('class', 'custom-control-label');
		label.setAttribute('for', id);
		label.appendChild(txt);

		let checkbox = document.createElement('input');
		checkbox.type = 'checkbox';
		checkbox.name = columns[i];
		checkbox.id = id;
		// the i-th item in the area represents the i-th + 1 column
		checkbox.value = i+1;
		checkbox.setAttribute('class', 'custom-control-input');
		checkbox.onclick = toggleView;

		// we're iterating through all of the columns here, so go 
		// ahead and initialize which columns to view while we're at it
		if ( defaultColumns[i + 1] ) {
			checkbox.checked = true;
			viewColumns[i + 1] = true;
		} else {
			viewColumns[i + 1] = false;
		}

		newDiv.appendChild(checkbox);
		newDiv.appendChild(label);
		document.getElementById(colControlId).appendChild(newDiv);

		// add an average checkbox
		if (isAverageable[i + 1]) {
			let newDiv = document.createElement('div');
			newDiv.setAttribute('class',
			    'custom-control custom-checkbox'
			    + ' custom-control-inline');

			let id = colControlId + '_' + columns[i]
			    + '_avg';
			let label = document.createElement('label');
			let txt = document.createTextNode(columns[i]
			    + ' moving average');
			label.setAttribute('class', 'custom-control-label');
			label.setAttribute('for', id);
			label.appendChild(txt);

			let checkbox = document.createElement('input');
			checkbox.type = 'checkbox';
			checkbox.name = columns[i] + 'Average';
			checkbox.id = id;
			// the i-th item in the area represents the i-th + 1
			// column
			checkbox.value = i+1 + '_avg';
			checkbox.setAttribute('class', 'custom-control-input');
			checkbox.onclick = toggleView;

			newDiv.appendChild(checkbox);
			newDiv.appendChild(label);
			document.getElementById(colControlId).appendChild(
			    newDiv);

			// start with averages off
			viewColumns[i + 1 + "_avg"] = false;
		}
	}

	function toggleView() {
		let sliderWrapper = document.getElementById(rangeControlId);
		let sliderParent;

		if (!comparison) {
			sliderParent = sliderWrapper;
		} else {
			let newDiv = document.getElementById(this.id
			    + '_slider_div');
			if (newDiv == null) {
				newDiv = document.createElement('div');
				newDiv.setAttribute('id', this.id +
				    '_slider_div');
			}
			sliderParent = newDiv;
		}

		// 'this' is reference to checkbox clicked on
		if ( this.checked ) {
			viewColumns[this.value] = true;
			// turn on avg slider if _avg
			if (this.id.endsWith('_avg') &&
			    document.getElementById(this.id + '_slider')) {
				sliderParent.style.display = 'initial';
			} else if (this.id.endsWith('_avg')) {
				let label = document.createElement('label');
				let txt = document.createTextNode("Average" +
				    ' Period');
				label.setAttribute('for', this.id + '_slider');
				label.appendChild(txt);

				let slider = document.createElement('input');
				slider.type = 'range';
				slider.id = this.id + '_slider';
				slider.setAttribute('class',
				    'form-control-range');
				slider.setAttribute('min', '2');
				slider.setAttribute('max', '21');
				slider.setAttribute('value', '7');
				slider.oninput = function () {
					avgDays = this.value;
					setChartView();
					dashboard.draw(view);
				}

				console.log("Appending to ", sliderParent);
				sliderParent.appendChild(label);
				sliderParent.appendChild(slider);
				sliderParent.style.display = 'initial';
				if (comparison) {
					sliderWrapper.style.display = 'initial';
					if (sliderWrapper.children.length 
					    == 0) {
						sliderWrapper.appendChild(sliderParent);
					}
				}
			}
		} else {
			viewColumns[this.value] = false;
			// turn off avg slider if _avg
			if (this.id.endsWith('_avg')) {
			    	sliderParent.style.display = 'none';
			}
		}

		console.log("But avg days ", avgDays);

		setChartView();
		dashboard.draw(view);
	}

	function setChartView() {
		// iterate through viewColumns. If key doesn't end in _avg
		// and value is false, skip it. On true, set it, and check 
		// key with _avg. If true, set it.
		let seriesIndex = 0;
		let myTargetAxis;
		let myColumns = [0];
		let mySeriesConf = {};
		for (let key in viewColumns) {
			myTargetAxis = 0;
			console.log("viewColumns ", viewColumns);
			if (!key.endsWith("_avg") && viewColumns[key]) {
				if (comparison && parseInt(key) > 4) {
				    let myLabel;
				    let myColumn = parseInt(key) - 4;
				    switch (key) {
				      case '5':
					myLabel = 'Cases';
					break;
				      case '6':
					myLabel = 'Recovered';
					break;
				      case '7':
					myLabel = 'Deaths';
					myTargetAxis = 1;
					break;
				      case '8':
					myLabel = 'Tests';
					break;
				    }
					myColumns.push({
					    type: 'number',
					    label: myLabel,
					    sourceColumn: myColumn,
					    calc: function (dt, row) {
					        if (row >= 2) {
						    return dt.getValue(row,
						        myColumn) -
							dt.getValue(row - 1,
							myColumn);
						} else {
						    return null;
						}
					    }
					});
					mySeriesConf = {...mySeriesConf,
					    [seriesIndex]: { targetAxisIndex:
					        myTargetAxis } };
					seriesIndex++;
				} else {
					myColumns.push(parseInt(key));
					if (parseInt(key) == 3) {
						myTargetAxis = 1;
					}
					mySeriesConf = {...mySeriesConf,
					    [seriesIndex]: { targetAxisIndex:
					        myTargetAxis } };
					seriesIndex++;
				}
				if (viewColumns[key + '_avg']) {
					if (comparison) {
					    let myLabel;
					    let myColumn = parseInt(key) - 4;
					    switch (key) {
					      case '5':
						myLabel = 'New Cases';
						break;
					      case '6':
						myLabel = 'New Recovered';
						break;
					      case '7':
						myLabel = 'New Deaths';
						break;
					      case '8':
						myLabel = 'New Tests';
						break;
					    }
					    console.log("Pushing for ", myColumn);
					myColumns.push({
					    type: 'number',
					    label: avgDays
					      + '-day ' + myLabel
					      + ' Moving Average',
					    sourceColumn: myColumn,
					    calc: function (dt, row) {
						if (row >= avgDays) {
							let total = 0;
							total = dt.getValue(
							    row, myColumn) - 
							    dt.getValue(
							    row - avgDays,
							    myColumn);
							let average = total /
							    avgDays;
							return average;
						} else {
			    				// null for < x days
			    				return null;
						}
					    }
					});
					mySeriesConf = {...mySeriesConf,
					    [seriesIndex]: { targetAxisIndex:
					        myTargetAxis } };
					seriesIndex++;
					} else {
					console.log("AvgDays here ", avgDays);
					// push the average
					myColumns.push({
					    type: 'number',
					    label: avgDays
					      + '-day moving average',
					    calc: function (dt, row) {
						if (row >= avgDays - 1) {
							let total = 0;
							for (let i = 0;
							  i < avgDays; i++)
							  {
								total +=
								    dt.getValue(row -i, 1);
							}
							let average =
							    total / avgDays;
							return {v: average,
							    f: average.toFixed(2)};
						} else {
			    				// null for < x days
			    				return null;
						}
					    }
					});
					mySeriesConf = {...mySeriesConf,
					    [seriesIndex]: { targetAxisIndex:
					        myTargetAxis } };
					seriesIndex++;
				        }
				}
			}
		}

		view = new google.visualization.DataView(data);
		view.setColumns(myColumns);

		if (comparison) {
			let myVAxes = {
				0: { 'viewWindow': {'min': 0} },
				1: { 'viewWindow': {'min': 0} }
			};

			config = {...config,
				'chartArea': {'height': '80%', 'width': '90%'},
				'hAxis': {'slantedText': false},
				'vAxes': myVAxes,
				'series': mySeriesConf,
				'legend': {'position': 'in'}
			}

			chart.setOptions(config);
			console.log("chart options are", chart.getOptions());
		}

	}

	//convert strings in column 0 to proper dates
	data.insertColumn(0, 'date', data.getColumnLabel(0));
	for ( let i = 0; i < data.getNumberOfRows(); i++ ) {
		val = data.getValue(i, 1);

		data.setValue(i, 0, new Date(val));
	}
	data.removeColumn(1);

	// set the initial view
	setChartView();
	dashboard.bind(control, chart);
	dashboard.draw(view);
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

	/*
	 * XXX for when we don't have user controls
	 */
	/*
	let data = response.getDataTable();
	switch ( graph.dataset.cwragcType ) {
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
	*/

	/*
	options = getOptions(graph.dataset.cwragcType);
	console.log("Returned options ", options);
	for (const key in options) {
		console.log("Checking for ", key);
		console.log("got ", graph.dataset['cwragc' + key]);
		console.log("with ", 'cwragc' + key);
		if (graph.dataset['cwragc' + key]) {
			console.log("Got one!");
			config = {...config,
			    [options[key]]: graph.dataset['cwragc' + key] };
			console.log("So now config is", config);
		}
	}
	console.log("Calculated config as ", config);
	chart.draw(data, config);
	*/
