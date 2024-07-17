$('#textarea').keypress(function (e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        $(this).closest('form').submit();
    }
});

function textarea(e) {
    var obj = e.target;
    var div = document.getElementById(obj.id + '_input');
    div.setAttribute('value', obj.textContent);
}

