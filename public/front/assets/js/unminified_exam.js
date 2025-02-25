const leftCol = document.getElementsByClassName('left_col');
const resizer = document.getElementsByClassName('resizer');
let isResizing = false;
let offsetX = 0;
let onchangecountdown;
let loaderVisibleonchange = false;
let secondsLeftcountdown = 5;
let show1time = true;
let calledtime = 0;
let question_time_reply = 0;
let sec = 0;
let qalan_vaxt;
let finishmodalshowed = false;
let redirect_url = null;
let allowReload = false;
let erroroccurs = false;
let errorCount = 0;
let isTonextRunning = false;
let clockIntervalId = null;

const time_end_exam = document.getElementById('time_end_exam');
const time_range_sections = document.getElementById('time_range_sections');
const section_start_time = document.getElementById('section_start_time');
const time_exam = document.getElementById('time_exam');
const next_section = document.getElementById('next_section');
const examTimer = new ExamTimer(parseInt(time_end_exam.value, 10) || 0);
const timerManager = new TimerManager();
const tonextDebounced = debounce(async (tolast, type) => {
  await tonext(tolast, type);
}, 1000);


function togglehours() {
  var clock_area = document.getElementById('timer_section');
  var clock_toggle_button = document.getElementById('timer_button');
  if (clock_area.classList.contains('hide')) {
    clock_area.classList.remove('hide');
    clock_toggle_button.text = hide_text;
  } else {
    clock_area.classList.add('hide');

    clock_toggle_button.text = show_text;
  }
}

function togglecalculator() {
  $('#desmoscalculator').toggle();
  //jquery Draggable
  $('#desmoscalculator')
    .draggable({
      containment: 'body',
      drag: function (event, ui) {
        $(this).css({
          top: ui.position.top + 'px',
          left: ui.position.left + 'px',
        });
      },
      touchStart: function (event, ui) {
        var offsetX =
          event.originalEvent.touches[0].pageX - $(this).offset().left;
        var offsetY =
          event.originalEvent.touches[0].pageY - $(this).offset().top;
        $(this).data('offset', {
          x: offsetX,
          y: offsetY,
        });
      },
      touchMove: function (event, ui) {
        var offset = $(this).data('offset');
        var x = event.originalEvent.touches[0].pageX - offset.x;
        var y = event.originalEvent.touches[0].pageY - offset.y;
        $(this).css({
          top: y + 'px',
          left: x + 'px',
        });
      },
    })
    .resizable({
      handles: 'n, e, s, w, ne, se, sw, nw',
      minHeight: 200,
      minWidth: 200,
    });
}

function touchHandler(event) {
  var touch = event.changedTouches[0];

  var simulatedEvent = document.createEvent('MouseEvent');
  simulatedEvent.initMouseEvent(
    {
      touchstart: 'mousedown',
      touchmove: 'mousemove',
      touchend: 'mouseup',
    }[event.type],
    true,
    true,
    window,
    1,
    touch.screenX,
    touch.screenY,
    touch.clientX,
    touch.clientY,
    false,
    false,
    false,
    false,
    0,
    null
  );

  touch.target.dispatchEvent(simulatedEvent);
}

function init() {
  document.addEventListener('touchstart', touchHandler, true);
  document.addEventListener('touchmove', touchHandler, true);
  document.addEventListener('touchend', touchHandler, true);
  document.addEventListener('touchcancel', touchHandler, true);
}

window.addEventListener('load', function () {
  init();
  timerManager.setTimer('updatepad', updatepad, 500);
  var page_type = document.getElementById('page_type');
  if (!page_type || page_type.value !== 'result') {
    timerManager.setTimer('updateClock', updateClock, 1000);
  }
});

function togglereferances() {
  $('#references').toggle();
  $('#references').draggable();
}

function toggleClass(element, className, state) {
  element.classList[state ? 'add' : 'remove'](className);
}

function toback() {
  try {
    showLoader();
    var current_question = document.getElementById('current_question').value;
    var first_question = document.getElementsByClassName('content_exam')[0];
    var currentDivQuestion = document.getElementById(
      `content_exam_${current_question}`
    );
    showfinishmodal('hide');
    if (current_question == first_question.dataset.id) {
      hideLoader();
    } else {
      // currentDivQuestion.classList.remove('show');
      toggleClass(currentDivQuestion, 'show', false);
      var new_key = parseInt(currentDivQuestion.dataset.key) - 1;
      var nextDivQuestion = document.querySelectorAll(
        `.content_exam[data-key="${new_key}"]`
      );
      nextDivQuestion.forEach(function (element) {
        element.classList.add('show');
        document.getElementById('current_question').value = element.dataset.id;
        document.getElementById('current_section_name').value =
          element.dataset.section_name;
        document.getElementById('current_section').value =
          element.dataset.section_id;
      });

      stopaudios();
      hideLoader();
    }

    question_time_reply = 0;
  } catch (error) {
    hideLoader();
    toast(error, 'error');
  }
}

function togglequestions(modal = false) {
  var footer_questions = document.getElementById('footer_questions');
  showfinishmodal('hide');
  if (footer_questions.classList.contains('active')) {
    footer_questions.classList.remove('active');
  } else {
    footer_questions.classList.add('active');
  }

  if (modal == true) {
    showfinishmodal('open');
  }
}

async function tonext(tolast = false, type = null) {
  try {
    if (isTonextRunning) return;
    isTonextRunning = true;
    showLoader();
    var current_question = document.getElementById('current_question').value;
    var all_questions = document.getElementById('all_questions').value;
    var currentDivQuestion = document.getElementById(`content_exam_${current_question}`);
    var loader_for_sections = document.getElementById('loader_for_sections');
    var form = document.getElementById('exam');
    question_time_reply = 0;
    stopaudios();
    if (all_questions == currentDivQuestion.dataset.key || tolast == true) {
      if (finishmodalshowed == false && tolast == false) {
        hideLoader();
        showfinishmodal('open');
      } else {
        if (
          type == 'imtahanzamanibitdi' &&
          next_section.value == true &&
          time_range_sections.value > 0
        ) {
          time_range_sections.value = time_range_sections.value - 1;
          if (time_range_sections.value > 0) {
            hideLoader();
            if (next_section.value == 1) {
              section_start_time.value = time_exam.value;
              form.classList.remove('d-block');
              form.style.display = 'none';
              loader_for_sections.classList.add('active');
            }
          } else {
            allowReload = true;
            await tonext(true, 'imtahanzamanibitdi');
          }
        } else {
          var forum = document.getElementById('exam');
          var formData = new FormData(forum);
          calledtime++;
          var page_type = document.getElementById('page_type');
          if (!page_type || !page_type.value || page_type.value != 'result') {
            fetch('/api/finish_exam', {
              method: 'POST',
              body: formData,
            })
              .then(response => {
                if (!response.ok) {
                  throw new Error('Network response was not ok.');
                }
                return response.json();
              })
              .then(data => {
                if (data.message) toast(data.message, data.status);
                hideLoader();
                
                if (data.status == 'success') {
                  if (data.url != null && data.url != '' && data.url != ' ') {
                    redirect_url = data.url;
                    errorCount = 0;
                    erroroccurs = false;
                    if (calledtime > 1) {
                      window.location.href = data.url;
                      return;
                    }
                  }

                  if (data.nextsection == false) {
                    allowReload = true;
                    window.location.href = data.url;
                  }

                  if (type == 'imtahanzamanibitdi') {
                    allowReload = true;
                    window.location.href = data.url;
                  }
                } else {
                  toast(
                    'Şəbəkədə problem var. 3 saniyə ərzində yenidən yoxlanılacaq...',
                    'info'
                  );

                  const delay = ms =>
                    new Promise(resolve => setTimeout(resolve, ms));

                  async function run() {
                    await delay(3000);
                    try {
                      await tonext(true);
                    } catch (error) {
                      console.error(error);
                    }
                  }

                  run();
                }
              })
              .catch(async error => {
                hideLoader();
                console.log('Bu funksiya isledi');
                erroroccurs = true;
                errorCount++;
                toast('İnternet xətası yenidən yoxlanılır!', 'error');
                // setTimeout(async () => {
                //   await tonext(tolast, type);
                // }, 1500);
                if (errorCount > 5) { // 5 denemeden sonra dur
                  toast('İnternet bağlantısı yok. Lütfen bağlantınızı kontrol edin ve tekrar deneyin.', 'error');
                  // İsteğe bağlı: İleri butonunu devre dışı bırakabilir veya bir uyarı modalı gösterebilirsiniz
                } else {
                  toast('İnternet xətası yenidən yoxlanılır!', 'error');
                  setTimeout(async () => {
                    await tonext(tolast, type);
                  }, 1500);
                }
              });

            if (time_range_sections.value > 0) {
              hideLoader();
              if (next_section.value == 1) {
                section_start_time.value = time_exam.value;
                form.classList.remove('d-block');
                form.style.display = 'none';
                loader_for_sections.classList.add('active');
              }
            }
          } else {
            window.location.href = '/';
          }
        }
      }
    } else {
      // currentDivQuestion.classList.remove('show');
      toggleClass(currentDivQuestion, 'show', false);
      var new_key = parseInt(currentDivQuestion.dataset.key) + 1;
      var nextDivQuestion = document.querySelectorAll(
        `.content_exam[data-key="${new_key}"]`
      );
      nextDivQuestion.forEach(function (element) {
        element.classList.add('show');
        document.getElementById('current_question').value = element.dataset.id;
        document.getElementById('current_section_name').value =
          element.dataset.section_name;
        document.getElementById('current_section').value =
          element.dataset.section_id;
      });
      hideLoader();
    }
  } catch (error) {
    console.error('Buradadir 2 xeta');
    console.log(error);
    erroroccurs = true;
    errorCount++;
    // toast('İnternet xətası yenidən yoxlanılır!', 'error');
    // setTimeout(async () => {
    //   await tonextDebounced(tolast, type);
    // }, 1000);
    if (errorCount > 5) { // 5 denemeden sonra dur
      toast('İnternet bağlantısı yok. Lütfen bağlantınızı kontrol edin ve tekrar deneyin.', 'error');
      // İsteğe bağlı: İleri butonunu devre dışı bırakabilir veya bir uyarı modalı gösterebilirsiniz
    } else {
      toast('İnternet xətası yenidən yoxlanılır!', 'error');
      setTimeout(async () => {
        await tonext(tolast, type);
      }, 1500);
    }
  } finally {
    isTonextRunning = false;
  }
}

function showfinishmodal(action) {
  var showfinishmodal = document.getElementById('showfinishmodal');
  var content_area_exam = document.getElementById('content_area_exam');
  var footer_questions = document.getElementById('footer_questions');
  var footer_questions_top = document.getElementById('footer_questions_top');
  if (action == 'open') {
    finishmodalshowed = true;
    showfinishmodal.classList.remove('hide');
    footer_questions_top.innerHTML = footer_questions.innerHTML;
    content_area_exam.classList.add('hide');
  } else {
    finishmodalshowed = false;
    showfinishmodal.classList.add('hide');
    footer_questions_top.innerHTML = '';
    content_area_exam.classList.remove('hide');
  }
}

function searchinarray(obj, searchVal) {
  var result = false;
  for (var i = 0; i < obj.length; i++) {
    if (obj[i] == searchVal) {
      result = true;
      break;
    }
  }

  return result;
}

function updatepad() {
  var current_question = document.getElementById('current_question').value;
  var first_question = document.getElementById('first_question').value;

  var footer_question_buttons = document.getElementsByClassName(
    'footer_question_buttons'
  );
  var marked_questions = document.getElementById('marked_questions').value;

  for (var i = 0; i < footer_question_buttons.length; i++) {
    const element_for_saved = footer_question_buttons[i];
    element_for_saved.classList.remove('current');

    if (element_for_saved.classList.contains('saved')) {
      element_for_saved.classList.remove('saved');
    }

    if (marked_questions != null && marked_questions.length > 0) {
      marked_questions_jsoned = JSON.parse(marked_questions);
      var buttonDataKey = element_for_saved.getAttribute('data-key');
      if (searchinarray(marked_questions_jsoned, buttonDataKey) == true) {
        element_for_saved.classList.add('saved');
      }
    }
  }

  if (current_question == first_question) {
    document.getElementById('to_back').classList.add('hide');
  } else {
    if (document.getElementById('to_back').classList.contains('hide'))
      document.getElementById('to_back').classList.remove('hide');
  }

  var all_questions = document.getElementById('all_questions').value;
  var footer_active_button = document.getElementById(
    `question_row_button_${current_question}`
  );
  var buttons = document.getElementsByClassName('btn-question');
  var currentDivQuestion = document.getElementById(
    `content_exam_${current_question}`
  );
  var next_button = document.getElementById('next_button');
  var time_range_sections = document.getElementById('time_range_sections')
    .value;
  var next_section = document.getElementById('next_section').value;
  if (all_questions == currentDivQuestion.dataset.key) {
    next_button.classList.remove('btn-secondary');
    next_button.classList.add('active');

    if (time_range_sections > 0) {
      if (next_section == 1) {
        next_button.innerHTML = layout_standart_right;
      } else {
        next_button.innerHTML = referances_standart_check;
      }
    } else {
      next_button.innerHTML = rf_open_check;
    }
  } else {
    next_button.classList.add('btn-secondary');
    next_button.classList.remove('active');
    next_button.innerHTML = rf_aaa;
  }

  var current_question_text = document.getElementById('current_question_text');
  current_question_text.innerText = currentDivQuestion.dataset.key;

  for (var i = 0; i < buttons.length; i++) {
    if (buttons[i].id == `question_row_button_${current_question}`) {
      buttons[i].classList.add('current');
    }
  }

  var section_name_area = document.getElementById('section_name');
  var current_section_name = document.getElementById('current_section_name')
    .value;
  section_name_area.innerText = current_section_name;
}

function getquestion(id) {
  try {
    showLoader();
    var activecontentquestions = document.getElementsByClassName(
      'content_exam'
    );
    showfinishmodal('hide');
    for (var i = 0; i < activecontentquestions.length; i++) {
      activecontentquestions[i].classList.remove('show');
    }

    var selected = document.getElementById(`content_exam_${id}`);
    selected.classList.add('show');
    document.getElementById('current_question').value = id;
    question_time_reply = 0;
    togglequestions();
    hideLoader();
  } catch (error) {
    hideLoader();
    toast(error, 'error');
  }
}

window.addEventListener('load', function () {
  setInterval(updatepad, 500);

  var page_type = document.getElementById('page_type');
  if (!page_type || !page_type.value || page_type.value != 'result') {
    clockIntervalId = setInterval(updateClock, 1000);
  }
});

setInterval(async () => {
  if (erroroccurs == true) {
    await tonext(true, 'imtahanzamanibitdi');
  }
}, 5000);

function pad(val) {
  return val.toString().padStart(2, '0');
}

function pad_new(val) {
  return val > 9 ? val : '0' + val;
}

async function updateClock() {
  sec++;
  question_time_reply++;
  var loader_for_sections = document.getElementById('loader_for_sections');
  let difference = examTimer.getRemainingTime();
  let minutesDifference = Math.floor(difference / (1000 * 60));
  let secondsDifference = Math.floor((difference % (1000 * 60)) / 1000);
  time_exam.value = sec;
  if (
    loader_for_sections.classList.contains('active') &&
    section_start_time.value > 0
  ) {
    qalan_vaxt = calculateRemainingTime(section_start_time.value, time_range_sections.value, time_exam.value);
    let minutesQalanVaxtDifference = qalan_vaxt > 0 ? Math.floor(qalan_vaxt / 60) : 0;
    let secondsQalanVaxtDifference = qalan_vaxt > 0 ? qalan_vaxt % 60 : 0;
    if (document.getElementById('seconds_start_time')) {
      var page_type = document.getElementById('page_type');
      if (!page_type || !page_type.value || page_type.value != 'result') {
        document.getElementById('seconds_start_time').innerHTML = pad_new(
          secondsQalanVaxtDifference
        );
      }
    }

    if (document.getElementById('minutes_start_time')) {
      var page_type = document.getElementById('page_type');
      if (!page_type || !page_type.value || page_type.value != 'result') {
        document.getElementById('minutes_start_time').innerHTML = pad_new(
          minutesQalanVaxtDifference
        );
      }
    }

    if (qalan_vaxt == 0) {
      if (erroroccurs) {
        await tonextDebounced(true, 'imtahanzamanibitdi');
      }

      allowReload = true;

      if (redirect_url != null) {
        timerManager.clearTimer('updateClock');
        window.location.href = redirect_url;
      } else {
        await tonextDebounced(true, 'imtahanzamanibitdi');
      }
    }
  } else {
    section_start_time.value = 0;
    allowReload = true;
  }

  if (difference <= 0) {
    if (timerManager) {
      timerManager.clearAll();
    }
    allowReload = true;
    document.getElementById('minutes').innerHTML = '00';
    document.getElementById('seconds').innerHTML = '00';
    var page_type = document.getElementById('page_type');
    if (!page_type || !page_type.value || page_type.value != 'result') {
      setTimeout(() => {
        tonext(true, 'imtahanzamanibitdi');
      }, 1000);
      return;
    }
  }

  if (clockIntervalId) {
    clearInterval(clockIntervalId);
  }
  if (!allowReload)
    clockIntervalId = setInterval(updateClock, 1000);

  if (document.getElementById('seconds')) {
    var page_type = document.getElementById('page_type');
    if (!page_type || !page_type.value || page_type.value != 'result') {
      document.getElementById('seconds').innerHTML = pad_new(
        secondsDifference
      );
    }
  }

  if (document.getElementById('minutes')) {
    var page_type = document.getElementById('page_type');
    if (!page_type || !page_type.value || page_type.value != 'result') {
      document.getElementById('minutes').innerHTML = pad_new(
        minutesDifference
      );
    }
  }
  requestAnimationFrame(updateClock);
  settimerforcurrentquestion();
}

function settimerforcurrentquestion() {
  if (document.getElementById('current_question')) {
    var current_question = document.getElementById('current_question').value;
    var replyElem = document.getElementById(`question_time_replies_${current_question}`);
    if (replyElem) {
      let currentVal = parseInt(replyElem.value, 10) || 0;
      replyElem.value = currentVal + 1;
    }
  }
}

function onchangeShowLoader() {
  loaderVisibleonchange = true;
  if (show1time == true) {
    alert(if_change_window_tab_text);
    show1time = false;
    allowReload = true;
  } else {
    var modalshowcountdown = `<div id="modalshowcountdown" class="modal custom-modal show" tabindex="-1" role="dialog"
                    aria-labelledby="myModalLabel">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body text-muted text-large my-1">
                                <div class='my-1 d-flex '>
                                    <span class='d-inline-block' id='minutescountdown'>00</span><span class='d-inline-block'>:</span><span
                                        id='secondscountdown' class='d-inline-block'>05</span>
                                </div>
                                <div class='text-center text-danger'>
                                    ${if_change_window_tab_text}
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>`;
    document.body.innerHTML += modalshowcountdown;
    toggleModalnow('modalshowcountdown', 'open');
  }

  onchangeStartCountdown();
}

function onchangeStartCountdown() {
  onchangecountdown = setInterval(async function () {
    if (secondsLeftcountdown == 0) {
      loaderVisibleonchange = false;
      var modalshowcountdown = document.getElementById('modalshowcountdown');

      if (modalshowcountdown != null) {
        setTimeout(() => {
          toggleModalnow('modalshowcountdown', 'hide');
          modalshowcountdown.remove();
        }, 1000);
      } else {
        clearInterval(onchangecountdown);
      }

      await tonext(true);
    }
    var page_type = document.getElementById('page_type');
    if (!page_type || !page_type.value || page_type.value != 'result') {
      if (document.getElementById('secondscountdown') != null) {
        document.getElementById(
          'secondscountdown'
        ).innerHTML = `0${secondsLeftcountdown}`;
      }
    }

    secondsLeftcountdown--;
  }, 1000);
}

function stopaudios() {
  var oneTimeAudios = document.querySelectorAll('.only1time');
  if (oneTimeAudios.length > 0) {
    oneTimeAudios.forEach(function (audio) {
      if (!audio.paused && !audio.ended) {
        audio.pause();
      }
    });
  }
}

function toggle_references_modal_content_element(key) {
  var toggler_button_reference = document.getElementById(
    `toggler_button_reference_${key}`
  );
  var body_reference = document.getElementById(`body_reference_${key}`);

  if (body_reference.classList.contains('hide')) {
    body_reference.classList.remove('hide');
    toggler_button_reference.innerHTML = '<i class="fa fa-minus"></i>';
  } else {
    body_reference.classList.add('hide');
    toggler_button_reference.innerHTML = '<i class="fa fa-plus"></i>';
  }
}

function toggle_references_modal_content(type) {
  var referance_toggle_buttons = document.getElementsByClassName(
    'referance_toggle_button'
  );
  var referance_bodyes = document.getElementsByClassName('referance_body');
  for (var i = 0; i < referance_toggle_buttons.length; i++) {
    if (type == 'open') {
      referance_toggle_buttons[i].innerHTML = '<i class="fa fa-minus"></i>';
    } else {
      referance_toggle_buttons[i].innerHTML = '<i class="fa fa-plus"></i>';
    }
  }

  for (var i = 0; i < referance_bodyes.length; i++) {
    if (type == 'open') {
      referance_bodyes[i].classList.remove('hide');
    } else {
      referance_bodyes[i].classList.add('hide');
    }
  }
}

function toggledesmosmodal(desmosid) {
  showLoader();
  var desmoscalc = document.getElementById(desmosid);
  var desmos_expandable = document.getElementById('desmos_expandable');
  if (desmoscalc.classList.contains('modal-lg')) {
    desmoscalc.classList.remove('modal-lg');
    desmos_expandable.innerHTML = '<i class="fa fa-expand-alt"></i>';
  } else {
    desmoscalc.classList.add('modal-lg');
    desmos_expandable.innerHTML = '<i class="fa fa-compress-alt"></i>';
  }
  hideLoader();
}

document.addEventListener('DOMContentLoaded', function () {
  renderMathInElement(document.body, {
    delimiters: [
      {
        left: '$$',
        right: '$$',
        display: true,
      },
      {
        left: '$',
        right: '$',
        display: false,
      },
      {
        left: '\\(',
        right: '\\)',
        display: true,
      },
      {
        left: '\\[',
        right: '\\]',
        display: true,
      },
    ],
    throwOnError: false,
  });
});

function increase_decrease_font(type) {
  var elements = document.getElementsByClassName('content_exam_info');

  for (var i = 0; i < elements.length; i++) {
    var fontSize = parseInt(window.getComputedStyle(elements[i]).fontSize); // Mevcut font boyutunu al

    if (type === 'increase') {
      elements[i].style.fontSize = fontSize + 1 + 'px'; // Font boyutunu artır
    } else if (type === 'decrease') {
      elements[i].style.fontSize = fontSize - 1 + 'px'; // Font boyutunu azalt
    }

    // p ve span elementlerini bul
    var pElements = elements[i].getElementsByTagName('p');
    var spanElements = elements[i].getElementsByTagName('span');

    // p elementlerinin font boyutunu değiştir
    for (var j = 0; j < pElements.length; j++) {
      var pFontSize = parseInt(
        window.getComputedStyle(pElements[j]).fontSize
      );
      if (type === 'increase') {
        pElements[j].style.fontSize = pFontSize + 1 + 'px';
      } else if (type === 'decrease') {
        pElements[j].style.fontSize = pFontSize - 1 + 'px';
      }
    }

    // span elementlerinin font boyutunu değiştir
    for (var k = 0; k < spanElements.length; k++) {
      var spanFontSize = parseInt(
        window.getComputedStyle(spanElements[k]).fontSize
      );
      if (type === 'increase') {
        spanElements[k].style.fontSize = spanFontSize + 1 + 'px';
      } else if (type === 'decrease') {
        spanElements[k].style.fontSize = spanFontSize - 1 + 'px';
      }
    }
  }
}

function remove_button_toggler() {
  var elements = document.getElementsByClassName('remove_button');
  var btn_question_container_undo_or_redo = document.getElementsByClassName(
    'btn-question_container_undo_or_redo'
  );
  var question_answer_one_element_container = document.getElementsByClassName(
    `question_answer_one`
  );
  var question_container_undo_or_redo = document.getElementsByClassName(
    `btn-question_container_undo_or_redo`
  );

  for (
    let index = 0;
    index < btn_question_container_undo_or_redo.length;
    index++
  ) {
    const element = btn_question_container_undo_or_redo[index];
    if (element.classList.contains('show')) {
      element.classList.remove('show');
      for (
        let index2 = 0;
        index2 < question_answer_one_element_container.length;
        index2++
      ) {
        const element2 = question_answer_one_element_container[index];
        element2.classList.remove('removable');
      }
      for (
        let index3 = 0;
        index3 < question_container_undo_or_redo.length;
        index3++
      ) {
        const element3 = question_container_undo_or_redo[index];
        element3.innerHTML = `<img src="${aplus_icon_path}" class="img-fluid img-responsive" />`;
      }
    } else {
      element.classList.add('show');
    }
  }

  for (var i = 0; i < elements.length; i++) {
    if (elements[i].classList.contains('active')) {
      elements[i].classList.remove('active');
    } else {
      elements[i].classList.add('active');
    }
  }
}

function select_answer(question_id, answer_id, type) {
  var all_questions = document.getElementById('all_questions').value;
  var currentDivQuestion = document.getElementById(
    `content_exam_${question_id}`
  );
  var answers_selected = document.getElementsByClassName(
    `answers_${question_id}`
  );
  var clicked_el = document.getElementById(
    `question_answer_one_${question_id}_${answer_id}_${type}`
  );
  var radio_input = clicked_el.querySelector('input[type="radio"]');
  var checkbox_input = clicked_el.querySelector('input[type="checkbox"]');

  set_question_value_on_session(question_id, answer_id, type);

  if (clicked_el.classList.contains('removable')) {
    return;
  }

  if (type == 'radio') {
    for (var i = 0; i < answers_selected.length; i++) {
      answers_selected[i].classList.remove('selected');
      var input = answers_selected[i].querySelector('input[type="radio"]');
      if (input) {
        input.checked = false;
      }
    }
  }

  clicked_el.classList.toggle('selected');

  if (radio_input) {
    radio_input.checked = clicked_el.classList.contains('selected');
  }

  if (checkbox_input) {
    checkbox_input.checked = clicked_el.classList.contains('selected');
  }

  if (all_questions != currentDivQuestion.dataset.key && type == 'radio') {
    // tonext();
  }

  document
    .getElementById(`question_row_button_${question_id}`)
    .classList.add('answered');
  updatepad();
}

function changeTextBox(question_id, type) {
  var text_box = document.getElementById(
    `question_answer_one_${question_id}_${type}`
  ).value;
  if (text_box.length > 5) {
    document.getElementById(
      `question_answer_one_${question_id}_${type}`
    ).value = text_box.substring(0, 5);
  }
  var answer_footer_buttons = document.getElementById(
    `question_row_button_${question_id}`
  );
  var question_textbox_text_span = document.getElementById(
    `question_textbox_text_span_${question_id}`
  );

  if (question_textbox_text_span !== null) {
    question_textbox_text_span.innerHTML = '';
  }
  text_box = document.getElementById(
    `question_answer_one_${question_id}_${type}`
  ).value;
  if (
    text_box.length > 0 &&
    text_box != null &&
    $.trim(text_box) != '' &&
    $.trim(text_box) != null &&
    $.trim(text_box) != ' '
  ) {
    var parts = text_box.split('/');
    answer_footer_buttons.classList.add('answered');
    if (parts.length === 2) {
      var x = parts[0];
      var y = parts[1];
      var rendered = katex.renderToString(`\\frac{${x}}{${y}}`, {
        throwOnError: false,
        displayMode: true,
      });
      question_textbox_text_span.innerHTML = `<span>${rendered}</span>`;
      set_question_value_on_session(question_id, rendered, type);
    } else {
      question_textbox_text_span.innerHTML = `${text_box}`;
      set_question_value_on_session(question_id, text_box, type);
    }
  } else {
    answer_footer_buttons.classList.remove('answered');
    question_textbox_text_span.innerHTML = '';
  }
}

function mark_unmark_question(id) {
  var exam_id = document.getElementById('exam_id').value;
  var exam_result_id = document.getElementById('exam_result_id').value;
  var user_id = document.getElementById('user_id').value;
  var language = document.getElementById('language').value;
  sendAjaxRequestOLD(
    '/api/mark_unmark_question',
    'post',
    {
      question_id: id,
      exam_id: exam_id,
      exam_result_id: exam_result_id,
      language: language,
      user_id: user_id,
    },
    function (e, t) {
      if (e) {
        toast(e, 'error');
      } else {
        try {
          if (t) {
            let n = JSON.parse(t);
            toast(n.message, n.status);
            var marked_questions = document.getElementById('marked_questions');
            marked_questions.value = JSON.stringify(n.data);
            var element = document.getElementById(
              `mark_question_button_${id}`
            );
            if (element.classList.contains('active')) {
              element.classList.remove('active');
              element.innerHTML = '<i class="far fa-bookmark"></i>';
            } else {
              element.classList.add('active');
              element.innerHTML = '<i class="fa fa-bookmark"></i>';
            }
          } else {
            // toast ('İnternet xətası yenidən yoxlanılır...', 'error');
          }
        } catch (err) {
          // toast ('İnternet xətası yenidən yoxlanılır...', 'error');
        }
      }
    }
  );
}

document.addEventListener('DOMContentLoaded', function () {
  var oneTimeAudios = document.querySelectorAll('.only1time');

  oneTimeAudios.forEach(function (audio) {
    audio.addEventListener(
      'play',
      function (event) {
        audio.controls = false;
        var audio_tag_text = document.querySelector('.audio_tag_text');
        audio_tag_text.innerHTML += `<span class="text-info">${audio_file_played_text}</span>`;
        audio.removeEventListener('play', arguments.callee);
      },
      {
        once: true,
      }
    );
  });
});

function draggingleftandrightcolumns() {
  for (let index = 0; index < resizer.length; index++) {
    const element = resizer[index];
    element.addEventListener('mousedown', startResize);
    element.addEventListener('touchstart', startResize);
    element.addEventListener('mouseover', handleMouseOver);
    element.addEventListener('mouseleave', handleMouseLeave);
  }
}

function startResize(e) {
  isResizing = true;
  offsetX = e.type === 'mousedown' ? e.clientX : e.touches[0].clientX;
  document.addEventListener('mousemove', resize);
  document.addEventListener('touchmove', resize);
  document.addEventListener('mouseup', stopResize);
  document.addEventListener('touchend', stopResize);
}

function resize(e) {
  if (!isResizing) {
    return;
  }
  var minusable = 0;

  if (e && e.type === 'touchstart') {
    minusable =
      e.touches[0].clientX -
      (e.srcElement.getBoundingClientRect().left || e.srcElement.offsetLeft);
  } else {
    if (e.layerX > 0) {
      minusable = e.clientX - (e.layerX + e.srcElement.offsetLeft);
    } else {
      minusable = e.clientX - e.srcElement.offsetLeft;
    }
  }

  const size = `${e.touches ? e.touches[0].clientX : e.clientX - minusable}px`;
  for (let index = 0; index < leftCol.length; index++) {
    const element = leftCol[index];
    element.style.width = size;
  }
}

function stopResize() {
  isResizing = false;
  document.removeEventListener('mousemove', resize);
  document.removeEventListener('touchmove', resize);
  document.removeEventListener('mouseup', stopResize);
  document.removeEventListener('touchend', stopResize);
}

function handleMouseOver(e) {
  if (!isResizing) {
    e.currentTarget.style.opacity = 1;
  }
}

function handleMouseLeave(e) {
  if (!isResizing) {
    e.currentTarget.style.opacity = 0.5;
  }
}

window.addEventListener('load', function () {
  draggingleftandrightcolumns();
  sortanswerarea();
});

function sortanswerarea() {
  let answerareas = document.getElementsByClassName('answers_match_area');
  if (answerareas != null && answerareas.length > 0) {
    for (let index = 0; index < answerareas.length; index++) {
      const element = answerareas[index];
      $(`#${element.id}`).sortable({
        start: function (event, ui) {
          var questionelem = ui.item[0];
          var questionId = questionelem.dataset.question_id;
          var sectionId = questionelem.dataset.section_id;
          var answer_footer_buttons = document.getElementById(
            `question_row_button_${questionId}`
          );
          answer_footer_buttons.classList.add('answered');
          var question_match_element = document.getElementById(
            `question_match_element_${sectionId}_${questionId}`
          );
          question_match_element.value = 1;
        },
      });
      $(`#${element.id}`).disableSelection();
    }
  }
}

function toggleabcline(question_id, value_id) {
  var question_answer_one_element_container_radio = document.getElementById(
    `question_answer_one_${question_id}_${value_id}_radio`
  );
  var question_answer_one_element_container_checkbox = document.getElementById(
    `question_answer_one_${question_id}_${value_id}_checkbox`
  );
  var question_container_undo_or_redo = document.getElementById(
    `question_container_undo_or_redo_${question_id}_${value_id}`
  );
  if (question_answer_one_element_container_radio != null) {
    if (
      question_answer_one_element_container_radio.classList.contains(
        'removable'
      )
    ) {
      question_answer_one_element_container_radio.classList.remove(
        'removable'
      );
      question_container_undo_or_redo.innerHTML = `<img src="${aplus_icon_path} class="img-fluid img-responsive" />`;
    } else {
      question_answer_one_element_container_radio.classList.add('removable');
      question_container_undo_or_redo.innerHTML = `<span>${undo_text}</span>`;
    }
  }

  if (question_answer_one_element_container_checkbox != null) {
    if (
      question_answer_one_element_container_checkbox.classList.contains(
        'removable'
      )
    ) {
      question_answer_one_element_container_checkbox.classList.remove(
        'removable'
      );
      question_container_undo_or_redo.innerHTML = `<img src="${aplus_icon_path} class="img-fluid img-responsive" />`;
    } else {
      question_answer_one_element_container_checkbox.classList.add(
        'removable'
      );
      question_container_undo_or_redo.innerHTML = `<span>${undo_text}</span>`;
    }
  }
}

function set_question_value_on_session(question_id, answer_id = null, type) {
  try {
    var exam_id = document.getElementById('exam_id').value;
    var exam_result_id = document.getElementById('exam_result_id').value;
    var user_id = document.getElementById('user_id').value;
    var language = document.getElementById('language').value;
    var _token = document
      .querySelector('meta[name="_token"]')
      .getAttribute('content');
    sendAjaxRequestOLD(
      '/exams_set_question_value_on_session',
      'post',
      {
        exam_id,
        result_id: exam_result_id,
        language,
        user_id,
        question_id,
        answer_id: answer_id,
        type_value: type,
        _token,
      },
      function (e, t) {
        try {
          if (t) {
            let n = JSON.parse(t);
          }
        } catch (err) {
        }
      }
    );
  } catch (err) {
    if (err) {
    }
    console.log(err);
  }
}

document.addEventListener('keyup', function (e) {
  var keyCode = e.keyCode ? e.keyCode : e.which;
  if (keyCode == 44) {
    stopPrntScr();
  }
});

function stopPrntScr() {
  var inpFld = document.createElement('input');
  inpFld.setAttribute('value', '.');
  inpFld.setAttribute('width', '0');
  inpFld.style.height = '0px';
  inpFld.style.width = '0px';
  inpFld.style.border = '0px';
  document.body.appendChild(inpFld);
  inpFld.select();
  document.execCommand('copy');
  inpFld.remove(inpFld);
}

function AccessClipboardData() {
  try {
    window.clipboardData.setData('text', 'Access   Restricted');
  } catch (err) { }
}

setInterval(AccessClipboardData(), 300);

function copyToClipboard() {
  var aux = document.createElement('input');
  aux.setAttribute('value', 'print screen disabled!');
  document.body.appendChild(aux);
  aux.select();
  document.execCommand('copy');
  document.body.removeChild(aux);
  toast("@lang('additional.messages.noprint')", 'error');
}

$(window).keyup(function (e) {
  if (e.keyCode == 44) {
    copyToClipboard();
  }
});

showLoader();
