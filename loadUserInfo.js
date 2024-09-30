document.addEventListener("DOMContentLoaded", function() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../php/user_info.php', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById('user-info').innerHTML = xhr.responseText;
        } else {
            document.getElementById('user-info').innerHTML = "Usuario";
        }
    };
    xhr.send();
});