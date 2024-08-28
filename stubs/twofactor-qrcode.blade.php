<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #000;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="height: 100vh;">
    <tr>
      <td align="center">
          <table>
              <tr>
                  <td>
                      <img src="{{ config('general.cdn_url') . '/logo/logo-fortune-white.png' }}" alt="Logo" style="width: 150px; margin-bottom: 20px;" />
                  </td>
              </tr>
          </table>
        <table width="250" border="0" cellspacing="0" cellpadding="0" align="center" style="background-color: #fff; border-radius: 0px;">
          <tr>
            <td align="center">
               <img src="{{ config('general.cdn_url') . $targetFolder }}" alt="" style="width: 250px;">
              <a href="{{ route('two-factor.verify') }}" class="button" style="background-color: #000000; color: white; padding: 14px 32px; text-decoration: none; display: inline-block; font-size: 16px; margin-top: 10px; cursor: pointer; font-family: 'Trebuchet MS', sans-serif; margin: 20px;">
          VERIFY NOW
          </a>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>