(function (e, t, n) {

    function r(e) {

        var t = P.appmsgList.length,

            n = 0;

        for (var r = 0; r < t; ++r) {

            var i = P.appmsgList[r];

            if ( !! i.remove) {

                n++;

                continue

            }

            if (i.rid == e) return r + 1 - n

        }

        return 1

    }

    function i(t, n) {

        var r = 580,

            i = t.outerHeight();

        n > 4 ? (e("#msgEditArea").css("margin-top", t.offset().top - j - r + i + 30 + "px"), e("#msgEditArea .a-out").css("margin-top", r - i / 2 - 54 + "px"), e("#msgEditArea .a-in").css("margin-top", r - i / 2 - 54 + "px")) : (e("#msgEditArea").css("margin-top", t.offset().top - j + 30 + "px"), e("#msgEditArea .a-out").css("margin-top", "0px"), e("#msgEditArea .a-in").css("margin-top", "0px"))

    }

    function s(e) {

        var t = 1e3,

            r = t,

            i = P.appmsgList.length;

        for (var s = 0; s < i; ++s) {

            var o = P.appmsgList[s];

            if ( !! o.remove) continue;

            o.rid > e && r > o.rid && (r = o.rid)

        }

        return r == t ? n : r

    }

    function o() {

        I++;

        var e = t.rAppmsgList.rid++;

        return t.rAppmsgList[e] = {}, P.appmsgList.push({

            rid: e

        }), e

    }

    function u() {

        P = C.appmsg.data.list[0], P.appmsgList.length || (F ? (o(), o()) : o()), I = P.appmsgList.length, P.count = I

    }

    function a() {

        e(".sub-add").click(function () {

            if (f() >= 8) return W.err("你最多只可以加入8条图文消息"), !1;

            e(W.t("#tNewMediaAppMsgEditItem", {

                rid: o(),

                msgItem: {}

            })).insertBefore(e(".sub-add")), h()

        })

    }

    function f() {

        var e = 0,

            t = P.appmsgList.length;

        for (var n = 0; n < t; ++n) {

            var r = P.appmsgList[n];

            if ( !! r.remove) continue;

            e++

        }

        return e

    }

    function l(e) {

        if (!e) return !0;

        var t = P.appmsgList.length;

        for (var n = 0; n < t; ++n) {

            var r = P.appmsgList[n];

            if (!r.remove) return r.rid == e ? (res = r.rid, !0) : !1;

            continue

        }

        return !1

    }

    function c(s) {

        e("#msgEditArea").show();

        var o = P.appmsgList,

            u = null;

        s == n && (s = o[0].rid), B = s, u = t.rAppmsgList[s] || {}, e("#title").val(D(u.title ? u.title : "")), u.source_url ? (e("#url-block-link").hide(), e("#url-block").show(), e("#url").val(u.source_url)) : (e("#url-block-link").show(), e("#url-block").hide(), e("#url").val(""));

        if (!u.fileId) e("#imgArea").hide();

        else {

            var a = k.getimgdata + "&mode=large&source=file&fileId=" + u.fileId + "&token=" + t.DATA.token;

            e("#imgArea").show(), e("#img").attr("src", a)

        }

        q.setContent(u.content ? u.content : ""), u.desc ? (e("#desc-block-link").hide(), e("#desc-block").show(), e("#desc").val(D(u.desc))) : (e("#desc-block-link").show(), e("#desc-block").hide(), e("#desc").val(""));

        var f = e("#appmsgItem" + s);

        i(f, r(s)), l(s) ? e("#upload-tip").html("大图片建议尺寸：720像素 * 400像素") : e("#upload-tip").html("小图片建议尺寸：400像素 * 400像素")

    }

    function h() {

        e(".appmsgItem").unbind("hover").hover(function () {

            e(this).addClass("sub-msg-opr-show")

        }, function () {

            e(this).removeClass("sub-msg-opr-show")

        }), e(".sub-msg-opr .iconEdit").unbind("click").click(function () {

            var t = e(this).data("rid");

            if (t == B) return;

            if (!b(!0)) return !1;

            c(t)

        }), e(".sub-msg-opr .iconDel").unbind("click").click(function () {

            var t = e(this).data("rid");

            if (f() > 2) {

                if (confirm("确认删除此消息？")) {

                    e("#appmsgItem" + t).remove(), y("remove", !0, t);

                    var n = s(t);

                    B == t && c(n), I--

                }

            } else W.err("无法删除，多条图文至少需要2条消息。")

        })

    }

    function p() {

        q.render("editor"), q.ready(function () {

            c(), h()

        }), e("#title").keyup(function () {

            e("#appmsgItem" + B + " .i-title").html(_(e("#title").val()))

        }), e("#desc").keyup(function () {

            e("#appmsgItem" + B + " .msg-text").html(_(e("#desc").val()))

        }), e("#delImg").click(function () {

            e("#imgArea").hide(), e("#appmsgItem" + B + " .i-img").hide(), e("#appmsgItem" + B + " .default-tip").show(), y("fileId", null, B)

        })

    }

    function d() {

        e("#save").click(function (t) {

            if (e("#save").attr("disable") === "true") return !1;

            var n = E();

            if (n.err) return !1;

            W.suc("保存中..."), e("#save").attr("disable", !0).addClass("btnDisable"), W.ajax(C.appmsg.postURL, n, function (t) {

                if (t.ret == 0) W.suc("保存成功"), location.href = C.appmsg.listURL;

                else {

                    switch (t.ret) {

                        case "64506":

                            W.err("保存失败,链接不合法");

                            break;

                        case "64507":

                            W.err("内容不能包含链接，请调整");

                            break;

                        case "-99":

                            W.err("内容超出字数，请调整");

                            break;

                        default:

                            W.err("保存失败")

                    }

                    e("#save").attr("disable", !1).removeClass("btnDisable")

                }

            }, function () {

                W.err("保存失败"), e("#save").attr("disable", !1).removeClass("btnDisable")

            }, function () {})

        })

    }

    function v() {

        e("#previewAppMsg").click(function (n) {

            var r = E();

            if (r.err) return !1;

            for (var i = 0; i < r.count; ++i) if (r["content" + i].split("<iframe ").length > 1) {

                e("#baidu_editor_0").css("display", "none");

                break

            }

            var s = t.Helpers.getCookie("appMsgPreviewName");

            W.d.show({

                title: "发送预览",

                content: W.t("#tAppMsgPreview", {

                    appMsgPreviewName: s

                }),

                width: 350,

                height: 150,

                onok: function () {

                    return m(r), !0

                },

                oncancle: function () {

                    e("#baidu_editor_0").css("display", "block")

                },

                onTopClose: function () {

                    e("#baidu_editor_0").css("display", "block")

                }

            }), W.d.get$Inside(".txt").keypress(function (e) {

                if (e.keyCode != "13") return;

                return m(r), !0

            }), W.d.get$Inside(".txt").focus(), e("#dialogOK").removeClass("btnDisableS").attr("disable", !1)

        })

    }

    function m(n) {

        if (e("#dialogOK").attr("disable") === "true") return !0;

        var r = e.trim(W.d.get$Inside(".txt").val());

        if (r == "" || r.length > 20) return W.err("微信号必须为1到20个字符"), W.d.get$Inside(".txt").focus(), !0;

        W.d.get$Inside(".appMsgPreview").hide(), W.d.get$Inside(".loading").show(), n.preusername = r, e("#dialogOK").addClass("btnDisableS").attr("disable", !0), W.ajax("/cgi-bin/operate_appmsg?sub=preview&t=ajax-appmsg-preview", n, function (n) {

            if (n && n.ret == "0") W.suc("发送预览成功，请留意你的手机微信"), e("#baidu_editor_0").css("display", "block"), W.d.hide(), P.appId = n.appMsgId, C.appmsg.postURL = "/cgi-bin/operate_appmsg?t=ajax-response&sub=update&token=" + t.DATA.token, t.Helpers.setCookie("appMsgPreviewName", r || "");

            else {

                switch (n.ret) {

                    case "64501":

                        W.err("你输入是非法的微信号，请重新输入");

                        break;

                    case "64502":

                        W.err("你输入的微信号不存在，请重新输入");

                        break;

                    case "64503":

                        W.err("你输入的微信号不是你的好友");

                        break;

                    case "64504":

                        W.err("保存图文消息发送错误，请稍后再试");

                        break;

                    case "64505":

                        W.err("发送预览失败，请稍后再试");

                        break;

                    case "64507":

                        W.err("内容不能包含链接，请调整");

                        break;

                    case "-99":

                        W.err("内容超出字数，请调整");

                        break;

                    default:

                        W.err("系统繁忙，请稍后重试")

                }

                W.d.get$Inside(".txt").focus()

            }

        }, function () {

            W.err("发送失败，请稍后重试"), W.d.get$Inside(".txt").focus()

        }, function () {

            e("#dialogOK").removeClass("btnDisableS").attr("disable", !1), W.d.get$Inside(".appMsgPreview").show(), W.d.get$Inside(".loading").hide()

        })

    }

    function g() {

        j = e(".msg-preview").offset().top, p(), d(), a(), v(), e(".msg-preview .msg-t").each(function (e, t) {

            t.innerHTML = t.innerHTML.replace(/&nbsp;/g, " ")

        })

    }

    function y(e, n, r) {

        t.rAppmsgList[r][e] = n;

        var i = P.appmsgList.length;

        for (var s = 0; s < i; ++s) {

            if (P.appmsgList[s].rid != r) continue;

            P.appmsgList[s][e] = t.rAppmsgList[r][e]

        }

    }

    function b(n) {

        y("title", e("#title").val(), B), y("desc", e("#desc").val(), B), y("source_url", e("#url").val(), B), y("content", q.getContent(), B);

        var r = e.trim(q.getContentTxt().replace(/(\<script\b[^>]*\>)([\s\S]*?)(\<\/scripst\s*\>)/, ""));

        return y("content_text", r, B), y("content_len", r.length, B), n ? !0 : w(t.rAppmsgList[B])

    }

    function w(r, i, s) {

        var o = !1;

        i = i || {}, s = s == n ? 0 : s, r.title = D(e.trim(r.title));

        if (!(r.title && r.title.length <= 64)) return W.err(L("#tSaveTitleFailure", 0)), !1;

        i["title" + s] = r.title;

        var u = r.content_len,

            a = r.content_text;

        u == n && (a = r.content.replace(/<\/?[^>]*\/?>/g, ""), u = a.length);

        if (!r.desc || r.desc.length == 0) i["digest" + s] = r.desc = a.substr(0, 54);

        else {

            r.desc = D(e.trim(r.desc));

            if (!(r.desc.length <= 120)) return W.err(L("#tSaveDescFailure", 0)), !1;

            i["digest" + s] = r.desc

        }

        if (!u || u > R) return W.err(L("#tSaveContentFailure", {

            len: u - R

        })), !1;

        if (r.content.split("<iframe ").length > 2) return W.err(L("#tCheckIframFailure")), !1;

        i["content" + s] = r.content;

        if (!r.fileId) return W.err(L("#tSaveImgFailure", 0)), !1;

        i["fileid" + s] = r.fileId, r.source_url = e.trim(r.source_url);

        if ( !! r.source_url) {

            var f = r.source_url,

                l = t.valid.isURL(f);

            if (f.indexOf("<") != -1 || f.indexOf(">") != -1) l = !1;

            if (f.length == 0) return W.err(L("#tSaveUrlEmpty", 0)), !1;

            if (!l) return W.err(L("#tSaveUrlFailure", 0)), !1;

            var c = new RegExp("^((https|http|ftp|rtsp|mms)://)");

            c.test(f) || (f = "http://" + f), i["sourceurl" + s] = f

        }

        return !0

    }

    function E() {

        b();

        var e, t = P.appmsgList.length,

            n = {

                error: !1,

                count: f(),

                AppMsgId: P.appId

            }, r = 0;

        for (var e = 0; e < t; ++e) {

            var i = P.appmsgList[e];

            i.remove || w(i, n, r++) || (c(i.rid), n.err = !0)

        }

        return n

    }

    var S = t.PageController,

        x = S.mediaSelector,

        T = S.dialog,

        N = S.tips,

        C = t.DATA,

        k = t.Links,

        L = t.Helpers.tmpl,

        A = null,

        O = 0,

        M = C.appmsg.subtype,

        _ = t.Helpers.htmlEncode,

        D = t.Helpers.htmlDecode,

        P = null,

        H = {}, B = -1,

        j = 0,

        F = C.isMulAppmsg,

        I = 0;

    t.rAppmsgList = {};

    var q = new baidu.editor.ui.Editor({

        wordCount: !1,

        elementPathEnabled: !1,

        customDomain: !0

    });

    u();

    var R = 2e4;

    t.Handles = {

        "#main": function (n) {

            var r = t.Helpers.createDomByHtml(t.Templates["#main"](C)),

                i = e(".content", r).html(L("#tAppMsgEditMainArea", {

                    item: P

                }));

            r.appendTo(n), g()

        }

    }, e("body").addClass("appMsgEdit");

    var U = t.Helpers.markSelectedInArr;

    U({

        tKey: "_id",

        tVal: "filemanager",

        list: C.nav

    }), t.main(), W.upload = {

        suc: function (t, n, r, i) {

            W.suc(t), y("fileId", i, B);

            var s = k.getimgdata + "&mode=large&source=file&fileId=" + i;

            e("#imgArea").show(), e("#img").attr("src", s), e("#appmsgItem" + B + " .i-img").attr("src", s).show(), e("#appmsgItem" + B + " .default-tip").hide(), e("#imgUpload").attr("src", k.indexpage + "&t=wxm-upload&lang=zh_CN&type=2&formId=" + r)

        },

        err: function (t, n, r) {

            W.err(t), e("#imgUpload").attr("src", k.indexpage + "&t=wxm-upload&lang=zh_CN&type=2&formId=" + r)

        }

    }

})(jQuery, WXM);