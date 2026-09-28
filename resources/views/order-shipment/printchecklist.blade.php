<!DOCTYPE html>
<html style='font-family: "Instrument Sans", ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";'>
<head>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <title>CHECKLIST</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            margin: 25px;
        }
        body {
            margin: 0;
        }
    </style>
</head>
<body>
<x-cards.checklist :order="$order" :service="$service" :documentTypes="$documentTypes" :milestones="$milestones" />
</body>
</html>
