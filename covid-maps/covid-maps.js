var finalCaseDataArray;

function initializeCovidMaps() {
	var url = "https://covidtracking.com/api/v1/states/daily.json";
	
	// get the raw data
	$.get(url, function (data) {
	
		// "current" date is yesterday, in case today's data isn't up yet
		var thisWeekDate = new Date.today().addDays(-1);
		
		// transform the data into something useful - 3rd parameter is how many weeks to look back
		finalCaseDataArray = parseCovidCaseData(data, thisWeekDate, (-1 * weeksBack));
		
		drawCovidMaps(finalCaseDataArray, 'totalCases');
		
	});
	
}

function parseCovidCaseData(data, thisDate, weeks) {
	
	// put current and past dates into string format
	var currentDate = thisDate.toString("yyyyMMdd");
	var previousDate = thisDate.addWeeks(weeks).toString("yyyyMMdd");
	// exclude some states that don't appear on the maps
	var excludeStates = ['AS', 'GU', 'MP', 'PR', 'VI', 'DC'];
	
	var caseData = {};
	
	// loop through the raw data
	jQuery.each(data, function(i, val) {
		
		// build current data
		if (currentDate == val.date.toString()) {
			
			if (!(excludeStates.includes(val.state))) {
				// if our transformed dataset doesn't include this state, add it
				if (typeof(caseData[val.state]) == 'undefined') {
					caseData[val.state] = {};
				}
				
				// add current data
				caseData[val.state]['thisWeekCaseTotal'] = val.positive;
				caseData[val.state]['thisWeekTestTotal'] = val.totalTestResults;
				
			}
			
		}
		
		// build past data
		if (previousDate == val.date.toString()) {
			
			if (!(excludeStates.includes(val.state))) {
				// if our transformed dataset doesn't include this state, add it
				if (typeof(caseData[val.state]) == 'undefined') {
					caseData[val.state] = {};
				}
				
				// add past data
				caseData[val.state]['lastWeekCaseTotal'] = val.positive;
				caseData[val.state]['lastWeekTestTotal'] = val.totalTestResults;
				
			}
			
			// no reason to parse the entire JSON dataset prior to previousDate - WY is the always last state, so bail
			if (val.state.toString == "WY") { return false; }
			
		}
	});

	// final data transformation
	var thisWeekRate;
	var lastWeekRate;
	var thisValue;
	var customTooltip;
	
	var output = {};
	output['totalCases'] = Array();
	output['positiveRate'] = Array();
	
	// first object value is always column headers, including custom text for tooltips (data that pops up on hover/click)
	output['totalCases'].push(['State', 'Case Trend', {type: 'string', role: 'tooltip'}]);
	output['positiveRate'].push(['State', 'Positive Test Rate Trend', {type: 'string', role: 'tooltip'}]);
	
	// loop through dataset created above - i is state, val is data values
	jQuery.each(caseData, function(i, val) {
		
		// doublecheck that we are never dividing by zero
		if (val.lastWeekCaseTotal > 0) {
			// calculate percentage difference in case totals, limit to 2 decimal points
			thisValue = parseFloat((((val.thisWeekCaseTotal - val.lastWeekCaseTotal) / val.lastWeekCaseTotal) * 100).toFixed(2));
			// create useful tooltip text - add commas to numbers >= 1000
			customTooltip = "Total Cases\nThen: "+val.lastWeekCaseTotal.toLocaleString()+"\nNow: "+val.thisWeekCaseTotal.toLocaleString()+"\nChange: "+thisValue+"%";
			// add to google geochart dataset
			output['totalCases'].push([i, thisValue, customTooltip]);
		}
		
		// doublecheck that we are never dividing by zero
		if (val.lastWeekTestTotal > 0) {
			// calculate percentage difference in positive test rates, limit to 2 decimal points
			thisWeekRate = ((val.thisWeekCaseTotal / val.thisWeekTestTotal) * 100);
			lastWeekRate = ((val.lastWeekCaseTotal / val.lastWeekTestTotal) * 100);
			thisValue = parseFloat((thisWeekRate - lastWeekRate).toFixed(2));
			// create useful tooltip text
			customTooltip = "Positive Test Rate\nThen: "+lastWeekRate.toFixed(2)+"%\nNow: "+thisWeekRate.toFixed(2)+"%\nChange: "+thisValue+"%";
			// add to google geochart dataset
			output['positiveRate'].push([i, thisValue, customTooltip]);
		}
		
	});

	// return final transformed data
	return output;
	
}


function drawCovidMaps(finalCaseDataArray, whichMap) {
	
	// create chart
	var chartData = google.visualization.arrayToDataTable(finalCaseDataArray[whichMap]);
	
	if (whichMap == 'totalCases') {
		// total cases
		var colorAxisObject = {minValue: 0, colors: ['white', 'red']};
	}
	else {
		// positive rate
		var colorAxisObject = {colors: ['green', 'yellow', 'red']};
	}

	// create map
	var options = { 
		region: 'US',
		resolution: 'provinces',
		colorAxis: colorAxisObject
	};
	var chart = new google.visualization.GeoChart(document.getElementById('map_display'));
	chart.draw(chartData, options);

}


// handle tab clicks
function changeMapTab(finalCaseDataArray, tab) {
	// GeoCharts doesn't render properly to a div where display: none,
	// so each tab click redraws the map in the same div with different data
	if (tab == "positiveRate") {
		document.getElementById("covid_total_trend_tab").style = "background-color: #e6e6e6";
		document.getElementById("covid_percent_positive_trend_tab").style = "background-color: #bfbfbf";
		drawCovidMaps(finalCaseDataArray, 'positiveRate');
	}
	else {
		document.getElementById("covid_total_trend_tab").style = "background-color: #bfbfbf";
		document.getElementById("covid_percent_positive_trend_tab").style = "background-color: #e6e6e6";
		drawCovidMaps(finalCaseDataArray, 'totalCases');
	}
}
		




