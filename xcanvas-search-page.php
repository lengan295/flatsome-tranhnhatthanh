<?php

/**
 * Template Name: xcanvas search page
 * xcanvas search page
 */

get_header();
do_action('flatsome_before_page'); ?>
    <div id="content" class="content-area page-wrapper" role="main">
        <div class="row row-main">
            <div class="large-12 col">
                <div class="col-inner">

                    <?php if (get_theme_mod('default_title', 0)) { ?>
                        <header class="entry-header">
                            <h1 class="entry-title mb uppercase"><?php the_title(); ?></h1>
                        </header>
                    <?php } ?>

                    <link rel="stylesheet"
                          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.css"/>
                    <link rel="stylesheet"
                          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
                    <link rel="stylesheet" href="https://xcanvas.me/css/jquery.mCustomScrollbar.css"/>
<!--                    <link rel="stylesheet" href="https://xcanvas.me/css/main.css" />-->

                    <div class="bg-grey" ng-app="main-app" ng-controller="search-ctrl" ng-cloak>

                        <div id="shortcut-link-modal" class="modal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Shortcut</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <a style="display: block" href="[[shortcut.downloadURL]]"
                                           ng-repeat="shortcut in shortcuts">[[shortcut.targetURL]]</a>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="preview-image-modal" class="image-modal" ng-click="onCloseImageModal()">
                            <span class="close-image-modal">&times;</span>
                            <img class="image-modal-content" ng-src="[[selectedReviewImage]]"/>
                        </div>
                        <div class="wrapper">
                            <div class="right-content box-right-content">
                                <div class="box-white-shadow">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12 pdr0">
                                            <div class="right-header">
                                                <div class="select-file">
                                                    <form class="form-link">
                                                        <input
                                                                type="text"
                                                                name="link"
                                                                placeholder="Nhập từ khóa, paste hình, hoặc link hình ..."
                                                                class="txtInput"
                                                                ng-model="filter.keyword"
                                                                ng-paste="onPasteURL($event)"
                                                                ng-keydown="$event.keyCode === 13 && onEnterKeyword()"/>
                                                    </form>
                                                </div>
                                                <div class="dis-block box-file">
                                                    <label for="intput_select_image1" class="iconw iconw-file"
                                                           style="cursor: pointer">
                                                        <input
                                                                id="intput_select_image1"
                                                                ng-image-model="onSelectedImage"
                                                                style="display: none"
                                                                type="file"
                                                                accept="image/x-png,image/gif,image/jpeg,image/jpg"/>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="box-uploads">
                                                <div class="box-upload">
                                                    <form class="form-upload">
                                                        <canvas
                                                                class="input-file-canvas"
                                                                id="myCanvas"
                                                                ng-image-drop-zone="[image/png, image/jpeg, image/jpg]"
                                                                ng-image-drop-zone-model="onSelectedImage"
                                                                data-max-file-size="5"
                                                                ng-style="{'background-image':'url(' + selectedImage + ')', 'background-repeat': 'no-repeat', 'background-size': 'contain', 'background-position': 'center'}"></canvas>
                                                        <label
                                                                ng-show="!selectedImage"
                                                                for="intput_select_image2"
                                                                class="input-file-canvas"
                                                                ng-image-drop-zone="[image/png, image/jpeg, image/jpg]"
                                                                ng-image-drop-zone-model="onSelectedImage"
                                                                data-max-file-size="5">
                                                            <input
                                                                    id="intput_select_image2"
                                                                    ng-image-model="onSelectedImage"
                                                                    style="display: none"
                                                                    type="file"
                                                                    accept="image/x-png,image/gif,image/jpeg,image/jpg"/>
                                                        </label>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-md-7 col-sm-12 col-xs-12">
                                            <div class="box-white">
                <span ng-show="myAccount"
                      style="position: absolute; font-size: 14px; top: 13px; left: 32px; font-weight: 600;">
                  [[myAccount.providerName.toUpperCase()]]
                    <!-- <i class="fa fa-sign-out logout" ng-click="onClickBtnLogout()"></i> -->
                </span>
                                                <div class="custom-scroll">
                                                    <ul masonry preserve-order class="masonry">
                                                        <li class="masonry-brick" ng-repeat="product in listProducts">
                                                            <a>
                                                                <div class="box-image"
                                                                     ng-click="onClickReviewImage(product)">
                                                                    <img ng-src="[[product.thumbURL]]"/>
                                                                    <span class="code-item">[[product.code]]</span>
                                                                </div>
                                                                <a class="textNormal" href="[[product.sourceURL]]"
                                                                   target="_blank" title="[[product.name]]"
                                                                   rel="noopener">[[product.name]]</a>
                                                                <!-- <span class="price">[[product.price ? product.price + '₫' : 'Free']]</span> -->
                                                                <span class="date-product"
                                                                      ng-show="product.createdTime">[[product.createdTime | toDate | date:"dd/MM/yyyy"]]</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="wrapper_mobile"></div>

                        <!--   Google Captcha JS Files   -->
                        <script src="https://www.google.com/recaptcha/api.js?render=6Lf93dcZAAAAAIYsbYAbhmods_uEGvSo4fWOidef"></script>

                        <!--   Core JS Files   -->
                        <script src="https://xcanvas.me/js/lib/jquery.min.js" type="text/javascript"></script>
                        <script src="https://xcanvas.me/js/lib/popper.min.js" type="text/javascript"></script>
                        <script src="https://xcanvas.me/js/lib/bootstrap.min.js" type="text/javascript"></script>
                        <script src="https://xcanvas.me/js/lib/angular.min.js" type="text/javascript"></script>

                        <!-- Plugins-->
                        <script src="https://xcanvas.me/js/plugin/bootbox.js" type="text/javascript"></script>
                        <script src="https://xcanvas.me/js/plugin/jquery.blockUI.js" type="text/javascript"></script>
                        <script src="https://xcanvas.me/js/plugin/imagesloaded.pkgd.min.js"
                                type="text/javascript"></script>
                        <script src="https://xcanvas.me/js/plugin/masonry.pkgd.min.js" type="text/javascript"></script>
                        <script src="https://xcanvas.me/js/plugin/ng-animate.min.js" type="text/javascript"></script>
                        <script src="https://xcanvas.me/js/plugin/ng-sanitize.min.js" type="text/javascript"></script>
                        <script src="https://xcanvas.me/js/plugin/ng-masonry.js" type="text/javascript"></script>

                        <!-- Angularjs App-->
                        <script src="https://xcanvas.me/js/angular/app.js" type="text/javascript"></script>
                        <script src="https://xcanvas.me/js/angular/app.filter.js" type="text/javascript"></script>
                        <script src="https://xcanvas.me/js/angular/app.directive.js" type="text/javascript"></script>
                        <script src="https://xcanvas.me/js/angular/service/str.service.js"
                                type="text/javascript"></script>
                        <script src="https://xcanvas.me/js/angular/service/http.service.js"
                                type="text/javascript"></script>
                        <script src="/wp-content/themes/flatsome-child/js/searchCtrl.js" type="text/javascript"></script>
                    </div>

                    <style>
                        .grecaptcha-badge {
                            display: none!important;
                        }
                    </style>


                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php do_action( 'flatsome_before_page_content' ); ?>

                        <?php the_content(); ?>

                        <?php if ( comments_open() || '0' != get_comments_number() ){
                            comments_template(); } ?>

                        <?php do_action( 'flatsome_after_page_content' ); ?>
                    <?php endwhile; // end of the loop. ?>

                </div>
            </div>
        </div>
    </div>

<?php
do_action('flatsome_after_page');
get_footer();
