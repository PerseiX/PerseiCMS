$(document).ready(function () {
    /* Unset operation*/
    $('.table .unset-button a').on('click', function (e) {
        e.preventDefault();
        var answer = confirm('Do you want to delete?');
        if (answer) {
            $.ajax({
                url: ($(this).attr('href')),
                method: "POST",
                dataType: "json"
            }).success(function (data) {
                if (data.unset == true) {
                    $('.box-info').empty().append(
                        '<div class="panel panel-success" style="display: none">' +
                        '<div class="panel-heading">Pomyślnie usunięto użytkownika.<span class="pull-right"></span></div>' +
                        '</div>'
                    );
                    $('html, body').animate({scrollTop: 0}, 800);
                    $(' .box-info .panel-success').fadeIn('slow').animate({opacity: 1.0}, 3000).fadeOut('slow');
                    setTimeout(function () {
                        window.location.replace($(location).attr('href'));
                    }, 3000);
                }
                else {
                    $('.box-info').empty().append(
                        '<div class="panel panel-danger"+  style="display: none">' + '<div class="panel-heading">' +
                        "Nie znaleziono użytkownika o podanym id." +
                        '<span class="pull-right"></span></div>' + '</div>');
                    $(".box-info .panel-danger ").fadeIn('slow').animate({opacity: 1.0}, 2000).fadeOut('slow');
                }
            });
        }
        else {
            $('.box-info').empty().append(
                '<div class="panel panel-danger" style="display: none">' + '<div class="panel-heading">' +
                "Cofnięto operację usówania" +
                '<span class="pull-right"></span></div>' + '</div>');
            $(".box-info .panel-danger").fadeIn('slow').animate({opacity: 1.0}, 2000).fadeOut('slow');
        }

    });

    /* Edit operation*/
    $.fn.serializeObject = function () {

        var self = this,
            json = {},
            push_counters = {},
            patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key": /[a-zA-Z0-9_]+|(?=\[\])/g,
                "push": /^$/,
                "fixed": /^\d+$/,
                "named": /^[a-zA-Z0-9_]+$/
            };


        this.build = function (base, key, value) {
            base[key] = value;
            return base;
        };

        this.push_counter = function (key) {
            if (push_counters[key] === undefined) {
                push_counters[key] = 0;
            }
            return push_counters[key]++;
        };

        $.each($(this).serializeArray(), function () {

            // skip invalid keys
            if (!patterns.validate.test(this.name)) {
                return;
            }

            var k,
                keys = this.name.match(patterns.key),
                merge = this.value,
                reverse_key = this.name;

            while ((k = keys.pop()) !== undefined) {

                // adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // push
                if (k.match(patterns.push)) {
                    merge = self.build([], self.push_counter(reverse_key), merge);
                }

                // fixed
                else if (k.match(patterns.fixed)) {
                    merge = self.build([], k, merge);
                }

                // named
                else if (k.match(patterns.named)) {
                    merge = self.build({}, k, merge);
                }
            }

            json = $.extend(true, json, merge);
        });

        return json;
    };
    $('.table .edit-button a').on('click', function (e) {
        e.preventDefault();

        $('.edit-button').css('pointer-events', 'none');


        var rowNumber = $(this).attr('id');
        var url = $('#'+rowNumber).attr('href');

        var id = $('#row'+rowNumber+' #id span').text();
        var username = $('#row'+rowNumber+' #username span').text();
        var email = $('#row'+rowNumber+' #email span').text();
        var birthday = $('#row'+rowNumber+' #birthday span').text();
        var about = $('#row'+rowNumber+' #about span').text();
        var isActive = $('#row'+rowNumber+' #isActive span').text();
        var roles = $('#row'+rowNumber+' #roles span').text();


        $(".show-row-"+rowNumber).fadeIn('slow');
        $('.jumbotron-'+rowNumber).empty().append(
            '<form method="post" class="formClass">'+
                '<input name="id" class="id" style="display:none;" aria-describedby="basic-addon1" type="text" />'+
                '<div class="form-group">'+
                    '<span class="filed-name ">Username</span>'+
                    '<input name="username" class="form-control username" aria-describedby="basic-addon2" type="text" />'+
                '</div>'+

                '<div class="form-group">'+
                    '<span class="filed-name ">Email</span>'+
                    '<input name="email" class="form-control email" aria-describedby="basic-addon2" type="text" />'+
                '</div>'+

                '<div class="form-group">'+
                    '<span class="filed-name">Date of birthday</span>'+
                    '<input name="birthday" id="datepicker" type="text" class="form-control birthday" placeholder="Date of birthday" aria-describedby="basic-addon2" autocomplete="off" required>'+
                '</div>'+

                '<div class="form-group">'+
                    '<span class="filed-name">About me</span>'+
                    '<textarea  name="about" class="form-control about" placeholder="About me" aria-describedby="basic-addon2" required ></textarea>'+
                '</div>'+

                '<div class="form-group">'+
                    '<span class="filed-name">Roles</span>'+
                    '<input name="roles" class="form-control roles" aria-describedby="basic-addon2" type="text" />'+
                '</div>'+

                '<div class="filed-name" style="width:70px;">'+
                    '<span class="filed-name">Is active</span>'+
                    '<select name="isActive" class="form-control" >' +
                        '<option id="No" value="0">No</option>' +
                        '<option id="Yes" value="1">Yes</option>' +
                    '</select>' +
                '</div><br>'+
                '<button class="btn btn-block btn-info">Zapisz</button>'+
            '</form>'+
            '<button class="anuluj btn-block btn-warning">Anuluj</button>'
        );

       /* Scroll to top a jumbotron block*/
        $('html, body').animate({
            scrollTop: $('.jumbotron-'+rowNumber).offset().top
        }, 1000);

        $('.id').val($.trim(id));
        $('.username').val($.trim(username));
        $('.email').val($.trim(email));
        $('.birthday').val($.trim(birthday));
        $('.about').val($.trim(about));
        $('.form-control #'+isActive).attr('selected', 'true');
        $('.roles').val($.trim(roles));

        $('.formClass .btn').on('click', function (e) {
            e.preventDefault();

            var jsonForm = $('.formClass').serializeObject();

            $.ajax({
                url: url,
                method: "POST",
                dataType: "json",
                data: jsonForm
            }).success(function (data) {
                if(data.success == true)
                {
                    $('.edit-button').css('pointer-events', 'visible');
                    $(".show-row-"+rowNumber).css('display', 'none');

                    $('.box-info').empty().append(
                        '<div class="panel panel-success" style="display: none">' +
                        '<div class="panel-heading">Zapisano zmiany.<span class="pull-right"></span></div>' +
                        '</div>'
                    );
                    $(".box-info .panel-success").fadeIn('slow').animate({opacity: 1.0}, 2000).fadeOut('slow');

                    $('html, body').animate({
                        scrollTop: $('.box-info .panel-success').offset().top
                    }, 1000);

                    setTimeout(function () {
                        window.location.replace($(location).attr('href'));
                    }, 3000);
                }
            });
        });
        /* Anuluj message*/
        $('.anuluj').on('click', function(){
            $('.edit-button').css('pointer-events', 'visible');
            $(".show-row-"+rowNumber).css('display', 'none');

            $('.box-info').empty().append(
                '<div class="panel panel-danger" style="display: none">' +
                '<div class="panel-heading">Edycja została anulowanna.<span class="pull-right"></span></div>' +
                '</div>'
            );
            $(".box-info .panel-danger").fadeIn('slow').animate({opacity: 1.0}, 2000).fadeOut('slow');

            $('html, body').animate({
                scrollTop: $('.box-info .panel-danger').offset().top
            }, 1000);
        });
    });
});