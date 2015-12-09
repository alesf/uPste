Dropzone.autoDiscover = false;
$(function () {
    $(document.body).dropzone({
        url: "/api/upload",
        previewsContainer: "#previews",
        clickable: ".upload-button",
        params: {'key': window.api_key},
        init: function () {
            this.on("success", function (file, responseText) {
                console.log(responseText);
                $(file.previewTemplate).append($('<a>', {
                    'href': responseText.url,
                    html: responseText.url
                }))
            }).on("addedfile", function(file) {
                if (!file.type.match(/image.*/)) {
                    this.emit("thumbnail", file, "/img/thumbnail.png");
                }
        })
    }})
});
