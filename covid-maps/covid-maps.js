var finalCaseDataArray;

function initializeCovidMaps() {
	var url = "https://covidtracking.com/api/v1/states/daily.json";
	
	// get the raw data
	$.get(url, function (data) {
	
		var thisWeekDate = new Date.today();
		var lastWeekDate = new Date.today().addWeeks(-1 * weeksBack);
		
		// transform the data into something useful - 3rd parameter is how many weeks to look back
		finalCaseDataArray = parseCovidCaseData(data, thisWeekDate, lastWeekDate);
		
		drawCovidMaps(finalCaseDataArray, 'totalCases');
		
	});
	
}

function parseCovidCaseData(data, currentDate, previousDate) {
	
	// put current and past dates into string format
	//var currentDate = thisDate.toString("yyyyMMdd");
	//var previousDate = thisDate.addWeeks(weeks).toString("yyyyMMdd");
	
	var currentDateArray = new Array();
	for (var x = 0; x <= 6; x++) {
		currentDateArray[x] = currentDate.addDays(-1).toString("yyyyMMdd");
	}
	var previousDateArray = new Array();
	for (var x = 0; x <= 6; x++) {
		previousDateArray[x] = previousDate.addDays(-1).toString("yyyyMMdd");
	}
	var stopDate = previousDate.addDays(-1).toString("yyyyMMdd");
	
	
	// exclude some states that don't appear on the maps
	var excludeStates = ['AS', 'GU', 'MP', 'PR', 'VI', 'DC'];
	
	var caseData = {};
	
	// loop through the raw data
	jQuery.each(data, function(i, val) {
		
		if ((currentDateArray.includes(val.date.toString())) || (previousDateArray.includes(val.date.toString()))) {
			
			if (!(excludeStates.includes(val.state))) {
				// if our transformed dataset doesn't include this state, add it
				if (typeof(caseData[val.state]) == 'undefined') {
					caseData[val.state] = {};
					caseData[val.state]['thisWeekCaseTotal'] = 0;
					caseData[val.state]['lastWeekCaseTotal'] = 0;
				}
				
				// add current data
				if (currentDateArray.includes(val.date.toString())) {
					// sum of a week's worth of data
					caseData[val.state]['thisWeekCaseTotal'] += val.positiveIncrease;
				}
				if (currentDateArray[0] == val.date.toString()) {
					// cumulative totals on this day
					caseData[val.state]['thisWeekCaseOneDay'] = val.positive;
					caseData[val.state]['thisWeekTestOneDay'] = val.totalTestResults;
				}
				
				// add past data
				if (previousDateArray.includes(val.date.toString())) {
					// sum of a week's worth of data
					caseData[val.state]['lastWeekCaseTotal'] += val.positiveIncrease;
				}
				if (previousDateArray[0] == val.date.toString()) {
					// cumulative totals on this day
					caseData[val.state]['lastWeekCaseOneDay'] = val.positive;
					caseData[val.state]['lastWeekTestOneDay'] = val.totalTestResults;
				}
					
			}
			
		}
		
		// once we hit the earliest date, stop looping thru data - better performance
		if (stopDate == val.date.toString()) { return false; }
		
	});

	// final data transformation
	var thisWeekRate;
	var lastWeekRate;
	var thisValue;
	var customTooltip;
	
	var output = {};
	output['totalCases'] = new Array();
	output['positiveRate'] = new Array();
	
	// first object value is always column headers, including custom text for tooltips (data that pops up on hover/click)
	output['totalCases'].push(['State', 'Case Trend', {type: 'string', role: 'tooltip'}]);
	output['positiveRate'].push(['State', 'Positive Test Rate Trend', {type: 'string', role: 'tooltip'}]);
	
	// loop through dataset created above - i is US state, val is data values
	jQuery.each(caseData, function(i, val) {
		
		// TOTAL CASES - doublecheck that we are never dividing by zero
		if (val.lastWeekCaseTotal > 0) {
			
			// low case counts in either week make for huge variance in trends, so make 50 cases/week the cutoff
			if ((val.thisWeekCaseTotal >= 50) && (val.lastWeekCaseTotal >= 50)) {
				
				// calculate percentage difference in case totals, limit to 2 decimal points
				thisValue = parseFloat((((val.thisWeekCaseTotal - val.lastWeekCaseTotal) / val.lastWeekCaseTotal) * 100).toFixed(2));
				
				// create useful tooltip text - add commas to numbers >= 1000
				customTooltip = "Total Cases\nThen: "+val.lastWeekCaseTotal.toLocaleString()+" /week\nNow: "+val.thisWeekCaseTotal.toLocaleString()+" /week\nChange: "+thisValue+"%";
				
				// add to google geochart dataset
				output['totalCases'].push([i, thisValue, customTooltip]);
				
			}
		}
		
		// POSITIVE RATE - doublecheck that we are never dividing by zero
		if ((val.thisWeekTestOneDay > 0) && (val.lastWeekTestOneDay > 0)) {
			
			// calculate percentage difference in positive test rates, limit to 2 decimal points
			thisWeekRate = ((val.thisWeekCaseOneDay / val.thisWeekTestOneDay) * 100);
			lastWeekRate = ((val.lastWeekCaseOneDay / val.lastWeekTestOneDay) * 100);
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
		var colorAxisObject = {minValue: -100, maxValue: 100, colors: ['green', 'yellow', 'red']};
	}
	else {
		// positive rate
		var colorAxisObject = {minValue: -2, maxValue: 2, colors: ['green', 'yellow', 'red']};
	}

	// create map
	var options = { 
		region: 'US',
		resolution: 'provinces',
		colorAxis: colorAxisObject,
		legend: 'none'
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
		




