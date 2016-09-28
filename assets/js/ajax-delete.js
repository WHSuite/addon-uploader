$(document).ready(function() {

    $('.ajax-delete').bind('click', function(e) {
        e.preventDefault();

        var click = $(this);
        var row = click.parents('li');
        row.slideUp('normal', function() {

            $(this).remove();
        });

        $.ajax({
            'url': click.attr('href'),
            'cache': false
        });
    });

});
