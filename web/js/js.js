// this is really bad code but I want it done fast
$(function() {
    var showLinks = $('.show-details');
    var hideLinks = $('.hide-details');

    for(var i=0; i<showLinks.length; i++) {
        var showLink = showLinks[i];
        var hideLink = hideLinks[i];
        $(showLink).click(function(e){
            e.preventDefault();
            var id = $(this).data('id');
            $('#hide_details_'+id).show();
            $(this).hide();
            $('#details_'+id).show();
        });
        $(hideLink).click(function(e){
            e.preventDefault();
            var id = $(this).data('id');
            $('#show_details_'+id).show();
            $(this).hide();
            $('#details_'+id).hide();
        });
    }
})
