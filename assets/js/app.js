/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import '../css/app.scss';
require('jquery-ui/ui/widgets/droppable');
require('jquery-ui/ui/widgets/sortable');
require('jquery-ui/ui/widgets/selectable');
import $ from 'jquery';

import 'font-awesome/scss/font-awesome.scss';

import 'bootstrap';
import 'popper.js';
import '@coreui/coreui';
// import 'bootstrap-select/js/bootstrap-select'
// import '@coreui/icons';
// import '@coreui/icons/js/index.js';

$(function(){
    $('#profile_form_avatar').on('change', function() {
        var file = $(this).get(0).files;
        $(".upload-photo-name").text(file[0].name);

    });

    $('#user_form_avatar').on('change', function() {
        var file = $(this).get(0).files;
        $(".upload-photo-name").text(file[0].name);
    });

    $('#create_category_form_image, #edit_category_form_image').on('change', function() {
        var file = $(this).get(0).files;
        $(".upload-photo-name").text(file[0].name);
    });

    $(".no-close").click(function(e){
        e.stopPropagation();
    });

    $(".category-position").sortable({
        cursor: "move",
        start: function ()
        {
            if (typeof this.indexArray == 'undefined') {
                var indexArray = [];
                $(this).children().each(function (index) {
                    var dataIndex = $(this).attr('data-index');
                    indexArray[dataIndex] = $(this).attr('data-position');
                });

                this.indexArray = indexArray;
            }
        },
        update: function (event, ui) {
            var indexArray = this.indexArray;
            $(this).children().each(function (index) {

                if ($(this).attr('data-index') != index) {
                    $(this).attr('data-index', index).addClass('updated');
                }

                $(this).attr('data-position',indexArray[index]);
            });
        }
    });

    $("#change_position").on("click", function (event) {
        var positions = [];
        var path = $(this).attr('data-path');

        $('.updated').each(function () {
            if (typeof $(this).attr('data-id') !== 'undefined') {
                positions.push({
                        id: $(this).attr('data-id'),
                        position: +$(this).attr('data-position')
                    }
                );
                $(this).removeClass('updated');
            }
        });

        $.ajax({
            url: path,
            method: "POST",
            dataType: 'json',
            data: {
                token: $('#change_position_input').attr('value'),
                positions: JSON.stringify(positions)
            }, success: function (response) {
                console.log(Object.keys(response));
                if (Object.keys(response)[0] === 'success') {
                    $(".position_response").toggleClass('alert alert-success text-center').text(response.success);
                } else {
                    $(".position_response").toggleClass('alert alert-danger text-center').text(response.error);
                }
            }
        });
    });

});


console.log('Hello Webpack Encore! Edit e in assets/js/app.js');
