(function($, window, document, undefined){

    /**
     * Assure namespace of correct type
     */
    if(typeof window.Intelligence === 'undefined'){ window.Intelligence = {}; }

    /**
     * Assure google charts namespace
     */
    if(typeof window.Intelligence.googlecharts === 'undefined'){ window.Intelligence.googlecharts = {}; }

    /**
     * Draw city chart
     *
     * @param data
     * @param start
     * @param end
     * @param elementID
     * @param options
     * @param loader
     */
    window.Intelligence.googlecharts.drawCityChart = function(data, start, end, elementID, printHandler, options, loader){
        $('#' + elementID).html(loader);
        var interval = end - start;
        options.region = 'US';
        options.dataMode = 'markers';
        WEv1api.setEndpoint("/intelligence/" + encodeURIComponent(data) + "/" + start + "/" + end + "/" + interval).get(function(response){
            if(response.intelligence != undefined){
                var gData = [];
                gData.push(['City', 'Count']);
                $.each(response.intelligence, function(index, intel) {
                    if(intel.values != undefined) {
                        $.each(intel.values, function (index, value) {
                            var gEntry = [];
                            gEntry[0] = value.text;
                            gEntry[1] = parseInt(value.count);
                            gData.push(gEntry);
                        });
                    }
                });
                var data = google.visualization.arrayToDataTable(gData);
                var chart = new google.visualization.GeoMap(document.getElementById(elementID));
                google.visualization.events.addListener(chart, 'ready', function () {
                    printHandler(chart);
                });
                var paintGraph = function() {
                    chart.draw(data, options);
                }
                paintGraph();
                var timeout = null;
                $(window).resize(function(){
                    clearTimeout(timeout);
                    timeout = setTimeout(paintGraph, 200);
                });
            }
        });
    };

    /**
     * Draw region chart
     *
     * @param data
     * @param start
     * @param end
     * @param elementID
     * @param options
     * @param loader
     */
    window.Intelligence.googlecharts.drawRegionChart = function(data, start, end, elementID, printHandler, options, loader){
        $('#' + elementID).html(loader);
        var interval = end - start;
        options.region = 'US';
        WEv1api.setEndpoint("/intelligence/" + encodeURIComponent(data) + "/" + start + "/" + end + "/" + interval).get(function(response){
            if(response.intelligence != undefined){
                console.log('regions', response.intelligence);
                var gData = [];
                gData.push(['Region', 'Count']);
                $.each(response.intelligence, function(index, intel) {
                    if(intel.values != undefined) {
                        $.each(intel.values, function (index, value) {
                            var gEntry = [];
                            gEntry[0] = value.text;
                            gEntry[1] = parseInt(value.count);
                            gData.push(gEntry);
                        });
                    }
                });
                var data = google.visualization.arrayToDataTable(gData);
                var chart = new google.visualization.GeoMap(document.getElementById(elementID));
                google.visualization.events.addListener(chart, 'ready', function () {
                    printHandler(chart);
                });
                var paintGraph = function() {
                    chart.draw(data, options);
                }
                paintGraph();
                var timeout = null;
                $(window).resize(function(){
                    clearTimeout(timeout);
                    timeout = setTimeout(paintGraph, 200);
                });
            }
        });
    };

    /**
     * Draw country chart
     *
     * @param data
     * @param start
     * @param end
     * @param elementID
     * @param options
     * @param loader
     */
    window.Intelligence.googlecharts.drawCountryChart = function(data, start, end, elementID, printHandler, options, loader){
        $('#' + elementID).html(loader);
        var interval = end - start;
        options.region = 'world';
        WEv1api.setEndpoint("/intelligence/" + encodeURIComponent(data) + "/" + start + "/" + end + "/" + interval).get(function(response){
            if(response.intelligence != undefined){
                var gData = [];
                gData.push(['Country', 'Count']);
                $.each(response.intelligence, function(index, intel) {
                    if(intel.values != undefined) {
                        $.each(intel.values, function (index, value) {
                            var gEntry = [];
                            gEntry[0] = value.text;
                            gEntry[1] = parseInt(value.count);
                            gData.push(gEntry);
                        });
                    }
                });
                var data = google.visualization.arrayToDataTable(gData);
                var chart = new google.visualization.GeoMap(document.getElementById(elementID));
                google.visualization.events.addListener(chart, 'ready', function () {
                    printHandler(chart);
                });
                var paintGraph = function() {
                    chart.draw(data, options);
                }
                paintGraph();
                var timeout = null;
                $(window).resize(function(){
                    clearTimeout(timeout);
                    timeout = setTimeout(paintGraph, 200);
                });
            }
        });
    };

    /**
     * Draw google piechart
     * based on intelligence data
     *
     * @param data
     * @param start
     * @param end
     * @param elementID
     * @param options
     * @param loader
     */
    window.Intelligence.googlecharts.drawPieChart = function(data, start, end, elementID, printHandler, options, loader){
        $('#' + elementID).html(loader);
        var interval = end - start;
        WEv1api.setEndpoint("/intelligence/" + encodeURIComponent(data) + "/" + start + "/" + end + "/" + interval).get(function(response){
            if(response.intelligence != undefined){
                var gData = [];
                gData.push(['Type', 'Count']);
                $.each(response.intelligence, function(index, intel) {
                    if(intel.values != undefined) {
                        $.each(intel.values, function (index, value) {
                            var gEntry = [];
                            gEntry[0] = value.text;
                            gEntry[1] = parseInt(value.count);
                            gData.push(gEntry);
                        });
                    }
                });
                var data = google.visualization.arrayToDataTable(gData);
                var chart = new google.visualization.PieChart(document.getElementById(elementID));
                google.visualization.events.addListener(chart, 'ready', function () {
                    printHandler(chart);
                });
                var paintGraph = function() {
                    chart.draw(data, options);
                }
                paintGraph();
                var timeout = null;
                $(window).resize(function(){
                    clearTimeout(timeout);
                    timeout = setTimeout(paintGraph, 200);
                });
            }
        });
    };

    /**
     * Draw googlecharts linechart
     * based on intelligence data
     *
     * @param data
     * @param start
     * @param end
     * @param interval
     * @param elementID
     * @param options
     */
    window.Intelligence.googlecharts.drawLineChart = function(data, start, end, interval, elementID, printHandler, options, loader){
        $('#' + elementID).html(loader);
        WEv1api.setEndpoint("/intelligence/" + encodeURIComponent(data) + "/" + start + "/" + end + "/" + interval).get(function(response){
            if(response.intelligence != undefined){
                var DataTable = new google.visualization.DataTable();
                var headers = [];
                var entries = [];
                $.each(response.intelligence, function(index, intel){
                    var entry = { label : intel.niceStart + " " + intel.niceEnd };
                    if(intel.values != undefined){
                        $.each(intel.values, function(index, value){
                            var already_found = false;
                            for(h in headers){
                                if(headers[h] == value.text){ already_found = true; }
                            }
                            if(!already_found){ headers.push(value.text); }
                            if(typeof entry.values === 'undefined'){ entry.values = []; }
                            entry.values.push(value);
                        });
                    }
                    entries.push(entry);
                });
                DataTable.addColumn("string", "Date");
                $.each(headers, function(index, header){
                    DataTable.addColumn("number", header);
                });
                DataTable.addRows(entries.length);
                $.each(entries, function(index, entry){
                    DataTable.setValue(index, 0, entry.label);
                    $.each(headers, function(hindex, header){
                        var foundData = false;
                        if(entry.values !== undefined){ $.each(entry.values, function(index, value){ if(header == value.text){ foundData = value.count; } }); }
                        if(foundData !== false){ DataTable.setValue(index, 1 + hindex, foundData); }else{ DataTable.setValue(index, 1 + hindex, 0); }
                    });
                });
                var chart = new google.visualization.LineChart(document.getElementById(elementID));
                google.visualization.events.addListener(chart, 'ready', function () {
                    printHandler(chart);
                });
                var paintGraph = function() {
                    chart.draw(DataTable, options);
                }
                paintGraph();
                var timeout = null;
                $(window).resize(function(){
                    clearTimeout(timeout);
                    timeout = setTimeout(paintGraph, 200);
                });
            }
        });
    };

    /**
     * Assure namespace of correct type
     */
    if(typeof window.MapTable === 'undefined'){ window.MapTable = {}; }

    /**
     * Assure google charts namespace
     */
    if(typeof window.MapTable.googlecharts === 'undefined'){ window.MapTable.googlecharts = {}; }

    /**
     * Draw googlecharts linechart
     * based on MapTable data
     *
     * @param table
     * @param start
     * @param end
     * @param interval
     * @param elementID
     * @param options
     */
    window.MapTable.googlecharts.drawLineChart = function(table, start, end, interval, elementID, printHandler, options, loader){
        $('#' + elementID).html(loader);
        WEv1api.setEndpoint("/intelligence/records/" + encodeURIComponent(table) + "/" + start + "/" + end + "/" + interval).get(function(response){
            if(response.intelligence != undefined){
                var DataTable = new google.visualization.DataTable();
                DataTable.addColumn("string", "Date");
                DataTable.addColumn("number", "Count");
                DataTable.addRows(response.intelligence.length);
                $.each(response.intelligence, function(index, intel){
                    DataTable.setValue(index, 0, intel.niceStart + " " + intel.niceEnd);
                    DataTable.setValue(index, 1, intel.count);
                });
                var chart = new google.visualization.LineChart(document.getElementById(elementID));
                google.visualization.events.addListener(chart, 'ready', function () {
                    printHandler(chart);
                });
                var paintGraph = function(){
                    chart.draw(DataTable, options);
                }
                paintGraph();
                var timeout = null;
                $(window).resize(function(){
                    clearTimeout(timeout);
                    timeout = setTimeout(paintGraph, 200);
                });
            }
        });
    };

})(jQuery, window, document);