window.onload = function() {

    setInterval(function() {
        getInfo();
    }, 15 * 1000);
}

function getInfo() {
    $.ajax({
        type: 'GET',
        url: 'api/index.php?do=update',

        success: function(response) {
            var data = JSON.parse(response);

            if(data.olx.length > 0 || data.realty.length > 0) {
                var audio = new Audio(); // Создаём новый элемент Audio
                audio.src = 'mp3/Sound_22372.mp3'; // Указываем путь к звуку "клика"
                audio.autoplay = true; // Автоматически запускаем
            }

            $('#olx').html(data.olx);
            $('#realty').html(data.realty);
            // $('#domRia').html(data.domRia);
        },

        error: function(response) {
            alert(response);
        },
    });

    var currentdate = new Date();
    var datetime = currentdate.getHours() + ":"
        + currentdate.getMinutes() + ":"
        + currentdate.getSeconds();

    console.log(datetime + ' --- Done!');
}