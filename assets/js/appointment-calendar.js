(function () {
  var root = document.querySelector("[data-calendar]");
  if (!root) return;

  var titleEl = root.querySelector("[data-cal-title]");
  var daysEl = root.querySelector("[data-cal-days]");
  var timesEl = root.querySelector("[data-cal-times]");
  var previewEl = root.querySelector("[data-cal-preview]");
  var dateInput = root.querySelector('input[name="preferred_date"]');
  var timeInput = root.querySelector('input[name="preferred_time"]');
  var view = new Date();
  view.setDate(1);
  var selectedDate = dateInput && dateInput.value ? dateInput.value : "";
  var selectedTime = timeInput && timeInput.value ? timeInput.value : "";
  var today = new Date();
  today.setHours(0, 0, 0, 0);

  var slots = [];
  for (var hour = 8; hour <= 17; hour++) {
    slots.push((hour < 10 ? "0" : "") + hour + ":00");
    slots.push((hour < 10 ? "0" : "") + hour + ":30");
  }
  slots.push("18:00");

  function pad(n) {
    return n < 10 ? "0" + n : String(n);
  }

  function iso(date) {
    return date.getFullYear() + "-" + pad(date.getMonth() + 1) + "-" + pad(date.getDate());
  }

  function parseIso(value) {
    var parts = String(value).split("-");
    return new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
  }

  function formatTime(value) {
    var bits = String(value).split(":");
    var date = new Date(2026, 0, 1, Number(bits[0] || 0), Number(bits[1] || 0));
    var hours = date.getHours();
    var minutes = pad(date.getMinutes());
    var suffix = hours >= 12 ? "PM" : "AM";
    hours = hours % 12 || 12;
    return hours + ":" + minutes + " " + suffix;
  }

  function renderPreview() {
    if (!previewEl) return;
    if (!selectedDate || !selectedTime) {
      previewEl.innerHTML = '<div class="mmh_booking_when mmh_booking_when--empty"><div class="mmh_booking_when_text"><strong>Choose a date and time</strong><span>Your booking will appear here the same way it will be confirmed.</span></div></div>';
      return;
    }
    var date = parseIso(selectedDate);
    var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    var shortMonths = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    var days = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    previewEl.innerHTML =
      '<div class="mmh_booking_when">' +
        '<div class="mmh_booking_when_cal">' +
          '<span class="mmh_booking_when_month">' + shortMonths[date.getMonth()] + "</span>" +
          '<span class="mmh_booking_when_day">' + date.getDate() + "</span>" +
          '<span class="mmh_booking_when_year">' + date.getFullYear() + "</span>" +
        "</div>" +
        '<div class="mmh_booking_when_text">' +
          "<strong>" + days[date.getDay()] + "</strong>" +
          "<span>" + months[date.getMonth()] + " " + date.getDate() + ", " + date.getFullYear() + "</span>" +
          "<em>" + formatTime(selectedTime) + "</em>" +
        "</div>" +
      "</div>";
  }

  function renderTimes() {
    timesEl.innerHTML = "";
    slots.forEach(function (slot) {
      var btn = document.createElement("button");
      btn.type = "button";
      btn.className = "mmh_calendar_time" + (selectedTime === slot ? " is-active" : "");
      btn.textContent = formatTime(slot);
      btn.addEventListener("click", function () {
        selectedTime = slot;
        timeInput.value = slot;
        renderTimes();
        renderPreview();
      });
      timesEl.appendChild(btn);
    });
  }

  function renderDays() {
    var monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    titleEl.textContent = monthNames[view.getMonth()] + " " + view.getFullYear();
    daysEl.innerHTML = "";
    var start = new Date(view.getFullYear(), view.getMonth(), 1);
    var offset = start.getDay();
    var last = new Date(view.getFullYear(), view.getMonth() + 1, 0).getDate();
    var i;
    for (i = 0; i < offset; i++) {
      var empty = document.createElement("span");
      empty.className = "mmh_calendar_day is-empty";
      daysEl.appendChild(empty);
    }
    for (i = 1; i <= last; i++) {
      var cellDate = new Date(view.getFullYear(), view.getMonth(), i);
      var value = iso(cellDate);
      var btn = document.createElement("button");
      btn.type = "button";
      btn.className = "mmh_calendar_day";
      btn.textContent = String(i);
      if (cellDate < today) {
        btn.disabled = true;
        btn.className += " is-disabled";
      }
      if (cellDate.getDay() === 0) {
        btn.disabled = true;
        btn.className += " is-disabled";
      }
      if (value === iso(today)) {
        btn.className += " is-today";
      }
      if (value === selectedDate) {
        btn.className += " is-active";
      }
      btn.addEventListener("click", function (chosen) {
        return function () {
          selectedDate = chosen;
          dateInput.value = chosen;
          renderDays();
          renderPreview();
        };
      }(value));
      daysEl.appendChild(btn);
    }
  }

  root.querySelector("[data-cal-prev]").addEventListener("click", function () {
    view.setMonth(view.getMonth() - 1);
    renderDays();
  });
  root.querySelector("[data-cal-next]").addEventListener("click", function () {
    view.setMonth(view.getMonth() + 1);
    renderDays();
  });

  if (selectedDate) {
    var current = parseIso(selectedDate);
    view = new Date(current.getFullYear(), current.getMonth(), 1);
  }

  renderDays();
  renderTimes();
  renderPreview();
})();
