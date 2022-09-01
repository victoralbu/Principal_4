function appointmentButtonHandler(){
    if (selectedDate<moment().subtract(1, 'days').toDate()){
        document.getElementById('appointmentSubmit').style.visibility = 'hidden';
    } else {
        document.getElementById('appointmentSubmit').style.visibility = 'visible';
    }
}

// TODO: convert the redundant addEventListeners (calendar.js:9->30) to a generic addEventListeners function, then call it [see genericSelectElementsOnLoad()]

document.getElementById('months').addEventListener("click", function () {
    selectedDate = convertStringToDate(selectedDate.year() + "-" + this.value + "-" + 1);
    appointmentButtonHandler()
    printMonth(selectedDate, 1);
    refreshPrintParagraph();
    axiosSendGetParams(selectedDate.year() + "-" + Number(selectedDate.month() + 1) + "-" + 1, document.getElementById('locations').value, 0);
})

document.getElementById('years').addEventListener("click", function () {
    selectedDate = convertStringToDate(this.value + "-" + Number(selectedDate.month() + 1) + "-" + 1);
    appointmentButtonHandler()
    printMonth(selectedDate, 1);
    refreshPrintParagraph();
    axiosSendGetParams(selectedDate.year() + "-" + Number(selectedDate.month() + 1) + "-" + 1, document.getElementById('locations').value, 0);
})

document.getElementById('locations').addEventListener("click", function () {
    appointmentButtonHandler()
    refreshPrintParagraph();
    axiosSendGetParams(selectedDate.year() + "-" + Number(selectedDate.month() + 1) + "-" + selectedDate.format('D'), document.getElementById('locations').value, 0);
})

document.getElementById('appointmentSubmit').addEventListener('click', function () {
    let date = selectedDate.year() + "-" + Number(selectedDate.month() + 1) + "-" + selectedDate.format('D');
    let location = document.getElementById('locations').value;
    axiosSendPostParams(date, date, location);
});