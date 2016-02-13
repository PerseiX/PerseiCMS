$(document).ready(function(){

    $.fn.serializeObject = function(){

        var self = this,
            json = {},
            push_counters = {},
            patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
                "push":     /^$/,
                "fixed":    /^\d+$/,
                "named":    /^[a-zA-Z0-9_]+$/
            };


        this.build = function(base, key, value){
            base[key] = value;
            return base;
        };

        this.push_counter = function(key){
            if(push_counters[key] === undefined){
                push_counters[key] = 0;
            }
            return push_counters[key]++;
        };

        $.each($(this).serializeArray(), function(){

            // skip invalid keys
            if(!patterns.validate.test(this.name)){
                return;
            }

            var k,
                keys = this.name.match(patterns.key),
                merge = this.value,
                reverse_key = this.name;

            while((k = keys.pop()) !== undefined){

                // adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // push
                if(k.match(patterns.push)){
                    merge = self.build([], self.push_counter(reverse_key), merge);
                }

                // fixed
                else if(k.match(patterns.fixed)){
                    merge = self.build([], k, merge);
                }

                // named
                else if(k.match(patterns.named)){
                    merge = self.build({}, k, merge);
                }
            }

            json = $.extend(true, json, merge);
        });

        return json;
    };

    var formButton = $('.formController .btn');

    formButton.click(function(e) {
        e.preventDefault();
        //formButton.prop("disabled", true);

        var jsonForm = $('.formController').serializeObject();
        $.ajax({
            url: $(location).attr('href').split('/registry')[0]+"/formValidate",
            method: "POST",
            dataType: "json",
            data: jsonForm
        }).success(function(data) {
            //alert(data.s);
            if(data.success == true) {
                $('.formController .box-info').append(
                    '<div class="panel panel-success" style="display: none">' +
                    '<div class="panel-heading">Rejestracja przebiegła poprawnie. Zostaniesz przekierowany na stronę logowania.<span class="pull-right"></span></div>' +
                    '</div>');
                $('.formController .box-info .panel-success').fadeIn('slow').animate({opacity: 1.0}, 3000).fadeOut('slow');
                $('.formController').find("input[type=text], input[type=number], input[type=email], input[type=tel], input[type=radio], input[type=password], textarea").val("");
                setTimeout(function() {
                    window.location.replace($(location).attr('href').split('/registry')[0]+"/login");
                },3000);
            }
            else {
                $('.formController .box-info').append(
                    '<div class="panel panel-danger" style="display: none">'+ '<div class="panel-heading">' +
                    data.error +
                    ' <span class="pull-right"></span></div>'+ '</div>');

                $("#registryButton").attr('disabled', 'disabled');
                $(".formController .box-info .panel-danger").fadeIn('slow').animate({opacity: 1.0}, 2000).fadeOut('slow');

                setTimeout(function(){
                    $('#registryButton').removeAttr('disabled', 'disabled');
                    $(".panel").remove();
                }, 2800);
            }
        })


    })
});