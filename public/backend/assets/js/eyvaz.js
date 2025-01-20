function changeTabElements(clicked) {
    $("div.nav-tabs button").removeClass('active');
    $("div.tab-content .tab-pane").removeClass('show');
    $("div.tab-content .tab-pane").removeClass('active');
    $("div.nav-tabs button." + clicked).addClass('active');
    $("div.tab-content .tab-pane." + clicked).addClass('show');
    $("div.tab-content .tab-pane." + clicked).addClass('active');
}


function showLoader() {
    var loader = document.getElementById("loader");
    loader.style.display = "block";
}

function hideLoader() {
    var loader = document.getElementById("loader");
    loader.style.display = "none";
}