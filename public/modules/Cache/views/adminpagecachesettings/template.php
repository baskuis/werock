<?php

$view = '
<div class="content">
    <h1>{{title}}</h1>
    <p>{{description}}</p>
    ' . CoreTemplate::getView('formnotifications') .
        CoreForm::buildFormHeader('pagecachesettings') . '
    <table cellpadding="0" cellspacing="0" style="width: 595px;">
        <tbody>
            <tr>
                <td colspan="2">' . CoreForm::grabField('pagecachesettings', 'pagecacheenabled') . '</td>
            </tr>
            <tr>
                <td colspan="2">' . CoreForm::grabField('pagecachesettings', 'pagecacheduration') . '</td>
            </tr>
            <tr>
                <td colspan="2">' . CoreForm::grabField('pagecachesettings', 'cache_settings_submit') . '</td>
            </tr>
        </tbody>
    </table>' .
    CoreForm::buildFormFooter('pagecachesettings') . '
</div>';