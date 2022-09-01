function printMonth(date, dayAutoSelector) {
    let counter = 0;
    let dayNumber = 1;
    let backgroundDayNumber = 0;
    let boxes =  document.getElementsByClassName('day');
    refreshCalendar(boxes);

    let old_element = document.getElementById("table");
    let new_element = old_element.cloneNode(true);
    old_element.parentNode.replaceChild(new_element, old_element);

    for (const box of boxes) {
        box.style.visibility = 'visible';
        if (counter < date.startOf('month').day() || dayNumber > date.endOf('month').format('D')) {
            if (Number(box.getAttribute('data-row'))===6 && dayNumber > date.endOf('month').format('D')){
                if (Number(date.endOf('month').format('D'))+backgroundDayNumber<=35){
                    box.style.visibility = 'hidden';
                } else {
                    box.style.visibility = 'visible';
                }
            }
            box.classList.add('backgroundDay');
            if (dayNumber>=date.endOf('month').format('D')) {
                box.innerHTML = counter-dayNumber+2-backgroundDayNumber;
            } else {
                box.innerHTML = date.endOf('month').format('D')-date.startOf('month').day()+counter+1;
                backgroundDayNumber++;
            }
        } else {
            if (dayAutoSelector!==null && dayNumber===dayAutoSelector){
                box.classList.add('activeDay');
            }
            box.addEventListener('click', e => {
                document.getElementById('appointmentSubmit').style.visibility = 'visible';
                removeSelectedDay(boxes);
                box.classList.add('activeDay');
                selectedDate = convertStringToDate(box.getAttribute('data-date'))
                axiosSendGetParams(box.getAttribute('data-date'), document.getElementById('locations').value, 0);
                appointmentButtonHandler();
            });
            box.setAttribute('data-date', selectedDate.year() + "-" + Number(selectedDate.month() + 1) + "-" + dayNumber);
            box.innerHTML = dayNumber;
            dayNumber++;
        }
        counter++;
    }
}