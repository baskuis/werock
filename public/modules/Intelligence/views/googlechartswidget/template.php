<?php

$view = <<<EOF
    <div class="widget" id="widget_{{uniqueID}}">

        <div class="print_canvas">
            <div class="graph_image" style="width: 100%;"></div>
            <h3 class="range" style="text-align: center;"></h3>
        </div>

        <div class="header">
            {{#canEdit}}<i class="fa fa-cog options" title="Settings"></i>{{/canEdit}}
            <i class="fa fa-print print_out" title="Print"></i>
            <span class="text">{{label}}</span>
        </div>

        {{#canEdit}}
        <div class="controls">

            <input type="text" name="start" class="widget_control_start" style="display: none;" />
            <input type="text" name="end" class="widget_control_end" style="display: none;" />

            <div class="range_slider">
                <div id="slider_{{uniqueID}}"></div>
            </div>

            <div class="interval">
                Interval:
                <select name="interval" class="widget_control_interval">
                    <option value="3600">hour</option>
                    <option value="86400">day</option>
                    <option value="604800">week</option>
                    <option value="2592000">month</option>
                </select>
            </div>

            <div class="range_description">
                <!-- description loads here -->
            </div>

        </div>
        {{/canEdit}}

        <div id="this_chart_canvas_{{uniqueID}}" class="gchart" style="height: {{height}};">
            <!-- google chart loads here -->
        </div>
    </div>
EOF;

$script = <<<EOF
    (function($, window, document, undefined){

        var printChart = function(chart){
            $('.print_canvas .graph_image', '#widget_{{uniqueID}}').html('<img src="' + chart.getImageURI() + '" />');
        }

        $().ready(function(){

            var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];

            var maxInterval = 240;

            $('.header .options', '#widget_{{uniqueID}}').click(function(){
                $('.controls', '#widget_{{uniqueID}}').toggle();
            });

            var options = {
                width: "{{#width}}{{width}}{{/width}}{{^width}}100%{{/width}}",
                height: "{{#height}}{{height}}{{/height}}{{^height}}220px{{/height}}",
                backgroundColor: "#eeeeee",
                vAxis: {
                    logScale: true
                }
            };

            var data = '{{key}}';
            var start = parseInt('{{start}}');
            var end = parseInt('{{end}}');
            {{#interval}}
            var interval = parseInt('{{interval}}');
            {{/interval}}
            {{^interval}}
            var interval = 24 * 3600 + 1;
            {{/interval}}

            $('.widget_control_start', '#widget_{{uniqueID}}').val(start);
            $('.widget_control_end', '#widget_{{uniqueID}}').val(end);
            $('.widget_control_interval option', '#widget_{{uniqueID}}').each(function(){
                $(this).attr('selected', (interval == $(this).val()));
            });

            $('.controls input, .controls select', '#widget_{{uniqueID}}').change(function(){

                start = $('.widget_control_start', '#widget_{{uniqueID}}').val();
                end = $('.widget_control_end', '#widget_{{uniqueID}}').val();
                interval = $('.widget_control_interval option:selected', '#widget_{{uniqueID}}').val();

                drawSlider();
                updateDescription(false);
                startDrawingChart();

            });

            var startDrawingChart = function(){

                {{#isLineChart}}
                window.Intelligence.googlecharts.drawLineChart(data, start, end, interval, 'this_chart_canvas_{{uniqueID}}', printChart, options, WRTemplates.widgetloading.template);
                {{/isLineChart}}

                {{#isMapTable}}
                window.MapTable.googlecharts.drawLineChart(data, start, end, interval, 'this_chart_canvas_{{uniqueID}}', printChart, options, WRTemplates.widgetloading.template);
                {{/isMapTable}}

                {{#isPieChart}}
                window.Intelligence.googlecharts.drawPieChart(data, start, end, 'this_chart_canvas_{{uniqueID}}', printChart, options, WRTemplates.widgetloading.template);
                {{/isPieChart}}

                {{#isCountryChart}}
                window.Intelligence.googlecharts.drawCountryChart(data, start, end, 'this_chart_canvas_{{uniqueID}}', printChart, options, WRTemplates.widgetloading.template);
                {{/isCountryChart}}

                {{#isRegionChart}}
                window.Intelligence.googlecharts.drawRegionChart(data, start, end, 'this_chart_canvas_{{uniqueID}}', printChart, options, WRTemplates.widgetloading.template);
                {{/isRegionChart}}

                {{#isCityChart}}
                window.Intelligence.googlecharts.drawCityChart(data, start, end, 'this_chart_canvas_{{uniqueID}}', printChart, options, WRTemplates.widgetloading.template);
                {{/isCityChart}}

            }

            startDrawingChart();

            var drawSlider = function(){

                var max_ts = Math.floor(new Date().getTime() / 1000);
                var min_ts = max_ts - (maxInterval * interval);

                if(start < min_ts){
                    start = min_ts;
                }

                if(start > end){
                    end = start + (2 * interval);
                }

                $('#slider_{{uniqueID}}').slider({
                    range: true,
                    min: parseInt(min_ts),
                    max: parseInt(max_ts),
                    step: parseInt(interval),
                    values: [start, end],
                    stop: function(event, ui){
                        $('.widget_control_start', '#widget_{{uniqueID}}').val(ui.values[0]);
                        $('.widget_control_end', '#widget_{{uniqueID}}').val(ui.values[1]);
                        $('.widget_control_start', '#widget_{{uniqueID}}').trigger('change');
                    },
                    slide: function(event, ui){
                        start = ui.values[0];
                        end = ui.values[1];
                        updateDescription(true);
                    }
                });

            }

            drawSlider();

            var updateDescription = function(range_only){

                var startDate = new Date(start * 1000);
                var endDate = new Date(end * 1000);
                var intervalText = '';
                var rangeText = '';
                var alternativeInterval = false;

                switch(parseInt(interval)){
                    case 3600:
                        intervalText = 'hour';
                    break;
                    case 86400:
                        intervalText = 'day';
                    break;
                    case 604800:
                        intervalText = 'week';
                    break;
                    case 2592000:
                        intervalText = 'month';
                    break;
                    default:
                        intervalText = interval;
                        alternativeInterval = true;
                    break;
                }

                if(!alternativeInterval){
                    if(interval < 86400){
                        rangeText = '{{label}} every ' + intervalText + ' from ' + months[startDate.getMonth()] + ' ' + startDate.getDate() + ' ' + startDate.getFullYear() + ' ' + startDate.getHours() + ':' + startDate.getMinutes() + ' to ' + months[endDate.getMonth()] + ' ' + endDate.getDate() + ' ' + endDate.getFullYear() + ' ' + endDate.getHours() + ':' + endDate.getMinutes();
                    }else{
                        rangeText = '{{label}} every ' + intervalText + ' from ' + months[startDate.getMonth()] + ' ' + startDate.getDate() + ' ' + startDate.getFullYear() + ' to '  + months[endDate.getMonth()] + ' ' + endDate.getDate() + ' ' + endDate.getFullYear();
                    }
                }else{
                    rangeText = '{{label}} from ' + months[startDate.getMonth()] + ' ' + startDate.getDate() + ' ' + startDate.getFullYear() + ' to '  + months[endDate.getMonth()] + ' ' + endDate.getDate() + ' ' + endDate.getFullYear();
                }

                $('.range_description', '#widget_{{uniqueID}}').text(rangeText);
                $('.print_canvas .range', '#widget_{{uniqueID}}').text(rangeText);
                if(!range_only) $('.header .text', '#widget_{{uniqueID}}').text(rangeText);

            }

            updateDescription(false);

            $('.print_out', '#widget_{{uniqueID}}').click(function(e){
                e.preventDefault();
                var w = window.open();
                w.document.write('<html><head><title>Graph</title></head><body>' + $('.print_canvas', '#widget_{{uniqueID}}').html() + '</body></html>');
                w.window.print();
                w.document.close();
                return false;
            });

        });

    })(jQuery, window, document);
EOF;

