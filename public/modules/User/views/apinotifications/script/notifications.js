var handleApiNotificationsInterval = null;
var handleApiNotifications = function() {
    clearInterval(handleApiNotificationsInterval);
    var seconds = 5;
    $("#notification_closing .seconds").text(seconds);
    handleApiNotificationsInterval = setInterval(function () {
        $("#notification_closing .seconds").text(--seconds);
    }, 1000);
    setTimeout(function () {
        $("#overlay_notifications").fadeOut(1000, function () {
            $("#overlay_notifications").remove();
        });
    }, seconds * 1000);
};