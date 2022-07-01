$(document).ready(function() {
    /* it's all about the transitions */
    $('#container-header').fadeIn(1000);
    $('#masthead').fadeIn(2000);

    /* grab the cached twitter json */
    $.ajax({
        url: "http://sitrobotsit.com/twitter/cache.txt"
    })
        .then(function(data) {
            data = $.parseJSON(data);
            $('.hype').append(data.description);
        });

});



