function toggleInputFunction(e) {
    var t = document.querySelector(`input#${e}`),
        a = document.querySelector(`span#${e}_icon`),
        n = a.querySelector("i.fa-eye-slash"),
        s = a.querySelector("i.fa-eye");
    n && n.classList.remove("fa-eye-slash"), s && s.classList.remove("fa-eye"), "password" === t.getAttribute("type") ? (t.setAttribute("type", "text"), a.querySelector("i").classList.add("fa-eye")) : (t.setAttribute("type", "password"), a.querySelector("i").classList.add("fa-eye-slash"))
}

function tabselect(e) {
    for (var t = document.getElementsByClassName("user_or_freelancer_tab"), a = 0; a < t.length; a++) t[a].classList.remove("active");
    var n = document.getElementsByClassName(`user_or_freelancer_tab_${e}`);
    n.length > 0 && n[0].classList.add("active");
    var s = document.getElementsByClassName("tab_company_element"),
        l = document.getElementsByClassName("tab_student_element");
    "company" == e ? (s[0] && null != s[0] && (s[0].style.display = "block"), l[0] && null != l[0] && (l[0].style.display = "none"), document.getElementById("user_type").value = 2) : (s[0] && null != s[0] && (s[0].style.display = "none"), l[0] && null != l[0] && (l[0].style.display = "block"), document.getElementById("user_type").value = 1)
}

function changedFileLabel(e) {
    var t = document.getElementById(e),
        a = t.files.length > 0 ? t.files[0].name : "fayl";
    document.querySelector(`label[for="${e}"]`).querySelector(".file-name").textContent = a
}

function change_tabs_elements(e, t) {
    var a = document.getElementById(`${e}_tab`),
        n = document.getElementById(`${e}-${t}_button`),
        s = document.getElementById(`${e}_tabContent`),
        l = document.getElementById(`${e}-${t}_tab`);
    Array.from(a.getElementsByClassName("active")).forEach(function(e) {
        e.classList.remove("active")
    }), n.classList.add("active"), Array.from(s.getElementsByClassName("tab-pane")).forEach(function(e) {
        e.classList.remove("show", "active", "fade")
    }), l.classList.add("show", "active", "fade")
}

function changeTabElementsIncludedResult(e, t) {
    $(`div.nav-tabs button[data-result="${t}"]`).removeClass("active"), $(`div.tab-content .tab-pane[data-result="${t}"]`).removeClass("show active"), $(`div.nav-tabs button.${e}[data-result="${t}"]`).addClass("active"), $(`div.tab-content .tab-pane.${e}[data-result="${t}"]`).addClass("show active")
}

function createalert(e, t, a = null) {
    if (null != a) var n = document.querySelector(`form#${a} #messages`);
    else n = document.querySelector("#messages");
    n.style.display = "none", n.innerHTML = "";
    var s = document.createElement("div");
    s.className = "alert " + e, s.textContent = t, n.appendChild(s), n.style.display = "block", setTimeout(function() {
        fadeOut(s)
    }, 2e3), window.scrollTo({
        top: 0,
        behavior: "smooth"
    })
}

function fadeOut(e) {
    var t = 1,
        a = setInterval(function() {
            t <= .1 && (clearInterval(a), e.style.display = "none", document.querySelector("#messages").style.display = "none"), e.style.opacity = t, t -= .1
        }, 50)
}

function togglepopup(e) {
    var t = document.getElementById(e);
    t.classList.contains("active") ? t.classList.remove("active") : t.classList.add("active")
}

function isValidEmail(e) {
    return /\S+@\S+\.\S+/.test(e)
}

function validPhone(e) {
    var t = e.replace(/\D/g, "").match(/(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
    return t[2] ? t[1] + " " + t[2] + (t[3] ? " " + t[3] : "") + (t[4] ? " " + t[4] : "") : t[1]
}

function searchinfields(e, t, a = "exams", n = null) {
    if (event.preventDefault(), null != n) {
        if ("category" == n) {
            var s = {
                category: e,
                type: a,
                action: n
            };
            document.querySelectorAll(".category-item").forEach(function(e) {
                e.classList.remove("active")
            });
            for (var l = document.querySelectorAll(".category-item." + e), r = 0; r < l.length; r++) l[r].classList.contains("active") ? l[r].classList.remove("active") : l[r].classList.add("active")
        }
    } else s = {
        query: document.getElementsByName(e)[0].value,
        type: a,
        action: n
    };
    var i = document.getElementById(t);
    sendAjaxRequestOLD("/api/searchinfilled", "post", s, function(e, t) {
        if (e) createalert("error", e);
        else {
            let a = JSON.parse(t);
            i.innerHTML = "", i.innerHTML = a.view
        }
    })
}

function change_filter(e, t = "datas", a = "az", n = "services") {
    for (var s = document.querySelectorAll(".filter_view"), l = 0; l < s.length; l++) s[l].classList.remove("active");
    var r = document.querySelector("." + e);
    r.classList.contains("active") ? r.classList.remove("active") : r.classList.add("active");
    let i = [];
    "services" == n && document.querySelectorAll(".service_one").forEach(e => {
        let t = e.getAttribute("id").replace("service-", "");
        i.push(t)
    });
    var o = document.getElementById(t);
    sendAjaxRequest("/api/filterelements", "post", {
        ids: i,
        type: n,
        orderby: e,
        language: a
    }, function(e, t) {
        if (e) createalert("error", e);
        else {
            let a = JSON.parse(t);
            o.innerHTML = "", o.innerHTML = a.view
        }
    })
}

function showLoader() {
    document.getElementById("loader") && document.getElementById("loader").classList.add("active")
}

function hideLoader() {
    document.getElementById("loader") && document.getElementById("loader").classList.remove("active")
}

function togglefilterelements(e) {
    let t = document.getElementsByClassName(e);
    for (let a = 0; a < t.length; a++) {
        let n = t[a];
        n.classList.contains("active") ? n.classList.remove("active") : n.classList.add("active")
    }
}

function toggle_filter_contents(e, t) {
    let a = document.getElementsByClassName(t);
    for (let n = 0; n < a.length; n++) {
        let s = a[n];
        s.classList.contains("active") ? s.classList.remove("active") : s.classList.add("active")
    }
}

function setnewparametrandsearch(e, t, a) {
    let n = new FormData(document.getElementById("filter_inputs")),
        s = n.getAll(`${e}[]`);
    if ("select" == t) {
        s.includes(a.toString()) ? s = s.filter(e => e !== a.toString()) : s.push(a.toString());
        let l = new FormData;
        s.forEach(t => {
            l.append(`${e}[]`, t)
        });
        document.querySelector(`[name="${e}[]"]`).value = s.join(",")
    }
    let r = new FormData(document.getElementById("filter_inputs")),
        i = "/exams?";
    for (let [o, c] of r.entries()) n.append(o, c), i += `${o}=${encodeURIComponent(c)}&`;
    window.location.href = i
}

function sendAjaxRequestOLD(e, t = "post", a = null, n = null, s = 0) {
    var l = new XMLHttpRequest;
    l.timeout = 1e4, l.onreadystatechange = function() {
        4 === l.readyState && (200 === l.status ? n && n(null, l.responseText) : setTimeout(() => {
            handleRetry(e, t, a, n, s, `HTTP Error: ${l.status} - ${l.statusText}`)
        }, 5e3))
    }, l.ontimeout = function() {
        handleRetry(e, t, a, n, s, "Sorğu zaman müddətini keçdi!")
    }, l.onerror = function() {
        handleRetry(e, t, a, n, s, "İnternet bağlantısı xətası!")
    }, "post" === t.toLowerCase() ? (l.open("POST", e), l.setRequestHeader("Content-Type", "application/json"), l.send(JSON.stringify(a))) : (l.open("GET", e), l.send())
}

function handleRetry(e, t, a, n, s, l) {
    console.error(l), s < 3 ? (console.warn(`Bağlantı xətası! ${s+1}. dəfə yenidən cəhd edilir...`), sendAjaxRequestOLD(e, t, a, n, s + 1)) : (console.error("Bağlantı xətası! Maksimum deneme sayısına ulaşıldı."), n && n(Error("Bağlantı xətası! Maksimum deneme sayısına ulaşıldı.")))
}

function sendAjaxRequest(e, t = "post", a = null, n) {
    var s = new XMLHttpRequest;
    s.onreadystatechange = function() {
        4 === s.readyState && (200 === s.status ? n(null, s.responseText) : n(s.statusText))
    }, "post" === t.toLowerCase() ? (s.open("POST", e), s.send(a)) : (s.open("GET", e), s.send())
}

function getserializedlang(e, t, a) {
    var n;
    return null != e ? "name" == t ? n = "az" == a ? e.az_name : "ru" == a ? e.ru_name : "en" == a ? e.en_name : "tr" == a ? e.tr_name : e.az_name : "slug" == t ? n = "az" == a ? e.az_slug : "ru" == a ? e.ru_slug : "en" == a ? e.en_slug : "tr" == a ? e.tr_slug : e.az_slug : "description" == t && (n = "az" == a ? e.az_description : "ru" == a ? e.ru_description : "en" == a ? e.en_description : "tr" == a ? e.tr_description : e.az_description) : n = "", null != n ? n : ""
}

function toggleModalnow(e, t = "open") {
    var a = document.getElementById(e),
        n = document.getElementById("myModalOverlay");
    "open" === t ? (a.style.display = "block", (n = document.createElement("div")).id = "myModalOverlay", n.className = "modal-overlay", document.body.appendChild(n)) : (n && n.remove(), a.style.display = "none")
}

function createRandomCode(e = "int", t = 4) {
    if ("int" === e) {
        if (4 === t) return Math.floor(9e3 * Math.random()) + 1e3
    } else if ("string" === e) {
        let a = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",
            n = a.length,
            s = "";
        for (let l = 0; l < t; l++) s += a.charAt(Math.floor(Math.random() * n));
        return s
    }
}

function getFileUrl(e, t) {
    return "/uploads/" + t + "/" + e
}

function getandseteditortexts(e = null) {
    try {
        var t = document.querySelectorAll("textarea");
        return t.length > 0 && t.forEach(function(t) {
            var a = null,
                n = tinymce.get(t.id);
            null != n && (a = n.getContent()), null != a && null != t.name && e.append(t.name, a)
        }), e
    } catch (a) {
        console.error(a)
    }
}
document.addEventListener("DOMContentLoaded", function() {
    hideLoader()
});