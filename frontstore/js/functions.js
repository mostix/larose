var base_url = "https://www.larose.bg/";
shortcut = {
    all_shortcuts: {},
    add: function(e, t, n) {
        var r = {
            type: "keydown",
            propagate: false,
            disable_in_input: false,
            target: document,
            keycode: false
        };
        if (!n) n = r;
        else {
            for (var i in r) {
                if (typeof n[i] == "undefined") n[i] = r[i]
            }
        }
        var s = n.target;
        if (typeof n.target == "string") s = document.getElementById(n.target);
        var o = this;
        e = e.toLowerCase();
        var u = function(r) {
            r = r || window.event;
            if (n["disable_in_input"]) {
                var i;
                if (r.target) i = r.target;
                else if (r.srcElement) i = r.srcElement;
                if (i.nodeType == 3) i = i.parentNode;
                if (i.tagName == "INPUT" || i.tagName == "TEXTAREA") return
            }
            if (r.keyCode) code = r.keyCode;
            else if (r.which) code = r.which;
            var s = String.fromCharCode(code).toLowerCase();
            if (code == 188) s = ",";
            if (code == 190) s = ".";
            var o = e.split("+");
            var u = 0;
            var a = {
                "`": "~",
                1: "!",
                2: "@",
                3: "#",
                4: "$",
                5: "%",
                6: "^",
                7: "&",
                8: "*",
                9: "(",
                0: ")",
                "-": "_",
                "=": "+",
                ";": ":",
                "'": '"',
                ",": "<",
                ".": ">",
                "/": "?",
                "\\": "|"
            };
            var f = {
                esc: 27,
                escape: 27,
                tab: 9,
                space: 32,
                "return": 13,
                enter: 13,
                backspace: 8,
                scrolllock: 145,
                scroll_lock: 145,
                scroll: 145,
                capslock: 20,
                caps_lock: 20,
                caps: 20,
                numlock: 144,
                num_lock: 144,
                num: 144,
                pause: 19,
                "break": 19,
                insert: 45,
                home: 36,
                "delete": 46,
                end: 35,
                pageup: 33,
                page_up: 33,
                pu: 33,
                pagedown: 34,
                page_down: 34,
                pd: 34,
                left: 37,
                up: 38,
                right: 39,
                down: 40,
                f1: 112,
                f2: 113,
                f3: 114,
                f4: 115,
                f5: 116,
                f6: 117,
                f7: 118,
                f8: 119,
                f9: 120,
                f10: 121,
                f11: 122,
                f12: 123
            };
            var l = {
                shift: {
                    wanted: false,
                    pressed: false
                },
                ctrl: {
                    wanted: false,
                    pressed: false
                },
                alt: {
                    wanted: false,
                    pressed: false
                },
                meta: {
                    wanted: false,
                    pressed: false
                }
            };
            if (r.ctrlKey) l.ctrl.pressed = true;
            if (r.shiftKey) l.shift.pressed = true;
            if (r.altKey) l.alt.pressed = true;
            if (r.metaKey) l.meta.pressed = true;
            for (var c = 0; k = o[c], c < o.length; c++) {
                if (k == "ctrl" || k == "control") {
                    u++;
                    l.ctrl.wanted = true
                } else if (k == "shift") {
                    u++;
                    l.shift.wanted = true
                } else if (k == "alt") {
                    u++;
                    l.alt.wanted = true
                } else if (k == "meta") {
                    u++;
                    l.meta.wanted = true
                } else if (k.length > 1) {
                    if (f[k] == code) u++
                } else if (n["keycode"]) {
                    if (n["keycode"] == code) u++
                } else {
                    if (s == k) u++;
                    else {
                        if (a[s] && r.shiftKey) {
                            s = a[s];
                            if (s == k) u++
                        }
                    }
                }
            }
            if (u == o.length && l.ctrl.pressed == l.ctrl.wanted && l.shift.pressed == l.shift.wanted && l.alt.pressed == l.alt.wanted && l.meta.pressed == l.meta.wanted) {
                t(r);
                if (!n["propagate"]) {
                    r.cancelBubble = true;
                    r.returnValue = false;
                    if (r.stopPropagation) {
                        r.stopPropagation();
                        r.preventDefault()
                    }
                    return false
                }
            }
        };
        this.all_shortcuts[e] = {
            callback: u,
            target: s,
            event: n["type"]
        };
        if (s.addEventListener) s.addEventListener(n["type"], u, false);
        else if (s.attachEvent) s.attachEvent("on" + n["type"], u);
        else s["on" + n["type"]] = u
    },
    remove: function(e) {
        e = e.toLowerCase();
        var t = this.all_shortcuts[e];
        delete this.all_shortcuts[e];
        if (!t) return;
        var n = t["event"];
        var r = t["target"];
        var i = t["callback"];
        if (r.detachEvent) r.detachEvent("on" + n, i);
        else if (r.removeEventListener) r.removeEventListener(n, i, false);
        else r["on" + n] = false
    }
};
shortcut.add("esc", function() {
    $("#loginform").hide();
    $("#modal_window_backgr").hide();
    $("#modal_window").hide().html("");
    $(".warning_field").remove()
});
var slider_rotation_time = 6e3;
var slider_enable_rotation = 0;
var slider_timer;
var slider_current = 1

function getAbsolutePath() {
    var e = window.location;
    var t = e.pathname.substring(0, e.pathname.lastIndexOf("/") + 1);
    return e.href.substring(0, e.href.length - ((e.pathname + e.search + e.hash).length - t.length))
}

function ConfirmCookiesPolicy() {
  createCookie('cookie_policy','1',168);
  $("#cookies_policy").remove();
}

function createCookie(e, t, n) {
    var r = "";
    if (n) {
        var i = new Date;
        i.setTime(i.getTime() + n * 60 * 60 * 1e3);
        r = "; expires=" + i.toGMTString()
    }
    document.cookie = e + "=" + t + r + "; path=/"
}

function readCookie(e) {
    var t = e + "=";
    var n = document.cookie.split(";");
    for (var r = 0; r < n.length; r++) {
        var i = n[r];
        while (i.charAt(0) == " ") i = i.substring(1, i.length);
        if (i.indexOf(t) == 0) return i.substring(t.length, i.length)
    }
    return null
}

function eraseCookie(e) {
    createCookie(e, "", -3)
}

function new_freecap() {
    if (document.getElementById) {
        thesrc = document.getElementById("freecap").src;
        thesrc = thesrc.substring(0, thesrc.lastIndexOf(".") + 4);
        document.getElementById("freecap").src = thesrc + "?" + Math.round(Math.random() * 1e5)
    } else {
        alert("Sorry, cannot autoreload freeCap image\nSubmit the form and a new freeCap will be loaded")
    }
}

function ShowAjaxLoader() {
  $("#ajax_loader_backgr, #ajax_loader").show();
  setTimeout(function () { $("#ajax_loader_backgr, #ajax_loader").hide(); }, 5000);
}

function HideAjaxLoader() {
  $("#ajax_loader_backgr, #ajax_loader").hide();
//  setTimeout(function () { $("#ajax_loader_backgr, #ajax_loader").hide(); }, 250);
}

function insertParam(e, t) {
    e = escape(e);
    t = escape(t);
    var n = document.location.search.substr(1).split("&");
    if (n == "") {
        document.location.search = "?" + e + "=" + t
    } else {
        var r = n.length;
        var i;
        while (r--) {
            i = n[r].split("=");
            if (i[0] == e) {
                i[1] = t;
                n[r] = i.join("=");
                break
            }
        }
        if (r < 0) {
            n[n.length] = [e, t].join("=")
        }
        document.location.search = n.join("&")
    }
}

function updateURLParameter(url, param, paramVal){
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (var i=0; i<tempArray.length; i++){
            if(tempArray[i].split('=')[0] != param){
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }

    var rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
}

function SliderRotator(e) {
    slider_enable_rotation = e;
    if (slider_enable_rotation == 0) {
        $("#slider_line_change").stop()
    } else {
        $("#slider_line_change").animate({
            width: "100%"
        }, slider_rotation_time, function() {
            $("#slider_line_change").css({
                width: "0px"
            })
        })
    }
    clearTimeout(slider_timer);
    slider_timer = setTimeout("SliderAutoRotate()", slider_rotation_time)
}

function SliderAutoRotate() {
    if (slider_enable_rotation == 1) {
        slider_current < 4 ? slider_current++ : slider_current = 1;
        SliderMakeRotation(slider_current)
    }
    slider_timer = setTimeout("SliderAutoRotate()", slider_rotation_time);
    $("#slider_line_change").animate({
        width: "100%"
    }, slider_rotation_time, function() {
        $("#slider_line_change").css({
            width: "0px"
        })
    })
}

function SliderMakeRotation(e) {
    var t = $(".slider_box").css("width");
    var n = $(".slider_box").length;
    for (i = 1; i < 5; i++) {
        if (i == e) {
            var r = parseInt(e) - 1;
            var s = "-" + parseInt(t) * r;
            $(".slider_box .slider_img").animate({
                opacity: "0",
                left: "120%"
            });
            $(".slider_box h2").animate({
                top: "-100px"
            });
            $(".slider_box p").animate({
                opacity: "0"
            });
            $(".slider_box a").animate({
                top: "400px"
            });
            $(".slider_box").removeClass("slider_box_current");
            $(".slider_thumb").removeClass("current_thumb");
            if (r == n) {
                $("#slider_thumb_1").addClass("current_thumb");
                $("#slider_stripe").stop().animate({
                    left: 0
                }, 500, function() {
                    $("#slider_box_1 .slider_img").animate({
                        opacity: "1",
                        left: "60%"
                    }, 300);
                    $("#slider_box_1 h2").animate({
                        top: "60px"
                    }, 700);
                    $("#slider_box_1 p").animate({
                        opacity: "1"
                    }, 700);
                    $("#slider_box_1 a").animate({
                        top: "270px"
                    }, 700)
                });
                $("#current_slider").val("1");
                slider_current = 1
            } else {
                $("#slider_thumb_" + e).addClass("current_thumb");
                $("#slider_stripe").stop().animate({
                    left: s
                }, 500, function() {
                    $("#slider_box_" + e + " .slider_img").animate({
                        opacity: "1",
                        left: "60%"
                    }, 300);
                    $("#slider_box_" + e + " h2").animate({
                        top: "60px"
                    }, 700);
                    $("#slider_box_" + e + " p").animate({
                        opacity: "1"
                    }, 700);
                    $("#slider_box_" + e + " a").animate({
                        top: "270px"
                    }, 700)
                });
                $("#current_slider").val(e);
                slider_current = e
            }
        }
    }
}

function GetPuzzelSliderImages() {
    var e = $(".tabs.active").attr("data-id");
    $.ajax({
        url: "/frontstore/ajax/get-puzzel-slider-images.php",
        type: "POST",
        data: {
            puzzle_image_slider: e
        }
    }).done(function(e) {
        $("#sortable").html(e)
    }).fail(function(e) {
        console.log(e)
    })
}

function OpenModalWindow(data) {
  $("#modal_window").html(data);
  $("#modal_window").append('<a href="javascript:;" class="close_btn"></a>');
  CalculateModalWindowSize();
  $("#modal_window_backgr").show();
  $("#modal_window").show();
  $("#modal_window .close_btn").click(function() {
    $("#modal_window_backgr").hide();
    $("#modal_window").hide().html("");
  });
}

function CalculateModalWindowSize() {
  var html_width = $(window).width();
  var html_height = $(window).height();
  var modal_window_width = $("#modal_window").width();
  var modal_window_height = $("#modal_window").height();
  //alert(modal_window_width);alert(modal_window_height);
  var modal_window_left = parseInt(html_width-modal_window_width-10)/2.1;
  var modal_window_top = parseInt(html_height-modal_window_height-10)/2.1;
  //alert(modal_window_top);alert(modal_window_left);
  $("#modal_window").css({top: modal_window_top+"px",left: modal_window_left+"px"})
}

function LogInUser(e) {
    ShowAjaxLoader();
    var t = $("#login_customer_email").val();
    var n = $("#login_customer_password").val();
    var r = $("#login_customer_captcha").val();
    var i = $("#current_page_path_string").val();
    $.ajax({
        url: "/frontstore/ajax/log-in-user.php",
        type: "POST",
        data: {
            current_lang: e,
            customer_email: t,
            customer_password: n,
            customer_captcha: r
        }
    }).done(function(e) {
        if (e == "") {
            window.location = i
        } else {
            $("#loginform div").html(e)
        }
        event.preventDefault();
        HideAjaxLoader();
    }).fail(function(e) {
        console.log(e)
    })
}

function CheckIfUserEmailIsValid(e, t) {
    $.ajax({
        url: "/frontstore/ajax/check-if-user-email-is-valid.php",
        type: "POST",
        data: {
            customer_email: e,
            current_lang: t
        }
    }).done(function(e) {
        if (e == "") {
            $("#customer_email_is_valid").html("");
            $(".email").removeClass("form-error");
            $(".email span").html("");
            $("#customer_email_status").val("ok")
        } else {
            $("#customer_email_is_valid").html(e);
            $(".email").addClass("form-error");
            $("#customer_email_status").val("error")
        }
    }).fail(function(e) {
        console.log(e)
    })
}

function CheckIfUserEmailIsValidForUpdate(e, t) {
    var n = $("#customer_id").val();
    $.ajax({
        url: "/frontstore/ajax/check-if-user-email-is-valid.php",
        type: "POST",
        data: {
            customer_id: n,
            customer_email: e,
            current_lang: t
        }
    }).done(function(e) {
        if (e == "") {
            $("#customer_email_is_valid").html("");
            $("#customer_email_status").val("ok")
        } else {
            $("#customer_email_is_valid").html(e);
            $("#customer_email_status").val("error")
        }
    }).fail(function(e) {
        console.log(e)
    })
}

function ValidateUserPassword(e, t) {
    if ($("#customer_email_status").val() == "error") return;
    $.ajax({
        url: "/frontstore/ajax/check-if-user-password-is-valid.php",
        type: "POST",
        data: {
            customer_password: e,
            current_lang: t
        }
    }).done(function(e) {
        if (e == "") {
            $("#customer_password_is_valid").html("")
        } else {
            $("#customer_password_is_valid").html(e);
            //$("#customer_password").focus()
        }
    }).fail(function(e) {
        console.log(e)
    })
}

function AddOptionValueForSort(e, t) {
    if (t == "color") {
        var n = $("#selected_colors_ids").val();
        $("#selected_colors_ids").val(e + "," + n)
    } else {
        var r = $("#selected_option_value_ids").val();
        $("#selected_option_value_ids").val(e + "," + r)
    }
}

function DisplayCountryAddressForm(country_id) {
    $(".customer_address_country_id").val(country_id);
    if (country_id == 33) {
        //alert("bg");return;
        $("#not_bg_form").hide();
        $("#bg_form").show()
    } else {
        $("#bg_form").hide();
        $("#not_bg_form").show()
    }
}

function RemoveCategoryFromProduct(e, t) {
    if (t == "color") {
        var n = $("#selected_colors_ids").val();
        var r = n.replace(e + ",", "");
        $("#selected_colors_ids").val(r)
    } else {
        var i = $("#selected_option_value_ids").val();
        var s = i.replace(e + ",", "");
        $("#selected_option_value_ids").val(s)
    }
}

var QueryString = function () {
  // This function is anonymous, is executed immediately and 
  // the return value is assigned to QueryString!
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
        // If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = decodeURIComponent(pair[1]);
        // If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
      query_string[pair[0]] = arr;
        // If third or later entry with this name
    } else {
      query_string[pair[0]].push(decodeURIComponent(pair[1]));
    }
  } 
    return query_string;
}();

function GetOptions() {
    var e = [];
    var t = $(".options_header input, .options_header select").serialize();
    //console.log(t);
    if (t != "") {
        e.push(t)
    }
    return e.join("&")
}

function GetColorOptions() {
    var e = [];
    var t = $(".colors input").serialize();
    if (t != "") {
        e.push(t)
    }
    return e.join("&")
}

function PushSortProductsState(type,language_id,value) {
  var i = $("#cid").val(),
      cpid = $("#cpid").val();
  if(type == "sort") {
    var url = window.location.protocol + "//" + window.location.host + decodeURI(window.location.pathname);
    var urlParams = "?cid=" + i+"&cpid=" + cpid + "&" + GetOptions();
    History.pushState({language_id: language_id,option: value,type: type}, document.title, url+urlParams);
  }
  else if(type == "pagination") {
    var c = window.location.href,
        d = updateURLParameter(c, "offset", value);
    History.pushState({language_id: language_id,offset: value,type: type}, document.title, d);
  }
}
            
function LoadPaginationProductsForCategory(e) {
    if (e == "") return;
    ShowAjaxLoader();
    var n = $("#order_by_price").val();
    var r = $(".current_cat_href").val();
    var i = $(".cd_pretty_url").val();
    var s = $(".current_cat_id").val();
    var o = $(".language_id").val();
    var u = $(".products_count").val();
    $.ajax({
        url: "/frontstore/ajax/load-pagination-products-for-category.php",
        type: "POST",
        data: {
            order_by_price: n,
            current_cat_href: r,
            cd_pretty_url: i,
            current_category_id: s,
            language_id: o,
            offset: e,
            products_count: u
        }
    }).done(function(n) {
        $(".product_list").html(n);
        $('html, body').animate({scrollTop:0}, 'slow');
        HideAjaxLoader();
    }).fail(function(e) {
        console.log(e)
    })
}

function SortProductsByOptionValue(language_id, option) {
    ShowAjaxLoader();
    var r = $(".page-heading").html();
    var i = $(".current_cat_href").val();
    var s = $(".cd_pretty_url").val();
    var o = $(".current_cat_id").val();
    var g = "sort";
    var current_lang = $(".current_lang").val();
    var u = $("#cid").val();
    var cpid = $("#cpid").val();
    if(language_id === undefined) {
      language_id = $(".language_id").val();
    }
    var f = GetOptions();
    var l = base_url + i + "?cid=" + u +"&cpid=" + cpid;
    var c = "&" + f;
    $.ajax({
        url: "/frontstore/ajax/sort-products-by-option-value.php?" + c,
        type: "POST",
        data: {
            current_category_id: o,
            cd_name: r,
            current_lang:current_lang,
            language_id: language_id,
            current_cat_href: i,
            cd_pretty_url: s,
            option:option
        }
    }).done(function(e) {
        $(".product_list").html(e);
        
        HideAjaxLoader()
    }).fail(function(e) {
        console.log(e)
    })
}

function SortProductsByPrice(e) {
    ShowAjaxLoader();
    var n = $("#tabs_list li.active a").html();
    var r = $(".current_cat_href").val();
    var i = $(".current_cat_id").val();
    var s = $("#cid").val();
    var o = GetColorOptions();
    var u = GetOptions();
    var a = base_url + r + "?cid=" + s;
    var f = o == "" ? "" : "&" + o;
    f += u == "" ? "" : "&" + u;
    $.ajax({
        url: "/frontstore/ajax/sort-products-by-price.php",
        type: "POST",
        data: {
            current_category_id: i,
            cd_name: n,
            language_id: e,
            current_cat_href: r
        }
    }).done(function(e) {
        $(".product_list").html(e);
        window.history.pushState(null, null, a + f);
        HideAjaxLoader()
    }).fail(function(e) {
        console.log(e)
    })
}

function LoadOptionValuesForCategory(e, t) {
    ShowAjaxLoader();
    $.ajax({
        url: "/frontstore/ajax/load-option-values-for-category.php",
        type: "POST",
        data: {
            current_category_id: e,
            language_id: t
        }
    }).done(function(e) {
        $(".col-lg-2.category-list").html(e);
        var clear_search = true;
        SortProductsByOptionValue(t,clear_search);
        HideAjaxLoader()
    }).fail(function(e) {
        console.log(e)
    })
}

function DeleteCustomerAddress() {
  setTimeout(function() {
      $("#ajax_loader").hide()
  }, 5e3);
  var e = $(".delete_address_btn.active").attr("data-id");
  $.ajax({
      url: "/frontstore/ajax/delete-customer-address.php",
      type: "POST",
      data: {
          customer_address_id: e
      }
  }).done(function(t) {
      $("#modal_confirm").dialog("close");
      $("#customer_address_" + e).remove()
  }).fail(function(e) {
      console.log(e)
  })
}

$(document).on('click', '#layer_cart .cross, #layer_cart .continue, .layer_cart_overlay', function (e) {
  e.preventDefault();
  $('.layer_cart_overlay').hide();
  $('#layer_cart').fadeOut('fast');
});
  
function AddProductToCart(product_id, language_id) {
  ShowAjaxLoader();
  var product_block = "#product_block_"+product_id;
  var product_isbn = $(product_block+" .product_isbn").val();
  var product_price = $(product_block+" .product_price").val();
  var pd_price = $(product_block+" .pd_price").val();
  var product_name = $(product_block+" .product_name").val();
  var product_url = $(product_block+" .product_url").val();
  var product_qty = $(product_block+" .product_qty").val();
  var product_img_src = $(product_block+" .product_img").val();
  //alert(i);return;
  $.ajax({
      url: "/frontstore/ajax/add-product-to-cart.php",
      type: "POST",
      data: {
          product_id: product_id,
          language_id: language_id,
          product_isbn: product_isbn,
          product_price: product_price,
          pd_price: pd_price,
          product_name: product_name,
          product_url: product_url,
          product_qty: product_qty,
          product_img_src: product_img_src
      }
  }).done(function() {

      if(pd_price !== "") {
        product_price = pd_price;
      }
      $(".layer_cart_overlay").show();
      $("#layer_cart").fadeIn();
      $(".layer_cart_img").html("<img src='"+product_img_src+"' alt='"+product_name+"'>");
      $(".layer_cart_product_info #layer_cart_product_title").html(product_name);
      $(".layer_cart_product_info #layer_cart_product_quantity").html(product_qty);
      $(".layer_cart_product_info #layer_cart_product_price").html(product_price);
      var cart_product_qty = $(".layer_cart_cart #cart_product_qty").val();
      var cart_products_price = $(".layer_cart_cart .price_text").html();
      var cart_product_qty_new = parseInt(cart_product_qty)+parseInt(product_qty);
      if(cart_product_qty_new == 1) {
        $(".layer_cart_cart .ajax_cart_product_txt_s").addClass("unvisible");
        $(".layer_cart_cart .ajax_cart_product_txt").removeClass("unvisible");
      }
      else {
        $(".layer_cart_cart .ajax_cart_product_txt").addClass("unvisible");
        $(".layer_cart_cart .ajax_cart_product_txt_s").removeClass("unvisible");
        $(".layer_cart_cart .ajax_cart_product_txt_s .ajax_cart_quantity").html(cart_product_qty_new);
      }
      $(".layer_cart_cart #cart_product_qty").val(cart_product_qty_new);
      $(".layer_cart_cart .price_text").html((parseFloat(product_price)+parseFloat(cart_products_price)).toFixed(2));

      UpdateShoppingCart();
      
      HideAjaxLoader()
  }).fail(function(e) {
      console.log(e)
  })
}

function AddProductRating(product_id) {
  ShowAjaxLoader();
  var rating_stars_value = $("#rating_stars_value").val();
  $.ajax({
    url: "/frontstore/ajax/add-product-rating.php",
    type: "POST",
    data: {
        product_id: product_id,
        rating_stars_value:rating_stars_value
    }
  }).done(function(rating) {
    
    $("#rating_stars").html(rating);
    
    HideAjaxLoader()
  }).fail(function(e) {
    console.log(e)
  })
}

function AddProductToWishlist(product_id) {
  ShowAjaxLoader();
  var product_name = $("#product_block_"+product_id+" .product_name").val();
  var product_url = $("#product_block_"+product_id+" .product_url").val();
  var product_price = $("#product_block_"+product_id+" .product_price").val();
  var pd_price = $("#product_block_"+product_id+" .pd_price").val();
  var product_image = $("#product_block_"+product_id+" .product_img").val();
  var current_lang = $("#current_lang").val();
  $.ajax({
      url: "/frontstore/ajax/add-product-to-wishlist.php",
      type: "POST",
      data: {
        product_id: product_id,
        product_name:product_name,
        product_url:product_url,
        product_price:product_price,
        pd_price:pd_price,
        product_image:product_image,
        current_lang:current_lang
      }
  }).done(function(result) {
      HideAjaxLoader();

      OpenModalWindow(result);
  }).fail(function(e) {
      console.log(e)
    })
}

function UpdateProductCount(product_id, operator) {
  setTimeout(function() {
      $("#ajax_loader").hide()
  }, 5e3);
  $.ajax({
      url: "/frontstore/ajax/update-product-count-in-cart.php",
      type: "POST",
      data: {
        product_id: product_id,
        operator: operator
      }
  }).done(function(products_list) {
    
      if($("#cart_block_product_"+product_id).length) {
        $("#cart_block_product_"+product_id).remove();
      }
      $(".cart_block .cart_block_list").html(products_list);
      
      HideAjaxLoader();
  }).fail(function(e) {
      console.log(e)
  })
}

function UpdateShoppingCart(product_id) {
  setTimeout(function() {
      $("#ajax_loader").hide()
  }, 5e3);
  $.ajax({
      url: "/frontstore/ajax/update-shopping-cart.php",
      type: "POST",
      data: {
        product_id:product_id
      }
  }).done(function(products_list) {

      if($("#cart_block_product_"+product_id).length) {
        $("#cart_block_product_"+product_id).remove();
      }
      if($("#cart_summary #product_"+product_id).length) {
        $("#cart_summary #product_"+product_id).remove();
        CalculateTotalPrice();
      }
      $(".cart_block .cart_block_list").html(products_list);

      HideAjaxLoader();
  }).fail(function(e) {
      console.log(e)
  })
}

function DeleteProductFromCart(product_id) {
  setTimeout(function() {
      $("#ajax_loader").hide()
  }, 5e3);
  $.ajax({
      url: "/frontstore/ajax/delete-product-from-cart.php",
      type: "POST",
      data: {
        product_id:product_id
      }
  }).done(function(products_list) {

      $(".cart_block .cart_block_list").html(products_list);

      //$(".cart-inner media-body .ajax_cart_total").html();
  }).fail(function(e) {
      console.log(e)
  })
}

function CalculateTotalPrice() {
  var total_order_price_value = "0.00";
  //alert("1");
  $(".product_price_total").each(function() {
    total_order_price_value = (parseFloat(total_order_price_value)+parseFloat($(this).html())).toFixed(2);
  });
  $("#total_order_price").html(total_order_price_value);
}

function CalculateTotalPriceWithDelivery(delivery_price) {
  var total_order_price_value = delivery_price;
  $(".product_price_total").each(function() {
    total_order_price_value = (parseFloat(total_order_price_value)+parseFloat($(this).html())).toFixed(2);
  });
  //console.log(total_order_price_value);
  
  $("#total_order_price").html(total_order_price_value);
}

function GetSpeedyServices() {
  ShowAjaxLoader();
  var user_access = $(".second_menu .active .active .third_menu_link").attr("users-rights-access");
  if(user_access == undefined) { user_access = $(".second_menu .active .second_menu_link").attr("users-rights-access");}
  if(user_access == undefined) { user_access = $("#menu .selected .active_first_level").attr("users-rights-access");}
  var city_type = $("#customer_address_city_type").val();
  var city = $("#customer_address_city").val();
  var postcode = $("#customer_address_postcode").val();
  var taking_date = $("#taking_date").val();
  $.ajax({
  url: "/frontstore/ajax/get-speedy-services.php",
  type:"POST",
  data:{
    user_access:user_access,
    city_type:city_type,
    city:city,
    postcode:postcode,
    taking_date:taking_date
    }
  }).done(function(list_services){

    $("#list_services #service").html(list_services);
    GetSpeedyOffices();
    GetAllowanceFixedTimeDelivery();

    HideAjaxLoader();
  }).fail(function(error){
    console.log(error);
  })
}

function GetAllowanceFixedTimeDelivery() {
  var fixed_time_is_allowed = $("#service option:selected").attr("fixed_time");
  //alert(fixed_time_is_allowed);
  if(fixed_time_is_allowed == 1) {
    $("#fixed_hour_box").addClass("active");
    $("#fixed_hour").attr("disabled",false);
    $("#fixed_hour").removeClass("disabled");
    $("#fixed_minutes").attr("disabled",false);
    $("#fixed_minutes").removeClass("disabled");
  }
  else {
    $("#fixed_hour_box").removeClass("active");
    $("#fixed_hour").attr("disabled",true);
    $("#fixed_hour").addClass("disabled");
    $("#fixed_minutes").attr("disabled",true);
    $("#fixed_minutes").addClass("disabled");
  }
}

function moveToNextField(x, y) {
  if(y.length == x.maxLength) {
    var next = x.tabIndex;
    document.speedy_address.fixed_minutes.focus();
  }
}

function GetSpeedyOffices() {
  ShowAjaxLoader();
  var user_access = $(".second_menu .active .active .third_menu_link").attr("users-rights-access");
  if(user_access == undefined) { user_access = $(".second_menu .active .second_menu_link").attr("users-rights-access");}
  if(user_access == undefined) { user_access = $("#menu .selected .active_first_level").attr("users-rights-access");}
  var city_type = $("#customer_address_city_type").val();
  var city = $("#customer_address_city").val();
  $.ajax({
  url: "/frontstore/ajax/get-speedy-offices.php",
  type:"POST",
  data:{
    user_access:user_access,
    city_type:city_type,
    city:city
    }
  }).done(function(list_offices){

    if(list_offices != "") {
      $("#speedy_offices_box").show();
      $("#speedy_offices_box #speedy_offices_list").html(list_offices);
    }

    HideAjaxLoader();
  }).fail(function(error){
    console.log(error);
  })
}

function ToggleSpeedyOffices() {
  var speedy_offices_list_display = $("#speedy_offices_list").css("display");
  //alert(speedy_offices_list_display);
  if(speedy_offices_list_display == "none") {
    $("#speedy_offices_list").show();
  }
  else {
    $("#speedy_offices_list").hide();
  }
}

function PrintAreaById(printable_area_id) {
  var printContents = document.getElementById(printable_area_id).innerHTML;
  var originalContents = document.body.innerHTML;
  document.body.innerHTML = "<html><head><title></title></head><body>" + printContents + "</body>";
  window.print();
  document.body.innerHTML = originalContents;
//  var url = "/frontstore/print-area.php?printContents="+encodeURIComponent(printContents);
//  window.open(url,'mywindow','status=no,location=no,resizable=yes,scrollbars=yes,width=950,height=800,left=0,top=0,screenX=0,screenY=0');
}

function GetTimeIntervals(selected_date) {
  var order_total = $("#order_total").val();
  var today = new Date();
  var tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  today = $.datepicker.formatDate('yy-mm-dd', today);
  
  $.ajax({
  url: "/frontstore/ajax/get-shipping-time-intervals.php",
  type:"POST",
  data:{
    selected_date:selected_date
    }
  }).done(function(time_intervals){

    $("#time_interval").html(time_intervals);
    
    var delivery_price = 0.00;
    if(selected_date > today) {
      if(order_total <= 50) {
        delivery_price = 5.00;
      }
    }
    else {
      delivery_price = 10.00;
    }
    if(selected_date == "2019-02-14") {
      $("p.date_info").removeClass("hidden");
      delivery_price = 10.00;
    }
    
    $("#total_shipping .price").html(delivery_price.toFixed(2));
    $("#delivery_price").val(delivery_price.toFixed(2));
    
    CalculateTotalPriceWithDelivery(delivery_price);

    HideAjaxLoader();
  }).fail(function(error){
    console.log(error);
  })
}
function GetTimeIntervals(i) {
    var s = $("#order_total").val(),
        n = new Date,
        t = new Date;
    shipping_site_id = $("#shipping_site_id").val(), t.setDate(t.getDate() + 1), n = $.datepicker.formatDate("yy-mm-dd", n), $.ajax({
        url: "/frontstore/ajax/get-shipping-time-intervals.php",
        type: "POST",
        data: {
            selected_date: i,
            current_lang: current_lang
        }
    }).done(function(t) {
        $("#time_interval").html(t);
        var e = 0;
        215 == shipping_site_id && n < i ? s <= 50 && (e = 5) : e = 10;
        if(i == "2019-02-14" || i == "2019-03-08" || i == "2019-03-09") {
          if(i == "2019-02-14" || i == "2019-03-08") $("p.date_info").removeClass("hidden");
          e = 10.00;
        }
        $("#total_shipping .price").html(e.toFixed(2)), $("#delivery_price").val(e.toFixed(2)), CalculateTotalPriceWithDelivery(e), HideAjaxLoader()
    }).fail(function(t) {
        console.log(t)
    })
}
function ShowTimeIntervals(selected_date) {
  var order_total = $("#order_total").val();
  var today = new Date();
  var hours = today.getHours();
  var minutes = ('0'+today.getMinutes()).slice(-2);
  var current_hour = hours+""+minutes;
  var tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  today = $.datepicker.formatDate('yy-mm-dd', today);
  
  $(".time_interval").removeClass("hidden").removeClass("active");
  var delivery_price = 0.00;
  if(selected_date > today) {
    if(order_total < 50) {
      delivery_price = 5.00;
    }
    $("#total_shipping .price").html(delivery_price.toFixed(2));
    $("#delivery_price").val(delivery_price.toFixed(2));
  }
  else {
    //console.log(minutes);
    if(order_total < 50) {
      delivery_price = 10.00;
    }
    $("#total_shipping .price").html(delivery_price.toFixed(2));
    $("#delivery_price").val(delivery_price.toFixed(2));
    if(current_hour >= 800 && current_hour < 1100) {
      $(".interval_1").addClass("hidden");
    }
    else if(current_hour >= 1100 && current_hour < 1400) {
      $(".interval_1").addClass("hidden");
      $(".interval_2").addClass("hidden");
    }
    else if(current_hour >= 1400 && current_hour < 1600) {
      $(".interval_1").addClass("hidden");
      $(".interval_2").addClass("hidden");
      $(".interval_3").addClass("hidden");
      $(".interval_4").addClass("active");
    }
    else {
      $(".time_interval").removeClass("hidden");
    }
  }
  CalculateTotalPriceWithDelivery(delivery_price);
}

if (!!$.prototype.fancybox) {
  $('#send_friend_button').fancybox({
          'hideOnContentClick': false
  });
}
  
$('#send_friend_form_content .closefb').click(function(e) {
    $.fancybox.close();
    e.preventDefault();
});

function SendToAFriend(){
    var friend_name = $('#send_friend_form #friend_name').val();
    var friend_email = $('#send_friend_form #friend_email').val();
    var product_id = $('.product_id').val();
    var product_name = $('.product_name').val();
    var product_url = $('.product_url').val();
    var product_img = $('.product_img_big').val();
    //console.log(product_id);
    if (friend_name && friend_email && !isNaN(product_id))
    {
      $.ajax({
      url: "/frontstore/ajax/send-to-a-friend.php",
      type:"POST",
      data:{
        friend_name:friend_name, 
        friend_email:friend_email, 
        product_id:product_id,
        product_name:product_name,
        product_url:product_url,
        product_img:product_img
        }
      }).done(function(result) {
        
        $.fancybox.close();
        fancyMsgBox((result ? stf_msg_success : stf_msg_error), stf_msg_title);

        HideAjaxLoader();
      }).fail(function(error){
        console.log(error);
      })
    }
    else {
      $('#send_friend_form_error').text(stf_msg_required);
    }
}

function fancyMsgBox(msg, title)
{
    if (title) msg = "<h2>" + title + "</h2><p>" + msg + "</p>";
    msg += "<br/><p class=\"submit\" style=\"text-align:right; padding-bottom: 0\"><input class=\"button\" type=\"button\" value=\"OK\" onclick=\"$.fancybox.close();\" /></p>";
	if(!!$.prototype.fancybox)
    	$.fancybox( msg, {'autoDimensions': false, 'autoSize': false, 'width': 500, 'height': 'auto', 'openEffect': 'none', 'closeEffect': 'none'} );
}

$(document).ready(function() {
  
  //Newsletter
  $('#newsletter-input').on({
    focus: function() {
      if ($(this).val() == placeholder_blocknewsletter) $(this).val('');
    },
    blur: function() {
      if ($(this).val() == '') $(this).val(placeholder_blocknewsletter);
    }
  });

  var cssClass = 'alert alert-danger';
  if (typeof nw_error != 'undefined' && !nw_error) cssClass = 'alert alert-success';

  if (typeof msg_newsl != 'undefined' && msg_newsl) {
    $('#columns').prepend('<div class="clearfix"></div><p class="' + cssClass + '"> ' + alert_blocknewsletter + '</p>');
    $('html, body').animate({scrollTop: $('#columns').offset().top}, 'slow');
  }
  
  //FORM VALIDATION JAVASCRIPT----------------------------------------------------
  $('form#contact-form').submit(function(event) {
    var hasError = false;
    $('.requiredField').each(function() {
      var parent = $(this).parent();
      if(jQuery.trim($(this).val()) == '') {
        //console.log("empty");
        if($(this).hasClass('email')) {
          if(!parent.find('.invalid_email').hasClass('hidden')) {
            parent.find('.invalid_email').addClass('hidden');
          }
        }
        parent.find('.error').removeClass('hidden');
        hasError = true;
      } else if($(this).hasClass('email')) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if(!emailReg.test(jQuery.trim($(this).val()))) {
          console.log("invalid_email");
          parent.find('.error').addClass('hidden');
          parent.find('.invalid_email').removeClass('hidden');
          hasError = true;
        }
        else {
          parent.find('.error').addClass('hidden');
          parent.find('.invalid_email').addClass('hidden');
        }
      }
      else {
        parent.find('.error').addClass('hidden');
      }
    });
    
    if(!hasError) {
      $('form#contact-form input.submit').fadeOut('normal', function() {
        $(this).parent().append('');
      });
      var formInput = $(this).serialize();
      $.post($(this).attr('action'),formInput, function(data) {
        if(data == "terms_error") {
          $(".terms_error").removeClass('hidden');
        }
        else if(data == "privacy_policy_error") {
          $(".privacy_policy_error").removeClass('hidden');
        }
        else {
          $('form#contact-form').slideUp("fast", function() {
            $(".contact-form-container p.alert-success").removeClass("hidden");
          });
        }
      });
    }

    event.preventDefault();
  });
  
});