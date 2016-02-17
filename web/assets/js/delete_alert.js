$(document).ready(function () {
    $('.table a').on('click', function (e) {
        e.preventDefault();
        var answer = confirm('Do you want to delete?');
        if (answer) {
            $.ajax({
                url: ($(this).attr('href')),
                method: "POST",
                dataType: "json"
            }).success(function (data) {
                if (data.unset == true) {
                    $('.box-info').append(
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
                    $('.box-info').append(
                        '<div class="panel panel-danger" style="display: none">' + '<div class="panel-heading">' +
                        "Nie znaleziono użytkownika o podanym id." +
                        '<span class="pull-right"></span></div>' + '</div>');
                    $(".box-info .panel-danger").fadeIn('slow').animate({opacity: 1.0}, 2000).fadeOut('slow');
                }
            });
        }
        else {
            $('.box-info').append(
                '<div class="panel panel-danger" style="display: none">' + '<div class="panel-heading">' +
                "Cofnięto operację usówania" +
                '<span class="pull-right"></span></div>' + '</div>');
            $(".box-info .panel-danger").fadeIn('slow').animate({opacity: 1.0}, 2000).fadeOut('slow');
        }

    });
});