<?php

class EmailTemplates
{

   


    public function CustomerEmail($pretext, $headlinetext, $emailmessage, $buttonUtm)
    {
        //var_dump($cartarray);exit();
        setlocale(LC_MONETARY, 'en_US.UTF-8');
        $bodyhtml = "<!DOCTYPE html>
<html lang='en' xmlns='http://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>

    <head>
    <!-- The title tag shows in email notifications, like Android 4.4. -->

    <title>P Giving Cards</title><meta charset='utf-8'> <!-- utf-8 works for most cases -->
    <meta name='viewport' content='width=device-width'> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv='X-UA-Compatible' content='IE=edge'> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name='x-apple-disable-message-reformatting'> <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <meta name='format-detection' content='telephone=no,address=no,email=no,date=no,url=no'>
    
    <!-- Tell iOS not to automatically link certain text strings. -->
    <!-- Web Font / @font-face : BEGIN -->
    <!-- NOTE: If web fonts are not required, lines 10 - 27 can be safely removed. -->
    <!-- Desktop Outlook chokes on web font references and defaults to Times New Roman, so we force a safe fallback font. -->
    
    <!--[if mso]>
    <style>* {font-family: sans-serif !important;}
    </style>
    <![endif]-->
    
    <!-- All other clients get the webfont reference; some will render the font and others will silently fail to the fallbacks. More on that here: http://stylecampaign.com/blog/2015/02/webfont-support-in-email/ -->
    
    <!--[if !mso]><!-->
    <!-- insert web font reference, eg: <link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'> -->
    <!--<![endif]-->
    
    <!-- Web Font / @font-face : END -->
    
    <!-- CSS Reset : BEGIN -->
    
    <style>
    /* What it does: Remove spaces around the email design added by some email clients. */
    /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
        margin: 0 !important;
        padding: 0 !important;
        height: 100% !important;
        width: 100% !important;
        }
    
    /* What it does: Stops email clients resizing small text. */
        * {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
        }
    
    /* What it does: Centers email on Android 4.4 */
        div[style*='margin: 16px 0'] {
        margin: 0 !important;
        }
    
    /* What it does: Stops Outlook from adding extra spacing to tables. */
        table,
        td {
        mso-table-lspace: 0pt !important;
        mso-table-rspace: 0pt !important;
        }
    
    /* What it does: Fixes webkit padding issue. */
        table {
        border-spacing: 0 !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
        margin: 0 auto !important;
        }
    
    /* What it does: Uses a better rendering method when resizing images in IE. */
    img {
    -ms-interpolation-mode: bicubic;
    }
    
    /* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
        a {
        text-decoration: none;
        }
    
    /* What it does: A work-around for email clients meddling in triggered links. */
        a[x-apple-data-detectors],
        /* iOS */
        .unstyle-auto-detected-links a,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
    
    /* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
        .a6S {
            display: none !important;
            opacity: 0.01 !important;
        }
    
    /* What it does: Prevents Gmail from changing the text color in conversation threads. */
        .im {
            color: inherit !important;
        }
    
    /* If the above doesn't work, add a .g-img class to any image in question. */
        img.g-img+div {
            display: none !important;
        }
    
    /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
    /* Create one of these media queries for each additional viewport size you'd like to fix */
    
        /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
            @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
                u~div .email-container {
                    min-width: 320px !important;
                }
                
                .item-name,
                .item-price {
                font-size: 14px !important;
                }
                
                .button-a {
                    text-align: center;
                }
            }
            
        /* iPhone 6, 6S, 7, 8, and X */
            @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
                u~div .email-container {
                    min-width: 375px !important;
                }
            }
            
        /* iPhone 6+, 7+, and 8+ */
            @media only screen and (min-device-width: 414px) {
                u~div .email-container {
                    min-width: 414px !important;
                }
            }
    </style>

    <!-- What it does: Makes background images in 72ppi Outlook render at correct size. -->
    
    <!--[if gte mso 9]>
    <style type='text/css'>
        table {border-collapse: collapse; border-spacing: 0; }
    </style>
    <xml>
        <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    
    <!-- CSS Reset : END -->
    
    <!-- Progressive Enhancements : BEGIN -->
    
    <style>
    /* What it does: Hover styles for buttons */
        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }
        .button-td-primary:hover,
        .button-a-primary:hover {
            background: #109fec !important;
            border-color: #109fec !important;
            color: #ffffff !important;
        }
        .button-td-secondary:hover,
        .button-a-secondary:hover {
            background: #134c62 !important;
            border-color: #134c62 !important;
            color: #ffffff !important;
        }
        .button-td-card:hover,
        .button-a-card:hover {
            background: #ffffff !important;
            border-color: #ffffff !important;
            color: #714e61 !important;
        }
        
    /* Media Queries */
        @media screen and (max-width: 600px) {
        /* What it does: Adjust typography on small screens to improve readability */
            .email-container p {
                font-size: 17px !important;
            }
        }
    </style>

    <!-- Progressive Enhancements : END -->
    
</head>
    
    <!-- The email background color (#fff) is defined in three places:
        1. body tag: for most email clients
        2. center tag: for Gmail and Inbox mobile apps and web versions of Gmail, GSuite, Inbox, Yahoo, AOL, Libero, Comcast, freenet, Mail.ru, Orange.fr
        3. mso conditional: For Windows 10 Mail -->
    
    <body width='100%' style='margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #fff;'>
    <center style='width: 100%; background-color: #fff;'>
        
        <!--[if mso | IE]>
        <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color: #fff;'>
            <tr>
                <td>
        <![endif]--> 
        
        <!-- ==================================== -->
        <!-- VISUALLY HIDDEN PREHEADER TEXT -->
        <!-- ==================================== -->
        
        <div style='display: none; font-size: 1px; line-height: normal; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: Verdana, sans-serif;'>


        " . $pretext . "

       
        </div>
        
        <!-- Visually Hidden Preheader Text : END -->
        
        <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
        
        <!-- Preview Text Spacing Hack : BEGIN -->
        
        <div style='display: none; font-size: 1px; line-height: auto; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: Verdana, sans-serif;'>Give the gift of love</div>
<span style='display:none !important;visibility:hidden;mso-hide:all;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;'>
<!--EOA COMMENT: This snippet of white space has been added to ensure short preview text does not run into the following text of your email.-->
&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;</span>
<span style='display:none !important;visibility:hidden;mso-hide:all;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;'>
<!--EOA COMMENT: This snippet of white space has been added to ensure short preview text does not run into the following text of your email.-->
&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;</span>
        
        <!-- Preview Text Spacing Hack : END --> 
        
        <!-- Set the email width. Defined in two places:
            1. max-width for all clients except Desktop Windows Outlook, allowing the email to squish on narrow but never go wider than 600px.
            2. MSO tags for Desktop Windows Outlook enforce a 600px width. -->
        
        <div style='max-width: 600px; margin: 0 auto;' class='email-container'>
        
        <!--[if mso]>
            <table align='center' role='presentation' cellspacing='0' cellpadding='0' border='0' width='600'>
            <tr>
            <td>
            <![endif]-->
    
    <!-- Email Body : BEGIN -->
    
    <table align='center' role='presentation' cellspacing='0' cellpadding='0' border='0' style='margin: auto; background: #f6f9fa; width: 100%;'>
        <tbody> 
                        
            <!-- Email Header : BEGIN -->
            
            <tr>
                <td style='padding: 20px 0; text-align: center; background: #134c62; font-size: 0px;' align='center' valign='top'>
                
                <a href='https:/https://parkavenueconciergemedicine.com/'>
                    <img src='https://parkavenueconciergemedicine.com/wp-content/uploads/2021/05/pacmny-text-logo-white3.png' width='193' height='' alt='Park Avenue Concierge Medicine' border='0' style='height: auto; background: #134c62; font-family: Verdana, sans-serif; font-size: 15px; line-height: auto; color: #ffffff; font-weight: bold; margin: 0px;'>
                </a>
    
                </td>
            </tr>
            
            <!-- ==================================== -->
            <!-- COLOR BAR VISUAL ELEMENT -->
            <!-- ==================================== -->
            
            <!-- <tr>
                <td valign='top' align='center'>
                <img src='http://pcusa.img-us3.com/andrew.miller/colorbar.jpg' alt='null' width='600' height='8' border='0' style='width: 100%; max-width: 600px; height: auto; display:block;'>
                </td>
            </tr> -->
            
            <!-- Email Header : END -->
            
            <!-- FEATURED CONTENT : BEGIN -->
            
            <!-- Hero Image, Flush : BEGIN -->
            
            <tr>
                
                <!-- FEATURED CONTENT: IMAGE -->
                
                <td style='background-color: #f6f9fa;'>
               
            
                
                <img src='https://parkavenueconciergemedicine.com/wp-content/uploads/2021/02/home-slide1.jpg' width='600' border='0' style='width: 100%; max-width: 600px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555; margin: auto; display: block;' class='g-img' alt='PGC_ Digital Giving Card Experience_ Email Header Image-small.jpg'>
                
            
              
                </td>
                
            </tr>
            
            <!-- Hero Image, Flush : END -->
            
            <!-- 1 Column Text + Button : BEGIN -->
            
            <tr>
                <td style='background-color: #f6f9fa;' align='center' valign='top'>
                    <table role='presentation' cellspacing='0' cellpadding='0' border='0' style='width: 83%;'>
                        <tbody>
                            <tr>
                                
                                <!-- FEATURED CONTENT: HEADER TEXT -->
                                
                                <td style='padding: 20px; font-family: Verdana, sans-serif; font-size: 14px; line-height: auto; color: #134c62; text-align: center;'>
                                <h1 style='margin: 0 0 14px 0; font-family: Verdana, sans-serif; font-size: 30px; line-height: auto; color: #134c62; font-weight: bold;'>
                               

                " . $headlinetext . "


                                </h1>
                                
                                <!-- FEATURED CONTENT: BODY TEXT 1 -->
                                
                                <p style='text-align: center; padding-top: 20px'>
                                <span id='docs-internal-guid-50dbff47-7fff-7a8e-16da-136bafa2168f'>
                                

                " . $emailmessage . "


                                </span>
                                </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            
            <!-- 1 Column Text + Button : END -->
            
            <!-- FEATURED CONTENT : END -->          
            
            <!-- 1 Column Text + Button : END -->
            
            <!-- Social Icons -->
            
            
            <!-- End Social Icons -->
            
        </tbody>
    </table>
    
    <!-- Email Body : END -->
    
    <!-- Email Footer : BEGIN -->
    
        
    <table align='center' role='presentation' cellspacing='0' cellpadding='0' border='0' style='margin: auto; background: #484e52; width: 100%;'>
        <tbody>
            <tr>
                <td style='padding: 20px; font-family: Verdana, sans-serif; font-size: 12px; line-height: auto; text-align: center; color: #c0c0c0;'>
                
                <!-- ==================================== -->
                <!-- STANDARD EMAIL FOOTER : BEGIN -->
                <!-- ==================================== -->
                
                <p style='font-family: Verdana, sans-serif; padding-bottom: 14px; font-size: 12px; line-height: auto; text-align: center; color: #c0c0c0;'>
                You are receiving this email because you are a patient of PACM and has given consent that PACM can Communicate with you via email
                 You will only receive further communications and/or marketing emails if you are subscribed to our email list(s).
                </p>

                
                <p style='font-family: Verdana, sans-serif; font-size: 12px; line-height: auto; text-align: center; color: #888888;'>
                Park Avenue Concierge Medicine (PACMNY)
                <br>
                127 East 61st Street Ground Floor
                <br>
                New York, NY 10065
                <br>
                United States
                </p>
                
                
                </td>
            </tr>
        </tbody>
    </table>
    
    <!-- Email Footer : END -->

    <!--[if mso]>
            </td>
        </tr>
    </table>
    <![endif]-->
    
    </div>
    
    <!--[if mso | IE]>
            </td>
        </tr>
    </table>
    <![endif]-->
    
    </center>
    </body>
</html>";
        return $bodyhtml;
    }

    public function DonarEmail($pretext, $headlinetext, $emailmessage)
    {
        //var_dump($cartarray);exit();
        setlocale(LC_MONETARY, 'en_US.UTF-8');
        $bodyhtml = "<html lang='en' xmlns='http://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>

    <head>
    <!-- The title tag shows in email notifications, like Android 4.4. -->

    <title>P Giving Cards</title><meta charset='utf-8'> <!-- utf-8 works for most cases -->
    <meta name='viewport' content='width=device-width'> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv='X-UA-Compatible' content='IE=edge'> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name='x-apple-disable-message-reformatting'> <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <meta name='format-detection' content='telephone=no,address=no,email=no,date=no,url=no'>
    
    <!-- Tell iOS not to automatically link certain text strings. -->
    <!-- Web Font / @font-face : BEGIN -->
    <!-- NOTE: If web fonts are not required, lines 10 - 27 can be safely removed. -->
    <!-- Desktop Outlook chokes on web font references and defaults to Times New Roman, so we force a safe fallback font. -->
    
    <!--[if mso]>
    <style>* {font-family: sans-serif !important;}
    </style>
    <![endif]-->
    
    <!-- All other clients get the webfont reference; some will render the font and others will silently fail to the fallbacks. More on that here: http://stylecampaign.com/blog/2015/02/webfont-support-in-email/ -->
    
    <!--[if !mso]><!-->
    <!-- insert web font reference, eg: <link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'> -->
    <!--<![endif]-->
    
    <!-- Web Font / @font-face : END -->
    
    <!-- CSS Reset : BEGIN -->
    
    <style>
    /* What it does: Remove spaces around the email design added by some email clients. */
    /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
        margin: 0 !important;
        padding: 0 !important;
        height: 100% !important;
        width: 100% !important;
        }
    
    /* What it does: Stops email clients resizing small text. */
        * {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
        }
    
    /* What it does: Centers email on Android 4.4 */
        div[style*='margin: 16px 0'] {
        margin: 0 !important;
        }
    
    /* What it does: Stops Outlook from adding extra spacing to tables. */
        table,
        td {
        mso-table-lspace: 0pt !important;
        mso-table-rspace: 0pt !important;
        }
    
    /* What it does: Fixes webkit padding issue. */
        table {
        border-spacing: 0 !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
        margin: 0 auto !important;
        }
    
    /* What it does: Uses a better rendering method when resizing images in IE. */
    img {
    -ms-interpolation-mode: bicubic;
    }
    
    /* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
        a {
        text-decoration: none;
        }
    
    /* What it does: A work-around for email clients meddling in triggered links. */
        a[x-apple-data-detectors],
        /* iOS */
        .unstyle-auto-detected-links a,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
    
    /* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
        .a6S {
            display: none !important;
            opacity: 0.01 !important;
        }
    
    /* What it does: Prevents Gmail from changing the text color in conversation threads. */
        .im {
            color: inherit !important;
        }
    
    /* If the above doesn't work, add a .g-img class to any image in question. */
        img.g-img+div {
            display: none !important;
        }
    
    /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
    /* Create one of these media queries for each additional viewport size you'd like to fix */
    
        /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
            @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
                u~div .email-container {
                    min-width: 320px !important;
                }
                
                .item-name,
                .item-price {
                font-size: 14px !important;
                }
                
                .button-a {
                    text-align: center;
                }
            }
            
        /* iPhone 6, 6S, 7, 8, and X */
            @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
                u~div .email-container {
                    min-width: 375px !important;
                }
            }
            
        /* iPhone 6+, 7+, and 8+ */
            @media only screen and (min-device-width: 414px) {
                u~div .email-container {
                    min-width: 414px !important;
                }
            }
    </style>

    <!-- What it does: Makes background images in 72ppi Outlook render at correct size. -->
    
    <!--[if gte mso 9]>
    <style type='text/css'>
        table {border-collapse: collapse; border-spacing: 0; }
    </style>
    <xml>
        <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    
    <!-- CSS Reset : END -->
    
    <!-- Progressive Enhancements : BEGIN -->
    
    <style>
    /* What it does: Hover styles for buttons */
        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }
        .button-td-primary:hover,
        .button-a-primary:hover {
            background: #714e61 !important;
            border-color: #714e61 !important;
            color: #ffffff !important;
        }
        .button-td-secondary:hover,
        .button-a-secondary:hover {
            background: #134c62 !important;
            border-color: #134c62 !important;
            color: #ffffff !important;
        }
        .button-td-card:hover,
        .button-a-card:hover {
            background: #ffffff !important;
            border-color: #ffffff !important;
            color: #714e61 !important;
        }
        
    /* Media Queries */
        @media screen and (max-width: 600px) {
        /* What it does: Adjust typography on small screens to improve readability */
            .email-container p {
                font-size: 17px !important;
            }
        }
    </style>

    <!-- Progressive Enhancements : END -->
    
</head>
    
    <!-- The email background color (#fff) is defined in three places:
        1. body tag: for most email clients
        2. center tag: for Gmail and Inbox mobile apps and web versions of Gmail, GSuite, Inbox, Yahoo, AOL, Libero, Comcast, freenet, Mail.ru, Orange.fr
        3. mso conditional: For Windows 10 Mail -->
    
    <body width='100%' style='margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #fff;'>
    <center style='width: 100%; background-color: #fff;'>
        
        <!--[if mso | IE]>
        <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color: #fff;'>
            <tr>
                <td>
        <![endif]--> 
        
        <!-- ==================================== -->
        <!-- VISUALLY HIDDEN PREHEADER TEXT -->
        <!-- ==================================== -->
        
        <div style='display: none; font-size: 1px; line-height: normal; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: Verdana, sans-serif;'>


    " . $pretext . "


        </div>
        
        <!-- Visually Hidden Preheader Text : END -->
        
        <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
        
        <!-- Preview Text Spacing Hack : BEGIN -->
        
        <div style='display: none; font-size: 1px; line-height: auto; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: Verdana, sans-serif;'>Give the gift of love</div>
<span style='display:none !important;visibility:hidden;mso-hide:all;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;'>
<!--EOA COMMENT: This snippet of white space has been added to ensure short preview text does not run into the following text of your email.-->
&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;</span>
<span style='display:none !important;visibility:hidden;mso-hide:all;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;'>
<!--EOA COMMENT: This snippet of white space has been added to ensure short preview text does not run into the following text of your email.-->
&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;</span>
        
        <!-- Preview Text Spacing Hack : END --> 
        
        <!-- Set the email width. Defined in two places:
            1. max-width for all clients except Desktop Windows Outlook, allowing the email to squish on narrow but never go wider than 600px.
            2. MSO tags for Desktop Windows Outlook enforce a 600px width. -->
        
        <div style='max-width: 600px; margin: 0 auto;' class='email-container'>
        
        <!--[if mso]>
            <table align='center' role='presentation' cellspacing='0' cellpadding='0' border='0' width='600'>
            <tr>
            <td>
            <![endif]-->
    
    <!-- Email Body : BEGIN -->
    
    <table align='center' role='presentation' cellspacing='0' cellpadding='0' border='0' style='margin: auto; background: #f6f9fa; width: 100%;'>
        <tbody> 
                        
            <!-- Email Header : BEGIN -->
            
            <tr>
                <td style='padding: 20px 0; text-align: center; background: #134c62; font-size: 0px;' align='center' valign='top'>
                
                <a href='https://presbyteriangifts.pcusa.org/'>
                
                    <img src='http://pcusa.img-us3.com/andrew.miller/logo.png' width='193' height='' alt='Park Avenue Concierge Medicine' border='0' style='height: auto; background: #134c62; font-family: Verdana, sans-serif; font-size: 15px; line-height: auto; color: #ffffff; font-weight: bold; margin: 0px;'>
                
                </a>
                
                </td>
            </tr>
            
            <!-- ==================================== -->
            <!-- COLOR BAR VISUAL ELEMENT -->
            <!-- ==================================== -->
            
            <!-- <tr>
                <td valign='top' align='center'>
                <img src='http://pcusa.img-us3.com/andrew.miller/colorbar.jpg' alt='null' width='600' height='8' border='0' style='width: 100%; max-width: 600px; height: auto; display:block;'>
                </td>
            </tr> -->
            
            <!-- Email Header : END -->
            
            <!-- FEATURED CONTENT : BEGIN -->
            
            <!-- Hero Image, Flush : BEGIN -->
            
            <tr>
                
                <!-- FEATURED CONTENT: IMAGE -->
                
                <td style='background-color: #f6f9fa;'>
               
            
                
                <img src='https://content.app-us1.com/W2zK5/2021/09/16/8751daf6-c2a1-408b-a389-91ff8781e5d1.jpeg?id=6524198' width='600' border='0' style='width: 100%; max-width: 600px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 15px; color: #555555; margin: auto; display: block;' class='g-img' alt='PGC_ Digital Giving Card Experience_ Email Header Image-small.jpg'>
                
            
              
                </td>
                
            </tr>
            
            <!-- Hero Image, Flush : END -->
            
            <!-- 1 Column Text + Button : BEGIN -->
            
            <tr>
                <td style='background-color: #f6f9fa;' align='center' valign='top'>
                    <table role='presentation' cellspacing='0' cellpadding='0' border='0' style='width: 83%;'>
                        <tbody>
                            <tr>
                                
                                <!-- FEATURED CONTENT: HEADER TEXT -->
                                
                                <td style='padding: 20px; font-family: Verdana, sans-serif; font-size: 14px; line-height: auto; color: #134c62; text-align: center;'>
                                <h1 style='margin: 0 0 14px 0; font-family: Verdana, sans-serif; font-size: 30px; line-height: auto; color: #134c62; font-weight: bold;'>
                               

                " . $headlinetext . "


                                </h1>
                                
                               
                                
                                <p style='text-align: center; padding-top: 20px'>
                                <span id='docs-internal-guid-50dbff47-7fff-7a8e-16da-136bafa2168f'>
                                

                " . $emailmessage . "


                                </span>
                                </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            
            <!-- 1 Column Text + Button : END -->
            
            <!-- FEATURED CONTENT : END -->          
            
            <!-- 1 Column Text + Button : END -->
            
            <!-- Social Icons -->
            
            
            <!-- End Social Icons -->
            
        </tbody>
    </table>
    
    <!-- Email Body : END -->
    
    <!-- Email Footer : BEGIN -->
    
        
    <table align='center' role='presentation' cellspacing='0' cellpadding='0' border='0' style='margin: auto; background: #484e52; width: 100%;'>
        <tbody>
            <tr>
                <td style='padding: 20px; font-family: Verdana, sans-serif; font-size: 12px; line-height: auto; text-align: center; color: #c0c0c0;'>
                
                <!-- ==================================== -->
                <!-- STANDARD EMAIL FOOTER : BEGIN -->
                <!-- ==================================== -->
                
                <p style='font-family: Verdana, sans-serif; padding-bottom: 14px; font-size: 12px; line-height: auto; text-align: center; color: #c0c0c0;'>
                You are receiving this email because you sent a gift through the
    <a href='https://honorcards.pcusa.org/' style='color: #c0c0c0; text-decoration: underline; font-size: 12px;'>
        Digital Giving Card
    </a>
    experience of the <a href='https://presbyteriangifts.pcusa.org/' style='color: #c0c0c0; text-decoration: underline; font-size: 12px;'>PACM</a>. You will only receive further communications and/or marketing emails if you are subscribed to our email list(s).
                </p>

                
                <p style='font-family: Verdana, sans-serif; font-size: 12px; line-height: auto; text-align: center; color: #888888;'>
                Presbyterian Church USA
                <br>
                100 Witherspoon Street
                <br>
                Louisville, KY 40202
                <br>
                United States
                </p>
                
                
                </td>
            </tr>
        </tbody>
    </table>
    
    <!-- Email Footer : END -->

    <!--[if mso]>
            </td>
        </tr>
    </table>
    <![endif]-->
    
    </div>
    
    <!--[if mso | IE]>
            </td>
        </tr>
    </table>
    <![endif]-->
    
    </center>
    </body>
</html>";
        return $bodyhtml;
    }
}