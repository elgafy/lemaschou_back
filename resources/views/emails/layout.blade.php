{{-- 
    Base email layout — Le Maschou
    Usage: @extends('emails.layout') @section('content') ... @endsection
--}}
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="x-apple-disable-message-reformatting" />
    <title>{{ $subject ?? 'Le Maschou' }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:AllowPNG/>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style type="text/css">
        /* Reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; height: 100% !important; }
        a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }
        /* Responsive */
        @media only screen and (max-width: 620px) {
            .email-container { width: 100% !important; max-width: 100% !important; }
            .fluid { max-width: 100% !important; height: auto !important; }
            .stack-column { display: block !important; width: 100% !important; }
            .mobile-padding { padding-left: 20px !important; padding-right: 20px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #E9DED7; font-family: Arial, Helvetica, sans-serif;">

    {{-- Preheader text (hidden) --}}
    <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all;">
        {{ $preheader ?? '' }}
    </div>

    {{-- Full-width wrapper --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #E9DED7;">
        <tr>
            <td align="center" style="padding: 20px 10px;">

                {{-- Header --}}
                @include('emails.partials.header')

                {{-- Content area --}}
                <table width="600" cellpadding="0" cellspacing="0" border="0" class="email-container" style="max-width: 600px; width: 100%; background-color: #EFE8E1;">
                    <tr>
                        <td style="padding: 40px 40px 30px 40px;" class="mobile-padding">
                            @yield('content')
                        </td>
                    </tr>
                </table>

                {{-- Footer --}}
                @include('emails.partials.footer')

            </td>
        </tr>
    </table>

</body>
</html>
