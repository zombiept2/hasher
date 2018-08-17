<html>
    <head>
        <title>File Hasher</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
        <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
        <style>
            .header {
                margin-top: 40px;
            }
            .output {
                display: none;
                margin-bottom: 120px;
            }
            @media only screen and (max-width: 768px) {
                .output {
                    margin-top: 30px;
                }
            }
            .centered {
                text-align: center;
            }
            .drop-over {
                line-height: 70px;
                font-size: 1.4em;
            }
            #drop-area {
                height: 100px;
                text-align: center;
                border: 2px dashed #ddd;
                padding: 10px;
            }
            #drop-area.over {
                background: #ffeeba;
                border: 2px dashed #000;
            }
            .tiny_img_container {
                margin-bottom: 15px;
            }
            .tiny_img_container img {
                max-width: 100%;
            }
            .details_container {
                max-width: 400px;
                margin: 0 auto;
                margin-bottom: 15px;
            }
            #loader-wrapper {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10010;
                background-color: #fff;
                display: none;
            }
            #loader {
                display: block;
                position: relative;
                left: 50%;
                top: 50%;
                width: 148px;
                height: 148px;
                margin: -74px 0 0 -74px;
                border: 3px solid transparent;
                /*
                border-top-color: #0d3072;
                border-bottom-color: #0d3072;
                border-radius: 50%;
                -webkit-animation: spin 1.5s  infinite;
                      animation: spin 1.5s  infinite;
                */
            }
            #loader img {
                height: 100%;
                margin-top: 0;
                margin-left: 0;
            }
            #loader:before {
                content: "";
                position: absolute;
                top: -45px;
                left: -45px;
                right: -45px;
                bottom: -45px;
                border: 3px solid transparent;
                border-top-color: #707070;
                border-radius: 50%;
                -webkit-animation: spin 1s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
                      animation: spin 1s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
            }
            #loader:after {
                content: "";
                position: absolute;
                top: -55px;
                left: -55px;
                right: -55px;
                bottom: -55px;
                border: 3px solid transparent;
                border-top-color: #000;
                border-bottom-color: #000;
                border-radius: 50%;
                -webkit-animation: spin 1.5s  infinite; /* Chrome, Opera 15+, Safari 5+ */
                      animation: spin 1.5s  infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
            }
            @-webkit-keyframes spin {
                0%   { 
                    -webkit-transform: rotate(0deg);  /* Chrome, Opera 15+, Safari 3.1+ */
                    -ms-transform: rotate(0deg);  /* IE 9 */
                      transform: rotate(0deg);  /* Firefox 16+, IE 10+, Opera */
                }
                100% { 
                    -webkit-transform: rotate(36deg);  /* Chrome, Opera 15+, Safari 3.1+ */
                    -ms-transform: rotate(360deg);  /* IE 9 */
                      transform: rotate(360deg);  /* Firefox 16+, IE 10+, Opera */
                }
            }
            @keyframes spin {
                0%   { 
                    -webkit-transform: rotate(0deg);  /* Chrome, Opera 15+, Safari 3.1+ */
                    -ms-transform: rotate(0deg);  /* IE 9 */
                      transform: rotate(0deg);  /* Firefox 16+, IE 10+, Opera */
                }
                100% { 
                    -webkit-transform: rotate(36deg);  /* Chrome, Opera 15+, Safari 3.1+ */
                    -ms-transform: rotate(360deg);  /* IE 9 */
                      transform: rotate(360deg);  /* Firefox 16+, IE 10+, Opera */
                }
            }
        </style>
    </head>
    <body>
        <div id="loader-wrapper">
            <div id="loader"><img src="img/box.png" alt="What's in the box?!?!?" /></div>
        </div>
        <div class="container">
            <div class="header">
                <h1>File Hasher</h1>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="upload">
                        <div class="card">
                            <div class="card-header">File Upload</div>
                            <div class="card-body">
                                <form id="form_upload">
                                    <input type="file" id="fileinput" />
                                </form>
                                <div id="drop-area">
                                    <span id="drop-over" class="drop-over">Drop files here!</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div id="output" class="output">
                        <div class="card">
                            <div class="card-header">File Details</div>
                            <div class="card-body centered">
                                <div class="details_container">
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>Filename:</strong> <span id="filename"></span></li>
                                        <li class="list-group-item"><strong>Filesize:</strong> <span id="filesize"></span></li>
                                        <li class="list-group-item list-group-item-danger"><strong>MD5 Hash</strong><br /><span id="md5_hash"></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function() {
                 document.getElementById('fileinput').addEventListener('change', function() {
                    var files = this.files;
                    for (var i=0; i<files.length; i++)
                    {
                        UploadFile(this.files[i]); // call the function to upload the file
                    }
                }, false);
                dropArea = document.getElementById("drop-area");
                dropOver = document.getElementById("drop-over");
                dropArea.addEventListener("dragleave", function (evt) {
                    var target = evt.target;
                    if (target && target === dropArea) {
                        this.className = "";
                    }
                    evt.preventDefault();
                    evt.stopPropagation();
                }, false);
                dropArea.addEventListener("dragenter", function (evt) {
                    this.className = "over";
                    evt.preventDefault();
                    evt.stopPropagation();
                }, false);
                dropOver.addEventListener("dragenter", function (evt) {
                    dropArea.className = "over";
                    evt.preventDefault();
                    evt.stopPropagation();
                }, false);
                dropOver.addEventListener("dragover", function (evt) {
                    dropArea.className = "over";
                    evt.preventDefault();
                    evt.stopPropagation();
                }, false);
                dropArea.addEventListener("dragover", function (evt) {
                    evt.preventDefault();
                    evt.stopPropagation();
                }, false);
                dropArea.addEventListener("drop", function (evt) {
                    TraverseFiles(evt.dataTransfer.files);
                    this.className = "";
                    evt.preventDefault();
                    evt.stopPropagation();
                }, false);
            });
            function TraverseFiles(files) {
                if (typeof files !== "undefined") {
                    for (var i=0, l=files.length; i<l; i++) {
                        UploadFile(files[i]);
                    }
                }
            }
            function UploadFile(file) {
                ShowLoader();
                var url = 'hasher.php';
                var xhr = new XMLHttpRequest();
                var fd = new FormData();
                xhr.open('POST', url, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Every thing ok, file uploaded
                        if (xhr.responseText) {
                            var obj = JSON.parse(xhr.responseText);
                            $('#filename').html(obj.filename);
                            $('#filesize').html(obj.filesize_readable);
                            $('#md5_hash').html(obj.md5_hash);
                            $('#output').show();
                            ScrollTo('output');
                            $('#form_upload')[0].reset();
                        }
                        //console.log(xhr.responseText); // handle response.
                        HideLoader();
                    }
                    else {
                        $('#form_upload')[0].reset();
                        $('#ouput').hide();
                        HideLoader();
                    }
                };
                fd.append('upload_file', file);
                xhr.send(fd);
            }
            function OpenWindow(url) {
                window.open(url, '_blank');
            }
            function Redirect(url) {
                window.location.href = url;
            }
            function ScrollTo(id, speed) {
                speed = speed || 500;
                $('html, body').animate({
                    scrollTop: $('#'+id).offset().top
                }, speed);
            }
            function ShowLoader() {
                $('#loader-wrapper').show();
            }
            function HideLoader() {
                $('#loader-wrapper').fadeOut();
            }
        </script>
    </body>
</html>