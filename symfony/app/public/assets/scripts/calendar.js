let selectedDate = markSelectedDate();
printMonth(selectedDate, null);

genericSelectElementsOnLoad('months', Number(selectedDate.month() + 1));
genericSelectElementsOnLoad('years', selectedDate.year());

function removeSelectedDay(boxes) {
    for (const box of boxes) {
        box.classList.remove('activeDay');
    }
}

function refreshCalendar(boxes) {
    for (const box of boxes) {
        box.removeAttribute('data-date');
        box.classList.remove('activeDay');
        box.classList.remove('backgroundDay');
    }
}

function refreshPrintParagraph(date) {
    document.getElementById('datePrintParagraph').innerHTML = "";
}

function axiosSendGetParams(dateParam, locationParam, timeout) {
    axios({
        method: 'get',
        url: '/date',
        delay: timeout,
        params: {
            date: dateParam,
            locationId: locationParam,
        }
    }).then(function (response) {
        printAppointments(response.data, dateParam)
    })
    document.getElementById('datePrintParagraph').innerHTML = "";
}

function axiosSendPostParams(from, to, location) {
    const data = new FormData();
    data.set('date_start', from);
    data.set('id_location', location);
    // data.set('_token', document.querySelector('meta[name=csrfToken]').content);
    axios.post('/date',
        data,
    ).then(function (response) {
        axiosSendGetParams(from, location, 0);
        if (response.data['state'] === 'good') {
            customAlertSuccess('Appointment made successfully!');
        } else {
            customAlertError(response.data);
        }
    });
}

function printAppointments(appointments, date) {
    refreshPrintParagraph(date);
    if (appointments.length === 0) {
        document.getElementById('datePrintParagraph').innerHTML = 'No appointments for today';
    } else {
        let appointmentIdToDelete = null;
        for (let appointment of appointments) {
            document.getElementById('datePrintParagraph').innerHTML += `
            <div class="appointment">
            <img src="${appointment.profile_picture}" class="profilePicture" alt="poza">
                <p id="user_name" class="appointment_name">${appointment.user_name}</p>
                <h2 class="appointment_address">${appointment.location_address}</h2>
                <h2 class="appointment_city">${appointment.location_city}</h2> `
            if (appointment.isAppointmentFromLoggedInUser === true) {
                document.getElementById('datePrintParagraph').lastChild.innerHTML +=
                    `<div class="deleteAppointment" id="deleteAppointment"><b>DELETE</b><div>`
                appointmentIdToDelete = appointment.id;
            }
        }
        document.getElementById('deleteAppointment')?.addEventListener("click", e => {
            axios({
                method: 'GET',
                url: '/deleteAppointment',
                params: {
                    appointmentId: appointmentIdToDelete,
                    appointmentDate: date,
                }
            }).then(function (response) {
                if (response.data['state'] === 'good') {
                    axiosSendGetParams(date, document.getElementById('locations').value, 0);
                    customAlertSuccess('Appointment deleted successfully!');
                } else {
                    customAlertError(response.data);
                }
            })
        });
    }
}

function genericSelectElementsOnLoad(id, parsedFunction) {
    for (let child of document.getElementById(id).children) {
        if (parseInt(child.value) === parsedFunction) {
            child.setAttribute('selected', 'selected');
        }
    }
}

function isSomethingSelected() {
    // TODO: check if date is sent trough Axios(GET), return true, else return false
    return false;
}

function markSelectedDate() {
    if (isSomethingSelected()) {
        // TODO: retrieve attributes from Axios(GET), call function convertStringToDate(parse Axios GET Attributes), and return it
    } else {
        return moment();
    }
}

function convertStringToDate(string) {
    let dateObj = new Date(string);
    return moment(dateObj);
}