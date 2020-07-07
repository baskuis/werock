<?php

$view = '
<table>
    <tr>
      <td>
        <p>Hi {{name}},</p>
        <p>Thanks for confirming your email.</p>
        <h1>We Know That Logging In Is A Drag</h1>
        <h2>Any time you want to access your account please just click the link below, from any device, and we will be logged in auto-magically.</h2>
        <p>Please simply use the link below to be logged in to your account with ' . SITE_NAME . '.</p>
        <table class="btn-primary" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td bgcolor="#EB7035" style="padding: 12px 18px 12px 18px; -webkit-border-radius:3px; border-radius:3px" align="center">
              <a href="' . HTTP_PROTOCOL . DOMAIN_NAME . '/do/access/{{accessToken}}" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #ffffff; text-decoration: none; display: inline-block;">Log Me In</a>
            </td>
          </tr>
        </table>
        <p>
            <em>If the above button is not visible please follow the following url:<br />
            <a href="' . HTTP_PROTOCOL . DOMAIN_NAME . '/do/access/{{accessToken}}" target="_blank">
                ' . HTTP_PROTOCOL . DOMAIN_NAME . '/do/access/{{accessToken}}
            </a>
            </em>
        </p>
        <p>
            <strong>You might want to save this email, since it provides access to your account.</strong>
        </p>
      </td>
    </tr>
</table>';