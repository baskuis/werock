<?php

$view = '
<div class="content experiments">
    <h1 class="page-header">{{title}}</h1>
    <p class="lead">{{description}}</p>
    {{#experiments}}
    <div class="experiment">
        <h2><i class="fa fa-random"></i> {{name}} <a href="/admin/tools/experiments?primary_value={{id}}&action=edit" target="_blank"><i class="fa fa-gears"></i></a></h2>
        <p>{{description}}</p>
        {{#ExperimentationSummaryObject}}
        <div class="meta">
            <span class="exposures"><strong>{{exposures}}</strong> Exposures</span>
            <span class="conversions"><strong>{{conversions}}</strong> Conversions</span>
        </div>
        {{/ExperimentationSummaryObject}}
        <div class="variants">
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th></th>
                        <th>Variant</th>
                        <th>Template</th>
                        <th>Count</th>
                        <th>Pass</th>
                        <th>Rate</th>
                    </tr>
                </thead>
                <tbody>
                    {{#variants}}
                    <tr class="variant">
                        <td>
                            {{#base}}<i class="fa fa-bullseye"></i>{{/base}}
                            {{#selected}}<i class="fa fa-check"></i>{{/selected}}
                        </td>
                        <td><strong><i class="fa fa-puzzle-piece"></i> {{name}}</strong></td>
                        <td>{{template}}</td>
                        {{#ExperimentationVariantEntrySummaryObject}}
                        <td>{{exposures}}</td>
                        <td>{{conversions}}</td>
                        <td>
                            <span class="lowerBound">{{lowerBoundPercentage}}%</span>
                            <span class="conversionRate">{{conversionRatePercentage}}%</span>
                            <span class="upperBound">{{upperBoundPercentage}}%</span>
                        </td>
                        {{/ExperimentationVariantEntrySummaryObject}}
                    </tr>
                    {{/variants}}
                </tbody>
            </table>
            <div class="conclusive">
            {{#ExperimentationSummaryObject}}
                {{#conclusive}}
                    <i class="fa fa-check"></i> Conclusive
                {{/conclusive}}
                {{^conclusive}}
                    <i class="fa fa-cog fa-spin"></i> Currently Running
                {{/conclusive}}
            {{/ExperimentationSummaryObject}}
            </div>
        </div>
    </div>
    {{/experiments}}
</div>
';