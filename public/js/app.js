webpackJsonp([1],{

/***/ "./resources/assets/js/app.js":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(jQuery, $) {
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

__webpack_require__("./resources/assets/js/bootstrap.js");

window.this_url = window.this_url || '';

/** Global Profiles Module */
var profiles = function ($, undefined) {

    /** @type {string} the current URL */
    var this_url = window.this_url;

    /**
     * Checks to see if an input is empty.
     *
     * @param {HTMLElement} input
     */
    var _input_is_empty = function _input_is_empty(input) {
        switch (input.getAttribute('type')) {
            case 'file':
                return input.files.length == 0;
            case 'checkbox':
                return !input.checked;
            // @todo: other input types
            default:
                return input.value == null || input.value == '';
        }
    };

    /**
     * Clears an input text or textarea row
     *
     * @param {HTMLElement} elem
     */
    var clear_row = function clear_row(elem) {
        parent_elem = $(elem).parent().parent();
        parent_elem.hide().find("input[type=text], input[type=url], input[type=month], input.clearable, textarea, select").val('');
    };

    /**
     * Toggles a class on an element or specified target.
     *
     * The class to toggle may be specified in the [data-toggle-class=] attribute.
     * Optional target element may be specified in the [data-target=] attribute.
     *
     * @param {Event} evt - jQuery event object
     * @this {HTMLElement} - the DOM element that was clicked
     */
    var toggle_class = function toggle_class(evt) {
        var $this = $(this);
        var $target = $this.data('target') ? $($this.data('target')) : $this;
        $target.toggleClass($this.data('toggle-class'));
    };

    /**
     * Replaces an existing FontAwesome icon with another.
     *
     * The replacement icon may be specified in the [data-newicon=] attribute.
     * Optional target (existing icon parent) element may be specified in the [data-target=] attribute.
     * Optional additional classes on the new icon may be specified in the [data-newiconclasses=] attribute.
     * Optional input element to check for emptiness may be specified in the [data-inputrequired=] attribute.
     *
     * @param {Event} evt - jQuery event object
     * @this {HTMLElement} - the DOM element for which the event was registered
     */
    var replace_icon = function replace_icon(evt) {
        if (this.dataset.inputrequired && _input_is_empty(document.querySelector(this.dataset.inputrequired))) {
            return;
        }

        var target = this.dataset.target ? document.querySelector(this.dataset.target) : this;
        var existing_icon = target.querySelector('svg');
        var replacement_icon = FontAwesome.icon({
            prefix: 'fas',
            iconName: this.dataset.newicon
        }, {
            classes: this.dataset.newiconclasses ? this.dataset.newiconclasses.split(' ') : []
        }).node[0];

        target.replaceChild(replacement_icon, existing_icon);

        // this shouldn't be needed, but for some reason Chrome occasionally fails
        // to propogate when a submit button is clicked.
        if (this.getAttribute('type') === 'submit') {
            $(this).closest('form').submit();
        }
    };

    var toggle_show = function toggle_show(evt) {
        var $this = $(this);
        var target = $this.data('toggle-target') || this;
        var toggle_value = $this.data('toggle-value') || true;
        var current_value = $this.val();

        if ($this.is('input[type=radio], input[type=checkbox]')) {
            current_value = $this.prop('checked');
        }

        if (current_value == toggle_value) {
            $(target).slideDown(200).find(':input').prop('disabled', false);
        } else {
            $(target).slideUp(200).find(':input').prop('disabled', true);
        }
    };

    /**
    * Registers and enables any tag editors on the page.
    * 
    * @return {void}
    */
    var registerTagEditors = function registerTagEditors() {
        $('.tags-editor').each(function (i, editor) {
            registerTagPicker('#' + editor.querySelector('select').id.replace('[]', '\\[\\]'));
        });
    };

    /**
     * Creates and initializes Bootstrap-Tagsinput / Typeahead.js Tag Picker.
     *
     * @param  {String} selector : CSS selector for the input field to register
     * @param  {String} api      : URL to the tag API
     * @return {void}
     */
    var registerTagPicker = function registerTagPicker(selector, api) {
        if (typeof api === 'undefined') api = this_url + '/tags/api';
        var $select = $(selector);
        if ($select.length === 0) return;

        var tagSearch = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('tag'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            limit: 50,
            remote: {
                url: api + '/search?name=%QUERY',
                wildcard: '%QUERY'
            }
        });

        $select.tagsinput({
            typeaheadjs: {
                name: 'taglist',
                displayKey: 'name'.en,
                limit: 75,
                source: tagSearch.ttAdapter()
            },
            freeInput: true,
            afterSelect: function afterSelect() {
                $select.tagsinput('input').val('');
            }
        });

        $select.tagsinput('input').on('typeahead:asyncrequest', function () {
            $(this).closest('.twitter-typeahead').css('background', 'no-repeat center url(' + this_url + '/img/ajax-loader.gif)');
        }).on('typeahead:asyncreceive typeahead:asynccancel', function () {
            $(this).closest('.twitter-typeahead').css('background-image', 'none');
        });

        $select.closest('.modal-content').find('.tagsInsertBtn').click(function (event) {
            postTags($select);
        });
    };

    /**
     * Posts updated tags to the API URL.
     * 
     * @param  {jQuery} $select the select element containing the tags
     * @return {void}
     */
    var postTags = function postTags($select) {
        var tags = $select.tagsinput('items');
        var formData = new FormData();
        formData.append('_token', $select.data('token'));
        formData.append('model', $select.data('model'));
        formData.append('id', $select.data('model-id'));
        for (var i = 0; i < tags.length; i++) {
            formData.append('tags[]', tags[i]);
        }

        $.ajax({
            method: "POST",
            url: $select.data('url'),
            dataType: 'json',
            processData: false,
            contentType: false,
            data: formData,
            success: function success(data, textStatus) {
                $('#' + $select.data('model-name') + '_tags_editor').modal('hide');
                $('#' + $select.data('model-name') + '_current_tags').html(data.view);
            },
            error: function error(xHr, textStatus, errorThrown) {
                alert(textStatus + ': ' + errorThrown);
            }
        });
    };

    return {
        clear_row: clear_row,
        toggle_class: toggle_class,
        toggle_show: toggle_show,
        replace_icon: replace_icon,
        registerTagEditors: registerTagEditors
    };
}(jQuery);

window.profiles = profiles;

$(document).ready(function () {

    __webpack_require__("./node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.js");

    $('.datepicker.year').datepicker({
        autoclose: true,
        assumeNearbyYear: true,
        clearBtn: true,
        forceParse: false,
        keepEmptyValues: true,
        minViewMode: 2,
        format: 'yyyy'
    });

    $('.datepicker.month').datepicker({
        autoclose: true,
        assumeNearbyYear: true,
        clearBtn: true,
        forceParse: false,
        keepEmptyValues: true,
        minViewMode: 1,
        format: 'yyyy/mm'
    });

    //show preview of uploaded image
    $('input[type="file"]').on('change', function () {
        $('label[for="' + this.id + '"]').addClass('active').text(this.files[0].name);
        $('#' + this.id + '-img').attr('src', window.URL.createObjectURL(this.files[0]));
    });

    //enable drag and drop sorting for items with sotable class
    if ($('.sortable').length > 0) {
        Sortable.create($('.sortable')[0], {
            handle: '.handle',
            scroll: true,
            scrollSpeed: 50,
            ghostClass: "sortable-ghost"
        });
    }

    //trigger clearing of elements when trash is clicked
    $('.actions .trash').on('click', function (e) {
        profiles.clear_row(this);
    });

    $('.back.btn').on('click', function (e) {
        window.history.go(-1);
    });

    $('.flash-message').on('click', function () {
        $(this).hide();
    }).animate({
        opacity: 0
    }, 5000);

    //animate anchor clicks on page
    $('a[href^="#"]:not([href="#"])').on('click', function (event) {

        var target = $($(this).attr('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top
            }, 1000);
        }
    });

    // register tag editors if tagsinput is loaded
    if (typeof $.fn.tagsinput === 'function' && typeof Bloodhound === 'function') {
        profiles.registerTagEditors();
    }

    $('[data-toggle=class]').on('click', profiles.toggle_class);
    $('[data-toggle=replace-icon]').on('click', profiles.replace_icon);
    $('[data-toggle=show]').on('change page_up', profiles.toggle_show).trigger('change');
});
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__("./node_modules/jquery/src/jquery.js"), __webpack_require__("./node_modules/jquery/src/jquery.js")))

/***/ }),

/***/ "./resources/assets/js/bootstrap.js":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* WEBPACK VAR INJECTION */(function(__webpack_provided_window_dot_jQuery) {/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__fortawesome_fontawesome__ = __webpack_require__("./node_modules/@fortawesome/fontawesome/index.es.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__fortawesome_fontawesome_free_solid__ = __webpack_require__("./node_modules/@fortawesome/fontawesome-free-solid/index.es.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__fortawesome_fontawesome_free_regular__ = __webpack_require__("./node_modules/@fortawesome/fontawesome-free-regular/index.es.js");
/** Load JavaScript dependencies */

window.Popper = __webpack_require__("./node_modules/popper.js/dist/esm/popper.js").default;

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
  window.$ = __webpack_provided_window_dot_jQuery = __webpack_require__("./node_modules/jquery/src/jquery.js");

  __webpack_require__("./node_modules/bootstrap/dist/js/bootstrap.js");
} catch (e) {}

/**
 * Font Awesome 5
 */



// import brands from '@fortawesome/fontawesome-free-brands';

__WEBPACK_IMPORTED_MODULE_0__fortawesome_fontawesome__["default"].library.add(__WEBPACK_IMPORTED_MODULE_1__fortawesome_fontawesome_free_solid__["default"]);
__WEBPACK_IMPORTED_MODULE_0__fortawesome_fontawesome__["default"].library.add(__WEBPACK_IMPORTED_MODULE_2__fortawesome_fontawesome_free_regular__["default"]);
// fontawesome.library.add(brands);

// Sortable
window.Sortable = __webpack_require__("./node_modules/sortablejs/Sortable.js");

// Typeahead Bloodhound
window.Bloodhound = __webpack_require__("./node_modules/corejs-typeahead/dist/typeahead.bundle.js");
/* WEBPACK VAR INJECTION */}.call(__webpack_exports__, __webpack_require__("./node_modules/jquery/src/jquery.js")))

/***/ }),

/***/ "./resources/assets/sass/app.scss":
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__("./resources/assets/js/app.js");
module.exports = __webpack_require__("./resources/assets/sass/app.scss");


/***/ })

},[0]);
//# sourceMappingURL=app.js.map