function generateCalendar() {
    const date = new Date();
    const month = date.getMonth();
    const year = date.getFullYear();

    const calendarContainer = document.getElementById('simple-calendar');
    calendarContainer.innerHTML = `<h2>${date.toLocaleString('default', { month: 'long' })} ${year}</h2>`;

    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const firstDayIndex = new Date(year, month, 1).getDay();

    let days = "";

    for (let i = 0; i < firstDayIndex; i++) {
        days += `<div class="empty"></div>`;
    }

    for (let i = 1; i <= daysInMonth; i++) {
        if (i === date.getDate()) {
            days += `<div class="today">${i}</div>`;
        } else {
            days += `<div>${i}</div>`;
        }
    }

    calendarContainer.innerHTML += `<div class="days">${days}</div>`;
    }

generateCalendar();

