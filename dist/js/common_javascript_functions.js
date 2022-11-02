$(document).ready(function(e) {

    $('.alphaonly').bind('keyup blur', function() {
        var node = $(this);
        node.val(node.val().replace(/[^a-z A-Z ]/g, ''));
    });


    $('.integeronly').keypress(function(e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }

    });


});


function showMenuSelected(menuId, pageId) {

    $(menuId).addClass("menu-open");

    if (pageId !== '') {
        $(pageId).addClass("active");
    }

    $(menuId).children().first().addClass("active");

}


function showCustomMessage(message) {

    $.alert({
        confirmButton: 'Ok',
        confirmButtonClass: "btn-sm btn-flat rounded-0 btn-info",
        title: 'Message',
        content: message
    });
}