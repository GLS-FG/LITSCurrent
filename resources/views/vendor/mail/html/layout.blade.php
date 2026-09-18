<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
}

.footer {
width: 100% !important;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
}
}
</style>
{{ $head ?? '' }}
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
{{ $header ?? '' }}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr><td class="lits-blank-space-5"></td></tr>
<tr><td class="lits-red-line"></td></tr>
<tr><td class="lits-blank-space-5"></td></tr>
<tr><td class="lits-blue-line"></td></tr>
<!-- Body content -->
<tr>
<td class="body-cell">

<table align="right" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="right">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<img src="{{ asset('/images/lits.png') }}" class="logo-lits" style="display: inline-block;" alt="LITS">
</td>
</tr>
</table>
</td>
</tr>
</table>

{{ Illuminate\Mail\Markdown::parse($slot) }}

<table align="left" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="left">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<img src="{{ asset('/images/gls.png') }}" class="logo-gls" style="display: inline-block;" alt="LITS">
</td>
</tr>
</table>
</td>
</tr>
<tr><td class="lits-blank-space-8"></td></tr>
</table>

{{ $subcopy ?? '' }}
</td>
</tr>
<tr>
<td>
<img src="{{ asset('/images/email-footer.png') }}" class="email-footer" style="display: inline-block;" alt="LITS">
</td>
</tr>
<tr><td class="lits-blank-space-8"></td></tr>
<tr><td class="lits-red-line"></td></tr>
<tr><td class="lits-blank-space-8"></td></tr>
</table>
</td>
</tr>

{{ $footer ?? '' }}
</table>
</td>
</tr>
</table>
</body>
</html>
