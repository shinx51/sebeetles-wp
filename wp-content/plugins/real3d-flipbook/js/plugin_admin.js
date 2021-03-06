/*
author http://codecanyon.net/user/zlac
*/

var R3D = R3D || {}
var scriptSource = (function(scripts) {
    var scripts = document.getElementsByTagName('script'),
        script = scripts[scripts.length - 1];
    if (script.getAttribute.length !== undefined) {
        return script.src
    }
    return script.getAttribute('src', -1)
}());

(function($) {

    R3D.EditFlipbooks = function() {
        console.log('edit flipbooks')

        this.books = $.parseJSON(flipbooks);
        var self = this
            // debugger

        function onAddNewCat() {

            // self.categories.push("a")

            self.categories = []

            var data = {
                action: 'real3dflipbook_get_option',
                name: 'flipbooks',
                // data: self.categories
            };

            $.post(ajaxurl, data, function(response) {
                var response = $.parseJSON(response)
                console.log("success")
                console.log(typeof(response))
                //console.log(response)

                var array = $.map(response, function(value, index) {
                    return [value];
                });

                //sortByKey(array,'id')
                //console.log(array);

                // sortByKey(array,'date')
                // console.log(array);

                array.sort(dynamicSort('id'))
                console.log(array);


                // handle response from the AJAX request.
            });
        }

        function getOption(optionName, callback) {

            var data = {
                action: 'real3dflipbook_get_option',
                name: optionName,
            };

            $.post(ajaxurl, data, function(response) {
                var response = $.parseJSON(response)
                callback(response)
            });
        }

        function addBook(book) {
            $('<tr>' + '<th scope="row" class="manage-column column-cb check-column">' + '<input type="checkbox" class="row-checkbox" name="' + book.id + '">' + '</th>      ' + '<td>' + '<strong><a href="#" title="Edit">' + book.name + '</a></strong>' + '<div class="row-actions"><span class="edit"><a href="#" title="Edit this item">Edit</a> | </span><span class="inline hide-if-no-js"><a href="#" title="Duplicate flipbook" >Duplicate</a> | </span><span class="trash"><a href="#" title="Move to trash" >Trash</a></span>' + '</div>' + '</td>' + '<td>[real3dflipbook id="' + book.id + '"]</td>' + '<td>' + book.date + '</td>' + ' </tr>').appendTo($('#flipbooks-table'))
        }

        /*for (var i = 0; i < 20; i++) {
            var book = this.books[i]
            addBook(book.id, book.name, book.date)
        };*/

        var keys = []
        var names = []
        var ids = []
        var dates = []
        for (var key in this.books) {
            keys.push(key);
            names.push(this.books[key].name)
            ids.push(this.books[key].id)
            dates.push(this.books[key].date)
        }

        var sortedDates = dates.sort()

        /*getOption('flipbooks',function(response){
            var array = $.map(response, function(value, index) {
                return [value];
            });
            var totalPages = parseInt(array.length / 20) + 1
            //debugger
            if(array.length <20) 
                $('.tablenav-pages').addClass('one-page')
            $('.total-pages').text(totalPages)
            $('.displaying-num').text(array.length + ' items')
        })*/

        /*var array = $.map(this.books, function(value, index) {
            return [value];
        });*/
        var pageSize = 20
        var currentPage = 0
        var totalPages = parseInt(keys.length / pageSize) + 1
            //debugger
        if (keys.length < pageSize)
            $('.tablenav-pages').addClass('one-page')
        $('.total-pages').text(totalPages)
        $('.displaying-num').text(keys.length + ' items')

        function showPage(index) {
            $('#flipbooks-table').empty()
            for (var i = pageSize * index; i < pageSize * (index + 1); i++) {
                var book = self.books[i.toString()]
                if (book)
                    addBook(book)
            }
            $('.current-page').val(index+1)
        }

        showPage(currentPage)


        /*function dynamicSort(property) {
            var sortOrder = 1;
            if (property[0] === "-") {
                sortOrder = -1;
                property = property.substr(1);
            }
            return function(a, b) {
                var x = typeof(a[property]) == 'string' ? a[property].toLowerCase() : a[property]
                var y = typeof(b[property]) == 'string' ? b[property].toLowerCase() : b[property]
                var result = (x < y) ? -1 : (x > y) ? 1 : 0;
                return result * sortOrder;
            }
        }*/

        $('.first-page').click(function(){
            currentPage = 0
            showPage(currentPage)
        })
        $('.prev-page').click(function(){
            if(currentPage > 0) currentPage --;
            showPage(currentPage)
        })
        $('.next-page').click(function(){
            if(currentPage < (totalPages-1)) currentPage ++;
            showPage(currentPage)
        })
        $('.last-page').click(function(){
            currentPage = totalPages-1
            showPage(currentPage)
        })




        function onAddNewFlipbook() {
            console.log(self.books)

            window.location = window.location.origin + window.location.pathname + '?page=real3d_flipbook_admin&action=add_new'
        }

        $("#add_new_cat").click(onAddNewCat)
        //$("#add_new_flipbook").click(onAddNewFlipbook)

        $('.bulkactions-apply').click(function() {
            var action = $(this).parent().find('select').val()
            if (action != '-1') {
                // console.log(action)
                var list = []
                $('.row-checkbox').each(function() {
                    // console.log(this)
                    if ($(this).is(':checked'))
                        list.push($(this).attr('name'))
                        // console.log(list)
                })
                if (list.length > 0) {
                    // console.log('post')
                    // console.log(window.location.href )
                    // console.log(window.location )
                    // console.log(window.location.origin + window.location.pathname )

                    window.location = window.location.origin + window.location.pathname + '?page=real3d_flipbook_admin&action=delete&bookId=' + list.join(",")

                    /*$.get(window.location.origin + window.location.pathname, { action: "delete", bookId: list.join(",") })
                      .done(function( data ) {
                    	alert( "Data Loaded: " + data );
                      });*/


                }
                //find book id-s selected from the list
            }
        })
    }



    $(document).ready(function() {



        /*if (typeof(options) == 'undefined') {*/

        new R3D.EditFlipbooks()
        //edit flipbooks


        return
        /* }*/

        $('.creating-page').hide()

        // console.log(options);

        var json_str = options.replace(/&quot;/g, '"');

        options = jQuery.parseJSON(json_str);

        function convertStrings(obj) {

            $.each(obj, function(key, value) {
                // console.log(key + ": " + options[key]);
                if (typeof(value) == 'object' || typeof(value) == 'array') {
                    convertStrings(value)
                } else if (!isNaN(value)) {
                    if (obj[key] == "")
                        delete obj[key]
                    else
                        obj[key] = Number(value)
                } else if (value == "true") {
                    obj[key] = true
                } else if (value == "false") {
                    obj[key] = false
                }
            });

        }
        convertStrings(options)

        // console.log(options);

        addOption("general", "name", "text", "Flipbook name", "");
        addOption("general", "mode", "dropdown", "Flipbook mode", "normal", ["normal", "lightbox", "fullscreen"]);
        addOption("general", "viewMode", "dropdown", "Flipbook view mode", "webgl", ["webgl", "3d", "2d"]);
        addOption("general", "pageMode", "dropdown", "Flipbook page mode", "singlePage", ["singlePage", "doubleWithCover"]);
        addOption("general", "pageSize", "dropdown", "Page size from media gallery", "full", ["full", "large"]);
        addOption("general", "singlePageMode", "checkbox", "Single page view", false);
        addOption("general", "skin", "dropdown", "Flipbook skin", "light", ["light", "dark"]);
        addOption("general", "sideNavigationButtons", "checkbox", "Navigation buttons on the sides", true);
        addOption("general", "hideMenu", "checkbox", "Hide bottom menu bar", false);
        addOption("general", "sound", "checkbox", "Sounds", true);
        addOption("general", "pageFlipDuration", "text", "Page flip duration", 1);
        addOption("general", "pageShadow1", "checkbox", "Shadow of flipping page", true);
        addOption("general", "pageShadow2", "checkbox", "Shadow of non flipping page", true);
        addOption("general", "pageShadow3", "checkbox", "Light overlay of pages", true);
        addOption("general", "tableOfContentCloseOnClick", "checkbox", "Close table of content when page is clicked", true);
        addOption("general", "allowPageScroll", "dropdown", "Allow page scroll when swiping the flipbook", "vertical", ["vertical", "auto", "none"]);

        addOption("pdf", "pdfUrl", "selectFile", "PDF file url - upload / select from media library the PDF file and flipbook will be generated automatically without converting to images, with links inside pdf working.", "");
        addOption("pdf", "pdfPageScale", "text", "PDF page scale (between 1 and 3, higher value equals higher resolution of generated jpg pages) ", 2);
        addOption("pdf", "pdfPageQuality", "text", "Quality of jpg images generated from pdf (between 0 and 1) ", 0.9);
        addOption("pdf", "pdfPageBgColor", "color", "PDF page background color ", "#FFFFFF");

        addOption("mobile", "singlePageModeIfMobile", "checkbox", "Single page view", false);
        addOption("mobile", "pdfBrowserViewerIfMobile", "checkbox", "Use default device pdf viewer instead of flipbook", false);
        addOption("mobile", "pdfBrowserViewerFullscreen", "checkbox", "Default device pdf viewer fullscreen", true);
        addOption("mobile", "pdfBrowserViewerFullscreenTarget", "dropdown", "Default device pdf viewer target", "_blank", ["_self", "_blank"]);

        addOption("mobile", "btnTocIfMobile", "checkbox", "Button table of content", true);
        addOption("mobile", "btnThumbsIfMobile", "checkbox", "Button thumbnails", true);
        addOption("mobile", "btnShareIfMobile", "checkbox", "Button share", false);
        addOption("mobile", "btnDownloadPagesIfMobile", "checkbox", "Button download pages", true);
        addOption("mobile", "btnDownloadPdfIfMobile", "checkbox", "Button view pdf", true);
        addOption("mobile", "btnSoundIfMobile", "checkbox", "Button sound", false);
        addOption("mobile", "btnExpandIfMobile", "checkbox", "Button fullscreen", true);
        addOption("mobile", "btnPrintIfMobile", "checkbox", "Button print", false);
        addOption("mobile", "textureSizeIfMobile", "dropdown", "Texture size", 1024, [512, 1024, 2048]);


        addOption("normal", "backgroundColor", "color", "Flipbook background color", "#818181");
        addOption("normal", "backgroundPattern", "selectImage", "Background image pattern url", "");

        addOption("normal", "height", "text", "Flipbook height", 400);
        addOption("normal", "fitToWindow", "checkbox", "Fit to window", false);
        addOption("normal", "fitToParent", "checkbox", "Fit to parent div", false);
        addOption("normal", "fitToHeight", "checkbox", "Fit to height", false);
        addOption("normal", "offsetTop", "text", "Flipbook offset top (fullscreen)", 0);
        addOption("normal", "responsiveHeight", "checkbox", "Responsive height", true);
        addOption("normal", "aspectRatio", "text", "Aspect ratio (for responsive height)", 2);

        addOption("lightbox", "lightboxCssClass", "text", "Lightbox element CSS class - any element with this CSS class that will trigger the lightbox", "");

        addOption("lightbox", "lightboxContainerCSS", "textarea", "Lightbox container CSS - add custom CSS code to lightbox container (div that contains lightbox thumbnail and lightbox link)", "display:inline-block;padding:10px;");

        addOption("lightbox", "lightboxThumbnailUrl", "selectImage", "Lightbox Thumbnail Url", "");
        addOption("lightbox", "lightboxThumbnailUrlCSS", "textarea", "Lightbox Thumbnail CSS", "display:block;");

        addOption("lightbox", "lightboxText", "text", "Lightbox link text", "");
        addOption("lightbox", "lightboxTextCSS", "textarea", "Lightbox link text CSS", "display:block;");
        addOption("lightbox", "lightboxTextPosition", "dropdown", "Lightbox link text position", "top", ["top", "bottom"]);

        addOption("lightbox", "lightBoxOpened", "checkbox", "Lightbox openes on start", false);
        addOption("lightbox", "lightBoxFullscreen", "checkbox", "Lightbox openes in fullscreen", false);
        addOption("lightbox", "lightboxCloseOnClick", "checkbox", "Lightbox closes when clicked outside", false);
        addOption("general", "thumbnailsOnStart", "checkbox", "Show thumbnails on start", false);
        addOption("general", "contentOnStart", "checkbox", "Show content on start", false);

        addOption("general", "rightToLeft", "checkbox", "Right to left mode", false);
        addOption("general", "loadAllPages", "checkbox", "Load all pages on start", false);
        addOption("general", "pageWidth", "text", "Page width (if not set then default page width will be used)", "");
        addOption("general", "pageHeight", "text", "Page height (if not set then default page width will be used)", "");
        addOption("general", "thumbnailWidth", "text", "Thumbnail width", 100);
        addOption("general", "thumbnailHeight", "text", "Thumbnail height", 141);

        //addOption("general", "zoom", "text", "Zoom",1);
        addOption("general", "zoomLevels", "text", "Zoom levels (steps), comma separated ", "0.95,1.5,3,6");
        addOption("general", "zoomDisabled", "checkbox", "Mouse wheel zoom disabled", false);

        addOption("currentPage", "currentPage[enabled]", "checkbox", "Enabled", true);
        addOption("currentPage", "currentPage[title]", "text", "Title", "Current page");

        addOption("btnNext", "btnNext[enabled]", "checkbox", "Enabled", true);
        addOption("btnNext", "btnNext[icon]", "text", "Icon CSS class", "fa-chevron-right");
        addOption("btnNext", "btnNext[title]", "text", "Title", "Next Page");

        addOption("btnFirst", "btnFirst[enabled]", "checkbox", "Enabled", false);
        addOption("btnFirst", "btnFirst[icon]", "text", "Button font awesome CSS class", "fa-step-backward");
        addOption("btnFirst", "btnFirst[title]", "text", "Button title", "First Page");

        addOption("btnLast", "btnLast[enabled]", "checkbox", "Enabled", false);
        addOption("btnLast", "btnLast[icon]", "text", "Button font awesome CSS class", "fa-step-forward");
        addOption("btnLast", "btnLast[title]", "text", "Button title", "First Page");

        addOption("btnPrev", "btnPrev[enabled]", "checkbox", "Enabled", true);
        addOption("btnPrev", "btnPrev[icon]", "text", "Button font awesome CSS class", "fa-chevron-left");
        addOption("btnPrev", "btnPrev[title]", "text", "Button title", "Next Page");

        addOption("btnZoomIn", "btnZoomIn[enabled]", "checkbox", "Enabled", true);
        addOption("btnZoomIn", "btnZoomIn[icon]", "text", "Button font awesome CSS class", "fa-plus");
        addOption("btnZoomIn", "btnZoomIn[title]", "text", "Button title", "Zoom in");

        addOption("btnZoomOut", "btnZoomOut[enabled]", "checkbox", "Enabled", true);
        addOption("btnZoomOut", "btnZoomOut[icon]", "text", "Button font awesome CSS class", "fa-minus");
        addOption("btnZoomOut", "btnZoomOut[title]", "text", "Button title", "Zoom out");

        addOption("btnToc", "btnToc[enabled]", "checkbox", "Enabled", true);
        addOption("btnToc", "btnToc[icon]", "text", "Button font awesome CSS class", "fa-list-ol");
        addOption("btnToc", "btnToc[title]", "text", "Button title", "Table of content");

        addOption("btnThumbs", "btnThumbs[enabled]", "checkbox", "Enabled", true);
        addOption("btnThumbs", "btnThumbs[icon]", "text", "Button font awesome CSS class", "fa-th-large");
        addOption("btnThumbs", "btnThumbs[title]", "text", "Button title", "Pages");

        addOption("btnShare", "btnShare[enabled]", "checkbox", "Enabled", true);
        addOption("btnShare", "btnShare[icon]", "text", "Button font awesome CSS class", "fa-share-alt");
        addOption("btnShare", "btnShare[title]", "text", "Button title", "Share");

        addOption("btnSound", "btnSound[enabled]", "checkbox", "Enabled", true);
        addOption("btnSound", "btnSound[icon]", "text", "Button font awesome CSS class", "fa-volume-up");
        addOption("btnSound", "btnSound[title]", "text", "Button stitle", "Sound");

        addOption("btnDownloadPages", "btnDownloadPages[enabled]", "checkbox", "Button download pages", false);
        addOption("btnDownloadPages", "btnDownloadPages[url]", "selectFile", "Url of zip file containing all pages", "");
        addOption("btnDownloadPages", "btnDownloadPages[icon]", "text", "Button font awesome CSS class", "fa-download");
        addOption("btnDownloadPages", "btnDownloadPages[title]", "text", "Button title", "Download pages");

        addOption("btnDownloadPdf", "btnDownloadPdf[enabled]", "checkbox", "Button download pdf", false);
        addOption("btnDownloadPdf", "btnDownloadPdf[url]", "selectFile", "url of pdf file", "");
        addOption("btnDownloadPdf", "btnDownloadPdf[icon]", "text", "Button font awesome CSS class", "fa-file");
        addOption("btnDownloadPdf", "btnDownloadPdf[title]", "text", "Button title", "Download pdf");
        addOption("btnDownloadPdf", "btnDownloadPdf[forceDownload]", "checkbox", "Force download", true);
        addOption("btnDownloadPdf", "btnDownloadPdf[openInNewWindow]", "checkbox", "Open PDF in new browser window", true);

        addOption("btnPrint", "btnPrint[enabled]", "checkbox", "Button print", true);
        addOption("btnPrint", "btnPrint[icon]", "text", "Button font awesome CSS class", "fa-print");
        addOption("btnPrint", "btnPrint[title]", "text", "Button title", "Print");

        addOption("btnExpand", "btnExpand[enabled]", "checkbox", "Button expand", true);
        addOption("btnExpand", "btnExpand[icon]", "text", "Button enabled font awesome CSS class", "fa-expand");
        addOption("btnExpand", "btnExpand[iconAlt]", "text", "Button disabled font awesome CSS class", "fa-compress");
        addOption("btnExpand", "btnExpand[title]", "text", "Button title", "Toggle fullscreen");

        addOption("btnExpandLightbox", "btnExpandLightbox[enabled]", "checkbox", "Button expand lightbox", true);
        addOption("btnExpandLightbox", "btnExpandLightbox[icon]", "text", "Button enabled font awesome CSS class", "fa-expand");
        addOption("btnExpandLightbox", "btnExpandLightbox[iconAlt]", "text", "Button disabled font awesome CSS class", "fa-compress");
        addOption("btnExpandLightbox", "btnExpandLightbox[title]", "text", "Button title", "Toggle fullscreen");


        addOption("google_plus", "google_plus[enabled]", "checkbox", "Enabled", true);
        addOption("google_plus", "google_plus[url]", "text", "URL", "");

        addOption("twitter", "twitter[enabled]", "checkbox", "Enabled", true);
        addOption("twitter", "twitter[url]", "text", "URL", "");
        addOption("twitter", "twitter[description]", "text", "Description", "");

        addOption("facebook", "facebook[enabled]", "checkbox", "Enabled", true);
        addOption("facebook", "facebook[url]", "text", "URL", "");
        addOption("facebook", "facebook[description]", "text", "Description", "");
        addOption("facebook", "facebook[title]", "text", "Title", "");
        addOption("facebook", "facebook[image]", "text", "Image", "");
        addOption("facebook", "facebook[caption]", "text", "Caption", "");

        addOption("pinterest", "pinterest[enabled]", "checkbox", "Enabled", true);
        addOption("pinterest", "pinterest[url]", "text", "URL", "");
        addOption("pinterest", "pinterest[image]", "text", "Image", "");
        addOption("pinterest", "pinterest[description]", "text", "Description", "");

        addOption("email", "email[enabled]", "checkbox", "Enabled", true);
        addOption("email", "email[url]", "text", "URL", "");
        addOption("email", "email[description]", "text", "Description", "");

        addOption("general", "startPage", "text", "Start page", 1);

        addOption("general", "deeplinking[enabled]", "checkbox", "Deep linking", false);
        addOption("general", "deeplinking[prefix]", "text", "Deep linking prefix", "");
        // addOption("general", "time1", "text", "Duration of first half of the flip [ms]",500);
        // addOption("general", "transition1", "dropdown", "Transition","easeInQuad", ["easeInQuad","easeInQuad"]);
        // addOption("general", "time2", "text", "Duration of second half of the flip [ms]",600);
        // addOption("general", "transition2", "dropdown", "Transition","easeOutQuad", ["easeOutQuad","easeOutQuad"]);

        // addOption("general", "webgl", "checkbox", "webgl",true);
        addOption("webgl", "webglMinAndroidVersion", "text", "Minimum android version (android devices with older android will use fallback view mode)", 4.4);
        addOption("webgl", "cameraDistance", "text", "Camera Distance", 2800);
        addOption("webgl", "pan", "text", "Camera pan angle", 0);
        addOption("webgl", "tilt", "text", "Camera tilt angle", 0);

        addOption("webgl", "rotateCameraOnMouseDrag", "checkbox", "Rotate camera on mouse drag", true);
        addOption("webgl", "panMax", "text", "Camera pan angle max", 20);
        addOption("webgl", "panMin", "text", "Camera pan angle min", -20);
        addOption("webgl", "tiltMax", "text", "Camera tilt angle max", 0);
        addOption("webgl", "tiltMin", "text", "Camera tilt angle min", -60);

        addOption("webgl", "rotateCameraOnMouseMove", "checkbox", "Rotate camera on mouse move", false);
        addOption("webgl", "panMax2", "text", "Camera pan angle max", 2);
        addOption("webgl", "panMin2", "text", "Camera pan angle min", -2);
        addOption("webgl", "tiltMax2", "text", "Camera tilt angle max", 0);
        addOption("webgl", "tiltMin2", "text", "Camera tilt angle min", -5);

        addOption("webgl", "bookX", "text", "Book x", 0);
        addOption("webgl", "bookY", "text", "Book y", 0);
        addOption("webgl", "bookZ", "text", "Book z", 0);

        addOption("webgl", "pageMaterial", "dropdown", "Page material", "phong", ["phong", "basic"]);
        // addOption("pageShadow", "checkbox", "Page shadow",false);
        addOption("webgl", "pageHardness", "text", "Page hardness", 2);
        addOption("webgl", "coverHardness", "text", "Cover hardness", 2);
        addOption("webgl", "pageSegmentsW", "text", "Page segments W", 10);
        addOption("webgl", "pageSegmentsH", "text", "Page segments H", 1);
        addOption("webgl", "pageShininess", "text", "Page shininess", 20);

        addOption("webgl", "ambLightColor", "color", "AmbientLightColor color ", "#CCCCCC");

        addOption("webgl", "spotLightColor", "color", "Spotlight color ", "#FFFFFF");
        addOption("webgl", "spotLightX", "text", "Spotlight x", 0);
        addOption("webgl", "spotLightY", "text", "Spotlight y", 0);
        addOption("webgl", "spotLightIntensity", "text", "Spotlight intensity", 0.1);
        addOption("webgl", "spotLightShadowDarkness", "text", "Spotlight shadow darkness", 0.5);

        addOption("webgl", "textureSize", "dropdown", "Texture size", 2048, [1024, 2048, 4096]);

        $('.flipbook-preview').click(function(e) {

            options.assets = {
                preloader: options.rootFolder + "images/preloader.jpg",
                left: options.rootFolder + "images/left.png",
                overlay: options.rootFolder + "images/overlay.jpg",
                flipMp3: options.rootFolder + "mp3/turnPage.mp3",
                shadowPng: options.rootFolder + "images/shadow.png"
            };

            $('#flipbook-preview-container-inner').empty()
            $('#flipbook-preview-container-inner').flipBook(options)
        });

        $('.postbox .hndle').click(function(e) {
            $(this).parent().toggleClass("closed")
        });
        $('.postbox .handlediv').click(function(e) {
            $(this).parent().toggleClass("closed")
        });


        $("#flipbook-general-options").find("select").change(function() {
            if (this.name == "mode") {
                if (this.value == "lightbox") {
                    $("#flipbook-lightbox-options").closest('.postbox').show();
                    $("#flipbook-normal-options").closest('.postbox').hide();
                } else {
                    $("#flipbook-lightbox-options").closest('.postbox').hide();
                    $("#flipbook-normal-options").closest('.postbox').show();
                }
            }
        });

        $("#flipbook-general-options").find("select").change();

        // $("#flipbook-general-options").find("input:checkbox").change(function () {
        // if(this.name == "webgl"){
        // if(this.checked)
        // $("#flipbook-webgl-options").closest('.postbox').show();
        // else
        // $("#flipbook-webgl-options").closest('.postbox').hide();
        // }
        // });
        // $("#flipbook-general-options").find("input:checkbox").change();

        //show webgl options depending on the dropdown value of view mode
        $("#flipbook-general-options").find("select").change(function() {
            if (this.name == "viewMode") {
                if (this.value == "webgl") {
                    $("#flipbook-webgl-options").closest('.postbox').show();
                } else {
                    $("#flipbook-webgl-options").closest('.postbox').hide();
                }
            }
        });
        $("#flipbook-general-options").find("select").change();

        function addOption(section, name, type, desc, defaultValue, values) {

            var table = $("#flipbook-" + section + "-options");
            var tableBody = table.find('tbody');
            var row = $('<tr valign="top"  class="field-row"></tr>').appendTo(tableBody);
            var th = $('<th scope="row">' + desc + '</th>').appendTo(row);
            var td = $('<td></td>').appendTo(row);

            // var list = $("#flipbook-options-list");
            // var li = $('<li />').appendTo(list);
            // var label = $('<label />').appendTo(li);
            // label.text(desc);

            switch (type) {
                case "text":
                    var input = $('<input type="text" name="' + name + '"/>').appendTo(td);
                    if (options[name] && typeof(options[name]) != 'undefined') {
                        input.attr("value", options[name]);
                    } else if (options[name.split("[")[0]] && name.indexOf("[") != -1 && typeof(options[name.split("[")[0]]) != 'undefined') {
                        input.attr("value", options[name.split("[")[0]][name.split("[")[1].split("]")[0]]);
                    } else {
                        input.attr('value', defaultValue);
                    }
                    break;
                case "color":
                    var input = $('<input type="text" name="' + name + '"/>').appendTo(td);
                    if (options[name] && typeof(options[name]) != 'undefined') {
                        input.attr("value", options[name]);
                    } else if (options[name.split("[")[0]] && name.indexOf("[") != -1 && typeof(options[name.split("[")[0]]) != 'undefined') {
                        input.attr("value", options[name.split("[")[0]][name.split("[")[1].split("]")[0]]);
                    } else {
                        input.attr('value', defaultValue);
                    }
                    input.wpColorPicker();
                    break;
                case "textarea":
                    var elem = $('<textarea name="' + name + '"/>').appendTo(td);
                    if (options[name] && typeof(options[name]) != 'undefined') {
                        elem.attr("value", options[name]);
                    } else if (options[name.split("[")[0]] && name.indexOf("[") != -1 && typeof(options[name.split("[")[0]]) != 'undefined') {
                        elem.attr("value", options[name.split("[")[0]][name.split("[")[1].split("]")[0]]);
                    } else {
                        elem.attr('value', defaultValue);
                    }
                    break;
                case "checkbox":
                    var inputHidden = $('<input type="hidden" name="' + name + '" value="false"/>').appendTo(td);
                    var input = $('<input type="checkbox" name="' + name + '" value="true"/>').appendTo(td);
                    if (typeof(options[name]) != 'undefined') {
                        input.attr("checked", options[name]);
                    } else if (options[name.split("[")[0]] && name.indexOf("[") != -1 && typeof(options[name.split("[")[0]]) != 'undefined') {
                        var val = options[name.split("[")[0]][name.split("[")[1].split("]")[0]]
                        input.attr("checked", val && val != 'false');

                    } else {
                        input.attr('checked', defaultValue);
                    }
                    break;
                case "selectImage":
                    var input = $('<input type="text" name="' + name + '"/><a class="select-image-button button-secondary button80" href="#">Select file</a>').appendTo(td);
                    if (typeof(options[name]) != 'undefined') {
                        input.attr("value", options[name]);
                    } else if (name.indexOf("[") != -1 && typeof(options[name.split("[")[0]]) != 'undefined') {
                        input.attr("value", options[name.split("[")[0]][name.split("[")[1].split("]")[0]]);
                    } else {
                        input.attr('value', defaultValue);
                    }
                    break;
                case "selectFile":
                    var input = $('<input type="text" name="' + name + '"/><a class="select-image-button button-secondary button80" href="#">Select file</a>').appendTo(td);
                    if (typeof(options[name]) != 'undefined') {
                        input.attr("value", options[name]);
                    } else if (name.indexOf("[") != -1 && typeof(options[name.split("[")[0]]) != 'undefined') {
                        input.attr("value", options[name.split("[")[0]][name.split("[")[1].split("]")[0]]);
                    } else {
                        input.attr('value', defaultValue);
                    }
                    break;
                case "dropdown":
                    var select = $('<select name="' + name + '">').appendTo(td);
                    for (var i = 0; i < values.length; i++) {
                        var option = $('<option name="' + name + '" value="' + values[i] + '">' + values[i] + '</option>').appendTo(select);
                        if (typeof(options[name]) != 'undefined') {
                            if (options[name] == values[i]) {
                                option.attr('selected', 'true');
                            }
                        } else if (defaultValue == values[i]) {
                            option.attr('selected', 'true');
                        }
                    }
                    break;

            }

        }
        // flipbook-options

        //for all pages in  options.pages create page 
        for (var i = 0; i < options.pages.length; i++) {
            var page = options.pages[i];
            var pagesContainer = $("#pages-container");
            var pageItem = createPageHtml("pages[" + i + "]", i + 1, page.title, page.src, page.thumb, page.htmlContent);
            pageItem.appendTo(pagesContainer);
            // pageItem.find('.add-link-button').click(function(e){
            // e.preventDefault();
            // var links = $(this).parent().find(".page-links");
            // var pageID = $(this).closest(".page").attr("id");
            // var linksCount = links.find(".page-link").length;		
            // var link = createLinkHtml("pages["+pageID+"][links]["+linksCount+"]", "", "", "", "", "page" ,"","","","","");
            // link.appendTo(links);
            // link.hide().fadeIn();
            // addListeners();
            // });
            //add links
            // if(page.links){
            // for(var j= 0; j < page.links.length; j++){
            // var linkObj = page.links[j]
            // var links = pageItem.find(".page-links");
            // var link = createLinkHtml("pages["+i+"][links]["+j+"]",
            // linkObj["x"],
            // linkObj["y"],
            // linkObj["width"],
            // linkObj["height"],
            // linkObj["target"],
            // linkObj["url"],
            // linkObj["color"],
            // linkObj["alpha"],
            // linkObj["hoverColor"],
            // linkObj["hoverAlpha"]
            // );
            // link.appendTo(links);


            // }
            // }


        }

        if (options.socialShare == null) options.socialShare = [];

        for (var i = 0; i < options.socialShare.length; i++) {
            var share = options.socialShare[i];
            var shareContainer = $("#share-container");
            var shareItem = createShareHtml("socialShare[" + i + "]", i, share.name, share.icon, share.url, share.target);
            shareItem.appendTo(shareContainer);

        }


        if (options.tableOfContent == null) options.tableOfContent = [];
        for (var i = 0; i < options.tableOfContent.length; i++) {
            var toc = options.tableOfContent[i];
            var tocContainer = $("#toc-container");
            var tocItem = createTocHtml("tableOfContent[" + i + "]", i, toc.title, toc.page);
            tocItem.appendTo(tocContainer);

        }

        $(".tabs").tabs();
        $(".ui-sortable").sortable();
        addListeners();


        $('#add-share-button').click(function(e) {

            e.preventDefault()

            var shareContainer = $("#share-container");
            var shareCount = shareContainer.find(".share").length;
            var shareItem = createShareHtml("socialShare[" + shareCount + "]", "", "", "", "", "_blank");
            shareItem.appendTo(shareContainer);

            addListeners();
            $(".tabs").tabs();
        });



        $('.delete-all-pages-button').click(function(e) {
            //open editor to select one or multiple images and create pages from them
            e.preventDefault();

            $('.page').remove();

            //deleteBook
            var bookName = $("input[name='name']").attr('value');
            var pluginDir = scriptSource.split("js/plugin_admin")[0];
            var phpurl = pluginDir + 'includes/process.php';

            $.ajax({
                type: "POST",
                url: phpurl,
                data: {
                    deleteBook: bookName,
                },
                success: function(e) {
                    // console.log(e)
                },
                error: function(e) {
                    // console.log(e)
                }
            }).done(function(e) {
                // console.log(e)
            });

        });

        $('#add-new-page-button').click(function(e) {

            var pagesContainer = $("#pages-container");
            var pagesCount = pagesContainer.find(".page").length;
            var pageItem = createPageHtml("pages[" + pagesCount + "]", "", "", "", "", "");
            pageItem.appendTo(pagesContainer);
            pageItem.hide().fadeIn();
            $(".tabs").tabs();
            addListeners();
            //add page listeners
            pageItem.find('.add-link-button').click(function(e) {
                e.preventDefault();
                var links = $(this).parent().find(".page-links");
                var pageID = $(this).closest(".page").attr("id");
                var linksCount = links.find(".page-link").length;
                var link = createLinkHtml("pages[" + pageID + "][links][" + linksCount + "]");
                link.appendTo(links);
                link.hide().fadeIn();
                addListeners();
            });



        });

        $('.add-pages-button').click(function(e) {
            //open editor to select one or multiple images and create pages from them
            e.preventDefault();



            var pdf_uploader = wp.media({
                    title: 'Select pages',
                    button: {
                        text: 'Select'
                    },
                    multiple: true // Set this to true to allow multiple files to be selected
                })
                .on('select', function() {
                    var arr = pdf_uploader.state().get('selection');
                    var pages = new Array();

                    var ps
                    $("#flipbook-general-options").find("select").each(function() {
                        if (this.name == "pageSize") {
                            ps = this.value
                        }
                    })


                    for (var i = 0; i < arr.models.length; i++) {
                        var url
                        if (ps == "large" && arr.models[i].attributes.sizes.large)
                            url = arr.models[i].attributes.sizes.large.url;
                        else
                            url = arr.models[i].attributes.sizes.full.url;
                        var thumb = (typeof(arr.models[i].attributes.sizes.medium) != "undefined") ? arr.models[i].attributes.sizes.medium.url : url;
                        var title = arr.models[i].attributes.title;
                        pages.push({
                            title: title,
                            url: url,
                            thumb: thumb
                        });
                    }

                    var pagesContainer = $("#pages-container");

                    for (var i = 0; i < pages.length; i++) {

                        var pagesCount = pagesContainer.find(".page").length;
                        var pageItem = createPageHtml("pages[" + pagesCount + "]", pagesCount + 1, pages[i].title, pages[i].url, pages[i].thumb, "");
                        pageItem.appendTo(pagesContainer);
                        pageItem.hide().fadeIn();
                        $(".tabs").tabs();
                        addListeners();
                        //add page listeners
                        pageItem.find('.add-link-button').click(function(e) {
                            e.preventDefault();
                            var links = $(this).parent().find(".page-links");
                            var pageID = $(this).closest(".page").attr("id");
                            var linksCount = links.find(".page-link").length;
                            var link = createLinkHtml("pages[" + pageID + "][links][" + linksCount + "]");
                            link.appendTo(links);
                            link.hide().fadeIn();
                            addListeners();
                        });

                    }

                    generateLightboxThumbnail()


                    //here create page for each url in urls

                    // var attachment = pdf_uploader.state().get('selection').first().toJSON();
                    // $('.custom_media_image').attr('src', attachment.url);
                    // $('.custom_media_url').val(attachment.url);
                    // $('.custom_media_id').val(attachment.id);
                })
                .open();



        });

        function addListeners() {
            $('.submitdelete').click(function() {
                $(this).parent().parent().parent().animate({
                    'opacity': 0
                }, 100).slideUp(100, function() {
                    $(this).remove();
                });
            });

            $('.html-content').each(function() {
                $(this).parent().find('.html-content-hidden').val(escape($(this).val()))
            })
            $('.html-content').change(function() {
                $(this).parent().find('.html-content-hidden').val(escape($(this).val()))
            })

            // $('.select-image-button').click(function (e) {
            // e.preventDefault();
            // var imageURLInput = $(this).parent().find("input");
            // tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
            // $("#TB_window,#TB_overlay,#TB_HideSelect").one("unload", function (e) {
            // e.stopPropagation();
            // e.stopImmediatePropagation();
            // return false;
            // });

            // window.send_to_editor = function (html) {
            // var imgurl = jQuery('img',html).attr('src');
            // imageURLInput.val(imgurl);
            // tb_remove();
            // };
            // });	


            $('.select-image-button').click(function(e) {
                e.preventDefault();
                var imageURLInput = $(this).parent().find("input");
                var pdf_uploader = wp.media({
                        title: 'Select pages',
                        button: {
                            text: 'Select'
                        },
                        multiple: false // Set this to true to allow multiple files to be selected
                    })
                    .on('select', function() {
                        var arr = pdf_uploader.state().get('selection');
                        // var urls = new Array();
                        // for(var i=0;i<arr.models.length;i++){
                        // var url = arr.models[i].attributes.url;
                        // urls.push(url);
                        // }
                        var url = arr.models[0].attributes.url;
                        imageURLInput.val(url);
                        // var attachment = pdf_uploader.state().get('selection').first().toJSON();
                        // $('.custom_media_image').attr('src', attachment.url);
                        // $('.custom_media_url').val(attachment.url);
                        // $('.custom_media_id').val(attachment.id);
                    })
                    .open();
            });
        }


        function createPageHtml(prefix, id, title, src, thumb, htmlContent) {
            htmlContent = unescape(htmlContent);
            if (htmlContent == 'undefined' || typeof(htmlContent) == 'undefined') htmlContent = ''
            if (title == 'undefined' || typeof(title) == 'undefined') title = ''
            return $('<div id="' + id + '"class="page">' + '<h3>Page ' + id + '</h3>' + '<div class="tabs settings-area">' + '<div class="page-img"><img src="' + thumb + '"/></div>' + '<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">' + '<li><a href="#tabs-1">Title</a></li>' + '<li><a href="#tabs-2">Image</a></li>' + '<li><a href="#tabs-3">Thumbnail</a></li>' + '<li><a href="#tabs-4">HTML Content</a></li>'
                // + '<li><a href="#tabs-5">Links</a></li>'
                + '</ul>' + '<div id="tabs-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom">' + '<div class="field-row">' + '<input id="page-title" name="' + prefix + '[title]" type="text" placeholder="Enter page title" value="' + title + '" />' + '</div>' + '</div>' + '<div id="tabs-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom">' + '<div class="field-row">' + '<input id="image-path" name="' + prefix + '[src]" type="text" placeholder="Image URL" value="' + src + '" />' + '<a class="select-image-button button-secondary button80" href="#">Select image</a> ' + '</div>' + '</div>' + '<div id="tabs-3" class="ui-tabs-panel ui-widget-content ui-corner-bottom">' + '<div class="field-row">' + '<input id="image-path" name="' + prefix + '[thumb]" type="text" placeholder="Thumbnail URL" value="' + thumb + '" />' + '<a class="select-image-button button-secondary button80" href="#">Select image</a> ' + '</div>' + '</div>' + '<div id="tabs-4" class="ui-tabs-panel ui-widget-content ui-corner-bottom">' + '<textarea class="html-content"  type="text" cols="50" rows="3" placeholder="Static HTML content">' + htmlContent + '</textarea>' + '<input type="hidden" class="html-content-hidden" name="' + prefix + '[htmlContent]" />' + '</div>'
                // + '<div id="tabs-5" class="ui-tabs-panel ui-widget-content ui-corner-bottom">'
                // + '<div class="page-links">'
                // + '</div>'
                // + '<br />'
                // + '<a class="alignRight add-link-button button-secondary button80" href="#">Add New Link</a> '
                // + '</div>'
                // + '<div class="button-secondary submitbox deletediv"><a class="submitdelete deletion">Delete</a></div>'
                + '<div class="submitbox deletediv"><span class="submitdelete deletion">Delete page</span></div>' + '</div>' + '</div>' + '</div>'
            );
        }

        function createShareHtml(prefix, id, name, icon, url, target) {

            if (typeof(target) == 'undefined' || target != "_self")
                target = "_blank";

            var markup = $('<div id="' + id + '"class="share">' + '<h4>Share button ' + id + '</h4>'

                + '<div class="tabs settings-area">' + '<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">' + '<li><a href="#tabs-1">Icon name</a></li>' + '<li><a href="#tabs-2">Icon css class</a></li>' + '<li><a href="#tabs-3">Link</a></li>' + '<li><a href="#tabs-4">Target</a></li>' + '</ul>' + '<div id="tabs-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom">' + '<div class="field-row">' + '<input id="page-title" name="' + prefix + '[name]" type="text" placeholder="Enter icon name" value="' + name + '" />' + '</div>' + '</div>' + '<div id="tabs-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom">' + '<div class="field-row">' + '<input id="image-path" name="' + prefix + '[icon]" type="text" placeholder="Enter icon CSS class" value="' + icon + '" />' + '</div>' + '</div>' + '<div id="tabs-3" class="ui-tabs-panel ui-widget-content ui-corner-bottom">' + '<div class="field-row">' + '<input id="image-path" name="' + prefix + '[url]" type="text" placeholder="Enter link" value="' + url + '" />' + '</div>' + '</div>' + '<div id="tabs-4" class="ui-tabs-panel ui-widget-content ui-corner-bottom">' + '<div class="field-row">'
                // + '<input id="image-path" name="'+prefix+'[target]" type="text" placeholder="Enter link" value="'+target+'" />'

                + '<select id="social-share" name="' + prefix + '[target]">'
                // + '<option name="'+prefix+'[target]" value="_self">_self</option>'
                // + '<option name="'+prefix+'[target]" value="_blank">_blank</option>'
                + '</select>' + '</div>' + '</div>' + '<div class="submitbox deletediv"><span class="submitdelete deletion">x</span></div>' + '</div>' + '</div>' + '</div>'
            );

            var values = ["_self", "_blank"];
            var select = markup.find('select');

            for (var i = 0; i < values.length; i++) {
                var option = $('<option name="' + prefix + '[target]" value="' + values[i] + '">' + values[i] + '</option>').appendTo(select);
                if (typeof(options["socialShare"][id]) != 'undefined') {
                    if (options["socialShare"][id]["target"] == values[i]) {
                        option.attr('selected', 'true');
                    }
                }
            }

            return markup;
        }

        function createLinkHtml(prefix, x, y, width, height, target, url, color, alpha, hoverColor, hoverAlpha) {
            var res = $('<div class="page-link link-options">' + '<div class="inside">' + '<div class="field-row">' + '<label for="" data-help="">x</label>' + '<input id="link-x" name="' + prefix + '[x]" type="text" placeholder="link x position" value="' + x + '"/>' + '</div> ' + '<div class="field-row">' + '<label for="" data-help="">y</label>' + '<input id="link-y" name="' + prefix + '[y]" type="text" placeholder="link y position"  value="' + y + '"/>' + '</div> ' + '<div class="field-row">' + '<label for="" data-help="">width</label>' + '<input id="link-width" name="' + prefix + '[width]" type="text" placeholder="link width" value="' + width + '"/>' + '</div> ' + '<div class="field-row">' + '<label for="" data-help="">height</label>' + '<input id="link-height" name="' + prefix + '[height]" type="text" placeholder="link height" value="' + height + '"/>' + '</div> ' + '<div class="field-row">' + '<label for="" data-help="">color</label>' + '<input id="link-color" name="' + prefix + '[color]" type="text" placeholder="link color" value="' + color + '"/>' + '</div> ' + '<div class="field-row">' + '<label for="" data-help="">alpha</label>' + '<input id="link-alpha" name="' + prefix + '[alpha]" type="text" placeholder="link alpha" value="' + alpha + '"/>' + '</div> ' + '<div class="field-row">' + '<label for="" data-help="">hoverColor</label>' + '<input id="link-hoverColor" name="' + prefix + '[hoverColor]" type="text" placeholder="link hoverColor" value="' + hoverColor + '"/>' + '</div> ' + '<div class="field-row">' + '<label for="" data-help="">hoverAlpha</label>' + '<input id="link-hoverAlpha" name="' + prefix + '[hoverAlpha]" type="text" placeholder="link hoverAlpha" value="' + hoverAlpha + '"/>' + '</div> ' + '<div class="field-row">' + '<label for="" data-help="">href</label>' + '<input id="link-url" name="' + prefix + '[url]" type="text" placeholder="page or URL" value="' + url + '"/>' + '<select id="link-target" name="' + prefix + '[target]">' + '<option class="page" value="page">page</option>' + '<option class="_self" value="_self">_self</option>' + '<option class="_blank" value="_blank">_blank</option>' + '</select>' + '</div>' + '<div class="button-secondary submitbox deletediv"><a class="submitdelete deletion">Delete</a></div>' + '<br />' + '</div>' + '</div>');
            res.find("." + target).attr('selected', 'true');

            // switch(target){
            // case "page":
            // res.("'."+target+"'").attr('selected','true');
            // break;
            // case "_self":
            // break;
            // case "_blank":
            // break;
            // }
            return res;
        }

        function getOptionValue(optionName) {
            return $("input[name='" + optionName + "']").attr('value');
        }

        function setOptionValue(optionName, value) {
            return $("input[name='" + optionName + "']").attr('value', value);
        }

        //=======================================
        //	 ADD PAGES FROM PDF
        //=======================================

        $('.select-pdf-button').click(function(e) {
            e.preventDefault();
            //clear "pdf file url"
            $("input[name='pdfUrl']").attr('value', "");
            //open editor to select pdf file from wp media library
            pdf_uploader.open();
        });

        var pdf_uploader = wp.media({
            title: 'Select pdf',
            button: {
                text: 'Select'
            },
            multiple: false // Set this to true to allow multiple files to be selected
        }).on('select', onPDFSelected);

        var creatingPage;

        function onPDFSelected() {
            var arr = pdf_uploader.state().get('selection');
            var pdfUrl = arr.models[0].attributes.url

            //we have the pdf url, now use pdf.js to open pdf 
            function getDocumentProgress(progressData) {
                // console.log(progressData.loaded / progressData.total);
                $('.creating-page').html('Loading PDF ' + parseInt(100 * progressData.loaded / progressData.total) + '% ')
                $('.creating-page').show()
            }
            PDFJS.workerSrc = scriptSource.split("plugin_admin")[0] + 'pdf.worker.min.js'
            PDFJS.getDocument(pdfUrl, null, false, getDocumentProgress).then(function(pdf) {
                creatingPage = 1
                loadPageFromPdf(pdf)
            });
        }

        function renderPdfPage(pdfPage, scale, pageName, onComplete) {
            var context, scale, viewport, canvas, context, renderContext;

            viewport = pdfPage.getViewport(scale);
            canvas = document.createElement('canvas');
            context = canvas.getContext('2d');
            canvas.width = viewport.width;
            canvas.height = viewport.height;

            renderContext = {
                canvasContext: context,
                viewport: viewport,
                intent: 'display'
                // intent:'print'
            };

            pdfPage.render(renderContext).then(function() {

                var destinationCanvas = document.createElement('canvas');
                destinationCanvas.width = canvas.width
                destinationCanvas.height = canvas.height
                //grab the context from your destination canvas
                var destCtx = destinationCanvas.getContext('2d');

                destCtx.fillStyle = getOptionValue('pdfPageBgColor'); // set canvas background color
                destCtx.fillRect(0, 0, canvas.width, canvas.height); // now fill the canvas
                //call its drawImage() function passing it the source canvas directly
                destCtx.drawImage(canvas, 0, 0);


                var dataurl = destinationCanvas.toDataURL("image/jpeg", parseFloat(getOptionValue('pdfPageQuality')));
                savePng(dataurl, pageName, onComplete)
            })
        }

        function savePng(dataUrl, pageName, onComplete) {
            var bookName = $("input[name='name']").attr('value');
            var pluginDir = scriptSource.split("js/plugin_admin")[0];
            var phpurl = pluginDir + 'includes/process.php';

            $.ajax({
                type: "POST",
                url: phpurl,
                data: {
                    bookName: bookName,
                    imgbase: dataUrl,
                    pageName: pageName
                },
                success: function(e) {
                    // console.log(e)
                },
                error: function(e) {
                    // console.log(e)
                }
            }).done(function(e) {
                // console.log(e)
                onComplete();
            });
        }

        function loadPageFromPdf(pdf, pageIndex) {

            $('.creating-page').html('Adding pages from PDF...<br/>Adding page ' + creatingPage + ' / ' + pdf.pdfInfo.numPages)

            pdf.getPage(creatingPage).then(function getPage(page) {
                var pdfPageScale = getOptionValue('pdfPageScale')
                    //create page jpg and save it to server
                var pagesContainer = $("#pages-container");
                var pagesCount = pagesContainer.find(".page").length;
                var currentPage = pagesCount + 1;
                var pageName = "page" + currentPage.toString()
                renderPdfPage(page, pdfPageScale, pageName, function() {

                    var viewport = page.getViewport(pdfPageScale)
                    setOptionValue("pageWidth", viewport.width);
                    setOptionValue("pageHeight", viewport.height);
                    setOptionValue("thumbnailWidth", 100);
                    setOptionValue("thumbnailHeight", 100 * viewport.height / viewport.width);


                    //page saved
                    //create thumbnail jpg and save it to server
                    var thumbName = "thumb" + currentPage.toString()
                    renderPdfPage(page, .3, thumbName, function() {
                        //thumb saved

                        //add new page
                        var bookName = $("input[name='name']").attr('value');
                        var pluginDir = scriptSource.split("js/plugin_admin")[0];
                        var uploadsDir = pluginDir.split("plugins")[0] + "uploads/";
                        var pagesContainer = $("#pages-container");
                        var pagesCount = pagesContainer.find(".page").length;
                        var currentPage = pagesCount + 1;
                        var pageItem = createPageHtml("pages[" + pagesCount + "]", currentPage, "page " + currentPage, uploadsDir + "real3dflipbook/" + bookName + "/page" + currentPage + ".jpg", uploadsDir + "real3dflipbook/" + bookName + "/thumb" + currentPage + ".jpg", "");
                        pageItem.appendTo(pagesContainer);
                        $(".tabs").tabs();
                        addListeners();

                        if (creatingPage < pdf.pdfInfo.numPages) {
                            creatingPage++
                            loadPageFromPdf(pdf)
                        } else {
                            $('.creating-page').html('Adding pages from PDF completed!')
                            setTimeout(function() {
                                $('.creating-page').hide();
                            }, 5000)
                            generateLightboxThumbnail()
                        }
                    })
                })
            })
        }

        function generateLightboxThumbnail() {
            console.log()
            var thumb = $("input[name='pages[0][thumb]']").attr('value')
            var lightboxThumbnailUrl = $("input[name='lightboxThumbnailUrl']").attr('value')
            if (lightboxThumbnailUrl == "")
                $("input[name='lightboxThumbnailUrl']").attr('value', thumb)

        }

    });
})(jQuery);

function stripslashes(str) {
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Ates Goral (http://magnetiq.com)
    // +      fixed by: Mick@el
    // +   improved by: marrtins
    // +   bugfixed by: Onno Marsman
    // +   improved by: rezna
    // +   input by: Rick Waldron
    // +   reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +   input by: Brant Messenger (http://www.brantmessenger.com/)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: stripslashes('Kevin\'s code');
    // *     returns 1: "Kevin's code"
    // *     example 2: stripslashes('Kevin\\\'s code');
    // *     returns 2: "Kevin\'s code"
    return (str + '').replace(/\\(.?)/g, function(s, n1) {
        switch (n1) {
            case '\\':
                return '\\';
            case '0':
                return '\u0000';
            case '':
                return '';
            default:
                return n1;
        }
    });
}