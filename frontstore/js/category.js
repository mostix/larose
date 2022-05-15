/*
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
$(document).ready(function () {
  resizeCatimg();
});
$(window).resize(function () {
  resizeCatimg();
});
$(document).on('click', '.lnk_more', function (e) {
  e.preventDefault();
  $('#category_description_short').hide();
  $('#category_description_full').show();
  $(this).hide();
});
function resizeCatimg()
{
  var div = $('.cat_desc').parent('div');
  if (div.css('background-image') == 'none')
    return;
  var image = new Image;
  $(image).load(function () {
    var width = image.width;
    var height = image.height;
    var ratio = parseFloat(height / width);
    var calc = Math.round(ratio * parseInt(div.outerWidth(false)));
    div.css('min-height', calc);
  });
  if (div.length)
    image.src = div.css('background-image').replace(/url\("?|"?\)$/ig, '');
}
function bindGrid()
{
  var view = $.totalStorage('display');

  if (!view && (typeof displayList != 'undefined') && displayList)
    view = 'list';

  gridType = "grid";
  if ($("#page").data("type") != 'undefined')
    gridType = $("#page").data("type");
  if (view && view != gridType)
    display(view);
  else
    display(gridType);
  $(document).on('click', '#grid', function (e) {
    e.preventDefault();

    display('grid');
    $.totalStorage('display', 'grid');
  });

  $(document).on('click', '#list', function (e) {
    e.preventDefault();

    display('list');
    $.totalStorage('display', 'list');
  });
}

function display(view)
{

  $('.display').find('div').removeClass('selected');
  $('.display').find('div#' + view).addClass('selected');
  classGrid = "col-xs-12 col-sm-6 col-md-4";
  //console.log(classGrid);
  //if($("#page").data("column") != 'undefined') classGrid = $("#page").data("column");
  if (view == 'list')
  {
    $('.product_list').removeClass('grid').addClass('list');
    $('.product_list .ajax_block_product').removeClass(classGrid).addClass('col-xs-12 col-sm-12 col-md-12');
    $('.product_list .ajax_block_product .product-container').each(function (index, element) {
      html = '';
      html = '<div class="row">';
      html += '<div class="left-block col-md-4 col-sm-4">' + $(element).find('.left-block').html() + '</div>';
      html += '<div class="right-block col-md-8 col-sm-8">' + $(element).find('.right-block').html() + '</div>';

      html += '</div>';
      $(element).html(html);
    });
    $('.display').find('li#list').addClass('selected');
    $('.display').find('li#grid').removeAttr('class');
    //$.totalStorage('display', 'list');
  } else
  {
    $('div.product_list').removeClass('list').addClass('grid');
    $('.product_list .ajax_block_product').removeClass('col-xs-12 col-sm-12 col-md-12').addClass(classGrid);
    $('.product_list .ajax_block_product .product-container').each(function (index, element) {
      html = '';
      html += '<div class="left-block">' + $(element).find('.left-block').html() + '</div>';
      html += '<div class="right-block">' + $(element).find('.right-block').html() + '</div>';

      $(element).html(html);
    });
    $('.display').find('li#grid').addClass('selected');
    $('.display').find('li#list').removeAttr('class');
    //$.totalStorage('display', 'grid');
  }
  if (typeof addEffectProducts == 'function') {
    addEffectProducts();
  }
}

function urlObject(options) {
  "use strict";
  /*global window, document*/

  var url_search_arr,
          option_key,
          i,
          urlObj,
          get_param,
          key,
          val,
          url_query,
          url_get_params = {},
          a = document.createElement('a'),
          default_options = {
            'url': window.location.href,
            'unescape': true,
            'convert_num': true
          };

  if (typeof options !== "object") {
    options = default_options;
  } else {
    for (option_key in default_options) {
      if (default_options.hasOwnProperty(option_key)) {
        if (options[option_key] === undefined) {
          options[option_key] = default_options[option_key];
        }
      }
    }
  }

  a.href = options.url;
  url_query = a.search.substring(1);
  url_search_arr = url_query.split('&');

  if (url_search_arr[0].length > 1) {
    for (i = 0; i < url_search_arr.length; i += 1) {
      get_param = url_search_arr[i].split("=");

      if (options.unescape) {
        key = decodeURI(get_param[0]);
        val = decodeURI(get_param[1]);
      } else {
        key = get_param[0];
        val = get_param[1];
      }

      if (options.convert_num) {
        if (val.match(/^\d+$/)) {
          val = parseInt(val, 10);
        } else if (val.match(/^\d+\.\d+$/)) {
          val = parseFloat(val);
        }
      }

      if (url_get_params[key] === undefined) {
        url_get_params[key] = val;
      } else if (typeof url_get_params[key] === "string") {
        url_get_params[key] = [url_get_params[key], val];
      } else {
        url_get_params[key].push(val);
      }

      get_param = [];
    }
  }

  urlObj = {
    protocol: a.protocol,
    hostname: a.hostname,
    host: a.host,
    port: a.port,
    hash: a.hash.substr(1),
    pathname: a.pathname,
    search: a.search,
    parameters: url_get_params
  };

  return urlObj;
}