/**
 * This file is part of the performancelite package.
 *
 * @author Mathias Reker
 * @copyright Mathias Reker
 * @license Commercial Software License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function callAjax(e, t, a, r) {
    let o = $("#ajaxCall-" + t);
    if (0 !== a && !confirm(a)) return $.growl.warning({
        duration: 5e3,
        title: "",
        message: performancelite.canceled
    }), !1;
    o.attr("disabled", !0), $.ajax({
        type: "POST", url: e, cache: !1, data: {ajax: !0}, success: function (e) {
            o.attr("disabled", !1), $.growl({
                duration: 5e3,
                title: "",
                message: JSON.parse(e).result
            }), 1 === JSON.parse(e).refresh && (o.attr("disabled", !0), location.reload(), $.growl({
                duration: 5e3,
                title: "",
                message: performancelite.reloading
            })), 1 === JSON.parse(e).reload && $(location).attr("href", performancelite.currentIndex + "&" + r), $("#ajaxCall-" + t + " span").text(JSON.parse(e).count), o.closest("tr").find(".pp-amount").text(JSON.parse(e).amount), o.closest("div").find("#PP_PRELOAD_FONTS_TEXT").text(JSON.parse(e).content), resetField(t)
        }, error: function () {
            $("#ajaxCall-" + t).attr("disabled", !1), $.growl.warning({
                duration: 5e3,
                title: "",
                message: performancelite.error
            })
        }
    })
}

function copyToClipboard(e) {
    if (window.clipboardData && window.clipboardData.setData) return window.clipboardData.setData("Text", e);
    if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
        let t = document.createElement("textarea");
        t.textContent = e, t.style.position = "fixed", document.body.appendChild(t), t.select();
        try {
            let a = document.execCommand("copy");
            return a && $.growl.notice({duration: 5e3, title: "", message: performancelite.copy}), a
        } catch (e) {
            $.growl.warning({duration: 5e3, title: "", message: performancelite.copyError})
        } finally {
            document.body.removeChild(t)
        }
    }
}

function resetField(e) {
    $("#ajaxCall-" + e).closest("tr").find(".label").text(performancelite.reset).removeClass("label-info").addClass("label-success")
}

$(document).ready(function () {
    $(".pp-margin-fix").closest('div[class^="form-group"]').css("margin-bottom", "5px")
});
