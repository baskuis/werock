<?php

$view = '
<table>
    <tr>
      <td>
        <p>Hi {{name}},</p>
        <p>Thanks for registering to ' . SITE_NAME . '!</p>
        <h1>Friendly Reminder</h1>
        <p>To complete your registration please click on \'Confirm Email\'.</p>
        <h2>You\'ll need to activate your email to login in the future.</h2>
        <p>Please simply click on the button below to complete your registration.</p>
        <table class="btn-primary" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td bgcolor="#EB7035" style="padding: 12px 18px 12px 18px; -webkit-border-radius:3px; border-radius:3px" align="center">
              <a href="' . HTTP_PROTOCOL . DOMAIN_NAME . '/email/activate/{{key}}" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #ffffff; text-decoration: none; display: inline-block;">Confirm Email</a>
            </td>
          </tr>
        </table>
      </td>
    </tr>
</table>';