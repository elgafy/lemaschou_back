@php
    $contactEmail = \App\Models\Setting::where('key', 'email')->first()?->value ?? '';
    $contactPhone = \App\Models\Setting::where('key', 'phone')->first()?->value ?? '';
    $contactAddress = \App\Models\Setting::where('key', 'address_en')->first()?->value ?? '';
@endphp
{{-- Email Footer — Le Maschou contact & social --}}
                {{-- Footer section --}}
                <tr>
                    <td style="background-color: #422a2c; padding: 40px 40px 30px 40px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            {{-- Restaurant name --}}
                            <tr>
                                <td align="center" style="padding-bottom: 25px;">
                                    <h2 style="margin: 0; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 28px; font-weight: normal; color: #E9DED7; letter-spacing: 3px; text-transform: uppercase;">
                                        Le Maschou
                                    </h2>
                                    <p style="margin: 8px 0 0 0; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #E5CBBD; letter-spacing: 2px; text-transform: uppercase;">
                                        French Mediterranean Cuisine
                                    </p>
                                </td>
                            </tr>

                            {{-- Divider --}}
                            <tr>
                                <td style="padding-bottom: 25px;">
                                    <table width="60" cellpadding="0" cellspacing="0" border="0" align="center">
                                        <tr>
                                            <td style="border-bottom: 1px solid #E5CBBD; font-size: 0; line-height: 0;">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            {{-- Contact info --}}
                            <tr>
                                <td align="center" style="padding-bottom: 20px;">
                                    <p style="margin: 0 0 10px 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #E5CBBD; line-height: 1.6;">
                                        <span style="font-weight: bold; color: #E9DED7;">Email</span><br/>
                                        <a href="mailto:{{ $contactEmail }}" style="color: #E5CBBD; text-decoration: none;">{{ $contactEmail }}</a>
                                    </p>
                                    <p style="margin: 0 0 10px 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #E5CBBD; line-height: 1.6;">
                                        <span style="font-weight: bold; color: #E9DED7;">Phone</span><br/>
                                        <a href="tel:{{ $contactPhone }}" style="color: #E5CBBD; text-decoration: none;">{{ $contactPhone }}</a>
                                    </p>
                                    <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #E5CBBD; line-height: 1.6;">
                                        <span style="font-weight: bold; color: #E9DED7;">Address</span><br/>
                                        {{ $contactAddress }}
                                    </p>
                                </td>
                            </tr>

                            {{-- Website link --}}
                            <tr>
                                <td align="center" style="padding-bottom: 25px;">
                                    <a href="https://lemaschou.gafystudio.com/en" target="_blank" style="display: inline-block; padding: 10px 30px; border: 1px solid #E5CBBD; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #E9DED7; text-decoration: none; letter-spacing: 2px; text-transform: uppercase;">
                                        Visit Our Website
                                    </a>
                                </td>
                            </tr>

                            {{-- Divider --}}
                            <tr>
                                <td style="padding-bottom: 20px;">
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td style="border-bottom: 1px solid rgba(229,203,189,0.3); font-size: 0; line-height: 0;">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            {{-- Copyright --}}
                            <tr>
                                <td align="center">
                                    <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: rgba(229,203,189,0.6); line-height: 1.5;">
                                        &copy; {{ date('Y') }} Le Maschou. All rights reserved.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Bottom accent line --}}
                <tr>
                    <td style="background-color: #E5CBBD; height: 3px; font-size: 0; line-height: 0;">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
