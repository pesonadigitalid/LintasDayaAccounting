$(document).ready(function(){
     $('.modal-trigger').click(function(){
        var modaltarget = $(this).attr("modal-target");
        $('#'+modaltarget).addClass("active");
        return false;
     })
     $('.modal-overlay , .modal-close-trigger').click(function(){
        $('.modal').removeClass('active');
     });
     $('.tab-item').click(function(){
        $('.tab-item').removeClass('active');
        $(this).addClass('active');
     });
     $('.toast .btn-clear').click(function(){
        $(this).parent(".toast").hide();
     });
     startTime();
});

function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
    $('#ftime').html(h + ":" + m + ":" + s);
    var t = setTimeout(startTime, 500);
}
function checkTime(i) {
    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
}