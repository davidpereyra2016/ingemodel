document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendarInicio')
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'multiMonthYear',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        views: {
            multiMonthYear: {
                type: 'multiMonthYear',
                duration: {
                    months: 6
                },
                duration: {
                    months: 6
                },
                buttonText: '6 meses'
            }
        },
        locale: 'es',
        initialDate: new Date(),
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true, // allow "more" link when too many events

    })
    calendar.render()
})