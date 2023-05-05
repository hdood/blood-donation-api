<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .wrapper {

            background-color: #F4F5FE;
            border-radius: 14px;
            padding: 1rem 1rem;
            width: 400px;
            gap: 1rem;
        }

        .infos {

            padding-top: 1rem;
            display: inline;

        }

        span {
            font-weight: bold;
            display: block;
        }


        img {
            width: 200px;
            height: 200px;
            border-radius: 14px;
            position: relative;
            left: 12rem;
        }

        .image-wrapper {
            display: inline;

        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="infos">
            <span class="name">{{ $name }}</span>
            <span> {{ $bloodGroup }}</span>
            <span>date of birth : 04 / 03 / 2002</span>
            <span>made in : {{ Carbon\Carbon::now(); }}</span>
        </div>

        <span>hello world this is working</span>
        <div class="image-wrapper   ">
            <img src="" alt="">
        </div>
    </div>
</body>

</html>