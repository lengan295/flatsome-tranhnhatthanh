ngApp.controller('search-ctrl', function ($scope, $timeout, httpService) {
    var domain = "https://xcanvas.me";
    var secretKey = "4tOpKTt7hTyQVqQcET4izg";
    var canvas = document.getElementById('myCanvas');
    var ctx = canvas.getContext('2d');

    $scope.filter = {};

    $scope.detectedFrames = [];
    $scope.customDetectedFrame = null;

    $scope.sourceImageSize = {};
    $scope.sourceDetectedFrames = [];
    $scope.sourceCustomDetectedFrame = {};

    $scope.queryParam = function (name) {
        return (location.search.split(name + '=')[1] || '').split('&')[0];
    }

    $(document).ready(function () {
        canvas.width = $("#myCanvas").width();
        canvas.height = $("#myCanvas").height();
        $(window).resize(function () {
            canvas.width = $("#myCanvas").width();
            canvas.height = $("#myCanvas").height();

            if ($scope.customDetectedFrame) {
                $scope.customDetectedFrame = $scope.translateToCanvasCoordinate([$scope.sourceCustomDetectedFrame])[0];
                $scope.drawFrames([$scope.customDetectedFrame]);
            } else {
                $scope.detectedFrames = $scope.translateToCanvasCoordinate($scope.sourceDetectedFrames);
                $scope.drawFrames($scope.detectedFrames);
            }
        });
    });

    var isHoldingCtrlKey = false;
    var isMovingMouse = false;
    var hasSelectNewFrame = false;
    document.addEventListener('keydown', function (event) {
        if (event.keyCode == 17) {
            isHoldingCtrlKey = true;
        }
    });

    document.addEventListener('keyup', function (event) {
        if (event.keyCode == 17) {
            isHoldingCtrlKey = false;
            if (hasSelectNewFrame) {
                $scope.doSearch($scope.detectedFrames);
                hasSelectNewFrame = false;
            }
        }
    });

    $scope.isBelongToDrawableZone = function (mouseX, mouseY) {
        var scaleWidth = canvas.width * 1.0 / $scope.sourceImageSize.width;
        var scaleHeight = canvas.height * 1.0 / $scope.sourceImageSize.height;
        var scale = Math.min(scaleWidth, scaleHeight);

        var paddingX = (canvas.width - ($scope.sourceImageSize.width * scale)) / 2;
        var paddingY = (canvas.height - ($scope.sourceImageSize.height * scale)) / 2;

        drawableZone = {
            'x': Math.round(paddingX),
            'y': Math.round(paddingY),
            'width': Math.round(scale * $scope.sourceImageSize.width),
            'height': Math.round(scale * $scope.sourceImageSize.height)
        };

        if (mouseX >= drawableZone.x && mouseX <= drawableZone.x + drawableZone.width) {
            if (mouseY >= drawableZone.y && mouseY <= drawableZone.y + drawableZone.height) {
                return true;
            }
        }
        return false;
    }

    var isMouseDown;
    var curMouseX, curMouseY;
    var lastMouseDownX, lastMouseDownY;

    $(canvas).on('mousedown touchstart', function (e) {
        if ($(window).data()["blockUI.isBlocked"] == 1 || !$scope.selectedImage) {
            return;
        }
        var tempMouseX = parseInt((e.clientX ? e.clientX : e.originalEvent.touches[0].pageX) - canvas.getBoundingClientRect().left);
        var tempMouseY = parseInt((e.clientY ? e.clientY : e.originalEvent.touches[0].pageY) - canvas.getBoundingClientRect().top);
        if ($scope.isBelongToDrawableZone(tempMouseX, tempMouseY)) {
            isMovingMouse = false;
            lastMouseDownX = tempMouseX;
            lastMouseDownY = tempMouseY;
            isMouseDown = true;
        }
    });

    $(canvas).on('mousemove touchmove', function (e) {
        if ($(window).data()["blockUI.isBlocked"] == 1 || !$scope.selectedImage || !isMouseDown) {
            return;
        }

        var tempMouseX = parseInt((e.clientX ? e.clientX : e.originalEvent.touches[0].pageX) - canvas.getBoundingClientRect().left);
        var tempMouseY = parseInt((e.clientY ? e.clientY : e.originalEvent.touches[0].pageY) - canvas.getBoundingClientRect().top);

        if ($scope.isBelongToDrawableZone(tempMouseX, tempMouseY)) {
            isMovingMouse = true;
            curMouseX = tempMouseX;
            curMouseY = tempMouseY;
            var width = curMouseX - lastMouseDownX;
            var height = curMouseY - lastMouseDownY;
            $scope.customDetectedFrame = {
                "x": lastMouseDownX,
                "y": lastMouseDownY,
                "width": width,
                "height": height,
                "isSelected": true
            };

            $scope.sourceCustomDetectedFrame = $scope.translateToImageCoordinate([$scope.customDetectedFrame])[0];
            $scope.drawFrames([$scope.customDetectedFrame]);
        }
    });

    $(canvas).on('mouseup touchend', function (e) {
        if ($(window).data()["blockUI.isBlocked"] == 1 || !$scope.selectedImage) {
            return;
        }

        var mouseX = parseInt((e.clientX ? e.clientX : e.originalEvent.touches[0].pageX) - canvas.getBoundingClientRect().left);
        var mouseY = parseInt((e.clientY ? e.clientY : e.originalEvent.touches[0].pageY) - canvas.getBoundingClientRect().top);
        isMouseDown = false;
        isMovingMouse = false;

        // onclick
        if (Math.abs(lastMouseDownX - mouseX) <= 2 && Math.abs(lastMouseDownY - mouseY) <= 2) {
            for (var i = 0; i < $scope.detectedFrames.length; i++) {
                $scope.detectedFrames[i].isSelected = isHoldingCtrlKey ? $scope.detectedFrames[i].isSelected : false;
                if (mouseX >= $scope.detectedFrames[i].x && mouseX <= $scope.detectedFrames[i].x + $scope.detectedFrames[i].width
                    && mouseY >= $scope.detectedFrames[i].y && mouseY <= $scope.detectedFrames[i].y + $scope.detectedFrames[i].height) {
                    hasSelectNewFrame = ($scope.detectedFrames[i].isSelected != true);
                    $scope.detectedFrames[i].isSelected = true;
                }
            }

            $scope.customDetectedFrame = null;
            $scope.drawFrames($scope.detectedFrames);
            if (!isHoldingCtrlKey && hasSelectNewFrame) {
                $scope.doSearch($scope.detectedFrames);
            }
        } else if ($scope.selectedImage && $scope.customDetectedFrame && $scope.customDetectedFrame.width > 10 && $scope.customDetectedFrame.height > 10) {
            $scope.doSearch([$scope.customDetectedFrame]);
        }
    });

    $scope.translateToCanvasCoordinate = function (frames) {
        var translatedFrames = angular.copy(frames);
        for (var i = 0; i < translatedFrames.length; i++) {
            var scaleWidth = canvas.width * 1.0 / $scope.sourceImageSize.width;
            var scaleHeight = canvas.height * 1.0 / $scope.sourceImageSize.height;
            var scale = Math.min(scaleWidth, scaleHeight);

            var paddingX = (canvas.width - ($scope.sourceImageSize.width * scale)) / 2;
            var paddingY = (canvas.height - ($scope.sourceImageSize.height * scale)) / 2;

            translatedFrames[i] = {
                'x': paddingX + scale * translatedFrames[i].x,
                'y': paddingY + scale * translatedFrames[i].y,
                'width': scale * translatedFrames[i].width,
                'height': scale * translatedFrames[i].height,
                isSelected: translatedFrames[i].isSelected
            };
        }

        return translatedFrames;
    }

    $scope.translateToImageCoordinate = function (frames) {
        var translatedFrames = angular.copy(frames);
        for (var i = 0; i < translatedFrames.length; i++) {
            var scaleWidth = $scope.sourceImageSize.width * 1.0 / canvas.width;
            var scaleHeight = $scope.sourceImageSize.height * 1.0 / canvas.height;
            var scale = Math.max(scaleWidth, scaleHeight);

            var paddingX = (canvas.width * scale - $scope.sourceImageSize.width) / 2;
            var paddingY = (canvas.height * scale - $scope.sourceImageSize.height) / 2;

            translatedFrames[i] = {
                'x': Math.round(translatedFrames[i].x * scale - paddingX),
                'y': Math.round(translatedFrames[i].y * scale - paddingY),
                'width': Math.round(scale * translatedFrames[i].width),
                'height': Math.round(scale * translatedFrames[i].height),
                isSelected: translatedFrames[i].isSelected
            };
        }

        return translatedFrames;
    }

    $scope.drawFrames = function (frames) {
        if (frames.length == 0) {
            return;
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        ctx.beginPath();
        ctx.moveTo(0, 0);
        ctx.lineTo(canvas.width, 0);
        ctx.lineTo(canvas.width, canvas.height);
        ctx.lineTo(0, canvas.height);
        ctx.lineTo(0, 0);
        ctx.closePath();

        for (var i = 0; i < frames.length; i++) {
            ctx.moveTo(frames[i].x, frames[i].y);
            ctx.lineTo(frames[i].x, frames[i].y + frames[i].height);
            ctx.lineTo(frames[i].x + frames[i].width, frames[i].y + frames[i].height);
            ctx.lineTo(frames[i].x + frames[i].width, frames[i].y);
            ctx.lineTo(frames[i].x, frames[i].y);
            ctx.closePath();
        }

        ctx.fillStyle = "rgba(0, 0, 0, 0.6)";
        ctx.fill();

        for (var i = 0; i < frames.length; i++) {
            if (frames[i].isSelected) {
                ctx.beginPath();
                var corner1 = { x: frames[i].x - 3, y: frames[i].y - 3 };
                var corner2 = { x: frames[i].x + frames[i].width + 3, y: frames[i].y + frames[i].height + 3 };

                ctx.moveTo(corner1.x, corner1.y);
                ctx.lineTo(corner1.x + 10, corner1.y);
                ctx.moveTo(corner1.x, corner1.y);
                ctx.lineTo(corner1.x, corner1.y + 10);

                ctx.moveTo(corner2.x, corner2.y);
                ctx.lineTo(corner2.x - 10, corner2.y);
                ctx.moveTo(corner2.x, corner2.y);
                ctx.lineTo(corner2.x, corner2.y - 10);

                ctx.lineWidth = 2;
                ctx.strokeStyle = "#42e6f5";
                ctx.stroke();
            }
        }
    }

    $scope.onSelectedImage = function (imageBase64) {
        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
        if (!imageBase64) {
            bootbox.alert('Vui lòng chọn ảnh');
            return;
        }

        $scope.selectedImage = imageBase64;

        grecaptcha.execute('6Lf93dcZAAAAAIYsbYAbhmods_uEGvSo4fWOidef', { action: 'search' }).then(function (captchaToken) {
            httpService.post({
                url: domain + "/search/detectFrames",
                params: {
                    captchaToken: captchaToken,
                    secretKey: secretKey,
                    imageBase64: $scope.selectedImage
                }
            }).then(function (data) {
                $scope.sourceImageSize = data.imageSize;
                $scope.sourceDetectedFrames = data.frames;
                $scope.customDetectedFrame = null;
                $scope.sourceCustomDetectedFrame = {};
                $scope.detectedFrames = $scope.translateToCanvasCoordinate($scope.sourceDetectedFrames);
                $scope.detectedFrames.forEach(function (frame) { frame.isSelected = true; });
                $scope.drawFrames($scope.detectedFrames);
                $scope.doSearch($scope.detectedFrames);
            });
        })
    }

    $scope.onSelectedSubmitProductImage = function (imagedata) {
        $scope.errMsgProductImageURL = "";
        $scope.submitProduct.imageURL = imagedata;
    }

    $scope.doSearch = function (frames) {
        var selectedFrames = [];
        for (var i = 0; i < frames.length; i++) {
            if (frames[i].isSelected) {
                selectedFrames.push(frames[i]);
            }
        }

        if (selectedFrames.length == 0) {
            return;
        }

        $scope.filter.keyword = '';
        httpService.post({
            url: domain + "/search/searchByImage",
            params: {
                secretKey: secretKey,
                filter: $scope.filter,
                imageBase64: $scope.selectedImage,
                frames: $scope.translateToImageCoordinate(selectedFrames)
            }
        }).then(function (data) {
            $scope.listProducts = data;
        });
    }

    $scope.doSearchByKeyword = function () {
        httpService.post({
            url: domain + "/search/searchByKeyword",
            params: {
                secretKey: secretKey,
                keyword: $scope.filter.keyword,
            }
        }).then(function (data) {
            $scope.listProducts = data;
        });
    }

    $scope.getLatestProducts = function () {
        httpService.get({
            url: domain + "/search/getLatestProducts",
            params: {
                secretKey: secretKey
            }
        }).then(function (data) {
            $scope.listProducts = data;
        });
    }

    $scope.onPasteURL = function (event) {
        var items = (event.clipboardData || event.originalEvent.clipboardData).items;
        for (index in items) {
            var item = items[index];
            if (item.kind === 'file') {
                var blob = item.getAsFile();
                var reader = new FileReader();
                reader.onload = function (event) {
                    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                    $scope.selectedImage = event.target.result;
                    $scope.onSelectedImage(event.target.result)
                };
                reader.readAsDataURL(blob);
            } else if (item.kind === 'string') {
                var imageURL = event.originalEvent.clipboardData.getData('text/plain');
                if (isValidImageURL(imageURL)) {
                    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                    $scope.selectedImage = imageURL;
                    httpService.get({
                        url: domain + "/search/getImageBase64FromURL",
                        params: {
                            secretKey: secretKey,
                            imageURL: imageURL
                        }
                    }).then(function (imagedata) {
                        $scope.onSelectedImage(imagedata);
                    });
                }
            }
        }
    }

    $scope.onEnterKeyword = function () {
        $scope.doSearchByKeyword();
        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    }

    $scope.onClickContextPreviewProduct = function (product) {
        $scope.onClickReviewImage(product);
    }

    $(document).on('keydown', function (e) {
        if (e.keyCode === 27) { // ESC
            document.getElementById("preview-image-modal").style.display = "none";
        }
    });

    $scope.onClickReviewImage = function (product) {
        $scope.selectedReviewImage = product.imageURL;
        document.getElementById("preview-image-modal").style.display = "block";
    }

    $scope.onCloseImageModal = function () {
        document.getElementById("preview-image-modal").style.display = "none";
    }

    grecaptcha.ready(function () {
        $scope.getLatestProducts();
    });
});