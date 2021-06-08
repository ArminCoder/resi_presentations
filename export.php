<?php
$files = scandir('./');
$options = [];

foreach ($files as $file) {
    if (is_dir($file) and $file != "." && $file != "..") {
        if (strpos($file, 'module') !== false) {
            array_push($options, $file);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en" style="height: 100%; display: flex">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="../../css/style.css" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../bootstrap.css" />
    <script src="../../js/aos.js"></script>
    <link rel="stylesheet" href="../../css/aos.css" />
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon" sizes="16x16" />
    <title>Module Export Success</title>
    <!-- <script src="./js/main.js"></script> -->
    <link rel="stylesheet" href="../../css/all.css" />
    <style>
        .container-fluid {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .button-navigation {
            position: absolute;
            bottom: 0;
            padding: 20px;
            right: 100px;
            width: 100%;
            left: 50%;
            transform: translate(-50%);
        }

        .border-separator {
            height: 14px;
            background: #33a4e7;
            width: 100px;
            margin: 0 auto 50px;
            border-radius: 3px;
        }

        a:focus {
            border: 5px solid #3b87cb;
            animation: border-pulsate 2s infinite;
            animation-timing-function: ease-in-out;
            outline: none;
        }

        @keyframes border-pulsate {
            0% {
                border-color: #3a96e7;
            }

            25% {
                border-color: #1e64a1;
            }

            50% {
                border-color: #185a94;
            }

            75% {
                border-color: #1e64a1;
            }

            100% {
                border-color: #3a96e7;
            }
        }
    </style>
</head>

<body class="w-100 h-100">
    <div class="container-fluid p-5 h-100">
        <!-- navigation -->
        <div class="w-100 navigation d-flex">
            <div class="left-section">
                <span class="h5 text-warning">Tv Presentations</span>
                <span class="h1 text-light">Export content</span>
            </div>
            <div class="triangle"></div>
        </div>
        <!-- navigation end -->

        <form action="./exportlang.php" class="container bg-light border rounded p-5 text-center">
            <div class="form-group">
                <label for="exampleFormControlSelect1" class="h4 mb-4">Select a module...</label>
                <select class="form-control" name="module">
                    <option>Select Module...</option>
                    <?php 
                        foreach($options as $option) {
                            echo "<option value='" . $option . "'>" . $option . "</option>";
                        }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Submit</button>
        </form>

    </div>
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/progressbar.js"></script>
    <script>

    </script>
</body>

</html>