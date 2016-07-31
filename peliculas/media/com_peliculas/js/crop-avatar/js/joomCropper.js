/**
 * Created by carless on 14/08/14.
 */

(function ($) {
    // Create the defaults once
    var pluginName = "joomCropper";
    var errormsgtoolong = "Recomendamos recudir el tamaño de la imagen. Puede utilizar la web : <a href=\"http://www.webresizer.com/resizer/?lang=es\" target=\"_blank\">http://www.webresizer.com/resizer</a>";
    var log = function (o) {
        try {
            console.log(o)
        } catch (e) {}
    };

    var defaults = {
        // Form options
        containerSelector:  '#joomcrooper-container',
        modalSelector:      '#joomcrooper-modal',
        loadingSelector:    '.joomcrooper-loading',

        imgScrSelector:     '.joomcrooper-src',
        imgDataSelector:    '.joomcrooper-data',
        inputSelector:      '.joomcrooper-input',

        listSelector:       '#joomcrooper-list',

        btnSaveSelector:    '.joomcrooper-save',
        UrlPost:            'index.php',
        defaultCountImages: 0,
        defaultFieldName:   'jform[crop]',
        defaultFieldNameId: 'jform_crop',

        containerWrapper:   '.joomcrooper-wrapper',
        containerPreview:   '.joomcrooper-preview'
    }

    // The actual plugin constructor
    function Plugin(element, options) {
        this.element = element;
        this.options = jQuery.extend({}, defaults, options);
        this._defaults = defaults;

        // Initialise selectors
        this.theContainer       = jQuery(this.options.containerSelector);
        this.theModalWindows    = jQuery(this.options.modalSelector);
        this.theImgSrc          = jQuery(this.options.imgScrSelector);
        this.theImgData         = jQuery(this.options.imgDataSelector);
        this.theInputSelector   = jQuery(this.options.inputSelector);
        this.theListImgSelector = jQuery(this.options.listSelector);

        this.theLoading         = jQuery(this.options.loadingSelector);
        this.theContainerWrapper = jQuery(this.options.containerWrapper);
        this.theContainerPreview = jQuery(this.options.containerPreview);

        this.theBtnSave         = jQuery(this.options.btnSaveSelector);
        this.UrltoPost          = this.options.UrlPost;

        this.countImages        = this.options.defaultCountImages;
        this.fieldName          = this.options.defaultFieldName;
        this.fieldNameId        = this.options.defaultFieldNameId;

        // Selector values
        this._name = pluginName;

        this.init();
        log(this);
    }

    Plugin.prototype = {

        support: {
            fileList: !!$("<input type=\"file\">").prop("files"),
            fileReader: !!window.FileReader,
            formData: !!window.FormData
        },

        init: function () {
            var self = this;

            this.activedCropper     = false;
            this.imagenFondo        = null;

            this.support.datauri = this.support.fileList && this.support.fileReader;

            this.addListener();
        },

        addListener: function () {
            this.theBtnSave.on("click", jQuery.proxy(this.save, this));
            this.theInputSelector.on("change", jQuery.proxy(this.change, this));
        },

        save: function() {
            var _this = this;
            var file;

            if (this.support.datauri) {
                files = this.theInputSelector.prop("files");
                if (files.length > 0) {
                    file = files[0];
                }
            } else {
                file = this.theInputSelector.val();
            }

            var formData = new FormData();
            formData.append('cropdata', this.theImgData.val() );
            formData.append('file', file);

            jQuery.ajax(this.UrltoPost , {
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,

                beforeSend: function () {
                    _this.submitStart();
                },

                success: function (data) {
                    _this.submitDone(data);
                },

                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    _this.submitFail(textStatus || errorThrown);
                },

                complete: function () {
                    _this.submitEnd();
                }
            });
        },

        submitStart: function () {
            this.theLoading.fadeIn();
            this.theModalWindows.modal('hide');
        },

        submitDone: function (data) {
            log(data);

            try {
                data = $.parseJSON(data);
            } catch (e) {};

            if (data && data.status == 1) {
                var $itemId = 'item-' + this.countImages;
                var $itemFieldNameID = this.fieldNameId + this.countImages;
                var $itemFieldName = this.fieldName + '[' + this.countImages + ']';
                var $item =
                    '<li id="' + $itemId + '">' +
                        '<a class="modal pull-left" rel="{handler:\'image\'}" href="' + data.urlfolder + data.filename + '">' +
                            '<img border="0" align="middle" src="' + data.urlfolder + data.filename + '" width="100px" />' +
                        '</a>' +
                        '<input type="hidden" name="' + $itemFieldName + '[name]" value="' + data.filename + '" >' +
                        '<input type="hidden" name="' + $itemFieldName + '[uri]" value="' + data.urifolder + '" >' +
                    '</li>'
                ;

                var $itemDetails =
                    '<div class="media-info">' +
                        '<div class="control-group">' +
                            '<div class="control-label">' +
                                '<label for="' + $itemFieldNameID + 'title">Título</label>' +
                            '</div>' +
                            '<div class="controls">'+
                                '<input id="' + $itemFieldNameID + 'title" type="text" size="20" value="" name="' + $itemFieldName + '[title]">' +
                            '</div>' +
                        '</div>' +
                        '<div class="control-group">' +
                            '<div class="control-label">' +
                                '<label for="' + $itemFieldNameID + 'desc">Descripción</label>' +
                            '</div>' +
                            '<div class="controls">'+
                                '<input id="' + $itemFieldNameID + 'desc" type="text" size="40" value="" name="' + $itemFieldName + '[description]">' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                ;

                this.theListImgSelector.append($item);
                jQuery('#' + $itemId).append($itemDetails);
                this.countImages = this.countImages + 1;
            } else {
                this.alert(data.error);
            }
        },

        submitFail: function (msg) {
            // this.alert(msg);
            this.alert(errormsgtoolong);
        },

        submitEnd: function () {
            this.theLoading.fadeOut();
        },

        change: function () {
            var files;
            var file;

            if (this.support.datauri) {
                files = this.theInputSelector.prop("files");

                if (files.length > 0) {
                    file = files[0];

                    if (this.isImageFile(file)) {
                        this.read(file);
                    }
                }
            } else {
                file = this.theInputSelector.val();

                if (this.isImageFile(file)) {
                    this.syncUpload();
                }
            }
        },

        isImageFile: function (file) {
            if (file.type) {
                return /^image\/\w+$/.test(file.type);
            } else {
                return /\.(jpg|jpeg|png|gif)$/.test(file);
            }
        },

        syncUpload: function () {
//            this.$avatarSave.click();
        },

        read: function (file) {
            var _this = this;
            var fileReader = new FileReader();

            fileReader.readAsDataURL(file);

            fileReader.onload = function () {
                _this.url = this.result
                _this.startCropper();
            };
        },

        startCropper: function () {
            var _this = this;

            if (this.activedCropper) {
                this.imagenFondo.cropper("setImgSrc", this.url);
            } else {
                this.imagenFondo = jQuery('<img src="' + this.url + '">');
                this.theContainerWrapper.empty().html(this.imagenFondo);

                this.imagenFondo.cropper({
                    aspectRatio: 16/9,
                    preview: this.theContainerPreview.selector,
                    done: function (data) {
                        var json = [
                            '{"x":' + data.x,
                            '"y":' + data.y,
                            '"height":' + data.height,
                            '"width":' + data.width + "}"
                        ].join();

                        _this.theImgData.val(json);
                    }
                });
                this.activedCropper = true;
            }
        },

        alert: function (msg) {
            var $alert = [
                '<div class="alert alert-danger joomCropper-alert">',
                '<h4 class="alert-heading">Mensaje</h4>',
                msg,
                '</div>'
            ].join("");

            this.theContainer.after($alert);
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    };
})(jQuery);