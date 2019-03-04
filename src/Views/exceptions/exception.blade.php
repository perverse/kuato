<html>
<head>
    <title>Oops! We stuffed up!</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <style>
        body {
            background-color: black;
        }

        h1 {
            color: white;
            text-align: center;
            font-family: Helvetica;
            font-family: 'Roboto', sans-serif;
            font-size: 42px;
            margin: 0px;
        }

        .container {
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -330px;
            margin-left: -375px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $title }}</h1>
        <img src="http://http.cat/{{ $code }}" alt="Error Code {{ $code }}">
    </div>
</body>
</html>