<?php

//view
$view =
CoreForm::buildFormHeader('writeMessage') . '
<table cellpadding="0" cellspacing="0" style="width: 595px;">
    <tbody>
        <tr>
            <td colspan="2">' . CoreForm::grabField('writeMessage', 'addressees') . '</td>
        </tr>
        <tr>
            <td colspan="2">' . CoreForm::grabField('writeMessage', 'message') . '</td>
        </tr>
        <tr>
            <td colspan="2">' . CoreForm::grabField('writeMessage', 'message_submit') . '</td>
        </tr>
    </tbody>
</table>' .
CoreForm::buildFormFooter('writeMessage');