// START OBSERVE FIELD
jQuery.fn.observe_field = function (frequency, callback) {

    return this.each(function () {
        var element = $(this);
        var prev = element.val();

        var chk = function () {
            var val = element.val();
            if (prev != val) {
                prev = val;
                element.map(callback); // invokes the callback on the element
            }
        };
        chk();
        frequency = frequency * 1000; // translate to milliseconds
        var ti = setInterval(chk, frequency);
        // reset counter after user interaction
        element.bind('keyup', function () {
            ti && clearInterval(ti);
            ti = setInterval(chk, frequency);
        });
    });

};
// END OBSERVE FIELD

/**
 * Ajax loading page
 *
 * @param string url
 * @param string params
 * @param string update
 * @return void
 */
function ajaxPage(url, params, update)
{
    $.ajax({
        type: "GET",
        url: url,
        data: params,
        dataType: "html",
        cache: false,
        success: function (data) {
            $("#" + update).html(data)
        }
    });
}

/**
 * Ajax submit
 *
 * @param string url
 * @param string update
 * @param string params
 * @return void
 */
function ajaxSubmit(url, update, params)
{
    $.ajax({
        type: "POST",
        url: url,
        data: params,
        dataType: "html",
        cache: false,
        success: function (data) {
            $("#" + update).html(data)
        }
    });
}

/**
 * Ajax observe field
 *
 * @param string id
 * @param string url
 * @param string name
 * @param string update
 * @return void
 */
function ajaxObserve(id, url, name, update)
{
    $("#" + id).bind('change', function (event) {
        $.ajax({
            type: "GET",
            url: url,
            data: name + "=" + this.value,
            dataType: "html",
            cache: false,
            success: function (data) {
                $("#" + update).html(data);
            }
        });
    });
}

/**
 * Radio uncheck uses for form product
 *
 * @param string name
 * @param string url
 * @param string update
 * @return void
 */
function radioUncheck(name)
{
    var elements = document.getElementsByName(name);
    var length = elements.length;
    var i;
    for (i = 0; i < length; i++)
    {
        elements[i].checked = false;
    }
}

/**
 * Set class uses for table list
 */
function setSelectClass(e)
{
    $(".selected").removeClass('selected');
    e.setAttribute((document.all ? 'className' : 'class'), 'selected' + (e.getAttribute('class') ? ' ' + e.getAttribute('class') : ''));
}

/**
 * Check caps lock
 */
function checkCapsLock(e, divId) {
    var myKeyCode = 0;
    var myShiftKey = false;
    var div = $("#" + divId);

    // Internet Explorer 4+
    if (document.all) {
        myKeyCode = e.keyCode;
        myShiftKey = e.shiftKey;
    }
    // Netscape 4
    else if (document.layers) {
        myKeyCode = e.which;
        myShiftKey = (myKeyCode == 16) ? true : false;
    }
    // Netscape 6
    else if (document.getElementById) {
        myKeyCode = e.which;
        myShiftKey = (myKeyCode == 16) ? true : false;
    }

    // Upper case letters are seen without depressing the Shift key, therefore Caps Lock is on
    if ((myKeyCode >= 65 && myKeyCode <= 90) && !myShiftKey) {
        div.show('fast');
    }
    // Lower case letters are seen while depressing the Shift key, therefore Caps Lock is on
    else if ((myKeyCode >= 97 && myKeyCode <= 122) && myShiftKey) {
        div.show('fast');
    }
    else
    {
        div.hide('fast');
    }
}

function checkCyrillic(el, divId)
{
    var reg = new RegExp("[АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоӨөПпРрСсТтУуҮүФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя]");
    var div = $("#" + divId);

    if (el.value.match(reg))
    {
        div.show('fast');
    }
    else
    {
        div.hide('fast');
    }

    if (el.value == '')
    {
        div.hide('fast');
    }
}

// AUTOCOMPLETE COMBO
(function ($) {
    if ($.widget)
    {
        $.widget("ui.combobox", {
            _create: function () {
                var self = this;
                var select = this.element.hide();
                var input = $("<input>")
                        .insertAfter(select)
                        .autocomplete({
                            source: function (request, response) {
                                var matcher = new RegExp(request.term, "i");
                                response(select.children("option").map(function () {
                                    var text = $(this).text();
                                    if (this.value && (!request.term || matcher.test(text)))
                                        return {
                                            id: this.value,
                                            label: text.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + $.ui.autocomplete.escapeRegex(request.term) + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>"),
                                            value: text
                                        };
                                }));
                            },
                            delay: 0,
                            change: function (event, ui) {
                                if (!ui.item) {
                                    // remove invalid value, as it didn't match anything
                                    $(this).val(select.find("[value='" + select.val() + "']").text());
                                    return false;
                                }
                                select.val(ui.item.id);
                                self._trigger("selected", event, {
                                    item: select.find("[value='" + ui.item.id + "']")
                                });
                            },
                            selectFirst: true,
                            minLength: 0
                        })
                        .addClass("ui-widget ui-widget-content ui-corner-left");
                //$("<input type='button'>")
                $("<button type='button'>&nbsp;</button>")
                        .attr("tabIndex", -1)
                        .attr("title", "сонгох…")
                        .insertAfter(input)
                        .button({
                            icons: {
                                primary: "ui-icon-triangle-1-s"
                            },
                            text: false
                        }).removeClass("ui-corner-all")
                        .addClass("ui-corner-right")
                        .click(function () {
                            // close if already visible
                            if (input.autocomplete("widget").is(":visible")) {
                                input.autocomplete("close");
                                return;
                            }
                            // pass empty string as value to search for, displaying all results
                            input.autocomplete("search", "");
                            input.focus();
                        });
                input.val(select.find(':selected').text());
            }
        });
    }

})(jQuery);

(function ($) {

    $(".ui-autocomplete-input").live("autocompleteopen", function () {
        var autocomplete = $(this).data("autocomplete"),
                menu = autocomplete.menu;

        if (!autocomplete.options.selectFirst) {
            return;
        }

        menu.activate($.Event({
            type: "mouseenter"
        }), menu.element.children().first());
    });

}(jQuery));

// NUMBER TO WORDS
function NumberToWords() {

    var units = ["", "нэг", "хоёр", "гурван", "дөрвөн", "таван", "зургаан",
        "долоон", "найман", "есөн", "арван"];
    var teens = ["арван нэгэн", "арван хоёр", "арван гурван", "арван дөрвөн", "арван таван",
        "арван зургаан", "арван долоон", "арван найман", "арван есөн", "хорин"];
    var tens = ["", "арван", "хорин", "гучин", "дөчин", "тавин", "жаран",
        "далан", "наян", "ерөн"];

    var othersIntl = ["мянга", "сая", "тэр бум", "триллион"];

    var getBelowHundred = function (n) {
        if (n >= 100) {
            return "greater than or equal to 100";
        }
        ;
        if (n <= 10) {
            return units[n];
        }
        ;
        if (n <= 20) {
            return teens[n - 10 - 1];
        }
        ;
        var unit = Math.floor(n % 10);
        n /= 10;
        var ten = Math.floor(n % 10);
        var tenWord = (ten > 0 ? (tens[ten] + " ") : '');
        var unitWord = (unit > 0 ? units[unit] : '');
        return tenWord + unitWord;
    };

    var getBelowThousand = function (n) {
        if (n >= 1000) {
            return "greater than or equal to 1000";
        }
        ;
        var word = getBelowHundred(Math.floor(n % 100));

        n = Math.floor(n / 100);
        var hun = Math.floor(n % 10);
        word = (hun > 0 ? (units[hun] + " зуун ") : '') + word;

        return word;
    };

    return {
        numberToWords: function (n) {
            if (isNaN(n)) {
                return "Not a number";
            }
            ;

            var word = '';
            var val;

            val = Math.floor(n % 1000);
            n = Math.floor(n / 1000);

            word = getBelowThousand(val);

            othersArr = othersIntl;
            divisor = 1000;
            func = getBelowThousand;

            var i = 0;
            while (n > 0) {
                if (i == othersArr.length - 1) {
                    word = this.numberToWords(n) + " " + othersArr[i] + " "
                            + word;
                    break;
                }
                ;
                val = Math.floor(n % divisor);
                n = Math.floor(n / divisor);
                if (val != 0) {
                    word = func(val) + " " + othersArr[i] + " " + word;
                }
                ;
                i++;
            }
            ;
            return word;
        }
    }
}

// CONVERT TO NUMBER
function convertToNumber(v)
{
    if (!v)
    {
        return 0;
    }

    var original = isNaN(v) ? 0 : parseFloat(v);
    var result = Math.round(original * 100) / 100;

    return result;
}

// POPUP
var windowRef = null;

function openWindow(url)
{
    var windowFeatures = 'height=500,width=800,toolbar=0,scrollbars=0,status=0,resizable=0,location=0,menuBar=0';

    windowFeatures += ',height=500,width=800';
    windowFeatures += ',top=50,left=50';

    window.open(url, "", windowFeatures).focus();

//  if(/*@cc_on!@*/false){ //do this only in IE
//    windowRef = window.open(null, "", windowFeatures);
//    windowRef.close();
//  }
//  windowRef = window.open(url, "", windowFeatures);
//  if (!windowRef.opener) {
//    windowRef.opener = self;
//  }
//  windowRef.focus();
//  return windowRef;
}

/*
 * 
 * Зөвхөн тоон утга бичиж болдог болгох.
 */
jQuery.fn.forceNumeric = function () {
    return this.each(function () {
        $(this).keypress(function (e) {
            var charCode = (e.which) ? e.which : e.keyCode;
            var forbiddenKeys = new Array('a', 'n', 'c', 'x', 'v', 'j', 'w', 'r', 'z');
            var key;
            var isCtrl;
            if (window.event)
            {
                key = window.event.keyCode;     //IE
                if (window.event.ctrlKey)
                    isCtrl = true;
                else
                    isCtrl = false;
            }
            else
            {
                key = e.which;     //firefox
                if (e.ctrlKey)
                    isCtrl = true;
                else
                    isCtrl = false;
            }
            if (isCtrl) {
                for (i = 0; i < forbiddenKeys.length; i++)
                {
                    //case-insensitive comparation
                    if (forbiddenKeys[i].toLowerCase() == String.fromCharCode(key).toLowerCase())
                    {
                        return true;
                    }
                }
            }
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        });
        // $(this).bind('paste',function (e) {
        //to-do in future.
        //can't do it in Javascript for now
        // })
    });
};


/*
 * minifix for menu items
 */
$(document).ready(function () { //just for lolz... by Atu :)
    ul = $('#topmenu ul');
    items = $('#topmenu ul li');
    var curwidth = 0;
    $(items).each(function (i, val) {
        curwidth += $(val).width();
        if (curwidth > $(ul).width()) {
            $(val).after('<br/>');
            curwidth = 0;
        }
    });
    $(".multiSelect").multiselect();
});
/*
 * Textarea-ны одоогийн байгаа Cursor-ийн байрлалыг авна.
 */
$.fn.getCursorPosition = function () {
    var el = $(this).get(0);
    var pos = 0;
    if ('selectionStart' in el) {
        pos = el.selectionStart;
    } else if ('selection' in document) {
        el.focus();
        var Sel = document.selection.createRange();
        var SelLength = document.selection.createRange().text.length;
        Sel.moveStart('character', -el.value.length);
        pos = Sel.text.length - SelLength;
    }
    return pos;
};

/*
 * Textarea-д мөр болгоны maximum тэмдэгтийн тоог хязгаарлах
 * Туршилтаар ажиллаж байна. optimization needed
 * Paste event дээр paste хийгдсан утгыг барьж авахад хүндрэлтэй байв.
 */
jQuery.fn.lineLimit = function (limit) {
    if ($(this).is('textarea')) {
        limit = limit || 10;
        return this.each(function () {
            $(this).keyup(function (e) {
                var charCode = (e.which) ? e.which : e.keyCode;
                if (charCode != 8) {
                    var txt = $(this).val()
                    var pos = 0;
                    while (pos < txt.length) {
                        if (txt[pos] == "\n" || pos == 0) {
                            i = pos;
                            count = 0;
                            while (i <= pos + limit && i < txt.length) {
                                if (txt[i + 1] != "\n" && pos != 0) {
                                    count++;
                                } else if (pos == 0 && txt[i] != "\n") {
                                    count++;
                                } else {
                                    if (count <= limit) {
                                        break;
                                    }
                                }
                                if (count > limit && pos == 0) {
                                    $(this).val(txt.substr(0, i) + "\n" + txt.substr(i));
                                    break;
                                } else if ((count >= limit + 1 && pos != 0)) {
                                    $(this).val(txt.substr(0, i + 1) + "\n" + txt.substr(i + 1));
                                    break;
                                }
                                i++;
                            }
                        }
                        pos++;
                    }
                }
                return true;
            });
        });
    }
}

/*
 * Гурав гурван оронгоор таслах
 */
$.fn.digitSeparator = function (separator, ext) {
    if (typeof (separator) === 'undefined')
        separator = ',';
    if (typeof (ext) === 'undefined')
        ext = '';

    return this.each(function () {
        $(this).text($(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1" + separator) + ext);
    })
}

// DESKTOP NOTIFICATION
function setNotification(title, body)
{
    if (window.webkitNotifications)
    {
        if (window.webkitNotifications.checkPermission() == 0)
        {
            var n = window.webkitNotifications.createNotification('', title, body);
            n.onclick = function (x) {
                window.focus();
                this.cancel();
            };
            n.show();
        }
    }
}

function accountantNotification(url) {
    $.ajax({
        type: "POST",
        url: url,
        success: function (msg) {
            $("#resultNotification").html(msg);
        }
    })
}

function dateCompare(d1, d2)
{
    var date1 = Date.parse(d1);
    var date2 = Date.parse(d2);

    if (date1 < date2)
    {
        return 2;
    }

    if (date1 > date2)
    {
        return 1;
    }

    return 0;
}
/**
 * scroll to html element
 */
function scrollTo(id) {
    $('html,body').animate({
        scrollTop: $("#" + id).offset().top
    }, 'slow');
}