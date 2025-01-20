function toggleInputFunction (id) {
  var input = document.querySelector (`input#${id}`);
  var span = document.querySelector (`span#${id}_icon`);
  var eyeSlash = span.querySelector ('i.fa-eye-slash');
  var eye = span.querySelector ('i.fa-eye');
  if (eyeSlash) {
    eyeSlash.classList.remove ('fa-eye-slash');
  }
  if (eye) {
    eye.classList.remove ('fa-eye');
  }
  if (input.getAttribute ('type') === 'password') {
    input.setAttribute ('type', 'text');
    span.querySelector ('i').classList.add ('fa-eye');
  } else {
    input.setAttribute ('type', 'password');
    span.querySelector ('i').classList.add ('fa-eye-slash');
  }
}

function tabselect (id) {
  var tabs = document.getElementsByClassName ('user_or_freelancer_tab');
  for (var i = 0; i < tabs.length; i++) {
    tabs[i].classList.remove ('active');
  }
  var selectedTab = document.getElementsByClassName (
    `user_or_freelancer_tab_${id}`
  );
  if (selectedTab.length > 0) {
    selectedTab[0].classList.add ('active');
  }
  var tabfreelancer = document.getElementsByClassName ('tab_company_element');
  var tabstudent = document.getElementsByClassName ('tab_student_element');
  if (id == 'company') {
    if (tabfreelancer[0] && tabfreelancer[0] != null)
      tabfreelancer[0].style.display = 'block';
    if (tabstudent[0] && tabstudent[0] != null)
      tabstudent[0].style.display = 'none';
    document.getElementById ('user_type').value = 2;
  } else {
    if (tabfreelancer[0] && tabfreelancer[0] != null)
      tabfreelancer[0].style.display = 'none';
    if (tabstudent[0] && tabstudent[0] != null)
      tabstudent[0].style.display = 'block';
    document.getElementById ('user_type').value = 1;
  }
}

function changedFileLabel (id) {
  var element = document.getElementById (id);
  var labelText = element.files.length > 0 ? element.files[0].name : 'fayl';
  var label = document.querySelector (`label[for="${id}"]`);
  label.querySelector ('.file-name').textContent = labelText;
}

function change_tabs_elements (class_onpage, key) {
  var navs = document.getElementById (`${class_onpage}_tab`);
  var button = document.getElementById (`${class_onpage}-${key}_button`);
  var tabs = document.getElementById (`${class_onpage}_tabContent`);
  var tab = document.getElementById (`${class_onpage}-${key}_tab`);
  var buttons = navs.getElementsByClassName ('active');

  Array.from (buttons).forEach (function (btn) {
    btn.classList.remove ('active');
  });
  button.classList.add ('active');
  var tabPanes = tabs.getElementsByClassName ('tab-pane');
  Array.from (tabPanes).forEach (function (tp) {
    tp.classList.remove ('show', 'active', 'fade');
  });
  tab.classList.add ('show', 'active', 'fade');
}


function changeTabElementsIncludedResult(clicked, result_id) {
    // Belirli result_id'ye sahip olan tab ve buttonları kaldır
    $(`div.nav-tabs button[data-result="${result_id}"]`).removeClass('active');
    $(`div.tab-content .tab-pane[data-result="${result_id}"]`).removeClass('show active');

    // Sadece belirli result_id'ye sahip olan tab ve buttonları aktif hale getir
    $(`div.nav-tabs button.${clicked}[data-result="${result_id}"]`).addClass('active');
    $(`div.tab-content .tab-pane.${clicked}[data-result="${result_id}"]`).addClass('show active');
}


function createalert (e, t, n = null) {
  if (null != n) var a = document.querySelector (`form#${n} #messages`);
  else a = document.querySelector ('#messages');
  (a.style.display = 'none'), (a.innerHTML = '');
  var i = document.createElement ('div');
  (i.className = 'alert ' + e), (i.textContent = t), a.appendChild (
    i
  ), (a.style.display = 'block'), setTimeout (function () {
    fadeOut (i);
  }, 2e3), window.scrollTo ({top: 0, behavior: 'smooth'});
}

function fadeOut (e) {
  var t = 1,
    n = setInterval (function () {
      t <= 0.1 &&
        (clearInterval (n), (e.style.display =
          'none'), (document.querySelector ('#messages').style.display =
          'none')), (e.style.opacity = t), (t -= 0.1);
    }, 50);
}

function togglepopup (e) {
  var t = document.getElementById (e);
  t.classList.contains ('active')
    ? t.classList.remove ('active')
    : t.classList.add ('active');
}

function isValidEmail (e) {
  return /\S+@\S+\.\S+/.test (e);
}

function validPhone (e) {
  var t = e.replace (/\D/g, '').match (/(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
  return t[2]
    ? t[1] + ' ' + t[2] + (t[3] ? ' ' + t[3] : '') + (t[4] ? ' ' + t[4] : '')
    : t[1];
}

function searchinfields (e, t, n = 'exams', a = null) {
  event.preventDefault ();

  if (null != a) {
    if ('category' == a) {
      var i = {category: e, type: n, action: a};
      document.querySelectorAll ('.category-item').forEach (function (e) {
        e.classList.remove ('active');
      });
      for (
        var l = document.querySelectorAll ('.category-item.' + e), s = 0;
        s < l.length;
        s++
      )
        l[s].classList.contains ('active')
          ? l[s].classList.remove ('active')
          : l[s].classList.add ('active');
    }
  } else
    i = {query: document.getElementsByName (e)[0].value, type: n, action: a};
  var r = document.getElementById (t);
  sendAjaxRequestOLD('/api/searchinfilled', 'post', i, function (e, t) {
    if (e) createalert ('error', e);
    else {
      let n = JSON.parse (t);
      (r.innerHTML = ''), (r.innerHTML = n.view);
    }
  });
}

function change_filter (e, t = 'datas', n = 'az', a = 'services') {
  for (
    var i = document.querySelectorAll ('.filter_view'), l = 0;
    l < i.length;
    l++
  )
    i[l].classList.remove ('active');
  var s = document.querySelector ('.' + e);
  s.classList.contains ('active')
    ? s.classList.remove ('active')
    : s.classList.add ('active');
  let r = [];
  'services' == a &&
    document.querySelectorAll ('.service_one').forEach (e => {
      let t = e.getAttribute ('id').replace ('service-', '');
      r.push (t);
    });
  var c = document.getElementById (t);
  sendAjaxRequest (
    '/api/filterelements',
    'post',
    {ids: r, type: a, orderby: e, language: n},
    function (e, t) {
      if (e) createalert ('error', e);
      else {
        let n = JSON.parse (t);
        (c.innerHTML = ''), (c.innerHTML = n.view);
      }
    }
  );
}

function showLoader () {
  document.getElementById ('loader').classList.add ('active');
}

function hideLoader () {
  document.getElementById ('loader').classList.remove ('active');
}

function togglefilterelements (element) {
  let elements = document.getElementsByClassName (element);
  for (let i = 0; i < elements.length; i++) {
    let elem = elements[i];
    if (elem.classList.contains ('active')) {
      elem.classList.remove ('active');
    } else {
      elem.classList.add ('active');
    }
  }
}

function toggle_filter_contents (event, element) {
  let elementSelectBox = document.getElementsByClassName (element);
  for (let i = 0; i < elementSelectBox.length; i++) {
    let elem = elementSelectBox[i];
    if (elem.classList.contains ('active')) {
      elem.classList.remove ('active');
    } else {
      elem.classList.add ('active');
    }
  }
}

function setnewparametrandsearch (element, type, id) {
  let formData = new FormData (document.getElementById ('filter_inputs'));

  let existingFilters = formData.getAll (`${element}[]`);

  if (type == 'select') {
    if (!existingFilters.includes (id.toString ())) {
      existingFilters.push (id.toString ());
    } else {
      existingFilters = existingFilters.filter (
        elem => elem !== id.toString ()
      );
    }
    let newFormData = new FormData ();
    existingFilters.forEach (elem => {
      newFormData.append (`${element}[]`, elem);
    });

    let inputElement = document.querySelector (`[name="${element}[]"]`);
    inputElement.value = existingFilters.join (',');
  }

  let newFormDataForUrl = new FormData (
    document.getElementById ('filter_inputs')
  );
  let newUrl = `/exams?`;
  for (const [key, value] of newFormDataForUrl.entries ()) {
    formData.append (key, value);
    newUrl += `${key}=${encodeURIComponent (value)}&`;
  }

  window.location.href = newUrl;
}

function sendAjaxRequestOLD (e, t = 'post', n = null, a) {
  var i = new XMLHttpRequest ();
  (i.onreadystatechange = function () {
    4 === i.readyState &&
      (200 === i.status ? a (null, i.responseText) : a (i.statusText));
  }), 'post' == t
    ? (i.open ('POST', e), i.setRequestHeader (
        'Content-Type',
        'application/json'
      ), i.send (JSON.stringify (n)))
    : (i.open ('GET', e), i.send ());
}

function sendAjaxRequest (url, method = 'post', formData = null, callback) {
  var xhr = new XMLHttpRequest ();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        callback (null, xhr.responseText);
      } else {
        callback (xhr.statusText);
      }
    }
  };

  if (method.toLowerCase () === 'post') {
    xhr.open ('POST', url);
    xhr.send (formData);
  } else {
    xhr.open ('GET', url);
    xhr.send ();
  }
}

function getserializedlang (data, type, lang) {
  var dat;
  if (data != null) {
    if (type == 'name') {
      if (lang == 'az') {
        dat = data.az_name;
      } else if (lang == 'ru') {
        dat = data.ru_name;
      } else if (lang == 'en') {
        dat = data.en_name;
      } else if (lang == 'tr') {
        dat = data.tr_name;
      } else {
        dat = data.az_name;
      }
    } else if (type == 'slug') {
      if (lang == 'az') {
        dat = data.az_slug;
      } else if (lang == 'ru') {
        dat = data.ru_slug;
      } else if (lang == 'en') {
        dat = data.en_slug;
      } else if (lang == 'tr') {
        dat = data.tr_slug;
      } else {
        dat = data.az_slug;
      }
    } else if (type == 'description') {
      if (lang == 'az') {
        dat = data.az_description;
      } else if (lang == 'ru') {
        dat = data.ru_description;
      } else if (lang == 'en') {
        dat = data.en_description;
      } else if (lang == 'tr') {
        dat = data.tr_description;
      } else {
        dat = data.az_description;
      }
    }
  } else {
    dat = '';
  }
  return dat != null ? dat : '';
}

function toggleModalnow (idelement, status = 'open') {
  var modal = document.getElementById (idelement);
  var overlay = document.getElementById ('myModalOverlay');

  if (status === 'open') {
    modal.style.display = 'block';
    overlay = document.createElement ('div');
    overlay.id = 'myModalOverlay';
    overlay.className = 'modal-overlay';
    document.body.appendChild (overlay);
  } else {
    if (overlay) {
      overlay.remove ();
    }
    modal.style.display = 'none';
  }
}

function createRandomCode (type = 'int', length = 4) {
  if (type === 'int') {
    if (length === 4) {
      return Math.floor (Math.random () * 9000) + 1000;
    }
  } else if (type === 'string') {
    const characters =
      '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const charactersLength = characters.length;
    let randomString = '';
    for (let i = 0; i < length; i++) {
      randomString += characters.charAt (
        Math.floor (Math.random () * charactersLength)
      );
    }
    return randomString;
  }
}

function getFileUrl (file, clasore) {
  let url = '/uploads/' + clasore + '/' + file;
  return url;
}

document.addEventListener ('DOMContentLoaded', function () {
  hideLoader ();
});

// window.addEventListener ('load', function () {
//   hideLoader ();
// });

function getandseteditortexts(formdata=null){
  try{
      var editors = document.querySelectorAll('textarea');
      if(editors.length>0){
          editors.forEach(function (editor) {
              var value=null;
              var instance=tinymce.get(editor.id);
              if (instance != null) {
                  var contentinstance = instance.getContent();
                  value=contentinstance;
              }

              if(value!=null && editor.name!=null)
                  formdata.append(editor.name,value);
          });
      }

      return formdata;
  }catch(err){
      console.error(err);
  }
}